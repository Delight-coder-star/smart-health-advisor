<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id', 'user_id', 'reminder_time',
        'day_of_week', 'is_active', 'last_sent_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function medication() { return $this->belongsTo(Medication::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeDue($q) { return $q->whereTime('reminder_time', '<=', now()->format('H:i:s')); }
}