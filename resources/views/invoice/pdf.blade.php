<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header-left h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }
        .header-left p {
            font-size: 11px;
            color: #666;
        }
        .header-right {
            text-align: right;
        }
        .header-right h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-info-left, .invoice-info-right {
            width: 48%;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-section p {
            margin: 6px 0;
            font-size: 11px;
            line-height: 1.6;
            display: block;
            clear: both;
            width: 100%;
        }
        .info-section strong {
            display: inline-block;
            width: 120px;
            vertical-align: top;
            font-weight: bold;
        }
        .booking-details {
            margin-bottom: 30px;
        }
        .booking-details h3 {
            font-size: 14px;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th,
        .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .details-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        .details-table td {
            font-size: 11px;
        }
        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .amount-table td {
            padding: 8px 10px;
            text-align: right;
            font-size: 11px;
        }
        .amount-table td:first-child {
            text-align: left;
            font-weight: bold;
        }
        .amount-table .total-row {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 13px;
        }
        .payment-history {
            margin-top: 30px;
        }
        .payment-history h3 {
            font-size: 14px;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-table th,
        .payment-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .payment-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #28a745;
            color: white;
        }
        .status-pending {
            background-color: #ffc107;
            color: #333;
        }
        .status-unpaid {
            background-color: #dc3545;
            color: white;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>{{ $order->hotel_name ?? 'Hotel' }}</h1>
            </div>
            <div class="header-right">
                <h2>INVOICE</h2>
                <p><strong>Invoice #:</strong> {{ $invoice_id }}</p>
                <p><strong>Date:</strong> {{ $invoice_date }}</p>
            </div>
        </div>

        <!-- Invoice Info -->
        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 45%; vertical-align: top; padding-right: 15px;">
                    <div class="info-section">
                        <h3>Guest Information</h3>
                        <table style="width: 100%;">
                            <tr>
                                <td style="padding: 4px 0;"><strong>Name:</strong></td>
                                <td style="padding: 4px 0;">{{ $order->billing_name ?? 'Guest' }}</td>
                            </tr>
                            @if($order->billing_email)
                            <tr>
                                <td style="padding: 4px 0;"><strong>Email:</strong></td>
                                <td style="padding: 4px 0;">{{ $order->billing_email }}</td>
                            </tr>
                            @endif
                            @if($order->billing_phone)
                            <tr>
                                <td style="padding: 4px 0;"><strong>Phone:</strong></td>
                                <td style="padding: 4px 0;">{{ $order->billing_phone }}</td>
                            </tr>
                            @endif
                            @if($order->billing_address)
                            <tr>
                                <td style="padding: 4px 0; vertical-align: top;"><strong>Address:</strong></td>
                                <td style="padding: 4px 0;">{{ $order->billing_address }}</td>
                            </tr>
                            @endif
                            @if($order->billing_city || $order->billing_state)
                            <tr>
                                <td style="padding: 4px 0;"><strong>City/State:</strong></td>
                                <td style="padding: 4px 0;">{{ trim(($order->billing_city ?? '') . ', ' . ($order->billing_state ?? ''), ', ') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </td>
                <td style="width: 48%; vertical-align: top; padding-left: 15px;">
                    <div class="info-section">
                        <h3>Booking Information</h3>
                        <table style="width: 100%;">
                            <tr>
                                <td style="padding: 4px 0;"><strong>Booking ID:</strong></td>
                                <td style="padding: 4px 0;">ORD-{{ $order->id }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Check-in:</strong></td>
                                <td style="padding: 4px 0;">{{ $check_in_date }} @ {{ $check_in_time }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Check-out:</strong></td>
                                <td style="padding: 4px 0;">{{ $check_out_date }} @ {{ $check_out_time }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0;"><strong>Nights:</strong></td>
                                <td style="padding: 4px 0;">{{ $order->number_of_nights }}</td>
                            </tr>
                            <!-- <tr>
                                <td style="padding: 4px 0;"><strong>Status:</strong></td>
                                <td style="padding: 4px 0;">
                                    @if($order->payment_status == 'paid')
                                        <span class="status-badge status-paid">PAID</span>
                                    @elseif($order->payment_status == 'partial')
                                        <span class="status-badge status-pending">PARTIAL</span>
                                    @else
                                        <span class="status-badge status-unpaid">UNPAID</span>
                                    @endif
                                </td>
                            </tr> -->
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Booking Details -->
        <div class="booking-details">
            <h3>Room Details</h3>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Room Numbers</th>
                        <th>Rooms</th>
                        <th>Adults</th>
                        <th>Children</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $order->room_type ?? 'N/A' }}</td>
                        <td>{{ !empty($room_numbers) ? implode(', ', $room_numbers) : 'Not Assigned' }}</td>
                        <td>{{ $order->rooms ?? 1 }}</td>
                        <td>{{ $order->adults ?? 1 }}</td>
                        <td>{{ $order->childs ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Amount Details -->
        <div class="booking-details">
            <h3>Amount Details</h3>
            <table class="amount-table">
                <tr>
                    <td>Room Rate ({{ $order->number_of_nights }} night(s)):</td>
                    <td>Rs. {{ number_format($order->room_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Sub Total:</td>
                    <td>Rs. {{ number_format($order->sub_total ?? 0, 2) }}</td>
                </tr>
                @if($order->discount && $order->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td>- Rs. {{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Grand Total:</td>
                    <td>Rs. {{ number_format($order->grand_total ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment History -->
        @if($payments && $payments->count() > 0)
        <div class="payment-history">
            <h3>Payment History</h3>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('d M Y') }}</td>
                        <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>{{ $payment->description ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total Paid:</strong></td>
                        <td><strong>Rs. {{ number_format($payments->sum('amount'), 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Balance:</strong></td>
                        <td><strong>Rs. {{ number_format(($order->grand_total ?? 0) - $payments->sum('amount'), 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Notes -->
        @if(isset($other_details['notes']) && !empty($other_details['notes']))
        <div class="booking-details">
            <h3>Notes</h3>
            <p style="font-size: 11px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #333;">{{ $other_details['notes'] }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing {{ $order->hotel_name ?? 'our hotel' }}!</p>
            <p>This is a computer-generated invoice. No signature required.</p>
        </div>
    </div>
</body>
</html>
