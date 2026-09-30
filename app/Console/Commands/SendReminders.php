<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SMSController;
use App\Mail\ExpirationReminderMail;
use App\Mail\ExpiryMail;
use App\Models\Entry;
use App\Models\Products;
use App\Models\ReminderLog;
use App\Models\ReminderRule;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    protected $signature = 'send:reminders {--dry-run : Show what would be sent without actually sending}';
    protected $description = 'Process all active reminder rules and send notifications via SMS, Email, and WhatsApp';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $rules = ReminderRule::active()->orderBy('sort_order')->get();

        if ($rules->isEmpty()) {
            $this->info('No active reminder rules found.');
            return 0;
        }

        $wa = new WhatsappService();
        $totalSent = 0;

        foreach ($rules as $rule) {
            $products = $this->getProductsForRule($rule);

            if ($products->isEmpty()) continue;

            $this->info("Rule: {$rule->name} — {$products->count()} product(s) found");

            foreach ($products as $product) {
                $entry = $product->entry;
                if (!$entry || !$entry->reminders_enabled) continue;

                foreach ($rule->channels as $channel) {
                    if ($this->alreadySent($product->id, $rule->id, $channel)) continue;

                    if ($dryRun) {
                        $this->line("  [DRY] {$channel} → {$entry->contact} ({$entry->contactno}) — {$product->product_name}");
                        continue;
                    }

                    $result = $this->sendViaChannel($channel, $rule, $product, $entry, $wa);

                    ReminderLog::create([
                        'product_id' => $product->id,
                        'entry_id' => $entry->id,
                        'rule_id' => $rule->id,
                        'channel' => $channel,
                        'status' => $result['success'] ? 'sent' : 'failed',
                        'error' => $result['error'] ?? null,
                        'created_at' => now(),
                    ]);

                    if ($result['success']) $totalSent++;
                }
            }
        }

        $this->info("Done. {$totalSent} reminder(s) sent.");
        return 0;
    }

    protected function getProductsForRule(ReminderRule $rule)
    {
        $query = Products::with('entry')->whereNotNull('expiry_date');

        return match ($rule->type) {
            'before_expiry' => $query->whereDate('expiry_date', now()->addDays($rule->days)->toDateString())->get(),
            'on_expiry' => $query->whereDate('expiry_date', now()->toDateString())->get(),
            'after_expiry' => $query->whereDate('expiry_date', now()->subDays($rule->days ?: 1)->toDateString())->get(),
        };
    }

    protected function alreadySent(int $productId, int $ruleId, string $channel): bool
    {
        return ReminderLog::where('product_id', $productId)
            ->where('rule_id', $ruleId)
            ->where('channel', $channel)
            ->where('status', 'sent')
            ->exists();
    }

    protected function sendViaChannel(string $channel, ReminderRule $rule, Products $product, Entry $entry, WhatsappService $wa): array
    {
        try {
            return match ($channel) {
                'sms' => $this->sendSms($rule, $product, $entry),
                'email' => $this->sendEmail($rule, $product, $entry),
                'whatsapp' => $this->sendWhatsapp($rule, $product, $entry, $wa),
                default => ['success' => false, 'error' => "Unknown channel: {$channel}"],
            };
        } catch (\Exception $e) {
            Log::error("Reminder {$channel} failed", [
                'product_id' => $product->id,
                'rule_id' => $rule->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendSms(ReminderRule $rule, Products $product, Entry $entry): array
    {
        if (empty($entry->contactno)) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $isExpired = in_array($rule->type, ['on_expiry', 'after_expiry']);
        $templateId = $rule->sms_template_id
            ?: env($isExpired ? 'EXPIRY_TEMPLATE_ID' : 'BEFORE_EXPIRY_TEMPLATE_ID');

        $message = $isExpired
            ? "Hi {$entry->contact}, We noticed that your services with Help Together Group has expired. We'd love to have you back! Please renew soon to continue enjoying our services. Contact us: +91 96346 44622"
            : "Hi {$entry->contact}, Your {$product->product_name} plan with Help Together Group expires on {$product->expiry_date}. Please renew to avoid interruption. Contact us: +91 96346 44622";

        SMSController::sendSms($templateId, $message, $entry->contactno);

        return ['success' => true];
    }

    protected function sendEmail(ReminderRule $rule, Products $product, Entry $entry): array
    {
        if (empty($entry->email)) {
            return ['success' => false, 'error' => 'No email address'];
        }

        $isExpired = in_array($rule->type, ['on_expiry', 'after_expiry']);
        $mailable = $isExpired
            ? new ExpiryMail($product)
            : new ExpirationReminderMail($product);

        Mail::to($entry->email)->send($mailable);

        $adminEmail = env('ADMIN_EMAIL');
        if ($adminEmail) {
            Mail::to($adminEmail)->send($isExpired ? new ExpiryMail($product) : new ExpirationReminderMail($product));
        }

        return ['success' => true];
    }

    protected function sendWhatsapp(ReminderRule $rule, Products $product, Entry $entry, WhatsappService $wa): array
    {
        if (!$wa->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp not configured'];
        }
        if (empty($entry->contactno)) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        if ($rule->wa_template_name) {
            $components = [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => $entry->contact ?: $entry->company],
                        ['type' => 'text', 'text' => $product->product_name],
                        ['type' => 'text', 'text' => $product->expiry_date],
                    ],
                ],
            ];

            return $wa->sendTemplate(
                $entry->contactno,
                $rule->wa_template_name,
                'en',
                $components,
                $entry->contact,
                'product',
                $product->id,
            );
        }

        $isExpired = in_array($rule->type, ['on_expiry', 'after_expiry']);
        $message = $isExpired
            ? "Hi {$entry->contact}, your {$product->product_name} plan with Help Together Group has expired. Please renew to continue enjoying our services. Contact us: +91 96346 44622"
            : "Hi {$entry->contact}, your {$product->product_name} plan with Help Together Group expires on {$product->expiry_date}. Please renew to avoid interruption. Contact us: +91 96346 44622";

        return $wa->sendText(
            $entry->contactno,
            $message,
            $entry->contact,
            'product',
            $product->id,
        );
    }
}
