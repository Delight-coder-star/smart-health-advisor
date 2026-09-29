<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'final_risk_score',
        'final_risk_level',
        'final_sentiment',
        'domain_scores',
        'top_concerns',
        'protective_factors',
        'crisis_indicators',
        'diagnostic_summary',
        'recommendations',
        'notes',
        'generated_at',
    ];

    protected $casts = [
        'domain_scores' => 'array',
        'top_concerns' => 'array',
        'protective_factors' => 'array',
        'crisis_indicators' => 'array',
        'diagnostic_summary' => 'array',
        'recommendations' => 'array',
        'final_sentiment' => 'float',
        'final_risk_score' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRiskColorAttribute(): string
    {
        return match ($this->final_risk_level) {
            'low' => 'success',
            'moderate' => 'warning',
            'high' => 'orange',
            'critical' => 'danger',
            default => 'secondary',
        };
    }
}