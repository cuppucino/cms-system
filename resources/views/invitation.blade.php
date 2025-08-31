<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Convocation Invitation</title>
  <style>
    body {
      font-family: "DejaVu Sans", sans-serif;
      font-size: 13px;
      line-height: 1.5;
      color: #111827;
      margin: 20px 40px;
    }

    .header {
      text-align: center;
      margin-bottom: 18px;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .brand-badge {
      width: 36px; height: 36px;
      border-radius: 8px;
      background: #4f46e5;
      color: #fff;
      font-weight: bold;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .brand-title {
      font-size: 18px;
      font-weight: 700;
      color: #4f46e5;
      margin: 0;
    }
    .meta {
      margin-top: 4px;
      font-size: 11px;
      color: #6b7280;
    }

    .panel {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 14px 16px;
      margin-bottom: 14px;
    }
    .panel-title {
      font-size: 12px;
      text-transform: uppercase;
      font-weight: 600;
      color: #3730a3;
      margin: 0 0 8px;
    }

    .grid {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 6px 10px;
    }
    .label { color: #6b7280; }
    .value { font-weight: 600; }

    .note {
      margin-top: 10px;
      padding: 8px 10px;
      border-left: 4px solid #4f46e5;
      background: #f9fafb;
      font-size: 12px;
    }

    .qr {
      text-align: center;
      margin-top: 14px;
      padding: 10px;
      border: 1px dashed #e5e7eb;
      border-radius: 8px;
      background: #f5f3ff;
    }
    .qr img {
      width: 120px; height: 120px;
      margin: 6px auto;
    }
    .qr small { display: block; color: #6b7280; font-size: 11px; }

    .footer {
      margin-top: 20px;
      padding-top: 10px;
      font-size: 11px;
      color: #6b7280;
      text-align: center;
      border-top: 1px solid #e5e7eb;
    }

    /* Avoid breaking QR across pages */
    .no-break { page-break-inside: avoid; }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div class="brand">
      <div class="brand-badge">CMS</div>
      <h1 class="brand-title">Convocation Invitation</h1>
    </div>
    <div class="meta">
      Issued: {{ $invitation->created_at ?? now() }} &nbsp; | &nbsp;
      Status: {{ ($invitation->is_active ?? false) ? 'Active' : 'Revoked' }}
    </div>
  </div>

  <!-- Recipient -->
  <div class="panel">
    <h2 class="panel-title">Recipient</h2>
    <div class="grid">
      <div class="label">Name</div><div class="value">{{ $invitation->user->name ?? '—' }}</div>
      <div class="label">Student ID</div><div class="value">{{ $invitation->user->student_id ?? '—' }}</div>
      <div class="label">Course</div><div class="value">{{ $invitation->user->course->name ?? '—' }}</div>
    </div>
    <div class="note">Congratulations! Please bring this invitation and a valid ID on the ceremony day.</div>
  </div>

  <!-- Ceremony -->
  <div class="panel no-break">
    <h2 class="panel-title">Ceremony Details</h2>
    <div class="grid">
      <div class="label">Session</div><div class="value">{{ $invitation->session->name ?? 'N/A' }}</div>
      <div class="label">Date</div><div class="value">{{ $invitation->session->date ?? 'TBA' }}</div>
      <div class="label">Time</div><div class="value">{{ $invitation->session->time ?? 'TBA' }}</div>
      <div class="label">Location</div><div class="value">{{ $invitation->session->location ?? 'TBA' }}</div>
      <div class="label">Code</div><div class="value">{{ $invitation->code ?? '—' }}</div>
    </div>
    <div class="note">Arrive at least <strong>30 minutes earlier</strong> for registration and gown collection.</div>

    @if (!empty($qrCode))
      <div class="qr">
        <strong>Entry QR Code</strong>
        <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
        <small>Show this QR at check-in</small>
      </div>
    @endif
  </div>

  <!-- Footer -->
  <div class="footer">
    © {{ date('Y') }} University Convocation Committee • System-generated, valid without signature.
  </div>
</body>
</html>
