<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentSession;
use App\Models\Recommendation;
use App\Models\HealthTip;

class RecommendationService
{
    public function generateFor(AssessmentSession $session, array $topConcerns = [], array $crisisIndicators = []): array
    {
        $recommendations = [];

        if (!empty($crisisIndicators) || $session->crisis_flag_raised) {
            $recommendations[] = [
                'title' => 'Immediate Support Needed',
                'description' => 'Your answers raised safety flags. Please reach out now — you are not alone.',
                'priority' => 'urgent',
                'type' => 'emergency',
                'action_items' => [
                    'Call the crisis line now: +263 717 923 021',
                    'Contact a trusted friend or family member',
                    'Go to the nearest clinic or hospital',
                    'Do not stay alone — keep someone with you',
                ],
            ];
        }

        foreach ($topConcerns as $concern) {
            $rec = $this->recommendationForDomain($concern['domain'] ?? '', $concern['level'] ?? 'moderate');
            if ($rec) {
                $recommendations[] = $rec;
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'title' => 'Keep Up the Good Work',
                'description' => 'Your assessment shows no significant concerns. Continue your healthy habits.',
                'priority' => 'low',
                'type' => 'lifestyle',
                'action_items' => [
                    'Keep exercising regularly',
                    'Maintain your sleep schedule',
                    'Stay socially connected',
                    'Re-assess in 4 weeks',
                ],
            ];
        }

        $recommendations[] = [
            'title' => 'General Wellness',
            'description' => 'Small daily habits create lasting change.',
            'priority' => 'low',
            'type' => 'lifestyle',
            'action_items' => [
                'Drink 6-8 glasses of water daily',
                'Get 7-8 hours of sleep',
                'Take 10 minutes for deep breathing',
            ],
        ];

        return $recommendations;
    }

    public function generateRecommendations(Assessment $assessment): array
    {
        $recommendations = [];
        $riskLevel = $assessment->risk_level ?? 'low';
        $templates = $this->legacyTemplates($riskLevel);

        foreach ($templates as $template) {
            Recommendation::create([
                'assessment_id' => $assessment->id,
                'user_id' => $assessment->user_id,
                'title' => $template['title'],
                'description' => $template['description'],
                'type' => $template['type'],
                'priority' => $template['priority'],
                'action_items' => $template['action_items'],
            ]);

            $recommendations[] = $template;
        }

        return $recommendations;
    }

    protected function recommendationForDomain(string $domain, string $level): ?array
    {
        return match (true) {
            str_contains($domain, 'mental_health') => [
                'title' => 'Mental Health Support',
                'description' => 'Your responses suggest you may benefit from mental health support.',
                'priority' => $this->priorityFor($level),
                'type' => 'counseling',
                'action_items' => [
                    'Book an appointment with campus counselling',
                    'Practice 10 minutes of mindfulness daily',
                    'Journal your thoughts',
                    'Reach out to a trusted friend',
                ],
            ],
            str_contains($domain, 'substance') => [
                'title' => 'Substance Use Support',
                'description' => 'Consider professional guidance to reduce or stop substance use.',
                'priority' => $this->priorityFor($level),
                'type' => 'counseling',
                'action_items' => [
                    'Speak to an addiction counsellor',
                    'Join a support group (AA/NA)',
                    'Identify your triggers and avoid them',
                    'Build a sober support network',
                ],
            ],
            str_contains($domain, 'pregnancy') => [
                'title' => 'Pregnancy Care',
                'description' => 'Regular prenatal care protects you and your baby.',
                'priority' => $this->priorityFor($level),
                'type' => 'medical',
                'action_items' => [
                    'Book a prenatal appointment',
                    'Take folic acid daily',
                    'Avoid alcohol and smoking',
                    'Eat a balanced diet',
                ],
            ],
            str_contains($domain, 'chronic') => [
                'title' => 'Chronic Condition Management',
                'description' => 'Keep your condition under control with consistent care.',
                'priority' => $this->priorityFor($level),
                'type' => 'medical',
                'action_items' => [
                    'Take medication as prescribed',
                    'Monitor your vitals regularly',
                    'Reduce salt and sugar intake',
                    'Attend all clinic appointments',
                ],
            ],
            str_contains($domain, 'infectious') => [
                'title' => 'Testing & Treatment',
                'description' => 'Early testing and treatment save lives.',
                'priority' => $this->priorityFor($level),
                'type' => 'medical',
                'action_items' => [
                    'Get tested at the nearest clinic',
                    'Complete the full course of medication',
                    'Inform close contacts if needed',
                    'Practice good hygiene',
                ],
            ],
            str_contains($domain, 'cancer') => [
                'title' => 'Cancer Screening',
                'description' => 'Early detection dramatically improves outcomes.',
                'priority' => $this->priorityFor($level),
                'type' => 'medical',
                'action_items' => [
                    'Book a screening appointment',
                    'Report any unusual symptoms',
                    'Maintain a healthy lifestyle',
                    'Note any family history',
                ],
            ],
            str_contains($domain, 'general') => [
                'title' => 'General Wellness',
                'description' => 'Small daily habits create big changes.',
                'priority' => 'low',
                'type' => 'lifestyle',
                'action_items' => [
                    'Stay active with 30 minutes of exercise',
                    'Eat a balanced diet',
                    'Get enough sleep',
                ],
            ],
            default => null,
        };
    }

    protected function priorityFor(string $level): string
    {
        return match ($level) {
            'critical' => 'urgent',
            'high' => 'high',
            'moderate' => 'medium',
            default => 'low',
        };
    }

    protected function legacyTemplates(string $riskLevel): array
    {
        return match ($riskLevel) {
            'critical' => [
                [
                    'title' => 'Immediate Professional Help Required',
                    'description' => 'Your assessment indicates urgent risk. Please contact a professional now.',
                    'type' => 'emergency',
                    'priority' => 'urgent',
                    'action_items' => [
                        'Call the crisis helpline now: +263 717 923 021',
                        'Go to the nearest emergency room',
                        'Tell someone you trust',
                    ],
                ],
            ],
            'high' => [
                [
                    'title' => 'Schedule a Counselling Session',
                    'description' => 'Your assessment shows elevated risk. Book a session within 48 hours.',
                    'type' => 'counseling',
                    'priority' => 'high',
                    'action_items' => [
                        'Book an appointment with campus counselling',
                        'Practice daily mood tracking',
                        'Establish a sleep routine',
                    ],
                ],
            ],
            'moderate' => [
                [
                    'title' => 'Self-Care Enhancement',
                    'description' => 'Focus on improving self-care and stress management.',
                    'type' => 'self_help',
                    'priority' => 'medium',
                    'action_items' => [
                        'Practice daily mindfulness',
                        'Exercise regularly',
                        'Limit alcohol and caffeine',
                    ],
                ],
            ],
            default => [
                [
                    'title' => 'Maintain Healthy Habits',
                    'description' => 'Continue your healthy routines.',
                    'type' => 'lifestyle',
                    'priority' => 'low',
                    'action_items' => [
                        'Exercise regularly',
                        'Maintain social connections',
                        'Sleep 7-8 hours',
                    ],
                ],
            ],
        };
    }

    public function getHealthTips(?string $category = null, int $limit = 5): array
    {
        $query = HealthTip::active()->ordered();
        if ($category) {
            $query->byCategory($category);
        }
        return $query->limit($limit)->get()->toArray();
    }
}