<?php
// api/reset_password.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $otpCode = sanitize($_POST['otp_code'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if (!$userId || empty($otpCode) || empty($newPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM user_otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$userId]);
    $otpRecord = $stmt->fetch();

    if (!$otpRecord || $otpRecord['otp_code'] !== $otpCode || strtotime($otpRecord['expires_at']) < time()) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired OTP.']);
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        $stmt = $pdo->prepare("DELETE FROM user_otps WHERE user_id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
    } catch (\Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'System error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method.']);
}
?>
