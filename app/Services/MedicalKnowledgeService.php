<?php

namespace App\Services;

use App\Models\ConditionKnowledge;
use App\Models\ResourceVideo;

class MedicalKnowledgeService
{
    public function research(string $text): array
    {
        $conditions = ConditionKnowledge::search($text);
        $keywords = $this->extractKeywords($text);
        $videos = ResourceVideo::forKeywords($keywords, 4);

        return [
            'conditions' => $conditions->map(function ($c) {
                return [
                    'name' => $c->condition_name,
                    'category' => $c->category,
                    'summary' => $c->summary,
                    'symptoms' => $c->common_symptoms ?? [],
                    'prevention' => $c->prevention ?? [],
                    'when_to_seek_help' => $c->when_to_seek_help ?? [],
                    'severity' => $c->severity,
                    'is_chronic' => $c->is_chronic,
                ];
            })->values()->all(),
            'videos' => $videos->map(function ($v) {
                return [
                    'title' => $v->title,
                    'url' => $v->url,
                    'youtube_id' => $v->youtube_id,
                    'category' => $v->category,
                ];
            })->values()->all(),
            'keywords' => $keywords,
        ];
    }

    public function extractKeywords(string $text): array
    {
        $stop = [
            'i', 'the', 'a', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should',
            'could', 'can', 'may', 'might', 'am', 'you', 'your', 'he', 'she',
            'it', 'we', 'they', 'them', 'us', 'my', 'me', 'this', 'that',
            'and', 'or', 'but', 'if', 'of', 'to', 'in', 'on', 'at', 'by',
            'with', 'from', 'for', 'about', 'what', 'why', 'when', 'where',
            'who', 'how', 'which', 'there', 'here', 'feel', 'feeling',
        ];

        $words = preg_split('/\W+/', strtolower($text));
        $words = array_filter($words, fn ($w) => strlen($w) >= 4 && !in_array($w, $stop));

        return array_values(array_unique($words));
    }
}