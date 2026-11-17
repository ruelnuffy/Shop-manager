@php
    $storeName = $tenant?->name ?? config('app.name', 'ShopManager');
    $currency = 'NGN ';
    $items = $order->items ?? collect();
    $isBankTransfer = $order->payment_method === 'bank_transfer';
@endphp
{{ $storeName }} — Order Confirmation

Hello {{ $order->shipping_name }},

Thank you for your order with {{ $storeName }}.

ORDER SUMMARY
-------------
Order Number: {{ $order->order_number }}
Payment Method: {{ $isBankTransfer ? 'Bank Transfer' : ucfirst(str_replace('_', ' ', (string) $order->payment_method)) }}
Payment Status: {{ ucfirst((string) $order->payment_status) }}
Total Amount: {{ $currency }}{{ number_format((float) $order->total_amount, 2) }}
@if($isBankTransfer)

BANK TRANSFER INSTRUCTIONS
--------------------------
@if(!empty($bankAccount['account_number']))
Bank: {{ $bankAccount['bank_name'] }}
Account Number: {{ $bankAccount['account_number'] }}
Account Name: {{ $bankAccount['account_name'] }}
Reference: {{ $order->order_number }}
@else
Please complete your transfer and confirm your payment with the store on WhatsApp using reference {{ $order->order_number }}.
@endif
@if($whatsappConfirmUrl !== '')
Confirm on WhatsApp: {{ $whatsappConfirmUrl }}
@endif
@endif
@if($items->isNotEmpty())

ITEMS ORDERED
-------------
@foreach($items as $item)
- {{ $item->product_name ?? $item->product?->name ?? 'Product' }}@if(!empty($item->variant_name ?? $item->variant_label)) ({{ $item->variant_name ?? $item->variant_label }})@endif x{{ $item->quantity }} — {{ $currency }}{{ number_format((float) ($item->subtotal ?? $item->total_price ?? 0), 2) }}
@endforeach

Subtotal: {{ $currency }}{{ number_format((float) $order->subtotal, 2) }}
Courier Fee: {{ $currency }}{{ number_format((float) $order->shipping_fee, 2) }}
Total: {{ $currency }}{{ number_format((float) $order->total_amount, 2) }}
@endif

DELIVERY DETAILS
----------------
{{ $order->shipping_name }}
{{ $order->shipping_email }}
{{ $order->shipping_phone }}
{{ $order->shipping_address }}, {{ $order->shipping_city }}
@if(!empty($order->notes))
Notes: {{ $order->notes }}
@endif

Please keep your order number {{ $order->order_number }} for reference.
