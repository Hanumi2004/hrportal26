<!DOCTYPE html>
<html>
<head>
    <title>Two-Factor Authentication Code</title>
</head>

<body>
    <h2>Hello {{ $user->name ?? 'User'}},</h2>
        <p>Your Two-Factor-Authentication code for HR Portal is:</p>
        <h1>{{ $code }}</h1>
        <p>This code will expire in 10 minutes.</p>
</body>
</html>