<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ResourceVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'youtube_id',
        'url',
        'category',
        'keywords',
        'description',
        'language',
        'duration_seconds',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function forKeywords(array $keywords, int $limit = 4): Collection
    {
        return static::active()->get()->filter(function ($video) use ($keywords) {
            $haystack = strtolower(
                $video->title . ' ' .
                implode(' ', $video->keywords ?? []) . ' ' .
                $video->category
            );

            foreach ($keywords as $k) {
                if (strlen($k) >= 4 && str_contains($haystack, strtolower($k))) {
                    return true;
                }
            }

            return false;
        })->take($limit);
    }
}