<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'dosage', 'frequency', 'condition',
        'start_date', 'end_date', 'instructions', 'notes', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function reminders() { return $this->hasMany(MedicationReminder::class); }
    public function logs() { return $this->hasMany(MedicationLog::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}