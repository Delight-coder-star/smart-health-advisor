<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiAdviceResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'user_id',
        'response_text',
        'possible_conditions',
        'recommended_actions',
        'when_to_see_doctor',
        'prevention_tips',
        'red_flags',
        'confidence',
        'source',
        'related_videos',
        'knowledge_refs',
        'disclaimer_shown',
    ];

    protected $casts = [
        'possible_conditions' => 'array',
        'recommended_actions' => 'array',
        'when_to_see_doctor' => 'array',
        'prevention_tips' => 'array',
        'red_flags' => 'array',
        'related_videos' => 'array',
        'knowledge_refs' => 'array',
        'disclaimer_shown' => 'boolean',
        'confidence' => 'float',
    ];

    /**
     * The original symptom query this advice responds to.
     * NOTE: named symptomQuery (not "query") to avoid clashing
     * with the base Model::query() static method.
     */
    public function symptomQuery()
    {
        return $this->belongsTo(SymptomQuery::class, 'query_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}