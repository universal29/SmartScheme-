<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Email is required.";
    } else {
        // In a real application, you would generate a unique token, save it to db with expiration,
        // and send an email to the user with the reset link.
        // For this implementation, we simulate the process.
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $message = "A password reset link has been simulated and sent to your email address.";
        } else {
            $error = "No user found with this email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SmartScheme Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Reset Password</h1>
            <p class="auth-subtitle">Enter your email to receive a reset link</p>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #ef4444; padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div style="background: #d1fae5; color: #047857; padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Link</button>
            </form>
            
            <p class="text-center mt-3" style="font-size: 0.9rem;">
                Remembered your password? <a href="auth_login.php">Log in</a>
            </p>
        </div>
    </div>
</body>
</html>
