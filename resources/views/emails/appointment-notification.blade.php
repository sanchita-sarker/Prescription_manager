<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .appointment-details {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #4a5568;
        }
        .detail-value {
            color: #1a202c;
        }
        .message {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
        }
        .message.created {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.reminder {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .message.deleted {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📅 Prescription Manager</h1>
        </div>

        @if($type === 'created')
            <div class="message created">
                <strong>✅ New Appointment Scheduled</strong><br>
                Your appointment has been successfully scheduled. Here are the details:
            </div>
        @elseif($type === 'reminder')
            <div class="message reminder">
                <strong>⏰ Appointment Reminder</strong><br>
                This is a friendly reminder about your upcoming appointment:
            </div>
        @elseif($type === 'deleted')
            <div class="message deleted">
                <strong>❌ Appointment Cancelled</strong><br>
                The following appointment has been cancelled:
            </div>
        @endif

        <div class="appointment-details">
            <div class="detail-row">
                <span class="detail-label">Doctor:</span>
                <span class="detail-value">{{ $appointment->doctor_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('g:i A') }}</span>
            </div>
            @if($appointment->location)
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">{{ $appointment->location }}</span>
            </div>
            @endif
            @if($appointment->notes)
            <div class="detail-row">
                <span class="detail-label">Notes:</span>
                <span class="detail-value">{{ $appointment->notes }}</span>
            </div>
            @endif
        </div>

        @if($type === 'reminder')
            <p>Please make sure to arrive 15 minutes before your scheduled appointment time.</p>
        @elseif($type === 'created')
            <p>If you need to reschedule or cancel this appointment, please log in to your account or contact the doctor's office directly.</p>
        @endif

        <div class="footer">
            <p>This email was sent from Prescription Manager</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>