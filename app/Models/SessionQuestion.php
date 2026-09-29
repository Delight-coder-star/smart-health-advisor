<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'sequence',
        'domain',
        'subdomain',
        'question_text',
        'question_type',
        'options',
        'weight',
        'is_crisis_sensitive',
        'source',
        'generation_metadata',
    ];

    protected $casts = [
        'options' => 'array',
        'generation_metadata' => 'array',
        'is_crisis_sensitive' => 'boolean',
        'weight' => 'integer',
        'sequence' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function answer()
    {
        return $this->hasOne(SessionAnswer::class, 'question_id');
    }

    public function getDomainLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->domain ?? 'general'));
    }
}