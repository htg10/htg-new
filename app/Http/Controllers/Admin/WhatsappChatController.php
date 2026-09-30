<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Telecaller;
use App\Models\WhatsappMessage;
use App\Models\WhatsappTemplate;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsappChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = $this->getConversations();
        $templates = WhatsappTemplate::where('is_active', true)->orderBy('name')->get();
        $openPhone = $request->query('phone');
        $openName = $request->query('name');

        return view('admin.whatsapp.chat', compact('conversations', 'templates', 'openPhone', 'openName'));
    }

    public function conversations(Request $request)
    {
        $search = $request->input('search', '');
        $conversations = $this->getConversations($search);
        return response()->json($conversations);
    }

    public function messages(Request $request, string $phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        WhatsappMessage::where('phone', $phone)
            ->where('direction', 'in')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = WhatsappMessage::where('phone', $phone)
            ->orderBy('created_at', 'asc')
            ->limit(200)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'direction' => $m->direction,
                'type' => $m->message_type,
                'content' => $m->content,
                'media_url' => $m->media_url,
                'status' => $m->status,
                'wa_message_id' => $m->wa_message_id,
                'time' => $m->created_at->format('h:i A'),
                'date' => $m->created_at->format('d M Y'),
                'timestamp' => $m->created_at->timestamp,
            ]);

        $contact = $this->resolveContact($phone);

        return response()->json([
            'messages' => $messages,
            'contact' => $contact,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:4096',
            'contact_name' => 'nullable|string|max:255',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }

        $wa = new WhatsappService();

        $result = $wa->sendText(
            $phone,
            $validated['message'],
            $validated['contact_name'] ?? null,
        );

        $msg = WhatsappMessage::create([
            'phone' => $phone,
            'contact_name' => $validated['contact_name'] ?? null,
            'direction' => 'out',
            'message_type' => 'text',
            'content' => $validated['message'],
            'wa_message_id' => $result['wa_message_id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'sent_by' => auth()->id(),
            'is_read' => true,
        ]);

        return response()->json([
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
            'message' => [
                'id' => $msg->id,
                'direction' => 'out',
                'type' => 'text',
                'content' => $msg->content,
                'status' => $msg->status,
                'time' => $msg->created_at->format('h:i A'),
                'date' => $msg->created_at->format('d M Y'),
                'timestamp' => $msg->created_at->timestamp,
            ],
        ]);
    }

    public function sendTemplate(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'template_name' => 'required|string',
            'template_params' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (strlen($phone) === 10) $phone = '91' . $phone;

        $wa = new WhatsappService();

        $components = [];
        if (!empty($validated['template_params'])) {
            $params = array_map('trim', explode(',', $validated['template_params']));
            $parameters = array_map(fn($p) => ['type' => 'text', 'text' => $p], $params);
            $components = [['type' => 'body', 'parameters' => $parameters]];
        }

        $result = $wa->sendTemplate(
            $phone,
            $validated['template_name'],
            'en',
            $components,
            $validated['contact_name'] ?? null,
        );

        $msg = WhatsappMessage::create([
            'phone' => $phone,
            'contact_name' => $validated['contact_name'] ?? null,
            'direction' => 'out',
            'message_type' => 'template',
            'content' => '[Template: ' . $validated['template_name'] . ']',
            'wa_message_id' => $result['wa_message_id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'sent_by' => auth()->id(),
            'is_read' => true,
        ]);

        return response()->json([
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
            'message' => [
                'id' => $msg->id,
                'direction' => 'out',
                'type' => 'template',
                'content' => $msg->content,
                'status' => $msg->status,
                'time' => $msg->created_at->format('h:i A'),
                'date' => $msg->created_at->format('d M Y'),
                'timestamp' => $msg->created_at->timestamp,
            ],
        ]);
    }

    public function webhookVerify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token', 'htg_whatsapp_verify_2026');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function webhookReceive(Request $request)
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook received', ['payload' => $payload]);

        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $messages = $value['messages'] ?? [];
                $statuses = $value['statuses'] ?? [];

                foreach ($messages as $message) {
                    $this->handleIncomingMessage($message, $value);
                }

                foreach ($statuses as $status) {
                    $this->handleStatusUpdate($status);
                }
            }
        }

        return response('OK', 200);
    }

    protected function handleIncomingMessage(array $message, array $value): void
    {
        $phone = $message['from'] ?? '';
        $waId = $message['id'] ?? '';
        $type = $message['type'] ?? 'text';
        $timestamp = $message['timestamp'] ?? now()->timestamp;

        if (WhatsappMessage::where('wa_message_id', $waId)->exists()) {
            return;
        }

        $content = null;
        $mediaUrl = null;
        $mediaMime = null;

        switch ($type) {
            case 'text':
                $content = $message['text']['body'] ?? '';
                break;
            case 'image':
                $content = $message['image']['caption'] ?? '[Image]';
                $mediaMime = $message['image']['mime_type'] ?? null;
                break;
            case 'document':
                $content = $message['document']['filename'] ?? '[Document]';
                $mediaMime = $message['document']['mime_type'] ?? null;
                break;
            case 'audio':
                $content = '[Voice message]';
                $mediaMime = $message['audio']['mime_type'] ?? null;
                break;
            case 'video':
                $content = '[Video]';
                $mediaMime = $message['video']['mime_type'] ?? null;
                break;
            default:
                $content = "[{$type}]";
        }

        $contacts = $value['contacts'] ?? [];
        $contactName = $contacts[0]['profile']['name'] ?? null;

        WhatsappMessage::create([
            'phone' => $phone,
            'contact_name' => $contactName,
            'direction' => 'in',
            'message_type' => $type,
            'content' => $content,
            'media_url' => $mediaUrl,
            'media_mime' => $mediaMime,
            'wa_message_id' => $waId,
            'status' => 'received',
            'is_read' => false,
            'created_at' => \Carbon\Carbon::createFromTimestamp($timestamp),
        ]);
    }

    protected function handleStatusUpdate(array $status): void
    {
        $waId = $status['id'] ?? '';
        $newStatus = $status['status'] ?? '';

        if (!$waId || !$newStatus) return;

        WhatsappMessage::where('wa_message_id', $waId)
            ->where('direction', 'out')
            ->update(['status' => $newStatus]);
    }

    protected function getConversations(string $search = ''): array
    {
        $query = WhatsappMessage::select(
            'phone',
            DB::raw('MAX(contact_name) as contact_name'),
            DB::raw('MAX(created_at) as last_message_at'),
            DB::raw('SUM(CASE WHEN direction = "in" AND is_read = 0 THEN 1 ELSE 0 END) as unread_count'),
        )
            ->groupBy('phone')
            ->orderByDesc(DB::raw('MAX(created_at)'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%");
            });
        }

        $conversations = $query->limit(100)->get();

        $result = [];
        foreach ($conversations as $conv) {
            $lastMsg = WhatsappMessage::where('phone', $conv->phone)
                ->latest()
                ->first();

            $contact = $this->resolveContact($conv->phone);

            $result[] = [
                'phone' => $conv->phone,
                'name' => $contact['name'],
                'company' => $contact['company'],
                'last_message' => $lastMsg ? \Illuminate\Support\Str::limit($lastMsg->content, 50) : '',
                'last_message_direction' => $lastMsg?->direction,
                'last_time' => $lastMsg ? $lastMsg->created_at->format('h:i A') : '',
                'last_date' => $lastMsg ? $lastMsg->created_at->format('d/m/Y') : '',
                'last_timestamp' => $lastMsg?->created_at?->timestamp ?? 0,
                'unread' => (int) $conv->unread_count,
            ];
        }

        return $result;
    }

    protected function resolveContact(string $phone): array
    {
        $last10 = substr($phone, -10);

        $entry = Entry::where('contactno', 'like', "%{$last10}%")->first();
        if ($entry) {
            return [
                'name' => $entry->contact ?: $entry->company,
                'company' => $entry->company,
                'phone' => $phone,
                'source' => 'client',
                'source_id' => $entry->id,
            ];
        }

        $lead = Telecaller::where('mobile', 'like', "%{$last10}%")->first();
        if ($lead) {
            return [
                'name' => $lead->name ?: $lead->business,
                'company' => $lead->business,
                'phone' => $phone,
                'source' => 'lead',
                'source_id' => $lead->id,
            ];
        }

        $storedName = WhatsappMessage::where('phone', $phone)
            ->whereNotNull('contact_name')
            ->value('contact_name');

        return [
            'name' => $storedName ?: $this->formatPhone($phone),
            'company' => null,
            'phone' => $phone,
            'source' => null,
            'source_id' => null,
        ];
    }

    protected function formatPhone(string $phone): string
    {
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            $num = substr($phone, 2);
            return '+91 ' . substr($num, 0, 5) . ' ' . substr($num, 5);
        }
        return '+' . $phone;
    }

    public function searchContacts(Request $request)
    {
        $search = $request->input('q', '');
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $contacts = [];

        $entries = Entry::where(function ($q) use ($search) {
            $q->where('company', 'like', "%{$search}%")
              ->orWhere('contact', 'like', "%{$search}%")
              ->orWhere('contactno', 'like', "%{$search}%");
        })->whereNotNull('contactno')->limit(10)->get();

        foreach ($entries as $e) {
            $phone = preg_replace('/[^0-9]/', '', $e->contactno);
            if (strlen($phone) === 10) $phone = '91' . $phone;
            $contacts[] = [
                'phone' => $phone,
                'name' => $e->contact ?: $e->company,
                'company' => $e->company,
                'source' => 'client',
            ];
        }

        $leads = Telecaller::where(function ($q) use ($search) {
            $q->where('business', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('mobile', 'like', "%{$search}%");
        })->whereNotNull('mobile')->limit(10)->get();

        foreach ($leads as $l) {
            $phone = preg_replace('/[^0-9]/', '', $l->mobile);
            if (strlen($phone) === 10) $phone = '91' . $phone;
            $contacts[] = [
                'phone' => $phone,
                'name' => $l->name ?: $l->business,
                'company' => $l->business,
                'source' => 'lead',
            ];
        }

        return response()->json($contacts);
    }
}
