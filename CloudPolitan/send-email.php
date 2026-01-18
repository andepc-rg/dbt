<?php
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Sanitize and validate form data
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''));
$lastName = htmlspecialchars(trim($_POST['lastName'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$company = htmlspecialchars(trim($_POST['company'] ?? ''));
$service = htmlspecialchars(trim($_POST['service'] ?? ''));
$subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message) || empty($service)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Recipient email address
$recipientEmail = 'veera@cloudpolitan.com';

// Email subject for admin
$emailSubject = "New Contact Form Submission from " . $firstName . " " . $lastName;

// Email body for admin
$emailBody = "<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background: linear-gradient(135deg, #0066cc 0%, #0052a3 50%, #00d4ff 100%); color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; }
        .field-label { font-weight: bold; color: #0066cc; margin-bottom: 5px; }
        .field-value { padding: 10px; background: #f8f9fa; border-left: 3px solid #00d4ff; padding-left: 15px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <div class='field'>
                <div class='field-label'>Name:</div>
                <div class='field-value'>" . $firstName . " " . $lastName . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Email:</div>
                <div class='field-value'><a href='mailto:" . $email . "'>" . $email . "</a></div>
            </div>
            <div class='field'>
                <div class='field-label'>Company:</div>
                <div class='field-value'>" . (!empty($company) ? $company : 'Not provided') . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Service Interest:</div>
                <div class='field-value'>" . ucfirst(str_replace('-', ' ', $service)) . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Subject:</div>
                <div class='field-value'>" . $subject . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Message:</div>
                <div class='field-value'>" . nl2br($message) . "</div>
            </div>
            <div class='footer'>
                <p>This is an automated email from your CloudPolitan contact form.</p>
            </div>
        </div>
    </div>
</body>
</html>";

// Set email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: " . $email . "\r\n";

// Send email to admin
$adminEmailSent = mail($recipientEmail, $emailSubject, $emailBody, $headers);

if (!$adminEmailSent) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try again.']);
    exit;
}

// Send confirmation email to client
$clientSubject = "We've Received Your Message - CloudPolitan";
$clientBody = "<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background: linear-gradient(135deg, #0066cc 0%, #0052a3 50%, #00d4ff 100%); color: white; padding: 20px; border-radius: 5px 5px 0 0; text-align: center; }
        .content { padding: 30px; }
        .message { color: #0066cc; font-size: 16px; line-height: 1.6; margin-bottom: 20px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999; text-align: center; }
        .contact-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Thank You for Contacting CloudPolitan</h2>
        </div>
        <div class='content'>
            <p class='message'>Dear " . $firstName . ",</p>
            <p class='message'>We have successfully received your message and appreciate you taking the time to reach out to us.</p>
            <p class='message'>Our team will review your inquiry and get back to you as soon as possible, typically within 24 hours.</p>
            
            <div class='contact-info'>
                <strong>In the meantime, feel free to reach us at:</strong><br>
                📧 Email: <a href='mailto:veera@cloudpolitan.com'>veera@cloudpolitan.com</a><br>
                📞 Phone: <a href='tel:+91-8790636868'>+91-8790636868</a><br>
                🕒 Hours: Monday-Friday, 9:00 AM - 6:00 PM PST
            </div>
            
            <p class='message'>Best regards,<br><strong>CloudPolitan Technologies Team</strong></p>
            <div class='footer'>
                <p>&copy; 2024 CloudPolitan Technologies. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>";

$clientHeaders = "MIME-Version: 1.0" . "\r\n";
$clientHeaders .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$clientHeaders .= "From: noreply@cloudpolitan.com" . "\r\n";

// Send confirmation to client (non-critical, don't fail if this doesn't send)
mail($email, $clientSubject, $clientBody, $clientHeaders);

// Success response
echo json_encode(['success' => true, 'message' => 'Email sent successfully']);
?>
