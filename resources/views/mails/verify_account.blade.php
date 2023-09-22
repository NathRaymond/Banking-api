<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.6;
        }

        h1 {
            font-weight: 700;
            font-size: 24px;
            text-align: center;
        }

        p {
            font-weight: 400;
            font-size: 16px;
            margin-bottom: 15px;
        }
    </style>
    <div style="max-width: 600px; margin: 0 auto;">
        <img src="{{ url('images/logo.png') }}" alt="{{ config('app.name') }}" width="200"
            height="auto" style="display: block; margin: 20px auto;">
        <h1 style="text-align: center;">Email Verification</h1>
        <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>
        <p>Thank you for registering on {{ config('app.name') }}! To complete your registration, please use the following
            verification code:</p>
        <div style="background-color: #f2f2f2; padding: 15px; text-align: center; font-size: 24px; font-weight: bold;">
            {{ $user->verification_code }}
        </div>
        <p>If you did not sign up for {{ config('app.name') }}, you can ignore this email.</p>
        <p>Best regards,<br>{{ config('app.name') }} Team</p>
        <hr>
        <p style="text-align: center; font-size: 12px; color: #888;">This email was sent from an auto-generated email
            address. Please do not reply to this email.</p>
    </div>
</body>

</html>
