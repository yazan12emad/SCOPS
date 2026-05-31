<!DOCTYPE html>
<html>
<body>
<h1>Hi {{ $firstName }},</h1>
<p>You requested to reset your SCOPS password.</p>
<p>Your verification code is:</p>
<h2 style="letter-spacing: 8px; font-size: 36px;">{{ $resetCode }}</h2>
<p>Enter this code in the SCOPS app to reset your password.</p>
<p>This code expires in 60 minutes.</p>
<p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>
