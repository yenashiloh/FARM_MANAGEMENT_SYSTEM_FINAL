<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->subject }}</title>
</head>
<body>
    <div class="email-container">
        <div class="email-body">
            <p>{!! $announcement->message !!}</p>
        </div>
        
    </div>
</body>
</html>
