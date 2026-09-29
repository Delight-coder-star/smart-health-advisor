<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SymptomQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question',
        'category',
        'symptoms_mentioned',
        'keywords_detected',
        'urgency',
        'status',
    ];

    protected $casts = [
        'symptoms_mentioned' => 'array',
        'keywords_detected' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function response()
    {
        return $this->hasOne(AiAdviceResponse::class, 'query_id');
    }

    public function getUrgencyColorAttribute(): string
    {
        return match ($this->urgency) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            default => 'secondary',
        };
    }
}