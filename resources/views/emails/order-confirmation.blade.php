<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f5f2;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#111827;">
@php
    $storeName = $tenant?->name ?? config('app.name', 'ShopManager');
    $currency = '₦';
    $items = $order->items ?? collect();
    $isBankTransfer = $order->payment_method === 'bank_transfer';
@endphp
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f6f5f2;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                <tr>
                    <td style="padding:28px 32px;background-color:#111827;color:#ffffff;">
                        <div style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#d1d5db;margin-bottom:6px;">
                            {{ $storeName }}
                        </div>
                        <h1 style="margin:0;font-size:22px;font-weight:700;line-height:1.3;">
                            Order Received: {{ $order->order_number }}
                        </h1>
                        <p style="margin:8px 0 0;font-size:14px;color:#e5e7eb;">
                            Thank you, {{ $order->shipping_name }}. We have received your order.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px 32px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:24px;background-color:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;">
                            <tr>
                                <td style="padding:16px 20px;">
                                    <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:8px;">
                                        Order Summary
                                    </div>
                                    <div style="font-size:14px;line-height:1.6;color:#111827;">
                                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                                        <strong>Payment Method:</strong> {{ $isBankTransfer ? 'Bank Transfer' : ucfirst(str_replace('_', ' ', (string) $order->payment_method)) }}<br>
                                        <strong>Payment Status:</strong> {{ ucfirst((string) $order->payment_status) }}<br>
                                        <strong>Total Amount:</strong> {{ $currency }}{{ number_format((float) $order->total_amount, 2) }}
                                    </div>
                                </td>
                            </tr>
                        </table>

                        @if($isBankTransfer)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:24px;background-color:#ecfdf5;border-radius:12px;border:1px solid #a7f3d0;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#065f46;margin-bottom:8px;">
                                            Bank Transfer Instructions
                                        </div>
                                        @if(!empty($bankAccount['account_number']))
                                            <div style="font-size:14px;line-height:1.6;color:#064e3b;">
                                                <strong>Bank:</strong> {{ $bankAccount['bank_name'] }}<br>
                                                <strong>Account Number:</strong> {{ $bankAccount['account_number'] }}<br>
                                                <strong>Account Name:</strong> {{ $bankAccount['account_name'] }}<br>
                                                <strong>Reference:</strong> {{ $order->order_number }}
                                            </div>
                                        @else
                                            <p style="margin:0;font-size:14px;line-height:1.6;color:#064e3b;">
                                                Please complete your transfer and confirm your payment with the store on WhatsApp using reference <strong>{{ $order->order_number }}</strong>.
                                            </p>
                                        @endif

                                        @if($whatsappConfirmUrl !== '')
                                            <div style="margin-top:14px;">
                                                <a href="{{ $whatsappConfirmUrl }}" style="display:inline-block;padding:10px 18px;background-color:#059669;color:#ffffff;text-decoration:none;border-radius:8px;font-size:13px;font-weight:600;">
                                                    Confirm Transfer on WhatsApp
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif

                        @if($items->isNotEmpty())
                            <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:10px;">
                                Items Ordered
                            </div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:24px;border-collapse:collapse;">
                                @foreach($items as $item)
                                    <tr>
                                        <td style="padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:14px;color:#111827;">
                                            <div style="font-weight:600;">{{ $item->product_name ?? $item->product?->name ?? 'Product' }}</div>
                                            @if(!empty($item->variant_name ?? $item->variant_label))
                                                <div style="font-size:12px;color:#6b7280;">{{ $item->variant_name ?? $item->variant_label }}</div>
                                            @endif
                                            <div style="font-size:12px;color:#6b7280;">Qty: {{ $item->quantity }}</div>
                                        </td>
                                        <td align="right" style="padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:14px;font-weight:600;color:#111827;white-space:nowrap;">
                                            {{ $currency }}{{ number_format((float) ($item->subtotal ?? $item->total_price ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="padding:10px 0 4px;font-size:13px;color:#6b7280;">Subtotal</td>
                                    <td align="right" style="padding:10px 0 4px;font-size:13px;color:#111827;">{{ $currency }}{{ number_format((float) $order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Courier Fee</td>
                                    <td align="right" style="padding:4px 0;font-size:13px;color:#111827;">{{ $currency }}{{ number_format((float) $order->shipping_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0 0;font-size:15px;font-weight:700;color:#111827;border-top:1px solid #e5e7eb;">Total</td>
                                    <td align="right" style="padding:10px 0 0;font-size:15px;font-weight:700;color:#111827;border-top:1px solid #e5e7eb;">{{ $currency }}{{ number_format((float) $order->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        @endif

                        <div style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;margin-bottom:8px;">
                            Delivery Details
                        </div>
                        <div style="font-size:14px;line-height:1.6;color:#374151;">
                            <strong>{{ $order->shipping_name }}</strong><br>
                            {{ $order->shipping_email }}<br>
                            {{ $order->shipping_phone }}<br>
                            {{ $order->shipping_address }}, {{ $order->shipping_city }}
                            @if(!empty($order->notes))
                                <br><em>Notes: {{ $order->notes }}</em>
                            @endif
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 32px;background-color:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;text-align:center;">
                        Sent by {{ $storeName }}. Please keep your order number <strong>{{ $order->order_number }}</strong> for reference.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
