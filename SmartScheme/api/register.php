<?php
// api/register.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $mobile = sanitize($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $state = sanitize($_POST['state'] ?? '');
    $role = sanitize($_POST['role'] ?? 'user');
    if ($role !== 'admin') $role = 'user';

    // Basic Validation
    if(empty($fullName) || empty($email) || empty($mobile) || empty($password) || empty($state)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if(!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid mobile format.']);
        exit;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Check Uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR mobile = ?");
    $stmt->execute([$email, $mobile]);
    if($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email or Mobile already registered.']);
        exit;
    }

    // Hash Password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $pdo->beginTransaction();
    try {
        // Insert User
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, mobile, password, role, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$fullName, $email, $mobile, $hashedPassword, $role]);
        $userId = $pdo->lastInsertId();

        // Initialize Profile with state
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, state) VALUES (?, ?)");
        $stmt->execute([$userId, $state]);

        // Generate OTP
        $otpCode = generateOtp();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+2 minutes'));
        
        $stmt = $pdo->prepare("INSERT INTO user_otps (user_id, otp_code, type, expires_at) VALUES (?, ?, 'email', ?)");
        $stmt->execute([$userId, $otpCode, $expiresAt]);

        $pdo->commit();
        echo json_encode([
            'status' => 'success', 
            'user_id' => $userId,
            'message' => 'OTP sent to email.',
            'mock_otp' => $otpCode // Sending back for demo purposes
        ]);
    } catch (\Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method.']);
}
?>
