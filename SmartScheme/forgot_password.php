<?php
require_once 'includes/functions.php';
if(isLoggedIn()) redirect('dashboard.php');
require_once 'includes/header.php';
?>
<div class="auth-container">
    <div class="auth-box glassmorphism">
        <h2>Forgot Password</h2>
        <p>Reset your password via OTP</p>
        
        <form id="forgotForm" action="api/forgot_password.php" method="POST">
            <div id="error-message" class="alert-error" style="display:none;"></div>
            <div id="success-message" class="alert-success" style="display:none;"></div>
            
            <div class="form-group">
                <label>Email or Mobile</label>
                <input type="text" name="email_or_mobile" id="email_or_mobile" required>
            </div>
            <button type="submit" class="btn-primary w-100">Send OTP</button>
        </form>

        <div id="resetSection" style="display:none;">
            <h3>Reset Password</h3>
            <form id="resetForm" action="api/reset_password.php" method="POST">
                <input type="hidden" name="user_id" id="reset_user_id">
                <div class="form-group">
                    <label>OTP Code</label>
                    <input type="text" name="otp_code" required maxlength="6" pattern="\d{6}">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required minlength="8">
                </div>
                <button type="submit" class="btn-primary w-100">Update Password</button>
            </form>
            <div id="reset-error" class="alert-error mt-2" style="display:none;"></div>
        </div>
        
        <p class="mt-4 text-center"><a href="login.php">Back to Login</a></p>
    </div>
</div>

<script>
document.getElementById('forgotForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('api/forgot_password.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.status === 'success') {
        document.getElementById('forgotForm').style.display = 'none';
        document.getElementById('resetSection').style.display = 'block';
        document.getElementById('reset_user_id').value = data.user_id;
        alert("Mock Reset OTP (for demo): " + data.mock_otp);
    } else {
        document.getElementById('error-message').innerText = data.message;
        document.getElementById('error-message').style.display = 'block';
    }
});

document.getElementById('resetForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('api/reset_password.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    if(data.status === 'success') {
        window.location.href = 'login.php?registered=1';
    } else {
        document.getElementById('reset-error').innerText = data.message;
        document.getElementById('reset-error').style.display = 'block';
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>
