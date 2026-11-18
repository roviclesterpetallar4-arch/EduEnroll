<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Application Approved</title>
</head>
<body style="margin:0;background:#f8fafc;color:#1e293b;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:640px;margin:32px auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <div style="background:#4a080a;padding:28px 32px;color:#ffffff;">
            <p style="margin:0;color:#fcd34d;font-size:12px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Northeastern Cebu Colleges, Inc.</p>
            <h1 style="margin:8px 0 0;font-size:24px;">Enrollment Approved</h1>
        </div>
        <div style="padding:28px 32px;">
            <p>Dear {{ $student->name }},</p>
            <p>Congratulations! Your enrollment application as a <strong>{{ $studentType }}</strong> has been approved.</p>
            @if ($organization && $academicPeriod)
                <p>You have been accepted into <strong>{{ $organization->name }} ({{ $organization->code }})</strong> for <strong>{{ $academicPeriod->label() }}</strong>.</p>
            @endif
            <p>Please submit the following requirements to complete your enrollment:</p>
            <ul>
                @foreach ($requirements as $requirement)
                    <li>{{ $requirement }}</li>
                @endforeach
            </ul>
            <p>Submit your documents to the Registrar's Office during the office hours below.</p>
        </div>
        <div style="background:#f1f5f9;padding:24px 32px;font-size:14px;">
            <strong>Registrar's Office Contact Details</strong>
            <p style="margin:8px 0 0;">Office Hours: Monday – Friday, 8:00 AM – 5:00 PM</p>
            <p style="margin:4px 0;">Submissions Location: Registrar's Office</p>
            <p style="margin:4px 0;">Email: <a href="mailto:nccdanao@gmail.com">nccdanao@gmail.com</a></p>
            <p style="margin:4px 0;">Phone: 032 326 9294 / (+63) 922 828 2445</p>
            <p style="margin:4px 0;">Facebook/Messenger: /NortheasternCebuCollegesInc</p>
        </div>
    </div>
</body>
</html>
