<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappLog;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use App\Models\WhatsappTemplate;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    // --- Settings ---

    public function settings()
    {
        $settings = WhatsappSetting::first();
        $templates = WhatsappTemplate::orderBy('name')->get();
        $logsCount = WhatsappLog::count();
        $sentToday = WhatsappLog::whereDate('created_at', today())->count();
        $failedCount = WhatsappLog::where('status', 'failed')->count();

        return view('admin.whatsapp.settings', compact('settings', 'templates', 'logsCount', 'sentToday', 'failedCount'));
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'phone_number_id' => 'required|string|max:50',
            'business_account_id' => 'nullable|string|max:50',
            'access_token' => 'required|string',
            'api_version' => 'required|string|max:10',
            'display_phone' => 'nullable|string|max:20',
        ]);

        $settings = WhatsappSetting::first();

        if ($settings) {
            $settings->update($validated);
        } else {
            WhatsappSetting::create($validated);
        }

        return redirect()->route('admin.whatsapp.settings')->with('success', 'WhatsApp API settings saved.');
    }

    public function testConnection()
    {
        $wa = new WhatsappService();

        if (!$wa->isConfigured()) {
            return back()->with('error', 'WhatsApp API is not configured. Save your settings first.');
        }

        $result = $wa->fetchTemplates();

        if ($result['success']) {
            $count = count($result['templates']);
            return back()->with('success', "Connection successful! Found {$count} templates in your account.");
        }

        return back()->with('error', 'Connection failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    // --- Templates ---

    public function syncTemplates()
    {
        $wa = new WhatsappService();
        $result = $wa->fetchTemplates();

        if (!$result['success']) {
            return back()->with('error', 'Failed to fetch templates: ' . ($result['error'] ?? 'Unknown error'));
        }

        $synced = 0;
        foreach ($result['templates'] as $tpl) {
            if (($tpl['status'] ?? '') !== 'APPROVED') continue;

            WhatsappTemplate::updateOrCreate(
                ['template_name' => $tpl['name']],
                [
                    'name' => $tpl['name'],
                    'language' => $tpl['language'] ?? 'en',
                    'category' => $tpl['category'] ?? 'UTILITY',
                    'components' => $tpl['components'] ?? [],
                    'is_active' => true,
                ]
            );
            $synced++;
        }

        return back()->with('success', "Synced {$synced} approved templates from Meta.");
    }

    public function deleteTemplate($id)
    {
        WhatsappTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Template removed.');
    }

    // --- Send Message ---

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:text,template',
            'message' => 'required_if:type,text|nullable|string|max:4096',
            'template_name' => 'required_if:type,template|nullable|string',
            'template_params' => 'nullable|string',
            'context_type' => 'nullable|string|max:50',
            'context_id' => 'nullable|integer',
        ]);

        $wa = new WhatsappService();

        if ($validated['type'] === 'template') {
            $components = [];
            if (!empty($validated['template_params'])) {
                $params = array_map('trim', explode(',', $validated['template_params']));
                $parameters = array_map(fn($p) => ['type' => 'text', 'text' => $p], $params);
                $components = [['type' => 'body', 'parameters' => $parameters]];
            }

            $result = $wa->sendTemplate(
                $validated['phone'],
                $validated['template_name'],
                'en',
                $components,
                $validated['name'] ?? null,
                $validated['context_type'] ?? null,
                isset($validated['context_id']) ? (int) $validated['context_id'] : null,
            );
        } else {
            $result = $wa->sendText(
                $validated['phone'],
                $validated['message'],
                $validated['name'] ?? null,
                $validated['context_type'] ?? null,
                isset($validated['context_id']) ? (int) $validated['context_id'] : null,
            );
        }

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (strlen($phone) === 10) $phone = '91' . $phone;

        WhatsappMessage::create([
            'phone' => $phone,
            'contact_name' => $validated['name'] ?? null,
            'direction' => 'out',
            'message_type' => $validated['type'],
            'content' => $validated['type'] === 'template'
                ? '[Template: ' . $validated['template_name'] . ']'
                : $validated['message'],
            'wa_message_id' => $result['wa_message_id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'sent_by' => auth()->id(),
            'is_read' => true,
        ]);

        if ($result['success']) {
            return back()->with('success', 'WhatsApp message sent successfully!');
        }

        return back()->with('error', 'Failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    // --- Logs ---

    public function logs(Request $request)
    {
        $query = WhatsappLog::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('to_phone', 'like', "%{$s}%")
                  ->orWhere('to_name', 'like', "%{$s}%")
                  ->orWhere('template_name', 'like', "%{$s}%");
            });
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        $logs = $query->paginate(15);
        $totalSent = WhatsappLog::where('status', 'sent')->count();
        $totalFailed = WhatsappLog::where('status', 'failed')->count();

        return view('admin.whatsapp.logs', compact('logs', 'totalSent', 'totalFailed'));
    }
}
