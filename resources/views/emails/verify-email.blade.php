<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;color:#253247;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f6f8;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;">
                    <tr>
                        <td align="center" style="padding:0 20px 20px;">
                            <img src="{{ asset('images/ncc-logo.png') }}" width="72" height="72" alt="Northeastern Cebu Colleges seal" style="display:block;width:72px;height:72px;object-fit:contain;">
                            <p style="margin:12px 0 0;color:#7b1113;font-size:13px;font-weight:bold;letter-spacing:1.2px;text-transform:uppercase;">Northeastern Cebu Colleges, Inc.</p>
                            <p style="margin:5px 0 0;color:#718096;font-size:12px;">Danao City, Cebu</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="overflow:hidden;border:1px solid #e2e8f0;border-radius:16px;background-color:#ffffff;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="height:6px;background-color:#7b1113;font-size:0;line-height:0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="padding:36px 40px 32px;">
                                        <p style="margin:0 0 8px;color:#7b1113;font-size:12px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Account verification</p>
                                        <h1 style="margin:0 0 18px;color:#172033;font-size:26px;line-height:1.3;">Welcome to NCC, {{ $student->name }}</h1>
                                        <p style="margin:0 0 14px;color:#526174;font-size:15px;line-height:1.7;">Thanks for creating your student account. Please verify your email address to secure your account and continue to the student portal.</p>
                                        <p style="margin:0 0 26px;color:#526174;font-size:15px;line-height:1.7;">Click the button below to confirm this email address belongs to you.</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin:0 auto 24px;">
                                            <tr>
                                                <td align="center" style="border-radius:9px;background-color:#7b1113;">
                                                    <a href="{{ $verificationUrl }}" target="_blank" style="display:inline-block;border:1px solid #7b1113;border-radius:9px;padding:14px 28px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Verify Email Address</a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin:0;color:#718096;font-size:13px;line-height:1.7;text-align:center;">This verification link expires in {{ $expirationMinutes }} minutes.</p>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:28px;">
                                            <tr>
                                                <td style="border-top:1px solid #e8edf2;padding-top:22px;">
                                                    <p style="margin:0 0 8px;color:#526174;font-size:13px;line-height:1.6;">If the button does not work, copy and paste this link into your browser:</p>
                                                    <p style="margin:0;word-break:break-all;color:#7b1113;font-size:12px;line-height:1.6;">
                                                        <a href="{{ $verificationUrl }}" style="color:#7b1113;">{{ $verificationUrl }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin:24px 0 0;color:#718096;font-size:13px;line-height:1.7;">If you did not create an account, you can safely ignore this email.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px 16px 0;">
                            <p style="margin:0;color:#7b8492;font-size:12px;line-height:1.7;">Northeastern Cebu Colleges, Inc. · Danao City, Cebu</p>
                            <p style="margin:5px 0 0;color:#9aa3af;font-size:11px;">This is an automated message. Please do not reply to this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
