<?php

namespace App\Services;

use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $apiUrl;
    protected string $token;
    protected string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', env('WHATSAPP_API_URL', 'https://api.fonnte.com'));
        $this->token = config('services.whatsapp.token', env('WHATSAPP_TOKEN', ''));
        $this->sender = config('services.whatsapp.sender', env('WHATSAPP_SENDER', ''));
    }

    /**
     * Send a WhatsApp message via Fonnte API
     */
    public function send(string $phone, string $message, ?int $leadId = null, ?int $contactId = null, ?int $campaignId = null): array
    {
        // Normalize phone number (remove non-digits, add country code)
        $phone = $this->normalizePhone($phone);

        if (empty($phone)) {
            return ['success' => false, 'message' => 'Nomor telepon tidak valid'];
        }

        $log = WhatsappLog::create([
            'to' => $phone,
            'message' => $message,
            'status' => 'pending',
            'lead_id' => $leadId,
            'contact_id' => $contactId,
            'campaign_id' => $campaignId,
            'sent_by' => auth()->id(),
        ]);

        try {
            if (empty($this->token) || $this->token === 'your-fonnte-token-here') {
                // Simulation mode when no API key configured
                Log::info("WhatsApp SIMULATION - To: {$phone}, Message: {$message}");
                $log->update(['status' => 'sent', 'sent_at' => now(), 'message_id' => 'SIM-' . uniqid()]);
                return ['success' => true, 'message' => 'Pesan terkirim (simulasi)', 'log_id' => $log->id];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post("{$this->apiUrl}/send", [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'message_id' => $data['id'] ?? null,
                ]);
                return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'log_id' => $log->id];
            }

            $error = $response->json()['reason'] ?? 'Unknown error';
            $log->update(['status' => 'failed', 'error_message' => $error]);
            return ['success' => false, 'message' => $error];

        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) return '';

        // Convert leading 0 to 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 if no country code
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
