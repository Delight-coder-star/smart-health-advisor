<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ConditionKnowledge extends Model
{
    use HasFactory;

    protected $table = 'condition_knowledge_base';

    protected $fillable = [
        'condition_name',
        'category',
        'summary',
        'common_symptoms',
        'risk_factors',
        'prevention',
        'when_to_seek_help',
        'keywords',
        'severity',
        'is_chronic',
        'requires_medication',
        'is_active',
    ];

    protected $casts = [
        'common_symptoms' => 'array',
        'risk_factors' => 'array',
        'prevention' => 'array',
        'when_to_seek_help' => 'array',
        'keywords' => 'array',
        'is_chronic' => 'boolean',
        'requires_medication' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public static function search(string $text): Collection
    {
        $needles = array_filter(
            preg_split('/\s+/', strtolower($text)),
            fn ($w) => strlen($w) >= 4
        );

        return static::active()->get()->filter(function ($condition) use ($needles) {
            $haystack = strtolower(
                $condition->condition_name . ' ' .
                $condition->summary . ' ' .
                implode(' ', $condition->keywords ?? []) . ' ' .
                implode(' ', $condition->common_symptoms ?? [])
            );

            foreach ($needles as $n) {
                if (str_contains($haystack, $n)) {
                    return true;
                }
            }

            return false;
        })->take(5);
    }
}