<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Renewal Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        .service-details,
        .contact-info {
            margin: 20px 0;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }

        .contact-info {
            background-color: #f0f0f0;
        }

        .contact-info strong {
            display: block;
            margin-bottom: 5px;
        }

        a.button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        a.button:hover {
            background-color: #45a049;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Renewal Reminder: Continue Enjoying Our Services, {{ $data['contact'] }}!</h1>

        <p>Dear {{ $data['contact'] }},</p>

        <p>We hope this message finds you well. As your current service period is nearing its end, we wanted to remind
            you about the upcoming renewal of your Product/Service with Help Together Group.</p>

        <div class="service-details">
            <strong>Service Details:</strong>
            {{-- <p>Service Name: <b>[Product/Service]</b></p>
            <p>Renewal Date: <b>[Date]</b></p> --}}
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
            </table>
        </div>

        <p>We’ve enjoyed partnering with you and hope you’ve found our services beneficial. Renewing your service will
            ensure you continue to receive uninterrupted access to the features and benefits you’ve come to rely on.</p>

        <p>To renew your service, simply <a href="#" class="button">Click here to Renew</a></p>

        <p>If you have any questions or need assistance with the renewal process, our support team is available from
            10:00 AM to 06:00 PM IST. You can reach us at <a
                href="mailto:support@helptogethergroup.com">support@helptogethergroup.com</a> or <a
                href="tel:+919634644622">+91 96346 44622</a>.</p>

        <p>Thank you for your continued trust in Help Together Group. We look forward to serving you in the future.</p>

        <div class="contact-info">
            <strong>Important Contacts:</strong>
            <p>For Complaints / Support: <a href="tel:+919634644622">(+91) 96346 44622</a> | <a
                    href="mailto:support@helptogether.co.in">support@helptogether.co.in</a></p>
        </div>

        <div class="footer">
            <p>&copy; 2024 Help Together Group. All rights reserved.</p>
        </div>
    </div>

</body>

</html>
