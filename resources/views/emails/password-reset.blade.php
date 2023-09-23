<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Code</title>
    <style>
        /* Add your custom CSS styles here */
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #eceff1;
            padding: 20px;
        }

        .header {
            background-color: #3498db;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 20px;
        }

        .code {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-size: 24px;
        }

        .message {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Password Reset Code</h1>
        </div>
        <div class="content">
            <p>Dear User,</p>
            <p>Use the verification code below to reset your password:</p>
            <div class="code">
                {{ $code }}
            </div>
            <p class="message">This code is valid for a limited time. Please do not share it with anyone.</p>
        </div>
    </div>
</body>
</html>
