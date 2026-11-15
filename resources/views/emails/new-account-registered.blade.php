<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Account Registered</title>
</head>
<body style="margin:0;padding:24px;background-color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="background:#2563eb;padding:20px 24px;color:#ffffff;">
                <div style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;opacity:0.9;">ShopManager Alert</div>
                <div style="font-size:20px;font-weight:800;margin-top:4px;">New Store Account Created</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#334155;">
                    A new user just registered on ShopManager. If they don't message on WhatsApp first, you can reach out directly using their number below to handle billing and activate their plan.
                </p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;width:140px;">Store Name</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $tenant?->name ?: 'Platform' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Selected Plan / Role</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:700;color:#2563eb;text-transform:uppercase;">{{ $tenant?->plan ?: ($user->role ?: 'standard') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Owner Name</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:600;color:#0f172a;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Email</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#0f172a;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Phone / WhatsApp</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:800;font-size:15px;color:#059669;">{{ $user->phone ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#64748b;">Subdomain</td>
                        <td style="padding:10px 0;color:#0f172a;">{{ $tenant?->subdomain ?: 'N/A' }}</td>
                    </tr>
                </table>

                @if ($whatsappReachoutUrl)
                    <div style="margin-top:24px;">
                        <a href="{{ $whatsappReachoutUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 20px;border-radius:10px;">
                            Message {{ $user->name }} on WhatsApp &rarr;
                        </a>
                    </div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
