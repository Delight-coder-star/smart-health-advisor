<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    |
    | If you have an OpenAI API key, set it here to enable enhanced sentiment
    | analysis. If not set, the system will use local lexicon-based analysis.
    |
    */

    'openai_api_key' => env('OPENAI_API_KEY'),
    'openai_api_url' => env('SENTIMENT_API_URL', 'https://api.openai.com/v1/chat/completions'),

    /*
    |--------------------------------------------------------------------------
    | Crisis Keywords
    |--------------------------------------------------------------------------
    |
    | These keywords trigger immediate crisis detection and alerts.
    |
    */

    'crisis_keywords' => [
        'suicide',
        'kill myself',
        'end my life',
        'want to die',
        'better off dead',
        'self-harm',
        'cutting',
        'hurt myself',
        'no reason to live',
        'can\'t go on',
        'overdose',
        'jump off',
        'hang myself',
        'shoot myself',
    ],

    /*
    |--------------------------------------------------------------------------
    | Substance Keywords
    |--------------------------------------------------------------------------
    |
    | Keywords related to substance use that should be flagged.
    |
    */

    'substance_keywords' => [
        'alcohol',
        'drugs',
        'marijuana',
        'weed',
        'cocaine',
        'heroin',
        'meth',
        'ecstasy',
        'mdma',
        'lsd',
        'pills',
        'prescription',
        'overdose',
        'drunk',
        'high',
        'stoned',
        'wasted',
        'intoxicated',
        'addicted',
        'craving',
        'withdrawal',
        'relapse',
        'using',
        'abusing',
        'substance',
    ],
];