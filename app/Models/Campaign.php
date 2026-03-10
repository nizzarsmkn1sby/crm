<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'subject', 'content', 'status',
        'created_by', 'scheduled_at', 'sent_at', 'total_recipients', 'sent_count',
        'opened_count', 'clicked_count', 'failed_count', 'target_filters',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'target_filters' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 1);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_recipients === 0) return 0;
        return round(($this->sent_count / $this->total_recipients) * 100, 1);
    }
}
