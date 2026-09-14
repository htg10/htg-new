<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #5b73e8;
            padding-bottom: 12px;
        }

        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 60px;
        }

        .header-logo img {
            height: 50px;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .header-text h2 {
            margin: 0 0 2px;
            font-size: 18px;
            color: #5b73e8;
        }

        .header-text p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 11px;
        }

        th {
            background: #5b73e8;
            color: #fff;
            font-weight: 600;
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f7f8fc;
        }

        .income {
            color: #0f7a52;
            font-weight: 600;
        }

        .expense {
            color: #c33c2e;
            font-weight: 600;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        .totals-row td {
            font-weight: 700;
            background: #eef0f8;
            border-top: 2px solid #5b73e8;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('assets/images/logo/htg_logo.png') }}" alt="HTG Logo">
        </div>
        <div class="header-text">
            <h2>Help Together Group</h2>
            <p>Finance Report &mdash; Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Company</th>
                <th>Service</th>
                <th>Payment Mode</th>
                <th>Contract Date</th>
                <th>Payment Date</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIncome = 0;
                $totalExpense = 0;
            @endphp

            @foreach ($records as $row)
                <tr>
                    <td class="{{ $row['type'] === 'Income' ? 'income' : 'expense' }}">
                        {{ $row['type'] }}
                    </td>
                    <td>{{ $row['company'] }}</td>
                    <td>{{ $row['service'] }}</td>
                    <td>{{ $row['payment'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['payment_date'] }}</td>
                    <td class="text-right">₹{{ number_format($row['amount'], 2) }}</td>
                </tr>
                @php
                    if ($row['type'] === 'Income') {
                        $totalIncome += $row['amount'];
                    } else {
                        $totalExpense += $row['amount'];
                    }
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="6" class="text-right">Total Income</td>
                <td class="text-right income">₹{{ number_format($totalIncome, 2) }}</td>
            </tr>
            <tr class="totals-row">
                <td colspan="6" class="text-right">Total Expense</td>
                <td class="text-right expense">₹{{ number_format($totalExpense, 2) }}</td>
            </tr>
            <tr class="totals-row">
                <td colspan="6" class="text-right">Net Balance</td>
                <td class="text-right" style="color: {{ ($totalIncome - $totalExpense) >= 0 ? '#0f7a52' : '#c33c2e' }}">
                    ₹{{ number_format($totalIncome - $totalExpense, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        HTG Console v2 &bull; Help Together Group &bull; Confidential
    </div>

</body>

</html>
