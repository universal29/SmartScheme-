<?php
require_once 'includes/functions.php';
if(!isLoggedIn()) redirect('login.php');
require_once 'config/database.php';
require_once 'includes/header.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$userId]);
$profile = $stmt->fetch() ?: [];
?>
<div class="dashboard-container">
    <h2>My Profile</h2>
    <div class="card glassmorphism mb-4">
        <h4>Profile Completeness: <span id="completion-text"><?= htmlspecialchars($profile['completeness_score'] ?? 0) ?>%</span></h4>
        <div class="progress-bar-container">
            <div class="progress-bar" id="completion-bar" style="width: <?= htmlspecialchars($profile['completeness_score'] ?? 0) ?>%;"></div>
        </div>
        <p class="text-sm mt-2 text-muted">Complete your profile to get the most accurate AI scheme recommendations.</p>
    </div>

    <div class="card glassmorphism">
        <form id="profileForm" action="api/update_profile.php" method="POST">
            <div id="form-msg" style="display:none;" class="alert-success"></div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($profile['dob'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?= ($profile['gender']??'') == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($profile['gender']??'') == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= ($profile['gender']??'') == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>State</label>
                    <input type="text" name="state" value="<?= htmlspecialchars($profile['state'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>District</label>
                    <input type="text" name="district" value="<?= htmlspecialchars($profile['district'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Annual Income Level</label>
                    <select name="income_range">
                        <option value="">Select Range</option>
                        <option value="Below 1 Lakh" <?= ($profile['income_range']??'') == 'Below 1 Lakh' ? 'selected' : '' ?>>Below 1 Lakh</option>
                        <option value="1-5 Lakhs" <?= ($profile['income_range']??'') == '1-5 Lakhs' ? 'selected' : '' ?>>1-5 Lakhs</option>
                        <option value="Above 5 Lakhs" <?= ($profile['income_range']??'') == 'Above 5 Lakhs' ? 'selected' : '' ?>>Above 5 Lakhs</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">Select Category</option>
                        <option value="SC" <?= ($profile['category']??'') == 'SC' ? 'selected' : '' ?>>SC</option>
                        <option value="ST" <?= ($profile['category']??'') == 'ST' ? 'selected' : '' ?>>ST</option>
                        <option value="OBC" <?= ($profile['category']??'') == 'OBC' ? 'selected' : '' ?>>OBC</option>
                        <option value="General" <?= ($profile['category']??'') == 'General' ? 'selected' : '' ?>>General</option>
                        <option value="EWS" <?= ($profile['category']??'') == 'EWS' ? 'selected' : '' ?>>EWS</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Are you a Farmer?</label>
                    <select name="is_farmer">
                        <option value="0" <?= ($profile['is_farmer']??0) == 0 ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ($profile['is_farmer']??0) == 1 ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>BPL Status</label>
                    <select name="bpl_status">
                        <option value="0" <?= ($profile['bpl_status']??0) == 0 ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ($profile['bpl_status']??0) == 1 ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>What type of schemes are you looking for?</label>
                    <select name="looking_for_module">
                        <option value="">Select Primary Interest</option>
                        <option value="Agriculture" <?= ($profile['looking_for_module']??'') == 'Agriculture' ? 'selected' : '' ?>>Agriculture</option>
                        <option value="Healthcare" <?= ($profile['looking_for_module']??'') == 'Healthcare' ? 'selected' : '' ?>>Healthcare</option>
                        <option value="Education" <?= ($profile['looking_for_module']??'') == 'Education' ? 'selected' : '' ?>>Education</option>
                        <option value="Women" <?= ($profile['looking_for_module']??'') == 'Women' ? 'selected' : '' ?>>Women</option>
                        <option value="Senior Citizen" <?= ($profile['looking_for_module']??'') == 'Senior Citizen' ? 'selected' : '' ?>>Senior Citizen</option>
                        <option value="Business" <?= ($profile['looking_for_module']??'') == 'Business' ? 'selected' : '' ?>>Business</option>
                        <option value="General" <?= ($profile['looking_for_module']??'') == 'General' ? 'selected' : '' ?>>General</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primary">Save Profile & Get Recommendations</button>
        </form>
    </div>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('api/update_profile.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    const msgDiv = document.getElementById('form-msg');
    msgDiv.style.display = 'block';
    if(data.status === 'success') {
        msgDiv.className = 'alert-success';
        msgDiv.innerText = data.message;
        document.getElementById('completion-text').innerText = data.completeness + '%';
        document.getElementById('completion-bar').style.width = data.completeness + '%';
    } else {
        msgDiv.className = 'alert-error';
        msgDiv.innerText = data.message;
    }
});
</script>
<?php require_once 'includes/footer.php'; ?>
