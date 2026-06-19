<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Account Created</title>
</head>

<body>
    <p>Dear {{ $user->name }},</p>

    <p>
        Your AHG HR Portal account has been created by the administrator. Please use the following credentials to log in:
    </p>

    <p>
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>Temporary Password:</strong> {{ $tempPassword }}
    </p>

    <p>
        For security reasons, please change your temporary password immediately after logging in.
    </p>

    <p>
        <a href="{{ url('/login') }}">
            Click here to log in
        </a>
    </p>

    <p>
        If you did not expect this email, please contact HR immediately.
    </p>

    <p>Regards,<br>
        AHG HR System Administrator</p>
</body>

</html>
