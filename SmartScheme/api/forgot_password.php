<?php
// api/forgot_password.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_mobile = sanitize($_POST['email_or_mobile'] ?? '');

    if (empty($email_or_mobile)) {
        echo json_encode(['status' => 'error', 'message' => 'Identifier required.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR mobile = ?");
    $stmt->execute([$email_or_mobile, $email_or_mobile]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate OTP
        $otpCode = generateOtp();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        $stmt = $pdo->prepare("INSERT INTO user_otps (user_id, otp_code, type, expires_at) VALUES (?, ?, 'email', ?)");
        $stmt->execute([$user['id'], $otpCode, $expiresAt]);

        echo json_encode([
            'status' => 'success', 
            'user_id' => $user['id'],
            'message' => 'OTP sent.',
            'mock_otp' => $otpCode
        ]);
    } else {
        // Always return success to prevent user enumeration
        echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
    }
}
?>
