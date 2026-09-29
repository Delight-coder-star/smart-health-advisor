<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'total_score',
        'sentiment_score',
        'sentiment_label',
        'risk_level',
        'indicators',
        'ai_analysis',
        'notes',
        'requires_attention',
        'alert_sent',
        'completed_at',
    ];

    protected $casts = [
        'indicators' => 'array',
        'ai_analysis' => 'array',
        'requires_attention' => 'boolean',
        'alert_sent' => 'boolean',
        'completed_at' => 'datetime',
        'sentiment_score' => 'float',
        'total_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function behavioralResponses()
    {
        return $this->hasMany(BehavioralResponse::class);
    }

    public function videoAnalysis()
    {
        return $this->hasOne(VideoAnalysis::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'low' => 'success',
            'moderate' => 'warning',
            'high' => 'orange',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    public function getSentimentEmojiAttribute(): string
    {
        $s = $this->sentiment_score ?? 0;
        if ($s > 0.3) return '😊';
        if ($s > 0) return '🙂';
        if ($s > -0.3) return '😐';
        if ($s > -0.6) return '😟';
        return '😢';
    }
}