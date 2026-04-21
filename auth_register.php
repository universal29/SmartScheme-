<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = $_POST['password'];
    $enteredOtp = $_POST['otp'];

    if (empty($fullName) || empty($email) || empty($mobile) || empty($password) || empty($enteredOtp)) {
        $error = "All fields and OTP are required.";
    } elseif (!isset($_SESSION['signup_otp']) || $_SESSION['signup_otp'] != $enteredOtp || $_SESSION['signup_otp_email'] !== $email) {
        $error = "Invalid or expired OTP. Please try again.";
    } else {
        // Double check existence just in case
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR mobile = ?");
        $stmt->execute([$email, $mobile]);
        if ($stmt->rowCount() > 0) {
            $error = "Email or Mobile already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, mobile, password_hash) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$fullName, $email, $mobile, $hashedPassword])) {
                // Clear OTP session
                unset($_SESSION['signup_otp']);
                unset($_SESSION['signup_otp_email']);
                
                // Auto login after registration
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['role'] = 'user';
                header("Location: profile.php?msg=welcome");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Premium SmartScheme</title>
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

        /* Branding Panel (Left) */
        .character-panel {
            background: radial-gradient(circle at top right, rgba(79, 70, 229, 0.8), rgba(30, 27, 75, 0.9));
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
            50% { transform: translateY(-15px) rotate(2deg); }
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
            background: linear-gradient(to right, #fff, #a5b4fc);
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

        /* Form Panel (Right) */
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

        /* Standard Form Inputs - Cool Line Drawing Animation */
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
        
        .input-group:nth-child(1) { animation-delay: 0.1s; }
        .input-group:nth-child(2) { animation-delay: 0.2s; }
        .input-group:nth-child(3) { animation-delay: 0.3s; }
        .input-group:nth-child(4) { animation-delay: 0.4s; }

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
        }

        /* Cool animated border effect */
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
        .input-group input:focus + .input-border {
            transform: scaleX(1);
        }
        .input-group input:focus ~ label {
            color: var(--brand-primary);
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
        .btn-submit:hover::before {
            left: 100%;
        }
        .btn-submit:active { transform: translateY(0); }

        /* Steps handling */
        #step1, #step2 { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        .hidden-step { display: none; opacity: 0; transform: scale(0.95); }
        .active-step { display: block; opacity: 1; transform: scale(1); animation: popIn 0.5s cubic-bezier(0.16, 1, 0.3, 1); }

        /* OTP Input styling */
        .otp-display {
            background: #eff6ff;
            border: 1px dashed #3b82f6;
            color: #1d4ed8;
            padding: 1rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: none;
        }
        .otp-inputs {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            justify-content: center;
        }
        .otp-inputlet {
            width: 3.5rem;
            height: 4rem;
            text-align: center;
            font-size: 1.8rem;
            font-weight: 800;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            outline: none;
            transition: all 0.3s;
            color: var(--brand-primary);
        }
        .otp-inputlet:focus {
            border-color: var(--brand-primary);
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(99, 102, 241, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.95rem;
            color: var(--text-gray);
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: 0.6s;
            opacity: 0;
        }
        .login-link a {
            color: var(--brand-primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }
        .login-link a:hover { color: var(--brand-secondary); }

        /* Back button */
        .btn-back {
            background: #f1f5f9;
            border: none;
            color: var(--text-gray);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-bottom: 2rem;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #e2e8f0; color: var(--text-dark); transform: translateX(-3px); }

        /* Keyframes */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loader */
        .loader {
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            display: none;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        @media (max-width: 950px) {
            .onboarding-grid { grid-template-columns: 1fr; margin: 1rem; min-height: auto; }
            .character-panel { padding: 3rem 2rem; }
            .form-panel { padding: 2.5rem 2rem; }
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
        
        <!-- Left Panel - Character Branding -->
        <div class="character-panel">
            <div class="character-glow"></div>
            <!-- The amazing AI generated 3D assistant image -->
            <img src="assets/img/assistant.png" alt="Smart Assistant" class="assistant-character" onerror="this.src=''; this.alt='Smart Assistant UI Loading';">
            <div class="welcome-text">
                <h1>Meet Sahay.</h1>
                <p>Your AI-driven guide to discovering and applying for smart government schemes instantly.</p>
            </div>
        </div>

        <!-- Right Panel - Animated Forms -->
        <div class="form-panel">
            
            <form id="registerForm" method="POST" action="">
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Step 1: User Details -->
                <div id="step1" class="<?= empty($error) || !isset($_POST['otp']) ? 'active-step' : 'hidden-step' ?>">
                    <div class="form-header">
                        <h2 class="form-title">Registration</h2>
                        <p class="form-subtitle">Let's set up your premium citizen account</p>
                    </div>

                    <div class="input-group">
                        <label for="full_name">Full Legal Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="full_name" name="full_name" placeholder="e.g. John Doe" required value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="mobile">Mobile Number</label>
                        <div class="input-wrapper">
                            <input type="tel" id="mobile" name="mobile" placeholder="Your 10-digit number" required value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>">
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Secure Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Min. 6 characters" required minlength="6">
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <button type="button" class="btn-submit" id="btnContinue">
                        <span>Authenticate Details</span>
                        <i class="fa-solid fa-arrow-right"></i>
                        <div class="loader" id="continueLoader"></div>
                    </button>
                    
                    <p class="login-link">
                        Already have an account? <a href="auth_login.php">Log in securely</a>
                    </p>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="<?= isset($_POST['otp']) && !empty($error) ? 'active-step' : 'hidden-step' ?>">
                    <button type="button" class="btn-back" id="btnBack">
                        <i class="fa-solid fa-arrow-left-long"></i> Back
                    </button>

                    <div class="form-header">
                        <h2 class="form-title">Verify Identity</h2>
                        <p class="form-subtitle" id="otpSubtitle">We've sent a secure 6-digit code.</p>
                    </div>

                    <div class="alert alert-error" id="otpErrorAlert" style="display:none;">
                        <i class="fa-solid fa-circle-exclamation"></i> <span id="otpErrorText"></span>
                    </div>

                    <!-- Hidden actual OTP input -->
                    <input type="hidden" name="otp" id="finalOtp" value="">
                    
                    <!-- Demo OTP Display -->
                    <div class="otp-display" id="demoOtpDisplay">
                        Demo OTP Code: <strong id="demoOtpText"></strong>
                    </div>

                    <div class="otp-inputs" id="otpInputsContainer">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-inputlet" maxlength="1" pattern="\d*">
                    </div>

                    <button type="submit" class="btn-submit" id="btnRegister">
                        <span>Confirm & Create Account</span>
                        <i class="fa-solid fa-shield-check"></i>
                    </button>

                    <p class="login-link" style="opacity: 1; animation: none;">
                        Didn't receive the code? <a href="#" id="btnResend" style="color: var(--text-gray);">Resend in <span id="resendTimer">30</span>s</a>
                    </p>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const btnContinue = document.getElementById('btnContinue');
            const btnBack = document.getElementById('btnBack');
            const btnRegister = document.getElementById('btnRegister');
            const continueLoader = document.getElementById('continueLoader');
            
            // Step 1 Inputs
            const inName = document.getElementById('full_name');
            const inEmail = document.getElementById('email');
            const inMobile = document.getElementById('mobile');
            const inPassword = document.getElementById('password');

            const alertContainer = document.getElementById('otpErrorAlert');
            const alertText = document.getElementById('otpErrorText');
            const demoDisplay = document.getElementById('demoOtpDisplay');
            const demoText = document.getElementById('demoOtpText');

            // OTP Input UI Logic
            const otpInputs = document.querySelectorAll('.otp-inputlet');
            const finalOtp = document.getElementById('finalOtp');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const val = e.target.value.replace(/\D/g, ''); // keep only digits
                    if (val.length > 1) {
                        e.target.value = val.slice(0, 1);
                    } else {
                        e.target.value = val;
                    }

                    if (e.target.value !== '' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateFinalOtp();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
                
                // Allow paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').trim();
                    if (text.length >= 6) {
                        for (let i = 0; i < 6; i++) {
                            otpInputs[i].value = text[i];
                        }
                        otpInputs[5].focus();
                        updateFinalOtp();
                    }
                });
            });

            function updateFinalOtp() {
                let code = '';
                otpInputs.forEach(i => code += i.value);
                finalOtp.value = code;
            }

            // Navigation
            btnContinue.addEventListener('click', async () => {
                // Basic Validation
                if (!inName.value.trim() || !inEmail.value.trim() || !inMobile.value.trim() || !inPassword.value) {
                    alert('Please fill out all mandatory fields.');
                    return;
                }
                if (inPassword.value.length < 6) {
                    alert('Security Requirement: Password must be at least 6 characters.');
                    return;
                }
                
                // Show loader
                const btnText = btnContinue.querySelector('span');
                const btnIcon = btnContinue.querySelector('i');
                btnText.style.display = 'none';
                btnIcon.style.display = 'none';
                continueLoader.style.display = 'block';
                btnContinue.disabled = true;

                // Call API to send OTP
                try {
                    const response = await fetch('api/send_otp.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            email: inEmail.value,
                            mobile: inMobile.value
                        })
                    });
                    const res = await response.json();
                    
                    if (res.success) {
                        // Switch panel with animation
                        step1.classList.remove('active-step');
                        step1.classList.add('hidden-step');
                        setTimeout(() => {
                            step2.classList.remove('hidden-step');
                            step2.classList.add('active-step');
                            otpInputs[0].focus();
                        }, 500); // Wait for fade out
                        
                        document.getElementById('otpSubtitle').textContent = `We sent a code to ${inEmail.value}`;
                        alertContainer.style.display = 'none';
                        
                        // Show Demos OTP
                        if (res.demo_otp) {
                            demoDisplay.style.display = 'block';
                            demoText.textContent = res.demo_otp;
                        }

                        startResendTimer();
                    } else {
                        // Show error
                        const existingError = document.querySelector('.alert-error:not(#otpErrorAlert)');
                        if (existingError) existingError.remove();
                        
                        const errHTML = `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <span>${res.message}</span></div>`;
                        step1.insertAdjacentHTML('afterbegin', errHTML);
                    }
                } catch (err) {
                    alert('A network error occurred. Please try again.');
                }

                // Restore button
                btnText.style.display = 'inline-block';
                btnIcon.style.display = 'inline-block';
                continueLoader.style.display = 'none';
                btnContinue.disabled = false;
            });

            btnBack.addEventListener('click', () => {
                step2.classList.remove('active-step');
                step2.classList.add('hidden-step');
                setTimeout(() => {
                    step1.classList.remove('hidden-step');
                    step1.classList.add('active-step');
                }, 400);
                // Clear OTP
                otpInputs.forEach(i => i.value = '');
                updateFinalOtp();
            });

            // Prevent submit if OTP isn't 6 digits
            document.getElementById('registerForm').addEventListener('submit', (e) => {
                if (step2.classList.contains('active-step')) {
                    if (finalOtp.value.length !== 6) {
                        e.preventDefault();
                        alertContainer.style.display = 'flex';
                        alertText.textContent = 'Please enter the fully 6-digit secure OTP.';
                    }
                }
            });

            // Resend Timer logic
            let timerInterval;
            function startResendTimer() {
                clearInterval(timerInterval);
                let timeLeft = 30;
                const link = document.getElementById('btnResend');
                const span = document.getElementById('resendTimer');
                
                link.style.pointerEvents = 'none';
                link.style.textDecoration = 'none';
                link.innerHTML = `Resend in <span id="resendTimer">${timeLeft}</span>s`;
                
                timerInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById('resendTimer').textContent = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        link.style.pointerEvents = 'auto';
                        link.style.color = 'var(--brand-primary)';
                        link.innerHTML = `Resend OTP`;
                        link.style.textDecoration = 'underline';
                    }
                }, 1000);
            }

            document.getElementById('btnResend').addEventListener('click', (e) => {
                e.preventDefault();
                if (e.target.style.pointerEvents === 'none') return;
                btnContinue.click(); 
            });

            // If PHP error after submit, ensure we show step 2 with the error
            <?php if (!empty($error) && isset($_POST['otp'])): ?>
                const errDiv = document.querySelector('.alert-error:not(#otpErrorAlert)');
                if (errDiv) errDiv.style.display = 'none';
                alertContainer.style.display = 'flex';
                alertText.textContent = "<?= htmlspecialchars(addslashes($error)) ?>";
            <?php endif; ?>
        });
    </script>
</body>
</html>
