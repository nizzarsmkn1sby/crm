<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'name', 'file_path', 'file_type', 'file_size', 'category',
        'lead_id', 'contact_id', 'deal_id', 'uploaded_by', 'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $size = $this->file_size;
        if ($size < 1024) return $size . ' B';
        if ($size < 1048576) return round($size / 1024, 1) . ' KB';
        return round($size / 1048576, 1) . ' MB';
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileIconAttribute(): string
    {
        $type = strtolower($this->file_type ?? '');
        if (str_contains($type, 'pdf')) return 'bi-file-earmark-pdf text-red-500';
        if (str_contains($type, 'word') || str_contains($type, 'document')) return 'bi-file-earmark-word text-blue-500';
        if (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) return 'bi-file-earmark-excel text-green-500';
        if (str_contains($type, 'image')) return 'bi-file-earmark-image text-purple-500';
        if (str_contains($type, 'zip') || str_contains($type, 'rar')) return 'bi-file-earmark-zip text-yellow-500';
        return 'bi-file-earmark text-gray-500';
    }
}
