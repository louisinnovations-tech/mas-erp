<!DOCTYPE html>
<html>

<head>
    <title>OTP Code</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        body,
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            padding: 0;
            font-family: "DM Sans", sans-serif;
        }

        body {
            background-color: #f4f4f7;
            color: #333333;
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #800000;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .content p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #800000;
            margin: 20px 0;
        }

        .footer {
            background-color: #f4f4f7;
            color: #777777;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }

        .footer a {
            color: #800000;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>MAS ERP</h1>
            <p>Marshela Alsuti Law Firm</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Hello,</p>
            <p>To complete your login, please use the following OTP code:</p>
            <div class="otp-code">1234567</div>
            <p>
                This code is valid for the next 10 minutes. Do not share it with
                anyone.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing MAS ERP.</p>
            <p>
                Visit our website:
                <a href="https://mas.com.qa" target="_blank">mas.com.qa</a>
            </p>
        </div>
    </div>
</body>

</html>
