<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SMSController;
use App\Models\BalanceReminderLog;
use App\Models\Entry;
use App\Models\Products;
use App\Services\WhatsappService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBalanceReminders extends Command
{
    protected $signature = 'send:balance-reminders
        {--dry-run : Show what would be sent without actually sending}
        {--interval=7 : Minimum days between reminders per product}
        {--min-amount=0 : Minimum balance amount to trigger reminder}
        {--channels=sms,email : Comma-separated channels: sms,email,whatsapp}';

    protected $description = 'Send payment reminders to clients with outstanding balances';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $interval = (int) $this->option('interval');
        $minAmount = (float) $this->option('min-amount');
        $channels = explode(',', $this->option('channels'));

        $products = Products::with('entry')
            ->where('balance_amount', '>', $minAmount)
            ->orderByDesc('balance_amount')
            ->get();

        if ($products->isEmpty()) {
            $this->info('No products with outstanding balance found.');
            return 0;
        }

        $this->info("Found {$products->count()} product(s) with outstanding balance.");

        $wa = new WhatsappService();
        $totalSent = 0;

        foreach ($products as $product) {
            $entry = $product->entry;
            if (!$entry || !$entry->reminders_enabled) continue;

            foreach ($channels as $channel) {
                $channel = trim($channel);
                if ($this->recentlySent($product->id, $channel, $interval)) {
                    $this->line("  [SKIP] {$channel} → {$entry->contact} — sent within {$interval}d");
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY] {$channel} → {$entry->contact} ({$entry->contactno}) — ₹{$product->balance_amount}");
                    continue;
                }

                $result = $this->sendViaChannel($channel, $product, $entry, $wa);

                BalanceReminderLog::create([
                    'product_id' => $product->id,
                    'entry_id' => $entry->id,
                    'channel' => $channel,
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'error' => $result['error'] ?? null,
                    'created_at' => now(),
                ]);

                if ($result['success']) $totalSent++;
            }
        }

        $this->info("Done. {$totalSent} balance reminder(s) sent.");
        return 0;
    }

    protected function recentlySent(int $productId, string $channel, int $intervalDays): bool
    {
        return BalanceReminderLog::where('product_id', $productId)
            ->where('channel', $channel)
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subDays($intervalDays))
            ->exists();
    }

    protected function sendViaChannel(string $channel, Products $product, Entry $entry, WhatsappService $wa): array
    {
        try {
            return match ($channel) {
                'sms' => $this->sendSms($product, $entry),
                'email' => $this->sendEmail($product, $entry),
                'whatsapp' => $this->sendWhatsapp($product, $entry, $wa),
                default => ['success' => false, 'error' => "Unknown channel: {$channel}"],
            };
        } catch (\Exception $e) {
            Log::error("Balance reminder {$channel} failed", [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendSms(Products $product, Entry $entry): array
    {
        if (empty($entry->contactno)) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $templateId = env('BALANCE_REMINDER_TEMPLATE_ID', env('BEFORE_EXPIRY_EMPLATE_ID'));

        $message = "Hi {$entry->contact}, this is a reminder from Help Together Group. "
            . "Your outstanding balance for {$product->product_name} is Rs.{$product->balance_amount}. "
            . "Please clear the payment at the earliest. Contact us: +91 96346 44622";

        SMSController::sendSms($templateId, $message, $entry->contactno);

        return ['success' => true];
    }

    protected function sendEmail(Products $product, Entry $entry): array
    {
        if (empty($entry->email)) {
            return ['success' => false, 'error' => 'No email address'];
        }

        $data = [
            'contact' => $entry->contact,
            'company' => $entry->company,
            'product_name' => $product->product_name,
            'total_amount' => $product->total_amount,
            'paid_amount' => $product->paid_amount,
            'balance_amount' => $product->balance_amount,
        ];

        Mail::send([], [], function ($message) use ($entry, $data) {
            $body = "Dear {$data['contact']},\n\n"
                . "This is a payment reminder from Help Together Group.\n\n"
                . "Product: {$data['product_name']}\n"
                . "Total Amount: ₹" . number_format($data['total_amount'], 2) . "\n"
                . "Paid Amount: ₹" . number_format((float) $data['paid_amount'], 2) . "\n"
                . "Balance Due: ₹" . number_format($data['balance_amount'], 2) . "\n\n"
                . "Please clear the outstanding balance at the earliest.\n\n"
                . "For any queries, contact us at +91 96346 44622.\n\n"
                . "Thank you,\nHelp Together Group";

            $message->to($entry->email)
                ->subject("Payment Reminder - Balance Due ₹" . number_format($data['balance_amount'], 2))
                ->text($body);
        });

        return ['success' => true];
    }

    protected function sendWhatsapp(Products $product, Entry $entry, WhatsappService $wa): array
    {
        if (!$wa->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp not configured'];
        }
        if (empty($entry->contactno)) {
            return ['success' => false, 'error' => 'No phone number'];
        }

        $message = "Hi {$entry->contact}, this is a payment reminder from Help Together Group. "
            . "Your outstanding balance for {$product->product_name} is ₹{$product->balance_amount}. "
            . "Please clear the payment at the earliest. Contact us: +91 96346 44622";

        return $wa->sendText(
            $entry->contactno,
            $message,
            $entry->contact,
            'product',
            $product->id,
        );
    }
}
