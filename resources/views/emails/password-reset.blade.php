<!DOCTYPE html>
<html>
<body>
<h1>Hi {{ $firstName }},</h1>
<p>You requested to reset your SCOPS password.</p>
<p>Click the link below to reset your password:</p>
<a href="{{ $resetLink }}">Reset Password</a>
<p>This link expires in 60 minutes.</p>
<p>If you did not request a password reset, please ignore this email.</p>
</body>
</html>
