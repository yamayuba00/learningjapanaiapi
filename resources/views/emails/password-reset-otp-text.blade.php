{{ config('app.name') }} - Password Reset

Hello {{ $user->name }},

We received a request to reset your password for your {{ config('app.name') }} account.

Password Reset Code: {{ $otp }}

This code will expire in 15 minutes.

Enter this code in the app to verify your identity and set a new password.

SECURITY NOTICE:
- This code is only valid for 15 minutes
- Never share this code with anyone
- Our support team will never ask for this code

IMPORTANT: If you didn't request a password reset, please ignore this email and your password will remain unchanged. Consider changing your password if you suspect unauthorized access to your account.

---
This is an automated message from {{ config('app.name') }}.
Please do not reply to this email.

Need help? Contact us at support@japanai.com

© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.