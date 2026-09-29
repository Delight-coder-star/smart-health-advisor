<?php

namespace App\Services;

class AIQuestionService
{
    public function __construct(
        protected QuestionSourceService $sources
    ) {
    }

    /**
     * Try to fetch a fresh question from any available AI source.
     * Returns null if none is available — caller falls back to the static bank.
     */
    public function nextQuestionForCategory(string $category, array $excludeTexts, ?string $previousAnswer): ?array
    {
        return $this->sources->fetchFresh($category, $excludeTexts, $previousAnswer);
    }
}