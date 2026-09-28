<?php

namespace App\Services;

use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected ?WhatsappSetting $settings;

    public function __construct()
    {
        $this->settings = WhatsappSetting::active();
    }

    public function isConfigured(): bool
    {
        return $this->settings !== null
            && !empty($this->settings->phone_number_id)
            && !empty($this->settings->access_token);
    }

    public function sendTemplate(
        string $phone,
        string $templateName,
        string $language = 'en',
        array $components = [],
        ?string $toName = null,
        ?string $contextType = null,
        ?int $contextId = null,
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $phone = $this->formatPhone($phone);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
            ],
        ];

        if (!empty($components)) {
            $body['template']['components'] = $components;
        }

        $result = $this->sendRequest($body);

        WhatsappLog::create([
            'to_phone' => $phone,
            'to_name' => $toName,
            'template_name' => $templateName,
            'message_type' => 'template',
            'content' => json_encode($components),
            'wa_message_id' => $result['wa_message_id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'error' => $result['error'] ?? null,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'sent_by' => auth()->id(),
        ]);

        return $result;
    }

    public function sendText(
        string $phone,
        string $message,
        ?string $toName = null,
        ?string $contextType = null,
        ?int $contextId = null,
    ): array {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp API not configured'];
        }

        $phone = $this->formatPhone($phone);

        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => ['body' => $message],
        ];

        $result = $this->sendRequest($body);

        WhatsappLog::create([
            'to_phone' => $phone,
            'to_name' => $toName,
            'template_name' => null,
            'message_type' => 'text',
            'content' => $message,
            'wa_message_id' => $result['wa_message_id'] ?? null,
            'status' => $result['success'] ? 'sent' : 'failed',
            'error' => $result['error'] ?? null,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'sent_by' => auth()->id(),
        ]);

        return $result;
    }

    protected function sendRequest(array $body): array
    {
        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            $this->settings->api_version,
            $this->settings->phone_number_id
        );

        try {
            $response = Http::withToken($this->settings->access_token)
                ->timeout(15)
                ->post($url, $body);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'wa_message_id' => $data['messages'][0]['id'] ?? null,
                ];
            }

            $error = $response->json('error.message', $response->body());
            Log::error('WhatsApp API error', ['status' => $response->status(), 'body' => $response->body()]);

            return ['success' => false, 'error' => $error];
        } catch (\Exception $e) {
            Log::error('WhatsApp API exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }

        return $phone;
    }

    public function fetchTemplates(): array
    {
        if (!$this->isConfigured() || empty($this->settings->business_account_id)) {
            return ['success' => false, 'error' => 'Business Account ID not configured'];
        }

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/message_templates',
            $this->settings->api_version,
            $this->settings->business_account_id
        );

        try {
            $response = Http::withToken($this->settings->access_token)
                ->timeout(15)
                ->get($url, ['limit' => 100]);

            if ($response->successful()) {
                return ['success' => true, 'templates' => $response->json('data', [])];
            }

            return ['success' => false, 'error' => $response->json('error.message', 'Unknown error')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
