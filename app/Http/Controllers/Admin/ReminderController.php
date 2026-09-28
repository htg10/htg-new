<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Products;
use App\Models\ReminderLog;
use App\Models\ReminderRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $in30 = now()->addDays(30)->toDateString();

        $upcoming = Products::with('entry')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate('expiry_date', '<=', $in30)
            ->orderBy('expiry_date')
            ->get();

        $expired = Products::with('entry')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->whereDate('expiry_date', '>=', now()->subDays(30)->toDateString())
            ->orderByDesc('expiry_date')
            ->get();

        $rules = ReminderRule::orderBy('sort_order')->get();

        $totalUpcoming = $upcoming->count();
        $totalExpired = $expired->count();
        $sentToday = ReminderLog::whereDate('created_at', $today)->where('status', 'sent')->count();
        $failedToday = ReminderLog::whereDate('created_at', $today)->where('status', 'failed')->count();

        $urgentCount = $upcoming->filter(fn($p) => $p->expiry_date <= now()->addDays(7)->toDateString())->count();

        return view('admin.reminders.index', compact(
            'upcoming', 'expired', 'rules',
            'totalUpcoming', 'totalExpired', 'sentToday', 'failedToday', 'urgentCount'
        ));
    }

    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:before_expiry,on_expiry,after_expiry',
            'days' => 'required|integer|min:0|max:365',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:sms,email,whatsapp',
            'wa_template_name' => 'nullable|string|max:255',
            'sms_template_id' => 'nullable|string|max:255',
        ]);

        $maxOrder = ReminderRule::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        ReminderRule::create($validated);

        return back()->with('success', 'Reminder rule created.');
    }

    public function updateRule(Request $request, $id)
    {
        $rule = ReminderRule::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:before_expiry,on_expiry,after_expiry',
            'days' => 'required|integer|min:0|max:365',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:sms,email,whatsapp',
            'wa_template_name' => 'nullable|string|max:255',
            'sms_template_id' => 'nullable|string|max:255',
        ]);

        $rule->update($validated);

        return back()->with('success', 'Rule updated.');
    }

    public function toggleRule($id)
    {
        $rule = ReminderRule::findOrFail($id);
        $rule->update(['is_active' => !$rule->is_active]);

        return back()->with('success', $rule->is_active ? 'Rule activated.' : 'Rule deactivated.');
    }

    public function deleteRule($id)
    {
        ReminderRule::findOrFail($id)->delete();
        return back()->with('success', 'Rule removed.');
    }

    public function toggleClient($id)
    {
        $entry = Entry::findOrFail($id);
        $entry->update(['reminders_enabled' => !$entry->reminders_enabled]);

        $state = $entry->reminders_enabled ? 'enabled' : 'disabled';
        return back()->with('success', "Reminders {$state} for {$entry->company}.");
    }

    public function runNow()
    {
        Artisan::call('send:reminders');
        $output = Artisan::output();

        return back()->with('success', 'Reminders processed. ' . trim($output));
    }

    public function logs(Request $request)
    {
        $query = ReminderLog::with(['product', 'entry', 'rule'])->latest('created_at');

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

        $channelStats = ReminderLog::selectRaw('channel, COUNT(*) as total, SUM(status = "sent") as sent, SUM(status = "failed") as failed')
            ->groupBy('channel')
            ->get()
            ->keyBy('channel');

        return view('admin.reminders.logs', compact('logs', 'channelStats'));
    }
}
