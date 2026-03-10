<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Events\LeadStatusChanged;
use App\Models\AutomationWorkflow;
use App\Models\Activity;
use App\Services\WhatsappService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AutomationTriggered;

class AutomationRunner
{
    public function __construct(
        protected WhatsappService $whatsapp,
        protected EmailService $email
    ) {}

    /**
     * Handle LeadCreated event
     */
    public function handleLeadCreated(LeadCreated $event): void
    {
        $this->runWorkflows('lead_created', ['lead' => $event->lead]);
    }

    /**
     * Handle LeadStatusChanged event
     */
    public function handleLeadStatusChanged(LeadStatusChanged $event): void
    {
        $this->runWorkflows('lead_status_changed', [
            'lead'       => $event->lead,
            'old_status' => $event->oldStatus,
            'new_status' => $event->lead->status,
        ]);
    }

    /**
     * Core runner: find matching workflows and execute their actions
     */
    private function runWorkflows(string $trigger, array $context): void
    {
        $workflows = AutomationWorkflow::where('trigger', $trigger)
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            try {
                if (!$this->checkConditions($workflow->trigger_conditions ?? [], $context)) {
                    continue;
                }

                foreach ($workflow->actions as $action) {
                    $this->executeAction($action, $context);
                }

                $workflow->increment('run_count');
                $workflow->update(['last_run_at' => now()]);

            } catch (\Throwable $e) {
                Log::error("Automation workflow [{$workflow->id}] failed: " . $e->getMessage());
            }
        }
    }

    private function checkConditions(array $conditions, array $context): bool
    {
        if (empty($conditions)) return true;

        foreach ($conditions as $condition) {
            $field    = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '=';
            $value    = $condition['value'] ?? null;

            $actual = data_get($context, $field);

            $match = match ($operator) {
                '='   => $actual == $value,
                '!='  => $actual != $value,
                'in'  => in_array($actual, (array) $value),
                default => true,
            };

            if (!$match) return false;
        }

        return true;
    }

    private function executeAction(array $action, array $context): void
    {
        $lead = $context['lead'] ?? null;
        $type = $action['type'] ?? null;

        switch ($type) {
            case 'send_whatsapp':
                if ($lead && ($lead->whatsapp || $lead->phone)) {
                    $message = $this->interpolate($action['message'] ?? '', $lead);
                    $this->whatsapp->send($lead->whatsapp ?? $lead->phone, $message, $lead->id);
                }
                break;

            case 'send_email':
                if ($lead && $lead->email) {
                    $subject = $this->interpolate($action['subject'] ?? 'Notifikasi CRM', $lead);
                    $body    = $this->interpolate($action['body'] ?? '', $lead);
                    $this->email->send($lead->email, $subject, $body, $lead->id);
                }
                break;

            case 'create_task':
                if ($lead) {
                    \App\Models\Task::create([
                        'title'       => $this->interpolate($action['title'] ?? 'Follow-up otomatis', $lead),
                        'description' => $action['description'] ?? null,
                        'lead_id'     => $lead->id,
                        'assigned_to' => $lead->assigned_to,
                        'priority'    => $action['priority'] ?? 'medium',
                        'due_date'    => now()->addDays($action['days'] ?? 1),
                        'status'      => 'pending',
                        'created_by'  => 1, // system
                    ]);
                }
                break;

            case 'log_activity':
                if ($lead) {
                    Activity::create([
                        'type'        => 'note',
                        'subject'     => $this->interpolate($action['subject'] ?? 'Otomasi dijalankan', $lead),
                        'description' => 'Dijalankan oleh sistem otomasi',
                        'lead_id'     => $lead->id,
                        'user_id'     => null,
                        'activity_at' => now(),
                        'status'      => 'completed',
                    ]);
                }
                break;

            case 'notify_admin':
                $admins = \App\Models\User::where('role', 'admin')->get();
                $message = $this->interpolate($action['message'] ?? 'Notifikasi otomasi', $lead);
                foreach ($admins as $admin) {
                    $admin->notify(new AutomationTriggered($message, $lead));
                }
                break;
        }
    }

    private function interpolate(string $template, $lead): string
    {
        if (!$lead) return $template;

        return str_replace(
            ['{name}', '{company}', '{status}', '{email}', '{phone}'],
            [$lead->name, $lead->company ?? '', $lead->status, $lead->email ?? '', $lead->phone ?? ''],
            $template
        );
    }
}
