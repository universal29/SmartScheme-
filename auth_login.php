<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loginId = trim($_POST['login_id']); // Email or Mobile
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // Remember Me
    $loginRole = $_POST['login_role'] ?? 'user'; // Selected toggle role

    if (empty($loginId) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id, password_hash, role FROM users WHERE email = ? OR mobile = ?");
        $stmt->execute([$loginId, $loginId]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Check if actual account role matches the selected toggle
            if ($user['role'] !== $loginRole) {
                if ($loginRole === 'admin') {
                    $error = "Access Denied: Your account does not have administrative privileges.";
                } else {
                    $error = "Access Denied: Admin accounts must authenticate via the Administrator portal.";
                }
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
            
            if ($remember) {
                // In production, set a secure persistent cookie token here.
                setcookie('remember_me', $user['id'], time() + (86400 * 30), "/"); 
            }

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            }
        } else {
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Premium SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Premium Reset and Base Styles */
        :root {
            --brand-primary: #4f46e5;
            --brand-secondary: #0ea5e9;
            --surface-color: rgba(255, 255, 255, 0.95);
            --text-dark: #0f172a;
            --text-gray: #475569;
            --input-border: #cbd5e1;
            --input-focus: #6366f1;
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            overflow: hidden;
            position: relative;
        }

        /* Abstract Magic Background */
        .magic-bg {
            position: absolute;
            width: 100vw;
            height: 100vh;
            top: 0; left: 0;
            overflow: hidden;
            z-index: 0;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 15s infinite ease-in-out alternate;
        }
        .orb-1 { width: 500px; height: 500px; background: rgba(79, 70, 229, 0.6); top: -100px; left: -100px; }
        .orb-2 { width: 600px; height: 600px; background: rgba(14, 165, 233, 0.5); bottom: -200px; right: -100px; animation-delay: -5s; }
        .orb-3 { width: 400px; height: 400px; background: rgba(236, 72, 153, 0.4); top: 40%; left: 40%; animation-delay: -10s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -50px) scale(1.1); }
            100% { transform: translate(-30px, 30px) scale(0.9); }
        }

        /* Container Grid */
        .onboarding-grid {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1200px;
            min-height: 700px;
            margin: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-radius: 30px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        /* Form Panel (Left this time for variation) */
        .form-panel {
            background: var(--surface-color);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-header {
            margin-bottom: 2.5rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .form-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        .form-subtitle {
            color: var(--text-gray);
            font-size: 1.05rem;
        }

        /* Branding Panel (Right this time) */
        .character-panel {
            background: radial-gradient(circle at top left, rgba(14, 165, 233, 0.8), rgba(30, 27, 75, 0.9));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            text-align: center;
            position: relative;
            color: white;
            overflow: hidden;
        }

        /* Cool Character Animation */
        .assistant-character {
            width: 320px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));
            animation: hover-character 4s infinite ease-in-out;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        @keyframes hover-character {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-2deg); }
        }

        .character-glow {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%);
            z-index: 1;
            animation: pulse-glow 3s infinite alternate;
        }

        @keyframes pulse-glow {
            from { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1.5); }
        }

        .welcome-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0 0 1rem 0;
            letter-spacing: -1px;
            background: linear-gradient(to right, #fff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            margin: 0;
            max-width: 350px;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease-out;
        }
        .alert-error { background: #fee2e2; color: var(--error-color); border: 1px solid #fecaca; }

        /* Role Toggle Switch */
        .toggle-container {
            background: rgba(226, 232, 240, 0.7);
            border-radius: 50px;
            display: flex;
            padding: 5px;
            margin-bottom: 2rem;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: 0.1s;
            opacity: 0;
        }

        .toggle-btn {
            flex: 1;
            text-align: center;
            padding: 0.75rem;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            color: var(--text-gray);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .toggle-btn.active {
            background: #ffffff;
            color: var(--brand-primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transform: scale(1.02);
        }

        /* Form Inputs */
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            margin-bottom: 1.5rem;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .input-group:nth-of-type(1) { animation-delay: 0.2s; }
        .input-group:nth-of-type(2) { animation-delay: 0.3s; }

        .input-group label {
            color: var(--text-dark);
            font-size: 0.9rem;
            font-weight: 700;
            transition: color 0.3s ease;
        }
        
        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-group input {
            width: 100%;
            padding: 1rem 1.25rem;
            background: #f8fafc;
            border: 2px solid transparent;
            border-radius: 14px;
            font-size: 1rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
            box-sizing: border-box;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            padding-left: 3rem; /* Space for icon */
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .input-border {
            position: absolute;
            bottom: 0; left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 0 0 14px 14px;
            pointer-events: none;
        }

        .input-group input:focus {
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.15);
        }
        .input-group input:focus + .input-icon { color: var(--brand-primary); }
        .input-group input:focus ~ .input-border { transform: scaleX(1); }
        .input-group input:focus ~ label { color: var(--brand-primary); }

        .forgot-link {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 0.85rem;
            color: var(--brand-secondary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--brand-primary); text-decoration: underline; }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: var(--text-gray);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2rem;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: 0.4s;
            opacity: 0;
        }

        /* Animated Button */
        .btn-submit {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: 0.5s;
            opacity: 0;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px rgba(99, 102, 241, 0.6);
        }
        .btn-submit:hover::before { left: 100%; }

        .signup-link {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.95rem;
            color: var(--text-gray);
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: 0.6s;
            opacity: 0;
        }
        .signup-link a {
            color: var(--brand-primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }
        .signup-link a:hover { color: var(--brand-secondary); }

        /* Keyframes */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 950px) {
            .onboarding-grid { grid-template-columns: 1fr; margin: 1rem; min-height: auto; }
            .form-panel { padding: 2.5rem 2rem; order: 2; border-radius: 0 0 30px 30px;}
            .character-panel { padding: 3rem 2rem; order: 1; border-radius: 30px 30px 0 0; }
            .assistant-character { width: 180px; margin-bottom: 1rem; }
            .welcome-text h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <div class="magic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="onboarding-grid">
        
        <!-- Form Panel (Left) -->
        <div class="form-panel">
            <div class="form-header">
                <h2 class="form-title">Welcome Back</h2>
                <p class="form-subtitle">Log in to resume your journey with Sahay.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="toggle-container">
                <div class="toggle-btn active" id="btnRoleUser" onclick="setRole('user')">
                    <i class="fa-solid fa-user"></i> Citizen
                </div>
                <div class="toggle-btn" id="btnRoleAdmin" onclick="setRole('admin')">
                    <i class="fa-solid fa-shield-halved"></i> Admin
                </div>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="login_role" id="login_role" value="user">
                
                <div class="input-group">
                    <label for="login_input">Email or Mobile Number</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope input-icon"></i>
                        <input type="text" id="login_input" name="login_id" placeholder="you@example.com" required value="<?= isset($_POST['login_id']) ? htmlspecialchars($_POST['login_id']) : '' ?>">
                        <div class="input-border"></div>
                    </div>
                </div>
                
                <div class="input-group">
                    <a href="auth_forgot_password.php" class="forgot-link">Forgot password?</a>
                    <label for="password">Secure Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <div class="input-border"></div>
                    </div>
                </div>
                
                <label class="remember-checkbox">
                    <input type="checkbox" name="remember" value="1" style="accent-color: var(--brand-primary); width: 1.1rem; height: 1.1rem;">
                    Keep me authenticated locally
                </label>

                <button type="submit" class="btn-submit">
                    <span>Unlock Account</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
                
                <div class="signup-link">
                    Don't have an account? <a href="auth_register.php">Sign up magically</a>
                </div>
            </form>
        </div>

        <!-- Branding Panel (Right) -->
        <div class="character-panel">
            <div class="character-glow"></div>
            <!-- Using the same AI generated assistant image -->
            <img src="assets/img/assistant.png" alt="Smart Assistant" class="assistant-character" onerror="this.src=''; this.alt='Smart Assistant UI Loading';">
            <div class="welcome-text">
                <h1>Hello again!</h1>
                <p>I'm Sahay, ready to match you with new government schemes today.</p>
            </div>
        </div>

    </div>

    <script>
        function setRole(role) {
            document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('login_role').value = role;
            
            const inputField = document.getElementById('login_input');
            const icon = inputField.previousElementSibling;

            if(role === 'user') {
                document.getElementById('btnRoleUser').classList.add('active');
                inputField.placeholder = 'Email or Mobile Number';
                icon.className = 'fa-solid fa-envelope input-icon';
            } else {
                document.getElementById('btnRoleAdmin').classList.add('active');
                inputField.placeholder = 'admin@smartscheme.gov';
                icon.className = 'fa-solid fa-user-shield input-icon';
            }
        }
    </script>
</body>
</html>
