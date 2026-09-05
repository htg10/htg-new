<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Help Together Group</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background-color: #5f7af1;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            color: white;
        }

        .content {
            margin: 20px 0;
        }

        .content p {
            margin: 10px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            color: #5f7af1;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Welcome to the Help Together Group, {{ $data['contact'] }}!</h1>
        </div>
        <div class="content">
            <p>Dear {{ $data['contact'] }},</p>
            <p>Thank you for choosing Help Together Group. We’re thrilled to have you with us!</p>
            <p>Your purchase of
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Total Amount</th>
                        <th>Balance Amount</th>
                        <th>Validity</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productDetails as $product)
                        <tr>
                            <td>{{ $product['product_name'] }}</td>
                            <td>{{ $product['total_amount'] }}</td>
                            <td>{{ $product['balance_amount'] }}</td>
                            <td>{{ $product['validity'] }}</td>
                            <td>{{ $product['expiry_date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table> is an excellent choice, and we hope it meets all your expectations.
            Our team is dedicated to providing the best service possible and ensuring your satisfaction.</p>
            <p>If you have any questions or need assistance, our support team is here to help from 10:00am to 06:00pm
                IST. You can reach us at <a href="mailto:support@helptogethergroup.com">support@helptogethergroup.com</a>
                or +91 9634644622.</p>
        </div>
        <div class="footer">
            <p>Warm regards,</p>
            <p>Support Team<br>Help Together Group</p>
            <p>Important Contacts:</p>
            <p>For Complaints / Support:<br>(+91) 96346 44622 | <a
                    href="mailto:support@helptogethergroup.com">support@helptogethergroup.com</a></p>
        </div>
    </div>
</body>

</html>
