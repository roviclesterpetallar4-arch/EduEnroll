<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;background:#f8fafc;color:#1e293b;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:640px;margin:32px auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <div style="background:#4a080a;padding:28px 32px;color:#ffffff;">
            <p style="margin:0;color:#fcd34d;font-size:12px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Northeastern Cebu Colleges, Inc.</p>
            <h1 style="margin:8px 0 0;font-size:24px;">{{ $heading }}</h1>
        </div>
        <div style="padding:28px 32px;">
            <p>Dear {{ $student->name }},</p>
            <p>{{ $bodyText }}</p>
            <p><strong>Course:</strong> {{ $course->code }} — {{ $course->title }}</p>
            <p>If you have questions, please contact NCC administration.</p>
        </div>
    </div>
</body>
</html>
