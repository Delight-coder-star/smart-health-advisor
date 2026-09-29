<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id', 'user_id', 'status',
        'scheduled_for', 'taken_at', 'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'taken_at' => 'datetime',
    ];

    public function medication() { return $this->belongsTo(Medication::class); }
    public function user() { return $this->belongsTo(User::class); }
}