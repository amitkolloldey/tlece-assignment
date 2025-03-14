<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .page-break {
            page-break-after: always;
        }

        .header,
        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }

        .header {
            top: 0;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .footer {
            bottom: 0;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        .content {
            padding: 20px;
        }

        .logo {
            max-height: 80px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .address-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .company-details,
        .customer-details {
            width: 45%;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 300px;
            margin-left: auto;
            margin-top: 30px;
        }

        .totals table {
            width: 100%;
        }

        .grand-total {
            font-weight: bold;
            font-size: 16px;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="logo">
    </div>

    <div class="footer">
        <div>{{ $company['name'] }} | {{ $company['address'] }} </div>
        <div>{{ $company['phone'] }} | {{ $company['email'] }}</div>
        <div class="page-number">Page </div>
    </div>

    <div class="content">
        <div class="invoice-title">INVOICE #{{ $order->id }}</div>

        <div class="address-container">
            <div class="company-details">
                <div class="label">FROM:</div>
                <div>{{ $company['name'] }}</div>
                <div>{{ $company['address'] }}</div>
                <div>{{ $company['phone'] }}</div>
                <div>{{ $company['email'] }}</div>
            </div>

            <div class="customer-details">
                <div class="label">BILL TO:</div>
                <div>{{ $order->user->name }}</div>
                <div>{{ $order->user->email }}</div>
            </div>
        </div>

        <div>
            <div class="label">Invoice Date:</div>
            <div>{{ date('F j, Y') }}</div>
        </div>
        <div>
            <div class="label">Order Date:</div>
            <div>{{ $order->created_at->format('F j, Y') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="40%">Item</th>
                    <th width="15%">SKU</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Unit Price</th>
                    <th width="15%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                @endphp
                @foreach ($order->products as $index => $product)
                    @php
                        $line_total = $product->price * $product->pivot->quantity;
                        $subtotal += $line_total;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td class="text-right">${{ number_format($line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div>
            <div class="label">Payment Methods:</div>
            <table>
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_method }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">${{ number_format($subtotal, 2) }}</td>
                </tr>

                <tr class="grand-total">
                    <td>Total:</td>
                    <td class="text-right">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
