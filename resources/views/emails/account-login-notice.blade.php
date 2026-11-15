<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Notice</title>
</head>
<body style="margin:0;padding:24px;background-color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="background:#0f172a;padding:20px 24px;color:#ffffff;">
                <div style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;opacity:0.85;">ShopManager Security</div>
                <div style="font-size:20px;font-weight:800;margin-top:4px;">Account Login Notice</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#334155;">
                    A user just signed in to ShopManager. Details are below:
                </p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;width:140px;">User</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Email</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#0f172a;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Role</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:600;color:#2563eb;text-transform:uppercase;">{{ $user->role ?: 'user' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Store</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:600;color:#0f172a;">{{ $tenant?->name ?: 'Platform' }}</td>
                    </tr>
                    @if ($tenant)
                        <tr>
                            <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Plan / Status</td>
                            <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#0f172a;text-transform:uppercase;">{{ $tenant->plan ?: 'standard' }} &bull; {{ $tenant->status }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">IP Address</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#0f172a;">{{ $ipAddress ?: 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#64748b;">Time</td>
                        <td style="padding:10px 0;color:#0f172a;">{{ $loggedInAt }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
