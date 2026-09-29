<?php

namespace Database\Seeders;

use App\Models\ConditionKnowledge;
use App\Models\ResourceVideo;
use Illuminate\Database\Seeder;

class MedicalKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            [
                'condition_name' => 'Hypertension (High Blood Pressure)',
                'category' => 'bp',
                'summary' => 'A chronic condition where blood pressure against artery walls is consistently too high. Often called the "silent killer" because symptoms may be absent.',
                'common_symptoms' => ['Headaches', 'Blurred vision', 'Nosebleeds', 'Shortness of breath', 'Chest pain', 'Dizziness'],
                'risk_factors' => ['Family history', 'Obesity', 'High salt intake', 'Stress', 'Smoking', 'Diabetes'],
                'prevention' => ['Reduce salt', 'Exercise 30 min daily', 'Limit alcohol', 'Quit smoking', 'Manage stress'],
                'when_to_seek_help' => ['BP reading above 180/120', 'Severe headache with vision changes', 'Chest pain'],
                'keywords' => ['bp', 'blood pressure', 'hypertension', 'high bp'],
                'severity' => 'high',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Type 2 Diabetes',
                'category' => 'diabetes',
                'summary' => 'A chronic condition where the body cannot properly use insulin, leading to high blood sugar.',
                'common_symptoms' => ['Excessive thirst', 'Frequent urination', 'Fatigue', 'Blurred vision', 'Slow healing wounds', 'Unexplained weight loss'],
                'risk_factors' => ['Obesity', 'Family history', 'Sedentary lifestyle', 'Age over 45'],
                'prevention' => ['Balanced diet', 'Regular exercise', 'Maintain healthy weight', 'Limit sugary drinks'],
                'when_to_seek_help' => ['Blood sugar above 300 mg/dL', 'Fruity breath', 'Confusion', 'Extreme thirst'],
                'keywords' => ['diabetes', 'sugar', 'blood sugar', 'insulin'],
                'severity' => 'high',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Pregnancy',
                'category' => 'pregnancy',
                'summary' => 'The period when a fetus develops in the uterus. Requires regular prenatal care.',
                'common_symptoms' => ['Missed period', 'Nausea', 'Fatigue', 'Breast tenderness', 'Frequent urination'],
                'risk_factors' => ['Age', 'Pre-existing conditions', 'Multiple pregnancies'],
                'prevention' => ['Folic acid supplements', 'Regular checkups', 'Avoid alcohol and smoking'],
                'when_to_seek_help' => ['Severe bleeding', 'Severe abdominal pain', 'Reduced fetal movement'],
                'keywords' => ['pregnancy', 'pregnant', 'expecting', 'antenatal'],
                'severity' => 'moderate',
                'is_chronic' => false,
                'requires_medication' => false,
            ],
            [
                'condition_name' => 'Malaria',
                'category' => 'infectious',
                'summary' => 'A mosquito-borne infectious disease common in Zimbabwe, caused by Plasmodium parasites.',
                'common_symptoms' => ['Fever', 'Chills', 'Headache', 'Muscle pain', 'Fatigue', 'Nausea'],
                'risk_factors' => ['Travel to endemic areas', 'No mosquito net', 'Weakened immunity'],
                'prevention' => ['Sleep under treated nets', 'Use repellent', 'Take prophylaxis when travelling'],
                'when_to_seek_help' => ['High fever with chills', 'Confusion', 'Difficulty breathing'],
                'keywords' => ['malaria', 'mosquito', 'fever', 'chills'],
                'severity' => 'high',
                'is_chronic' => false,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Cancer (General Awareness)',
                'category' => 'cancer',
                'summary' => 'Uncontrolled growth of abnormal cells. Early detection greatly improves outcomes.',
                'common_symptoms' => ['Unexplained weight loss', 'Persistent fatigue', 'Lumps', 'Changes in bowel/bladder habits', 'Persistent cough'],
                'risk_factors' => ['Smoking', 'Alcohol', 'Family history', 'Age', 'Poor diet'],
                'prevention' => ['No smoking', 'Limit alcohol', 'Regular screening', 'Healthy diet', 'Exercise'],
                'when_to_seek_help' => ['Any persistent symptom over 3 weeks', 'Unexplained bleeding', 'New lump'],
                'keywords' => ['cancer', 'tumour', 'tumor', 'oncology', 'lump'],
                'severity' => 'critical',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Depression',
                'category' => 'mental_health',
                'summary' => 'A common but serious mood disorder causing persistent sadness and loss of interest.',
                'common_symptoms' => ['Persistent sadness', 'Loss of interest', 'Fatigue', 'Sleep changes', 'Difficulty concentrating', 'Suicidal thoughts'],
                'risk_factors' => ['Family history', 'Trauma', 'Chronic illness', 'Substance abuse'],
                'prevention' => ['Regular exercise', 'Social connection', 'Sleep hygiene', 'Seek help early'],
                'when_to_seek_help' => ['Thoughts of self-harm', 'Unable to function daily', 'Symptoms over 2 weeks'],
                'keywords' => ['depression', 'depressed', 'sad', 'hopeless'],
                'severity' => 'high',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Anxiety Disorder',
                'category' => 'mental_health',
                'summary' => 'Excessive, persistent worry that interferes with daily life.',
                'common_symptoms' => ['Restlessness', 'Racing heart', 'Sweating', 'Difficulty concentrating', 'Sleep problems'],
                'risk_factors' => ['Stress', 'Family history', 'Trauma'],
                'prevention' => ['Mindfulness', 'Exercise', 'Limit caffeine', 'Therapy'],
                'when_to_seek_help' => ['Panic attacks', 'Inability to function', 'Symptoms over 6 months'],
                'keywords' => ['anxiety', 'anxious', 'panic', 'worry', 'nervous'],
                'severity' => 'moderate',
                'is_chronic' => true,
                'requires_medication' => false,
            ],
            [
                'condition_name' => 'HIV/AIDS',
                'category' => 'infectious',
                'summary' => 'A viral infection that attacks the immune system. With treatment, people live long healthy lives.',
                'common_symptoms' => ['Fever', 'Fatigue', 'Swollen lymph nodes', 'Weight loss', 'Recurrent infections'],
                'risk_factors' => ['Unprotected sex', 'Shared needles', 'Blood transfusion'],
                'prevention' => ['Condom use', 'Regular testing', 'PrEP', 'Never share needles'],
                'when_to_seek_help' => ['Positive test', 'Persistent fever', 'Rapid weight loss'],
                'keywords' => ['hiv', 'aids', 'immune', 'positive'],
                'severity' => 'high',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Tuberculosis (TB)',
                'category' => 'infectious',
                'summary' => 'A bacterial infection that mainly affects the lungs.',
                'common_symptoms' => ['Persistent cough > 2 weeks', 'Coughing blood', 'Night sweats', 'Weight loss', 'Fever'],
                'risk_factors' => ['Close contact with TB patient', 'HIV', 'Crowded living'],
                'prevention' => ['BCG vaccine', 'Good ventilation', 'Early treatment'],
                'when_to_seek_help' => ['Cough > 2 weeks', 'Coughing blood', 'Night sweats'],
                'keywords' => ['tb', 'tuberculosis', 'cough', 'lungs'],
                'severity' => 'high',
                'is_chronic' => true,
                'requires_medication' => true,
            ],
            [
                'condition_name' => 'Typhoid Fever',
                'category' => 'infectious',
                'summary' => 'A bacterial infection spread through contaminated food and water.',
                'common_symptoms' => ['High fever', 'Stomach pain', 'Headache', 'Constipation or diarrhoea', 'Weakness'],
                'risk_factors' => ['Poor sanitation', 'Contaminated water'],
                'prevention' => ['Drink boiled/bottled water', 'Wash hands', 'Vaccination'],
                'when_to_seek_help' => ['High fever > 3 days', 'Severe abdominal pain', 'Blood in stool'],
                'keywords' => ['typhoid', 'fever', 'stomach', 'diarrhoea', 'diarrhea'],
                'severity' => 'high',
                'is_chronic' => false,
                'requires_medication' => true,
            ],
        ];

        foreach ($conditions as $c) {
            ConditionKnowledge::create($c);
        }

        $videos = [
            ['title' => 'Understanding High Blood Pressure', 'category' => 'bp', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['bp', 'blood pressure', 'hypertension']],
            ['title' => 'Managing Diabetes Daily', 'category' => 'diabetes', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['diabetes', 'sugar', 'insulin']],
            ['title' => 'Pregnancy Care Basics', 'category' => 'pregnancy', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['pregnancy', 'prenatal']],
            ['title' => 'Malaria Prevention Tips', 'category' => 'infectious', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['malaria', 'mosquito']],
            ['title' => 'Cancer Awareness & Screening', 'category' => 'cancer', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['cancer', 'screening', 'tumour']],
            ['title' => 'Managing Anxiety & Stress', 'category' => 'mental_health', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['anxiety', 'stress', 'mental']],
            ['title' => 'Living Well with HIV', 'category' => 'infectious', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['hiv', 'aids']],
            ['title' => 'TB: Diagnosis & Treatment', 'category' => 'infectious', 'youtube_id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'keywords' => ['tb', 'tuberculosis']],
        ];

        foreach ($videos as $v) {
            ResourceVideo::create($v);
        }
    }
}