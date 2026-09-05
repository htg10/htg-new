<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

    <h3>Finance Report</h3>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Payment Mode</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>{{ number_format($row[3], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
