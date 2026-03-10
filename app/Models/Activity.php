<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'type', 'subject', 'description', 'lead_id', 'contact_id', 'deal_id',
        'user_id', 'activity_at', 'status', 'outcome', 'is_pinned',
    ];

    protected $casts = [
        'activity_at' => 'datetime',
        'is_pinned' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'call' => 'bi-telephone',
            'email' => 'bi-envelope',
            'whatsapp' => 'bi-whatsapp',
            'meeting' => 'bi-camera-video',
            'note' => 'bi-journal-text',
            'task' => 'bi-check-square',
            default => 'bi-activity',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'call' => 'text-green-500',
            'email' => 'text-blue-500',
            'whatsapp' => 'text-emerald-500',
            'meeting' => 'text-purple-500',
            'note' => 'text-yellow-500',
            'task' => 'text-orange-500',
            default => 'text-gray-500',
        };
    }
}
