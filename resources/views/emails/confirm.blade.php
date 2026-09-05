<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Renewal Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333333;
        }
        p {
            color: #555555;
            line-height: 1.6;
        }
        .details {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        .footer {
            background-color: #f7f7f7;
            padding: 10px;
            border-top: 1px solid #dddddd;
            text-align: center;
        }
        .footer p {
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Service Renewal is Confirmed, {{ $data['contact'] }}!</h2>
        <p>Dear {{ $data['contact'] }},</p>
        <p>We are pleased to inform you that your Product/Service with Help Together Group has been successfully renewed.</p>
        <div class="details">
            @foreach ($productDetails as $product)
            <p><strong>Service Name:</strong> {{ $product['product_name'] }}</p>
            <p><strong>New Service Period:</strong> {{ $data['date'] }} to {{ $product['expiry_date'] }}</p>
            @endforeach
        </div>
        <p>Thank you for continuing to trust us with your business needs. We are committed to providing you with exceptional service and support throughout this new service period.</p>
        <p>If you have any questions or need assistance, our support team is available from 10:00am to 06:00pm IST. You can reach us at <a href="mailto:support@helptogethergroup.com">support@helptogethergroup.com</a> or call us at +91 9634644622.</p>
        <p>Thank you once again for your continued partnership. We look forward to serving you.</p>
        <p>Warm regards,</p>
        <p><strong>Support Team</strong><br>Help Together Group</p>
        <div class="footer">
            <p><strong>Important Contacts:</strong></p>
            <p>For Complaints / Support:<br>
               (+91) 96346 44622 | <a href="mailto:support@helptogether.co.in">support@helptogether.co.in</a></p>
        </div>
    </div>
</body>
</html>
