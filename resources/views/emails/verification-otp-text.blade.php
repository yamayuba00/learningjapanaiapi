{{ config('app.name') }} - Email Verification

Hello {{ $user->name }}!

Thank you for registering with {{ config('app.name') }}. To complete your registration and verify your email address, please use the OTP code below:

Your Verification Code: {{ $otp }}

This code will expire in 10 minutes.

Enter this code in the app to verify your email address and start your Japanese learning journey!

SECURITY NOTICE: If you didn't create an account with {{ config('app.name') }}, please ignore this email. Never share your verification code with anyone.

---
This is an automated message from {{ config('app.name') }}.
Please do not reply to this email.

Need help? Contact us at support@japanai.com

© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.