<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Check-in</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 60px;
        }
        .success { color: green; font-size: 22px; }
        .warning { color: orange; font-size: 22px; }
        .error { color: red; font-size: 22px; }
    </style>
</head>
<body>
    <h1 class="{{ $status }}">{{ $message }}</h1>
    <p>Please show this screen to the convocation staff.</p>
</body>
</html>
