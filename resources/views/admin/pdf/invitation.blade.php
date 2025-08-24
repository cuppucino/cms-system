<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Convocation Invitation</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            margin: 40px;
            line-height: 1.6;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .details {
            margin-bottom: 25px;
        }
        .details p {
            margin: 6px 0;
        }
        .highlight {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            border-top: 1px solid #999;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>University Convocation Invitation</h1>
        <p><em>Congratulations on your achievement!</em></p>
    </div>

    <div class="details">
        <p>Dear <span class="highlight">{{ $user->name }}</span>,</p>

        <p>We are pleased to invite you to attend the upcoming convocation ceremony. Please find the details below:</p>

        <p><span class="highlight">Student ID:</span> {{ $user->student_id }}</p>
        <p><span class="highlight">Course:</span> {{ $user->course->name ?? 'N/A' }}</p>
        <p><span class="highlight">Session:</span> {{ $user->session->name ?? 'N/A' }}</p>
        <p><span class="highlight">Date:</span> {{ $user->session->date ?? 'TBA' }}</p>
        <p><span class="highlight">Location:</span> {{ $user->session->location ?? 'TBA' }}</p>
    </div>

    <p>
        Kindly arrive at least <strong>30 minutes earlier</strong> to complete registration and gown collection procedures.
    </p>

    <p>
        Please bring this invitation letter as proof of your eligibility to attend the ceremony.
    </p>

    <div class="footer">
        <p>© {{ date('Y') }} University Convocation Committee</p>
    </div>
</body>
</html>
