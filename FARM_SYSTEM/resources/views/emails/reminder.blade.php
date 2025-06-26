<!DOCTYPE html>
<html>
<head>
    <title>Document Submission Reminder</title>
</head>
<body>
    <p>Dear {{ $faculty->first_name }},</p>
    <p>This is a friendly reminder to submit your required documents to the system.</p>
    <p>Current submission progress: {{ number_format($progress, 2) }}%</p>
    <p>Please log in to the system and complete your submissions as soon as possible.</p>
    
    <p>You can easily upload your documents by clicking the link below:</p>
    <p><a href="https://pupt-farm.com/accomplishment/classroom-management">Upload Documents</a></p>

    <br>
    <p>Best regards,<br>{{ $sender->first_name }} {{ $sender->surname }}</p>
</body>
</html>
