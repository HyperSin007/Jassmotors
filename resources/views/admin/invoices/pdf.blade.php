<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            padding: 40px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }

        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .company-header {
            margin-bottom: 10px;
        }

        .company-header table {
            width: 100%;
            border: none;
        }

        .company-header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 70px;
            padding-right: 12px;
        }

        .logo-cell img {
            max-width: 70px;
            max-height: 70px;
        }

        .company-name {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .company-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            padding-right: 15px;
        }

        .company-logo img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .company-brand {
            display: table-cell;
            vertical-align: middle;
        }

        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
        }

        .invoice-title {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .invoice-title h1 {
            font-size: 36px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 10px;
        }

        .invoice-meta {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
        }

        .invoice-meta strong {
            color: #333;
            display: inline-block;
            width: 80px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-draft {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-final {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .details-section {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }

        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .bill-to h3 {
            font-size: 14px;
            font-weight: bold;
            color: #4B5563;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .customer-info {
            font-size: 13px;
            line-height: 1.8;
        }

        .customer-name {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background-color: #F3F4F6;
        }

        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #D1D5DB;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 13px;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .totals-section {
            width: 100%;
            margin-top: 30px;
        }

        .totals-table {
            width: 350px;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px 15px;
            font-size: 14px;
        }

        .totals-table .label {
            text-align: left;
            color: #6B7280;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 600;
            color: #1F2937;
        }

        .totals-table .subtotal-row {
            border-top: 1px solid #E5E7EB;
        }

        .totals-table .vat-row {
            border-bottom: 1px solid #E5E7EB;
        }

        .totals-table .total-row {
            background-color: #F9FAFB;
            border-top: 2px solid #4F46E5;
        }

        .totals-table .total-row td {
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
        }

        .footer {
            clear: both;
            margin-top: 80px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: #6B7280;
            line-height: 1.8;
        }

        .footer-text .thank-you {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- Header with Company Info and Invoice Title -->
    <div class="header">
        <div class="company-info">
            <div class="company-header">
                <table>
                    <tr>
                        @php
                            $logoPath = \App\Models\Setting::get('site_logo');
                            if ($logoPath) {
                                $fullLogoPath = public_path('storage/' . $logoPath);
                                $showLogo = file_exists($fullLogoPath);
                            } else {
                                $showLogo = false;
                            }
                        @endphp
                        @if($showLogo)
                        <td class="logo-cell">
                            <img src="{{ $fullLogoPath }}" alt="">
                        </td>
                        @endif
                        <td>
                            <div class="company-name">{{ \App\Models\Setting::get('business_name', 'Jass Motors') }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="company-details">
                {{ \App\Models\Setting::get('business_address', '123 Auto Street, Mechanic Lane') }}<br>
                {{ \App\Models\Setting::get('business_city', 'City, State - 123456') }}<br>
                Phone: {{ \App\Models\Setting::get('business_phone', '(123) 456-7890') }}<br>
                Email: {{ \App\Models\Setting::get('business_email', 'info@jassmotors.com') }}
            </div>
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="invoice-meta">
                <strong>Invoice #:</strong> {{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Date:</strong> {{ $invoice->date->format('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Bill To Section -->
    <div class="details-section">
        <div class="bill-to">
            <h3>Bill To:</h3>
            <div class="customer-info">
                <div class="customer-name">{{ $invoice->customer_name }}</div>
                {{ $invoice->customer_address }}<br>
                Phone: {{ $invoice->customer_phone }}<br>
                Email: {{ $invoice->customer_email }}
                @if($invoice->car_model || $invoice->license_plate)
                    <br><br>
                    @if($invoice->car_model)
                        <strong>Car Model:</strong> {{ $invoice->car_model }}<br>
                    @endif
                    @if($invoice->license_plate)
                        <strong>License Plate:</strong> {{ $invoice->license_plate }}
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Service</th>
                <th class="text-center" style="width: 100px;">Quantity</th>
                <th class="text-right" style="width: 120px;">Price</th>
                <th class="text-right" style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotalWithoutVAT = 0;
                $totalWithVAT = 0;
                $vatRate = 0.255;
            @endphp
            @foreach ($invoice->items as $item)
                @php
                    $itemTotal = $item->quantity * $item->price;
                    $totalWithVAT += $itemTotal;
                @endphp
                <tr>
                    <td>{{ $item->service_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">&euro;{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">&euro;{{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Section -->
    <div class="totals-section clearfix">
        @php
            $subtotalWithoutVAT = $totalWithVAT / (1 + $vatRate);
            $vatAmount = $totalWithVAT - $subtotalWithoutVAT;
        @endphp
        <table class="totals-table">
            <tr class="subtotal-row">
                <td class="label">Sub Total:</td>
                <td class="value">&euro;{{ number_format($subtotalWithoutVAT, 2) }}</td>
            </tr>
            <tr class="vat-row">
                <td class="label">VAT (25.5%):</td>
                <td class="value">&euro;{{ number_format($vatAmount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total (EUR):</td>
                <td class="value">&euro;{{ number_format($totalWithVAT, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-text">
            <div class="thank-you">{{ \App\Models\Setting::get('invoice_footer', 'Thank you for your business!') }}</div>
            @if(\App\Models\Setting::get('invoice_footer_note'))
                {{ \App\Models\Setting::get('invoice_footer_note') }} <strong>#{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</strong><br>
            @endif
            @if(\App\Models\Setting::get('invoice_footer_contact'))
                {{ \App\Models\Setting::get('invoice_footer_contact') }}
            @endif
        </div>
    </div>
</body>
</html>