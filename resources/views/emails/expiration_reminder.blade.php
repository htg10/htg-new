<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            color: #ffffff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .contact-info {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Your Service Plan Has Expired after 15 days, {{ $client->contact }}</h1>
        {{-- <p>Dear {{ $data['contact'] }},</p> --}}
        <p>We hope this message finds you well. We wanted to inform you that your {{ $client->product_name }} plan with
            Help
            Together Group has expired as of {{ $client->expiry_date }}.</p>

        <p><strong>Service Details:</strong></p>
        <p><strong>Service Name:</strong> {{ $client->product_name }}<br>
            <strong>Expiration Date:</strong> {{ $client->expiry_date }}
        </p>

        <p>We value your association with us and would love to continue providing you with our services. To avoid any
            disruption and resume your access to the benefits and features, we encourage you to renew your service plan.
        </p>

        <p><strong>Renewal Process:</strong><br>
            To renew your service,
        </p>

        <p>
            You can reach us at <a href="mailto:support@helptogethergroup.com">support@helptogethergroup.com</a> or call
            us at +91 9634644622.</p>

        <a href="[Renewal Link]" class="btn">Renew Your Service Now</a>

        <p class="footer">Thank you for your time and consideration. We hope to continue serving you.</p>

        <p class="footer">Best regards,<br>Support Team<br>Help Together Group</p>

        <p class="contact-info"><strong>Important Contacts:</strong><br>
            For Complaints / Support:<br>
            Phone: +91 96346 44622 | Email: <a href="mailto:support@helptogether.co.in">support@helptogether.co.in</a>
        </p>
    </div>
</body>

</html>
