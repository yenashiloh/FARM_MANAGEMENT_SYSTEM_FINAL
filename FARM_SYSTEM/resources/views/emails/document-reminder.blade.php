<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            line-height: 1.5;
            color: #6B7280;
            background-color: #EDF2F7 !important; 
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff; 
        }

        .logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .content {
            background-color: #ffffff; 
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .greeting {
            font-size: 18px;
            color: #374151;
            margin-bottom: 20px;
        }

        .message {
            color: #6B7280;
            margin-bottom: 16px;
        }

        .deadline {
            color: #374151;
            margin-bottom: 16px;
            font-weight: bold;
        }

        .documents-list {
            color: #6B7280;
            margin-bottom: 16px;
        }

        .button {
            display: inline-block;
            background-color: #374151;
            color: #ffffff !important;  
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin: 24px 0;
        }

        .footer {
            text-align: center;
            padding-top: 24px;
            font-size: 14px;
            color: #9CA3AF;
        }

        .help-text {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #E5E7EB;
            color: #6B7280;
            font-size: 14px;
        }

        .help-text a {
            color: #6B7280;
            text-decoration: underline;
        }

        .regards {
            margin-top: 24px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="greeting">
                Hi {{ $name }},
            </div>
            <div class="message">
                You have pending document uploads due:
            </div>
            <div class="deadline">
                Deadline: {{ $deadline }}
            </div>
            <div class="documents-list">
                Missing documents:
                <ul style="margin-top: 10px; list-style-type: disc; padding-left: 20px;">
                    @foreach($missingFolders as $folder)
                        <li>{{ $folder }}</li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ $uploadUrl }}" class="button">Upload Documents Now</a>
            <p class="message">
                Please ensure to upload all required documents before the deadline.
            </p>
            <p class="message">
                You will continue to receive daily reminders until all documents are uploaded.
            </p>
            <div class="regards">
                Regards,<br>
                PUP-T FARM
            </div>
            <div class="help-text">
                If you're having trouble clicking the "Upload Documents Now" button, copy and paste the URL below into your web browser: <a href="{{ $uploadUrl }}">{{ $uploadUrl }}</a>
            </div>
        </div>
        <div class="footer">
            © PUP-T FARM. All rights reserved.
        </div>
    </div>
</body>
</html>
