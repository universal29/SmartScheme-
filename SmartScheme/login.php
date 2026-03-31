<?php
require_once 'includes/functions.php';
if(isLoggedIn()) redirect('dashboard.php');
require_once 'includes/header.php';
?>
<style>
/* Scoped Animation & Layout for Auth Pages */
.auth-split-container {
    display: flex;
    min-height: 80vh;
    background: white;
    margin: 3rem auto;
    max-width: 1000px;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.auth-left {
    flex: 1;
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
    animation: rotateBg 30s linear infinite reverse;
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
    box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1);
    border-color: #10b981;
    background: white;
}

@media (max-width: 900px) {
    .auth-split-container { flex-direction: column; }
    .auth-left { padding: 3rem 2rem; }
    .auth-right { padding: 3rem 2rem; }
}
</style>

<div class="container">
    <div class="auth-split-container">
        <!-- Left Side: Splash & Value Prop -->
        <div class="auth-left">
            <h1 class="fw-bold mb-4" style="font-size: 2.8rem; z-index:1; line-height: 1.2;">Welcome Back</h1>
            <p class="mb-5" style="font-size: 1.15rem; opacity: 0.9; z-index:1;">Resuming your journey on the SmartScheme platform. Let's find you more algorithmic benefits today.</p>
            
            <div style="z-index:1;">
                <div class="auth-feature">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding: 1.2rem; border-radius: 16px; font-size: 1.5rem; display:flex; align-items:center; justify-content:center;">🎯</div>
                    <div>
                        <h4 style="margin:0; font-size: 1.15rem;">Direct Navigation</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.95rem;">Jump straight into your saved schemes.</p>
                    </div>
                </div>
                <div class="auth-feature">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding: 1.2rem; border-radius: 16px; font-size: 1.5rem; display:flex; align-items:center; justify-content:center;">🛡️</div>
                    <div>
                        <h4 style="margin:0; font-size: 1.15rem;">Encrypted Gateway</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.95rem;">Always secure and natively authenticated.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: The Form -->
        <div class="auth-right">
            <h2 class="mb-1" style="color: #047857;">Sign In to Account</h2>
            <p class="text-muted mb-4">Access your customized profile matrix below.</p>
            
            <?php if(isset($_GET['registered'])): ?>
                <div class="alert-success" style="animation: fadeIn 0.4s ease;">Identity Verified! Your account is active. Please sign in below.</div>
            <?php endif; ?>

            <form id="loginForm" action="api/login.php" method="POST">
                <div id="error-message" class="alert-error" style="display:none; animation: fadeIn 0.3s ease;"></div>
                
                <div class="form-group mb-4">
                    <div style="display:flex; gap:8px; background: #f3f4f6; padding: 6px; border-radius: 50px;">
                        <label style="flex:1; cursor:pointer; margin:0;" class="text-center">
                            <input type="radio" name="login_role" value="user" checked style="display:none;" id="roleUser">
                            <div class="role-btn" style="padding: 0.6rem; border-radius: 50px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); font-weight:600; color:#047857; transition:0.3s;" id="labelUser">Citizen</div>
                        </label>
                        <label style="flex:1; cursor:pointer; margin:0;" class="text-center">
                            <input type="radio" name="login_role" value="admin" style="display:none;" id="roleAdmin">
                            <div class="role-btn" style="padding: 0.6rem; border-radius: 50px; font-weight:600; color:#6b7280; transition:0.3s;" id="labelAdmin">Administrator</div>
                        </label>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label style="font-weight:600;">Email Address</label>
                    <input type="email" name="email" class="input-anim" required placeholder="john@example.com" style="padding: 1rem;">
                </div>
                
                <div class="form-group mb-2">
                    <label style="font-weight:600;">Secure Password</label>
                    <input type="password" name="password" class="input-anim" required placeholder="••••••••" style="padding: 1rem;">
                </div>
                
                <div class="text-right mb-4" style="text-align: right;">
                    <a href="forgot_password.php" style="font-size: 0.9rem; font-weight: 500; color: #047857; text-decoration: underline;">Forgot your password?</a>
                </div>

                <button type="submit" class="btn-primary w-100" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); font-size:1.1rem; padding: 1rem; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4); border:none;">Authenticate &rarr;</button>
            </form>

            <p class="mt-4 text-center text-muted" style="font-size:0.95rem;">Don't have an account? <a href="register.php" style="font-weight:700; text-decoration:none; color:#047857;">Sign up instantly</a></p>
        </div>
    </div>
</div>

<script>
// Highly reactive toggle UI animations for Login
document.getElementById('roleUser').addEventListener('change', () => {
    document.getElementById('labelUser').style.background = 'white';
    document.getElementById('labelUser').style.color = '#047857';
    document.getElementById('labelUser').style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    
    document.getElementById('labelAdmin').style.background = 'transparent';
    document.getElementById('labelAdmin').style.color = '#6b7280';
    document.getElementById('labelAdmin').style.boxShadow = 'none';
});
document.getElementById('roleAdmin').addEventListener('change', () => {
    document.getElementById('labelAdmin').style.background = 'white';
    document.getElementById('labelAdmin').style.color = '#047857';
    document.getElementById('labelAdmin').style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
    
    document.getElementById('labelUser').style.background = 'transparent';
    document.getElementById('labelUser').style.color = '#6b7280';
    document.getElementById('labelUser').style.boxShadow = 'none';
});

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerHTML = 'Executing Handshake...';
    btn.disabled = true;
    
    const formData = new FormData(e.target);
    const res = await fetch('api/login.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.status === 'success') {
        // Aesthetic delay for the fade-out feeling
        btn.innerHTML = 'Access Granted ✔️';
        btn.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.8)';
        setTimeout(() => {
            window.location.href = data.role === 'admin' ? 'admin/index.php' : 'dashboard.php';
        }, 400);
    } else {
        document.getElementById('error-message').innerText = data.message;
        document.getElementById('error-message').style.display = 'block';
        btn.innerHTML = 'Authenticate &rarr;';
        btn.disabled = false;
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>
