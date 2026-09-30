<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceReminderLog;
use App\Models\Entry;
use App\Models\Products;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BalanceReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::with('entry')
            ->where('balance_amount', '>', 0)
            ->orderByDesc('balance_amount');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('product_name', 'like', "%{$s}%")
                    ->orWhereHas('entry', function ($eq) use ($s) {
                        $eq->where('company', 'like', "%{$s}%")
                            ->orWhere('contact', 'like', "%{$s}%")
                            ->orWhere('contactno', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('sort')) {
            $query->reorder();
            match ($request->sort) {
                'amount_asc' => $query->orderBy('balance_amount'),
                'amount_desc' => $query->orderByDesc('balance_amount'),
                'company' => $query->join('entries', 'products.entry_id', '=', 'entries.id')
                    ->orderBy('entries.company')
                    ->select('products.*'),
                default => $query->orderByDesc('balance_amount'),
            };
        }

        $products = $query->paginate(15);

        $totalOutstanding = Products::where('balance_amount', '>', 0)->sum('balance_amount');
        $clientCount = Products::where('balance_amount', '>', 0)->distinct('entry_id')->count('entry_id');
        $productCount = Products::where('balance_amount', '>', 0)->count();

        $sentToday = BalanceReminderLog::whereDate('created_at', now()->toDateString())
            ->where('status', 'sent')->count();
        $sentThisWeek = BalanceReminderLog::where('created_at', '>=', now()->startOfWeek())
            ->where('status', 'sent')->count();

        $lastSentMap = BalanceReminderLog::where('status', 'sent')
            ->selectRaw('product_id, MAX(created_at) as last_sent')
            ->groupBy('product_id')
            ->pluck('last_sent', 'product_id');

        return view('admin.balance-reminders.index', compact(
            'products', 'totalOutstanding', 'clientCount', 'productCount',
            'sentToday', 'sentThisWeek', 'lastSentMap'
        ));
    }

    public function send(Request $request, $productId)
    {
        $product = Products::with('entry')->findOrFail($productId);
        $entry = $product->entry;

        if (!$entry) {
            return back()->with('error', 'No client linked to this product.');
        }

        $channels = $request->input('channels', ['sms', 'email']);
        $wa = new WhatsappService();
        $sent = 0;

        foreach ($channels as $channel) {
            $result = $this->sendViaChannel($channel, $product, $entry, $wa);

            BalanceReminderLog::create([
                'product_id' => $product->id,
                'entry_id' => $entry->id,
                'channel' => $channel,
                'status' => $result['success'] ? 'sent' : 'failed',
                'error' => $result['error'] ?? null,
                'sent_by' => auth()->id(),
                'created_at' => now(),
            ]);

            if ($result['success']) $sent++;
        }

        $total = count($channels);
        return back()->with('success', "Reminder sent to {$entry->company}: {$sent}/{$total} channel(s) delivered.");
    }

    public function sendBulk(Request $request)
    {
        $channels = $request->input('channels', ['sms', 'email']);
        $interval = (int) $request->input('interval', 7);

        Artisan::call('send:balance-reminders', [
            '--channels' => implode(',', $channels),
            '--interval' => $interval,
        ]);

        $output = Artisan::output();
        return back()->with('success', 'Bulk reminders processed. ' . trim($output));
    }

    public function logs(Request $request)
    {
        $query = BalanceReminderLog::with(['product', 'entry', 'sender'])->latest('created_at');

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('entry', function ($q) use ($s) {
                $q->where('company', 'like', "%{$s}%")
                    ->orWhere('contact', 'like', "%{$s}%")
                    ->orWhere('contactno', 'like', "%{$s}%");
            });
        }

        $logs = $query->paginate(15);

        $channelStats = BalanceReminderLog::selectRaw('channel, COUNT(*) as total, SUM(status = "sent") as sent, SUM(status = "failed") as failed')
            ->groupBy('channel')
            ->get()
            ->keyBy('channel');

        return view('admin.balance-reminders.logs', compact('logs', 'channelStats'));
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

        $templateId = env('BALANCE_REMINDER_TEMPLATE_ID', env('BEFORE_EXPIRY_TEMPLATE_ID'));

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
            'product_name' => $product->product_name,
            'total_amount' => $product->total_amount,
            'paid_amount' => $product->paid_amount,
            'balance_amount' => $product->balance_amount,
        ];

        Mail::send([], [], function ($message) use ($entry, $data) {
            $body = "Dear {$data['contact']},\n\n"
                . "This is a payment reminder from Help Together Group.\n\n"
                . "Product: {$data['product_name']}\n"
                . "Total Amount: Rs." . number_format($data['total_amount'], 2) . "\n"
                . "Paid Amount: Rs." . number_format((float) $data['paid_amount'], 2) . "\n"
                . "Balance Due: Rs." . number_format($data['balance_amount'], 2) . "\n\n"
                . "Please clear the outstanding balance at the earliest.\n\n"
                . "For any queries, contact us at +91 96346 44622.\n\n"
                . "Thank you,\nHelp Together Group";

            $message->to($entry->email)
                ->subject("Payment Reminder - Balance Due Rs." . number_format($data['balance_amount'], 2))
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
            . "Your outstanding balance for {$product->product_name} is Rs.{$product->balance_amount}. "
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
