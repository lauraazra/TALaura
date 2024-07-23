<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .invoice {
            width: 
            @foreach ($invoice->seller->custom_fields as $key => $value) 
                @if ($key == 'paper_size')
                    {{ $value }}
                @endif
            @endforeach
            ; /* Adjust based on your printer's paper width */
            margin: auto;
            padding: 10px;
            box-sizing: border-box;
        }
        .header, .footer, .content {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
        }
        .header p, .content p, .footer p {
            margin: 2px 0;
        }
        .line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .details {
            width: 100%;
            margin-bottom: 10px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td {
            padding: 2px 0;
        }
        .items {
            margin-top: 5px;
            width: 100%;
            text-align: left;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items td {
            padding: 2px 0;
        }
        .total {
            margin-top: 10px;
            width: 100%;
            text-align: left;
        }
        .total table {
            width: 100%;
            border-collapse: collapse;
        }
        .total td {
            padding: 2px 0;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h1>{{ $invoice->name }}</h1>
            <p>{{ $invoice->seller->address }}</p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td>{{ $invoice->getDate() }}</td>
                    <td style="text-align: right;">Kasir: {{ $invoice->seller->name }}</td>
                </tr>
                <tr>
                    <td>{{ $invoice->getSerialNumber() }}</td>
                    <td style="text-align: right;">Pembeli: {{ $invoice->buyer->name }}</td>
                </tr>
            </table>
        </div>

        <div class="line"></div>

        <div class="items">
            <table>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="padding-top: 5px;">{{ $item->title }}</td>
                    <td style="text-align: right;"></td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }} x {{ $item->price_per_unit }}</td>
                    <td style="text-align: right;">{{ $invoice->formatCurrency($item->sub_total_price) }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        <div class="line"></div>

        <div class="total">
            <table>
                <tr>
                    <td>Total Harga:</td>
                    <td style="text-align: right;">{{ $invoice->formatCurrency($invoice->total_amount) }}</td>
                </tr>
            </table>
        </div>

        <div class="line"></div>

        <div class="footer">
            <p>{!! $invoice->notes !!}</p>
        </div>
    </div>
</body>
</html>