<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailService
{
    /**
     * Send an email and log it
     */
    public function send(string $to, string $subject, string $body, ?int $leadId = null, ?int $contactId = null, ?int $campaignId = null): array
    {
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Alamat email tidak valid'];
        }

        $trackingId = Str::uuid()->toString();

        $log = EmailLog::create([
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'status' => 'pending',
            'lead_id' => $leadId,
            'contact_id' => $contactId,
            'campaign_id' => $campaignId,
            'sent_by' => auth()->id(),
            'tracking_id' => $trackingId,
        ]);

        try {
            // Add tracking pixel to HTML body
            $trackedBody = $body . $this->getTrackingPixel($trackingId);

            Mail::html($trackedBody, function($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            $log->update(['status' => 'sent', 'sent_at' => now()]);
            return ['success' => true, 'message' => 'Email berhasil dikirim', 'log_id' => $log->id];

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $log->update(['status' => 'failed', 'error_message' => $error]);
            Log::error('Email send error: ' . $error);

            // In dev/log driver mode, treat as success
            if (config('mail.default') === 'log') {
                $log->update(['status' => 'sent', 'sent_at' => now()]);
                return ['success' => true, 'message' => 'Email tercatat (mode log)', 'log_id' => $log->id];
            }

            return ['success' => false, 'message' => $error];
        }
    }

    public function trackOpen(string $trackingId): void
    {
        EmailLog::where('tracking_id', $trackingId)
            ->where('opened_at', null)
            ->update(['status' => 'opened', 'opened_at' => now()]);
    }

    protected function getTrackingPixel(string $trackingId): string
    {
        $url = route('email.track-open', $trackingId);
        return "<img src=\"{$url}\" width=\"1\" height=\"1\" style=\"display:none\" />";
    }
}
