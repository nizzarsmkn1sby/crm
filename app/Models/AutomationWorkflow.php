<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'name', 'description', 'trigger', 'trigger_conditions', 'actions',
        'is_active', 'created_by', 'run_count', 'last_run_at',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTriggerLabelAttribute(): string
    {
        return match($this->trigger) {
            'lead_created' => 'Lead baru dibuat',
            'lead_status_changed' => 'Status lead berubah',
            'deal_stage_changed' => 'Stage deal berubah',
            'deal_won' => 'Deal berhasil (Won)',
            'deal_lost' => 'Deal gagal (Lost)',
            'task_overdue' => 'Task terlambat',
            'no_activity_7days' => 'Tidak ada aktivitas 7 hari',
            'meeting_scheduled' => 'Meeting dijadwalkan',
            default => $this->trigger,
        };
    }
}
