<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Helper methods
    public function markAsRead(): bool
    {
        return $this->update(['status' => 'read']);
    }

    public function markAsReplied(): bool
    {
        return $this->update(['status' => 'replied']);
    }

    public function markAsClosed(): bool
    {
        return $this->update(['status' => 'closed']);
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }
}