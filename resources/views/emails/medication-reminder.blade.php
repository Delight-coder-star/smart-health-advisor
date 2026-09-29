<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medication Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; padding: 30px; color: #1f2937; margin: 0;">

<div style="max-width: 560px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

    <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 30px; text-align: center;">
        <h1 style="margin: 0; font-size: 22px;">💊 Time to Take Your Medication</h1>
        <p style="margin: 8px 0 0; opacity: 0.9;">Smart Health Advisor · Harare Polytechnic</p>
    </div>

    <div style="padding: 30px;">
        <p style="margin-top: 0;">Hi <strong>{{ $medication->user->name ?? 'there' }}</strong>,</p>

        <p>
            This is your scheduled reminder from <strong>Smart Health Advisor</strong>. It's time to take your medication.
        </p>

        <div style="background: #eef2ff; border-left: 4px solid #4f46e5; padding: 20px; margin: 24px 0; border-radius: 8px;">
            <h2 style="margin: 0 0 12px; font-size: 20px; color: #4338ca;">{{ $medication->name }}</h2>

            @if($medication->dosage)
                <p style="margin: 6px 0;"><strong>Dosage:</strong> {{ $medication->dosage }}</p>
            @endif

            @if($medication->frequency)
                <p style="margin: 6px 0;"><strong>Frequency:</strong> {{ $medication->frequency }}</p>
            @endif

            @if($medication->condition)
                <p style="margin: 6px 0;"><strong>For:</strong> {{ $medication->condition }}</p>
            @endif

            @if($medication->instructions)
                <p style="margin: 12px 0 0;"><strong>Instructions:</strong> {{ $medication->instructions }}</p>
            @endif
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin: 20px 0; border-radius: 8px;">
            <p style="margin: 0 0 8px; font-weight: bold; color: #92400e;">⚠️ Why you shouldn't skip this dose</p>
            <ul style="margin: 0; padding-left: 20px; color: #78350f;">
                <li>Skipping doses can reduce how well your treatment works.</li>
                <li>Missing regular doses may cause your symptoms to return or worsen.</li>
                <li>For chronic conditions (BP, diabetes, HIV), missed doses can lead to serious complications.</li>
                <li>If you miss a dose, take it as soon as you remember — but <strong>do not double up</strong>.</li>
                <li>If you keep missing doses, contact your healthcare provider immediately.</li>
            </ul>
        </div>

        <div style="background: #f3f4f6; padding: 16px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 8px; font-weight: bold;">How to stay on track:</p>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Take your medication at the same time every day.</li>
                <li>Keep it somewhere visible — near your bed or desk.</li>
                <li>Use a pill organizer if you have multiple medications.</li>
                <li>Tell a family member or friend to check on you.</li>
            </ul>
        </div>

        <p style="margin-top: 24px;">
            <strong>Need urgent help?</strong> Call the health helpline: <strong>+263 717 923 021</strong>
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
            This is an automated reminder from Smart Health Advisor.<br>
            Do not reply to this email.
        </p>
    </div>
</div>

</body>
</html>