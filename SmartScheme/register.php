<?php
require_once 'includes/functions.php';
if(isLoggedIn()) redirect('dashboard.php');
require_once 'includes/header.php';
?>
<style>
/* Scoped Animation & Layout for Auth Pages */
.auth-split-container {
    display: flex;
    min-height: 85vh;
    background: white;
    margin: 2rem auto;
    max-width: 1100px;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    animation: fadeIn 0.8s ease-out;
}

.auth-left {
    flex: 1;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 4rem 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.auth-left::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: rotateBg 30s linear infinite;
}

@keyframes rotateBg {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.auth-right {
    flex: 1.2;
    padding: 4rem;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.auth-feature {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateX(-40px);
}

.auth-feature:nth-child(1) { animation: slideRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.3s; }
.auth-feature:nth-child(2) { animation: slideRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.5s; }
.auth-feature:nth-child(3) { animation: slideRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.7s; }

@keyframes slideRight {
    to { opacity: 1; transform: translateX(0); }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.input-anim {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #e5e7eb;
    background: #f9fafb;
}
.input-anim:focus {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
    border-color: var(--primary-color);
    background: white;
}

@media (max-width: 900px) {
    .auth-split-container { flex-direction: column; }
    .auth-left { padding: 3rem 2rem; }
    .auth-right { padding: 2rem; }
}
</style>

<div class="container">
    <div class="auth-split-container">
        <!-- Left Side: Splash & Value Prop -->
        <div class="auth-left">
            <h1 class="fw-bold mb-4" style="font-size: 2.8rem; z-index:1; line-height: 1.2;">Unlock Your Government Benefits</h1>
            <p class="mb-5" style="font-size: 1.15rem; opacity: 0.9; z-index:1;">Join the SmartScheme platform today and let our AI instantly match you with federal and state programs you entirely qualify for.</p>
            
            <div style="z-index:1;">
                <div class="auth-feature">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding: 1.2rem; border-radius: 16px; font-size: 1.5rem; display:flex; align-items:center; justify-content:center;">🤖</div>
                    <div>
                        <h4 style="margin:0; font-size: 1.15rem;">AI-Powered Matching</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.95rem;">Algorithmic eligibility automation.</p>
                    </div>
                </div>
                <div class="auth-feature">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding: 1.2rem; border-radius: 16px; font-size: 1.5rem; display:flex; align-items:center; justify-content:center;">🔒</div>
                    <div>
                        <h4 style="margin:0; font-size: 1.15rem;">Secure & Encrypted</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.95rem;">Your data never leaves the platform.</p>
                    </div>
                </div>
                <div class="auth-feature">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding: 1.2rem; border-radius: 16px; font-size: 1.5rem; display:flex; align-items:center; justify-content:center;">⚡</div>
                    <div>
                        <h4 style="margin:0; font-size: 1.15rem;">Real-time Indexing</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.95rem;">Get notified as new schemes launch.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: The Form -->
        <div class="auth-right">
            <h2 class="text-primary mb-1">Create an Account</h2>
            <p class="text-muted mb-4">It takes less than 60 seconds.</p>
            
            <form id="registerForm" action="api/register.php" method="POST">
                <div id="error-message" class="alert-error" style="display:none; animation: fadeIn 0.3s ease;"></div>
                
                <div class="form-group mb-4">
                    <div style="display:flex; gap:8px; background: #f3f4f6; padding: 6px; border-radius: 50px;">
                        <label style="flex:1; cursor:pointer; margin:0;" class="text-center">
                            <input type="radio" name="role" value="user" checked style="display:none;" id="roleUser">
                            <div class="role-btn" style="padding: 0.6rem; border-radius: 50px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); font-weight:600; color:var(--primary-color); transition:0.3s;" id="labelUser">Citizen</div>
                        </label>
                        <label style="flex:1; cursor:pointer; margin:0;" class="text-center">
                            <input type="radio" name="role" value="admin" style="display:none;" id="roleAdmin">
                            <div class="role-btn" style="padding: 0.6rem; border-radius: 50px; font-weight:600; color:#6b7280; transition:0.3s;" id="labelAdmin">Administrator</div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="input-anim" required placeholder="John Doe">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="input-anim" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" pattern="[0-9]{10}" class="input-anim" required placeholder="9876543210">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Secure Password</label>
                        <input type="password" name="password" id="password" class="input-anim" required 
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,}"
                               title="Must contain at least one number and one uppercase and lowercase letter, one special character, and at least 8 or more characters"
                               placeholder="Min 8 chars, 1 Upper, 1 Special">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="input-anim" required placeholder="••••••••">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>State of Residence</label>
                    <select name="state" required class="input-anim">
                        <option value="">Select Region...</option>
                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                        <option value="Assam">Assam</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Gujarat">Gujarat</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Tamil Nadu">Tamil Nadu</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary w-100 mt-3" style="font-size:1.1rem; padding: 1rem; box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);">Secure Registration &rarr;</button>
            </form>

            <div id="otpSection" style="display:none; animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">💌</div>
                    <h3 class="text-primary">Verify Your Identity</h3>
                    <p class="text-muted">Enter the 6-digit secure code sent to your email address.</p>
                </div>
                <form id="otpForm" action="api/verify_otp.php" method="POST">
                    <input type="hidden" name="user_id" id="otp_user_id">
                    <div class="form-group">
                        <input type="text" name="otp_code" class="input-anim text-center" style="font-size:1.8rem; letter-spacing: 0.5em; padding: 1.2rem; font-weight:700; color:var(--text-main);" required maxlength="6" pattern="\d{6}" placeholder="000000">
                    </div>
                    <button type="submit" class="btn-primary w-100 mt-2" style="font-size:1.15rem; padding: 1rem; box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);">Verify & Connect Module</button>
                </form>
                <div id="otp-error" class="alert-error mt-3" style="display:none;"></div>
            </div>

            <p class="mt-4 text-center text-muted" style="font-size:0.95rem;">Already have a verified account? <a href="login.php" style="font-weight:600; text-decoration:underline;">Sign In here</a></p>
        </div>
    </div>
</div>

<script>
// Highly reactive toggle UI animations
document.getElementById('roleUser').addEventListener('change', () => {
    document.getElementById('labelUser').style.background = 'white';
    document.getElementById('labelUser').style.color = 'var(--primary-color)';
    document.getElementById('labelUser').style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    
    document.getElementById('labelAdmin').style.background = 'transparent';
    document.getElementById('labelAdmin').style.color = '#6b7280';
    document.getElementById('labelAdmin').style.boxShadow = 'none';
});
document.getElementById('roleAdmin').addEventListener('change', () => {
    document.getElementById('labelAdmin').style.background = 'white';
    document.getElementById('labelAdmin').style.color = 'var(--primary-color)';
    document.getElementById('labelAdmin').style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    
    document.getElementById('labelUser').style.background = 'transparent';
    document.getElementById('labelUser').style.color = '#6b7280';
    document.getElementById('labelUser').style.boxShadow = 'none';
});


document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerHTML = 'Connecting to Secure Server...';
    btn.disabled = true;
    
    const pwd = document.getElementById('password').value;
    const cpwd = document.getElementById('confirm_password').value;
    if(pwd !== cpwd) {
        document.getElementById('error-message').innerText = "Security Check Failed: Passwords do not match.";
        document.getElementById('error-message').style.display = 'block';
        btn.innerHTML = 'Secure Registration &rarr;';
        btn.disabled = false;
        return;
    }

    const formData = new FormData(e.target);
    const res = await fetch('api/register.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.status === 'success') {
        // Crossfade to OTP view
        document.getElementById('registerForm').style.opacity = '0';
        setTimeout(() => {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('otpSection').style.display = 'block';
            document.getElementById('otp_user_id').value = data.user_id;
            alert("Verification Gateway Simulation - OTP: " + data.mock_otp);
        }, 300);
    } else {
        document.getElementById('error-message').innerText = data.message;
        document.getElementById('error-message').style.display = 'block';
        btn.innerHTML = 'Secure Registration &rarr;';
        btn.disabled = false;
    }
});

document.getElementById('otpForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerHTML = 'Validating Encryption...';
    btn.disabled = true;
    
    const formData = new FormData(e.target);
    const res = await fetch('api/verify_otp.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.status === 'success') {
        window.location.href = 'login.php?registered=1';
    } else {
        document.getElementById('otp-error').innerText = data.message;
        document.getElementById('otp-error').style.display = 'block';
        btn.innerHTML = 'Verify & Connect Module';
        btn.disabled = false;
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>
