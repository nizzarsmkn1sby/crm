<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'whatsapp', 'company', 'position',
        'source', 'status', 'priority', 'estimated_value', 'currency',
        'notes', 'tags', 'assigned_to', 'pipeline_stage_id',
        'last_contacted_at', 'expected_close_date',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'expected_close_date' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'new' => 'badge-info',
            'contacted' => 'badge-primary',
            'qualified' => 'badge-warning',
            'proposal' => 'badge-secondary',
            'negotiation' => 'badge-accent',
            'won' => 'badge-success',
            'lost' => 'badge-error',
            default => 'badge-ghost',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'high' => 'badge-error',
            'medium' => 'badge-warning',
            'low' => 'badge-success',
            default => 'badge-ghost',
        };
    }
}
