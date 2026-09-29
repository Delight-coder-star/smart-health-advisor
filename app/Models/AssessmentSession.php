<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AssessmentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'status',
        'mode',
        'selected_categories',
        'questions_asked',
        'questions_answered',
        'max_questions',
        'running_sentiment',
        'running_risk_score',
        'current_risk_level',
        'domains_covered',
        'domains_pending',
        'ai_metadata',
        'merged_advice',
        'crisis_flag_raised',
        'crisis_flagged_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'domains_covered' => 'array',
        'domains_pending' => 'array',
        'selected_categories' => 'array',
        'ai_metadata' => 'array',
        'merged_advice' => 'array',
        'crisis_flag_raised' => 'boolean',
        'crisis_flagged_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'running_sentiment' => 'float',
        'running_risk_score' => 'integer',
        'questions_asked' => 'integer',
        'questions_answered' => 'integer',
        'max_questions' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = (string) Str::uuid();
            }
            if (empty($session->started_at)) {
                $session->started_at = now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(SessionQuestion::class, 'session_id')->orderBy('sequence');
    }

    public function answers()
    {
        return $this->hasMany(SessionAnswer::class, 'session_id');
    }

    public function report()
    {
        return $this->hasOne(AssessmentReport::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function nextSequence(): int
    {
        return ((int) $this->questions()->max('sequence')) + 1;
    }

    public function recordAnswer(SessionAnswer $answer): void
    {
        $answers = $this->answers()->with('question')->get();
        $count = $answers->count();

        $avgSentiment = $count > 0 ? $answers->avg('sentiment_score') : 0;

        $weighted = 0;
        $weights = 0;
        foreach ($answers as $a) {
            $weight = $a->question->weight ?? 1;
            $score = $a->numeric_score ?? 3;
            $weighted += $score * $weight;
            $weights += $weight * 5;
        }

        $risk = $weights > 0 ? (int) round(($weighted / $weights) * 100) : 0;
        $risk = max(0, min(100, $risk));

        $level = 'low';
        if ($risk >= 75) {
            $level = 'critical';
        } elseif ($risk >= 50) {
            $level = 'high';
        } elseif ($risk >= 25) {
            $level = 'moderate';
        }

        $crisis = $this->crisis_flag_raised || (bool) $answer->crisis_detected;

        $this->update([
            'questions_answered' => $count,
            'running_sentiment' => round((float) $avgSentiment, 4),
            'running_risk_score' => $risk,
            'current_risk_level' => $level,
            'crisis_flag_raised' => $crisis,
            'crisis_flagged_at' => ($crisis && !$this->crisis_flagged_at) ? now() : $this->crisis_flagged_at,
        ]);
    }
}