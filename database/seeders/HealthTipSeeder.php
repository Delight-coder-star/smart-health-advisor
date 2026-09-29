<?php

namespace Database\Seeders;

use App\Models\HealthTip;
use Illuminate\Database\Seeder;

class HealthTipSeeder extends Seeder
{
    public function run(): void
    {
        $tips = [
            [
                'title' => 'Practice Deep Breathing',
                'content' => 'When feeling anxious, try the 4-7-8 technique: Inhale for 4 seconds, hold for 7 seconds, exhale for 8 seconds. Repeat 3-4 times.',
                'category' => 'anxiety',
                'tags' => ['breathing', 'relaxation', 'anxiety'],
                'icon' => 'fas fa-wind',
                'priority' => 10,
            ],
            [
                'title' => 'Maintain a Sleep Schedule',
                'content' => 'Go to bed and wake up at the same time every day, even on weekends. This helps regulate your body\'s internal clock.',
                'category' => 'general',
                'tags' => ['sleep', 'routine', 'health'],
                'icon' => 'fas fa-bed',
                'priority' => 9,
            ],
            [
                'title' => 'Stay Connected',
                'content' => 'Social connection is vital for mental health. Reach out to a friend or family member today, even if it\'s just a quick text.',
                'category' => 'depression',
                'tags' => ['social', 'connection', 'support'],
                'icon' => 'fas fa-users',
                'priority' => 8,
            ],
            [
                'title' => 'Limit Substance Use',
                'content' => 'Alcohol and drugs can worsen mental health symptoms. If you\'re struggling with substance use, reach out for professional support.',
                'category' => 'addiction',
                'tags' => ['substance', 'addiction', 'health'],
                'icon' => 'fas fa-ban',
                'priority' => 10,
            ],
            [
                'title' => 'Exercise Regularly',
                'content' => 'Even 30 minutes of moderate exercise 3-4 times a week can significantly improve mood and reduce anxiety.',
                'category' => 'general',
                'tags' => ['exercise', 'mood', 'health'],
                'icon' => 'fas fa-running',
                'priority' => 7,
            ],
            [
                'title' => 'Practice Mindfulness',
                'content' => 'Spend 5-10 minutes each day focusing on the present moment. Use apps like Headspace or Calm to get started.',
                'category' => 'anxiety',
                'tags' => ['mindfulness', 'meditation', 'relaxation'],
                'icon' => 'fas fa-spa',
                'priority' => 7,
            ],
            [
                'title' => 'Seek Professional Help',
                'content' => 'There is no shame in asking for help. Campus counselors are available to support you through difficult times.',
                'category' => 'crisis',
                'tags' => ['help', 'support', 'counseling'],
                'icon' => 'fas fa-hands-helping',
                'priority' => 10,
            ],
            [
                'title' => 'Keep a Mood Journal',
                'content' => 'Tracking your mood daily can help you identify patterns and triggers. Note what you did, how you felt, and what helped.',
                'category' => 'depression',
                'tags' => ['journal', 'mood', 'self-awareness'],
                'icon' => 'fas fa-book',
                'priority' => 6,
            ],
        ];

        foreach ($tips as $tip) {
            HealthTip::create($tip);
        }

        $this->command->info('Health tips seeded successfully.');
    }
}