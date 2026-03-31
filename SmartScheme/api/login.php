<?php
// api/login.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $login_role = sanitize($_POST['login_role'] ?? 'user');

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Fields required.']);
        exit;
    }

    // Rate limiting check
    $stmt = $pdo->prepare("SELECT * FROM login_attempts WHERE email_or_mobile = ?");
    $stmt->execute([$email]);
    $attemptRecord = $stmt->fetch();

    if ($attemptRecord && $attemptRecord['attempts'] >= 5) {
        $lockout_time = strtotime($attemptRecord['last_attempt']) + (30 * 60); // 30 mins
        if (time() < $lockout_time) {
            echo json_encode(['status' => 'error', 'message' => 'Account locked due to multiple failed attempts. Try again in 30 minutes.']);
            exit;
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Enforce Role Selection
        $actual_role = $user['role'] ?? 'user';
        if ($actual_role !== $login_role) {
            echo json_encode(['status' => 'error', 'message' => "You do not have permission to log in as a $login_role."]);
            exit;
        }

        if ($user['is_verified'] == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Account not verified.']);
            exit;
        }

        // Reset login attempts
        if ($attemptRecord) {
            $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email_or_mobile = ?");
            $stmt->execute([$email]);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $actual_role;
        // Set session timeout timestamp
        $_SESSION['last_activity'] = time();
        
        echo json_encode(['status' => 'success', 'role' => $_SESSION['role']]);
    } else {
        if ($attemptRecord) {
            $attempts = $attemptRecord['attempts'] + 1;
            $stmt = $pdo->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = NOW() WHERE email_or_mobile = ?");
            $stmt->execute([$attempts, $email]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO login_attempts (email_or_mobile, attempts, last_attempt) VALUES (?, 1, NOW())");
            $stmt->execute([$email]);
        }
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }
}
?>
