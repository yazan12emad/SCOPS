<!DOCTYPE html>
<html>
<body>
    <h1>Hi {{ $firstName }},</h1>
    <p>Your <strong>{{ $serviceName }}</strong> subscription will renew in <strong>{{ $daysLeft }} days</strong>.</p>
    <p>Renewal date: {{ $renewalDate }}</p>
    <p>Log in to SCOPS to manage your subscription.</p>
</body>
</html>
