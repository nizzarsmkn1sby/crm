<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'description', 'start_at', 'end_at', 'location', 'meeting_link',
        'status', 'lead_id', 'contact_id', 'deal_id', 'created_by', 'attendees',
        'notes', 'reminder_sent',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'attendees' => 'array',
        'reminder_sent' => 'boolean',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationAttribute(): string
    {
        if (!$this->start_at || !$this->end_at) return '';
        $diff = $this->start_at->diff($this->end_at);
        if ($diff->h > 0) {
            return $diff->h . ' jam ' . ($diff->i > 0 ? $diff->i . ' menit' : '');
        }
        return $diff->i . ' menit';
    }
}
