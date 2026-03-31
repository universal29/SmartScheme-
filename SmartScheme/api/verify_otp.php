<?php
// api/verify_otp.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $otpCode = sanitize($_POST['otp_code'] ?? '');

    if (!$userId || empty($otpCode)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM user_otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $otpRecord = $stmt->fetch();

    if (!$otpRecord) {
        echo json_encode(['status' => 'error', 'message' => 'No OTP found.']);
        exit;
    }

    if ($otpRecord['attempts'] >= 3) {
        echo json_encode(['status' => 'error', 'message' => 'Maximum attempts reached.']);
        exit;
    }

    if (strtotime($otpRecord['expires_at']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'OTP has expired.']);
        exit;
    }

    if ($otpRecord['otp_code'] === $otpCode) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            $stmt->execute([$userId]);

            $stmt = $pdo->prepare("DELETE FROM user_otps WHERE user_id = ?");
            $stmt->execute([$userId]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Verification successful.']);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'System error.']);
        }
    } else {
        $attempts = $otpRecord['attempts'] + 1;
        $stmt = $pdo->prepare("UPDATE user_otps SET attempts = ? WHERE id = ?");
        $stmt->execute([$attempts, $otpRecord['id']]);
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method.']);
}
?>
