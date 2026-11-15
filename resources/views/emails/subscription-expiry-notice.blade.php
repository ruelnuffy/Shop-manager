<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Notice</title>
</head>
<body style="margin:0;padding:24px;background-color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#0f172a;">
    @php
        $isExpired = $noticeType === 'expired';
        $headerBg = $isExpired ? '#dc2626' : '#d97706';
        $headerLabel = $isExpired ? 'Subscription Expired' : 'Renewal Heads Up';
        $headerTitle = $isExpired
            ? "{$tenant->name} Plan Has Expired"
            : "{$tenant->name} Expires in {$daysRemaining} Day(s)";
    @endphp
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="background:{{ $headerBg }};padding:20px 24px;color:#ffffff;">
                <div style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;opacity:0.9;">{{ $headerLabel }}</div>
                <div style="font-size:20px;font-weight:800;margin-top:4px;">{{ $headerTitle }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px;">
                <p style="margin:0 0 16px;font-size:14px;line-height:1.5;color:#334155;">
                    @if ($isExpired)
                        The 1-month subscription cycle for <strong>{{ $tenant->name }}</strong> has ended, and store features have been paused until renewal.
                    @else
                        This is a heads-up that the 1-month subscription for <strong>{{ $tenant->name }}</strong> will expire in <strong>{{ $daysRemaining }} day(s)</strong>. Please renew before the expiry date to avoid interruption.
                    @endif
                </p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;width:150px;">Store Name</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:700;color:#0f172a;">{{ $tenant->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;color:#64748b;">Plan</td>
                        <td style="padding:10px 0;border-bottom:1px solid #f1f5f9;font-weight:700;color:#2563eb;text-transform:uppercase;">{{ $tenant->plan ?: 'standard' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#64748b;">Expiry Date</td>
                        <td style="padding:10px 0;font-weight:700;color:#0f172a;">{{ $tenant->subscription_expires_at?->format('M d, Y g:i A') ?? 'N/A' }}</td>
                    </tr>
                </table>

                <div style="margin-top:24px;">
                    <a href="{{ $whatsappRenewUrl }}" style="display:inline-block;background:#16a34a;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 20px;border-radius:10px;">
                        Renew on WhatsApp &rarr;
                    </a>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
