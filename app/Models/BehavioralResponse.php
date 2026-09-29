<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehavioralResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'question_key',
        'question_text',
        'response_text',
        'response_score',
        'sentiment_score',
        'keywords_detected',
    ];

    protected $casts = [
        'keywords_detected' => 'array',
        'sentiment_score' => 'float',
        'response_score' => 'integer',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}