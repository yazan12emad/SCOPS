<!DOCTYPE html>
<html>
<body>
<h1>Hi {{ $firstName }},</h1>
<p>Thank you for registering with SCOPS!</p>
<p>Your email verification code is:</p>
<h2 style="letter-spacing: 8px; font-size: 36px;">
    {{ $verificationCode }}
</h2>
<p>Enter this code in the app to verify your email.</p>
</body>
</html>
