<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'video_path',
        'duration_seconds',
        'pupil_dilation_score',
        'eye_movement_score',
        'blink_rate',
        'analysis_results',
        'status',
        'error_message',
    ];

    protected $casts = [
        'analysis_results' => 'array',
        'pupil_dilation_score' => 'float',
        'eye_movement_score' => 'float',
        'blink_rate' => 'float',
        'duration_seconds' => 'integer',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getVideoUrlAttribute(): string
    {
        $path = str_replace('storage/app/', '', (string) $this->video_path);
        return asset('storage/' . ltrim($path, '/'));
    }
}