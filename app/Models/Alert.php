<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'assigned_to',
        'type',
        'severity',
        'title',
        'message',
        'status',
        'acknowledged_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'info' => 'info',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'acknowledged' => 'info',
            'resolved' => 'success',
            'escalated' => 'danger',
            default => 'secondary',
        };
    }

    public function acknowledge(int $userId): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'assigned_to' => $userId,
        ]);
    }

    public function resolve(?string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }
}