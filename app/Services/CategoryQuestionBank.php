<?php

namespace App\Services;

class CategoryQuestionBank
{
    public const CATEGORIES = [

        'mental_health' => [
            'label' => 'Mental Health & Mood',
            'icon' => 'fas fa-brain',
            'description' => 'Depression, anxiety, mood swings, stress',
            'questions' => [
                ['text' => 'Over the past two weeks, how often have you felt down, depressed, or hopeless?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you find little interest or pleasure in activities you used to enjoy?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you feel nervous, anxious, or on edge?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you find it hard to stop or control worrying?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you have trouble concentrating on tasks?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Describe a moment in the past week when you felt overwhelmed.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel worthless or like a failure?', 'type' => 'frequency', 'weight' => 4, 'sensitive' => false],
                ['text' => 'How often do you have thoughts of self-harm or ending your life?', 'type' => 'frequency', 'weight' => 5, 'sensitive' => true],
                ['text' => 'How would you rate the quality of your sleep?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How satisfied are you with your social support?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel lonely even when around other people?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed changes in your appetite lately?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel irritable or easily angered?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you have difficulty falling or staying asleep?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel tired or have little energy?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you withdrawn from friends or family recently?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you experience physical symptoms when stressed (racing heart, sweating, trembling)?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How confident do you feel about your future?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Describe your typical day — how do you usually feel?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you ever received mental health support or counselling?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you struggle to make decisions?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have someone you can talk to when you are struggling?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
            ],
        ],

        'substance_abuse' => [
            'label' => 'Substance Use & Addiction',
            'icon' => 'fas fa-pills',
            'description' => 'Alcohol, drugs, prescription misuse',
            'questions' => [
                ['text' => 'In the past month, how often have you used alcohol or drugs?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you ever felt you should cut down on your drinking or drug use?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you used substances to cope with stress, sadness, or anxiety?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'What situations usually lead you to use substances?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you experienced withdrawal symptoms when stopping?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Has your substance use affected your studies, relationships, or finances?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you feel you need help to reduce or stop substance use?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have friends or family expressed concern about your substance use?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you use more than you intended to?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you driven or operated machinery under the influence?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Have you missed classes or work because of substance use?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you ever blacked out or forgotten things you did while using?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'How much do you spend on alcohol or drugs each week?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you tried to quit before? What happened?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you think about using substances?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you use substances alone or with others?', 'type' => 'text', 'weight' => 1, 'sensitive' => false],
                ['text' => 'Have you experienced legal problems related to substance use?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you feel guilty about your substance use?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you know where to get help for substance use?', 'type' => 'boolean', 'weight' => 1, 'sensitive' => false],
                ['text' => 'Describe what happens when you try to stop using.', 'type' => 'text', 'weight' => 3, 'sensitive' => false],
            ],
        ],

        'pregnancy' => [
            'label' => 'Pregnancy & Reproductive Health',
            'icon' => 'fas fa-baby',
            'description' => 'Pregnancy, prenatal care, reproductive concerns',
            'questions' => [
                ['text' => 'Are you currently pregnant or suspect you may be pregnant?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you missed a period recently?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you experience nausea or vomiting?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Are you experiencing any pain, bleeding, or unusual discharge?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => true],
                ['text' => 'Are you taking folic acid or prenatal vitamins?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have access to regular prenatal care?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Describe any concerns you have about pregnancy or reproductive health.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you experienced severe headaches or blurred vision?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How many weeks pregnant are you (if known)?', 'type' => 'text', 'weight' => 1, 'sensitive' => false],
                ['text' => 'Have you felt the baby move in the last 24 hours?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Are you experiencing swelling in your hands, feet, or face?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you had any falls or injuries during this pregnancy?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Are you avoiding alcohol, smoking, and other substances?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How would you rate your sleep quality during pregnancy?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you feel supported by your partner or family?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you experienced any contractions or cramping?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Describe your typical daily diet during pregnancy.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have any pre-existing medical conditions?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Are you taking any medications (prescribed or otherwise)?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you feel anxious about the pregnancy?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
            ],
        ],

        'chronic_illness' => [
            'label' => 'Chronic Conditions (BP, Diabetes, Asthma)',
            'icon' => 'fas fa-heartbeat',
            'description' => 'Blood pressure, diabetes, asthma, and other chronic illnesses',
            'questions' => [
                ['text' => 'Have you been diagnosed with high blood pressure, diabetes, or asthma?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you experience headaches, dizziness, or blurred vision?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you feel excessively thirsty or urinate frequently?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you take medication for a chronic condition regularly?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you missed any doses of your chronic medication this month?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you experience shortness of breath or chest tightness?', 'type' => 'frequency', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Describe your diet and exercise habits.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you had a blood pressure or blood sugar check in the last 3 months?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you experience fatigue that is not relieved by rest?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you experienced unexplained weight gain or loss?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you check your blood pressure or blood sugar?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you been hospitalized for your condition in the last year?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you miss work or class due to your condition?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you smoke or use tobacco?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How much salt and sugar do you consume daily?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you experience swelling in your legs or ankles?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you experience chest pain with exertion?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'How often do you wake up at night to urinate?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Describe any recent changes in your symptoms.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you feel your condition is well-controlled?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
            ],
        ],

        'infectious_disease' => [
            'label' => 'Infectious Diseases (HIV, TB, Malaria)',
            'icon' => 'fas fa-virus',
            'description' => 'HIV, TB, malaria, typhoid and other infections',
            'questions' => [
                ['text' => 'Have you had a fever, chills, or night sweats recently?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you have a cough that has lasted more than 2 weeks?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Have you experienced unexplained weight loss?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you been tested for HIV in the past year?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => true],
                ['text' => 'Do you have swollen lymph nodes or persistent fatigue?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Describe any recent travel or exposure to sick contacts.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you experienced persistent diarrhoea or stomach pain?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed blood in your cough or stool?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'How often do you use mosquito nets or repellent?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you had a recent insect bite with fever?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you been vaccinated against common infections?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you wash your hands with soap?', 'type' => 'frequency', 'weight' => 1, 'sensitive' => false],
                ['text' => 'Have you ever been treated for TB in the past?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you have access to clean drinking water?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you experience night sweats?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed any skin rashes or lesions?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Describe any recent illness that has not resolved.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you shared needles, razors, or personal items with others?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => true],
                ['text' => 'How often do you feel too weak to carry out daily activities?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you know your HIV status?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => true],
            ],
        ],

        'cancer' => [
            'label' => 'Cancer Awareness & Screening',
            'icon' => 'fas fa-ribbon',
            'description' => 'Cancer screening and warning signs',
            'questions' => [
                ['text' => 'Have you noticed any new lumps or masses in your body?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => true],
                ['text' => 'Have you experienced unexplained bleeding or discharge?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => true],
                ['text' => 'Have you had persistent pain in any part of your body for over 3 weeks?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed changes in your skin, moles, or birthmarks?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'When did you last have a cancer screening test?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Is there a family history of cancer?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Describe any symptoms that have concerned you recently.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Have you had persistent hoarseness or difficulty swallowing?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed a persistent sore that does not heal?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Have you experienced changes in bowel or bladder habits?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Do you smoke or use tobacco products?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'How often do you consume alcohol?', 'type' => 'frequency', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you noticed night sweats or unexplained fever?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'Have you experienced unexplained weight loss?', 'type' => 'boolean', 'weight' => 4, 'sensitive' => false],
                ['text' => 'Do you know the common warning signs of cancer?', 'type' => 'boolean', 'weight' => 1, 'sensitive' => false],
                ['text' => 'Have you ever had an abnormal Pap smear or mammogram?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => true],
                ['text' => 'Describe your diet — is it rich in fruits and vegetables?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you use sunscreen or protect your skin from the sun?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have any chronic infections or inflammatory conditions?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you exercise?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
            ],
        ],

        'general_wellness' => [
            'label' => 'General Wellness & Lifestyle',
            'icon' => 'fas fa-leaf',
            'description' => 'Overall health, fitness, nutrition, sleep',
            'questions' => [
                ['text' => 'How many hours of sleep do you get on average per night?', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you exercise for at least 30 minutes?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How would you rate your overall diet quality?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel tired or have little energy?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How much water do you drink daily?', 'type' => 'text', 'weight' => 1, 'sensitive' => false],
                ['text' => 'How satisfied are you with your overall health?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you smoke or use tobacco products?', 'type' => 'boolean', 'weight' => 3, 'sensitive' => false],
                ['text' => 'How often do you eat fast food or processed meals?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you eat fruits and vegetables?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How would you rate your stress levels?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How many hours a day do you spend on screens (excluding study)?', 'type' => 'text', 'weight' => 1, 'sensitive' => false],
                ['text' => 'How often do you feel rested when you wake up?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have a regular bedtime and wake-up time?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you engage in hobbies or activities you enjoy?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you feel overwhelmed by daily tasks?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have a regular source of healthcare?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How would you rate your work-life balance?', 'type' => 'scale', 'weight' => 2, 'sensitive' => false],
                ['text' => 'How often do you practise relaxation or mindfulness?', 'type' => 'frequency', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Do you have close friends or family you can rely on?', 'type' => 'boolean', 'weight' => 2, 'sensitive' => false],
                ['text' => 'Describe one thing you would like to improve about your health.', 'type' => 'text', 'weight' => 2, 'sensitive' => false],
            ],
        ],
    ];

    public static function listForPicker(): array
    {
        $out = [];
        foreach (self::CATEGORIES as $key => $meta) {
            $out[] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'description' => $meta['description'],
                'question_count' => count($meta['questions']),
            ];
        }
        return $out;
    }

    public static function questionsFor(array $categoryKeys): array
    {
        $all = [];
        foreach ($categoryKeys as $key) {
            if (!isset(self::CATEGORIES[$key])) continue;
            foreach (self::CATEGORIES[$key]['questions'] as $q) {
                $q['category'] = $key;
                $all[] = $q;
            }
        }
        shuffle($all);
        return $all;
    }

    public static function pickFrom(string $categoryKey, array $excludeTexts = []): ?array
    {
        if (!isset(self::CATEGORIES[$categoryKey])) return null;
        $pool = self::CATEGORIES[$categoryKey]['questions'];
        $available = array_values(array_filter(
            $pool,
            fn ($q) => !in_array($q['text'], $excludeTexts, true)
        ));
        if (empty($available)) return null;
        shuffle($available);
        $q = $available[0];
        $q['category'] = $categoryKey;
        $q['source'] = 'template';
        return $q;
    }

    public static function pickRandomWeighted(array $categoryKeys, array $excludeTexts = []): ?array
    {
        $pool = [];
        foreach ($categoryKeys as $key) {
            if (!isset(self::CATEGORIES[$key])) continue;
            foreach (self::CATEGORIES[$key]['questions'] as $q) {
                if (in_array($q['text'], $excludeTexts, true)) continue;
                $q['category'] = $key;
                $weight = max(1, (int) ($q['weight'] ?? 2));
                for ($i = 0; $i < $weight; $i++) {
                    $pool[] = $q;
                }
            }
        }
        if (empty($pool)) return null;
        $picked = $pool[array_rand($pool)];
        $picked['source'] = 'template';
        return $picked;
    }

    public static function countFor(string $key): int
    {
        return isset(self::CATEGORIES[$key])
            ? count(self::CATEGORIES[$key]['questions'])
            : 0;
    }

    public static function totalFor(array $categoryKeys): int
    {
        $total = 0;
        foreach ($categoryKeys as $key) {
            $total += self::countFor($key);
        }
        return $total;
    }

    public static function dropdown(): array
    {
        $out = [];
        foreach (self::CATEGORIES as $key => $meta) {
            $out[$key] = $meta['label'];
        }
        return $out;
    }
}