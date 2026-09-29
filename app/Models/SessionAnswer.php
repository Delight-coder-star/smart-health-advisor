<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'question_id',
        'user_id',
        'raw_answer',
        'numeric_score',
        'sentiment_score',
        'sentiment_label',
        'confidence',
        'keywords_detected',
        'analysis_snapshot',
        'crisis_detected',
        'substance_flagged',
        'self_harm_flagged',
        'response_latency_ms',
        'answered_at',
    ];

    protected $casts = [
        'keywords_detected' => 'array',
        'analysis_snapshot' => 'array',
        'crisis_detected' => 'boolean',
        'substance_flagged' => 'boolean',
        'self_harm_flagged' => 'boolean',
        'sentiment_score' => 'float',
        'confidence' => 'float',
        'numeric_score' => 'integer',
        'answered_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function question()
    {
        return $this->belongsTo(SessionQuestion::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}