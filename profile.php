<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireLogin();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

// Load existing data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Load interests
$stmtInt = $pdo->prepare("SELECT interest FROM user_interests WHERE user_id = ?");
$stmtInt->execute([$userId]);
$userInterests = $stmtInt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process update
    $address = $_POST['address'] ?? '';
    $state = $_POST['state'] ?? '';
    $religion = $_POST['religion'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $annual_income = (float)($_POST['annual_income'] ?? 0);
    $category = $_POST['category'] ?? '';
    $interests = $_POST['interests'] ?? []; // Array

    try {
        $pdo->beginTransaction();
        
        $updateStmt = $pdo->prepare("UPDATE users SET address=?, state=?, religion=?, occupation=?, age=?, annual_income=?, category=? WHERE id=?");
        $updateStmt->execute([$address, $state, $religion, $occupation, $age, $annual_income, $category, $userId]);
        
        // Update interests: Delete old and insert new
        $pdo->prepare("DELETE FROM user_interests WHERE user_id=?")->execute([$userId]);
        if (!empty($interests)) {
            $insertInt = $pdo->prepare("INSERT INTO user_interests (user_id, interest) VALUES (?, ?)");
            foreach ($interests as $interest) {
                $insertInt->execute([$userId, $interest]);
            }
        }
        
        $pdo->commit();
        $message = "Profile updated successfully. AI recommendations are now re-calibrated.";
        
        // Reload data
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        $stmtInt->execute([$userId]);
        $userInterests = $stmtInt->fetchAll(PDO::FETCH_COLUMN);
        
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = "Failed to update profile.";
    }
}

// Calculate Completion Percentage
$fieldsToCheck = ['address', 'state', 'religion', 'occupation', 'age', 'annual_income', 'category'];
$filledCount = 0;
foreach($fieldsToCheck as $f) {
    if(!empty($user[$f])) $filledCount++;
}
if(count($userInterests) > 0) $filledCount++;
$totalFields = count($fieldsToCheck) + 1;
$completionPct = round(($filledCount / $totalFields) * 100);

// Initials for Avatar
$words = explode(" ", trim($user['full_name']));
$initials = strtoupper(mb_substr($words[0], 0, 1));
if(count($words) > 1) {
    $initials .= strtoupper(mb_substr(end($words), 0, 1));
}

$categoriesList = ['Education', 'Health', 'Agriculture', 'Women', 'Senior Citizen', 'Employment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            max-width: 1100px;
        }
        .avatar-box {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 auto 1rem auto;
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }
        .progress-bg {
            background: #e2e8f0;
            border-radius: 9999px;
            height: 8px;
            width: 100%;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .progress-bar {
            height: 100%;
            background: var(--primary-color);
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @media (max-width: 900px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header animate-fade-up">
                <div>
                    <h1 class="page-title">Profile Settings</h1>
                    <p class="text-muted">Manage your personal data and application preferences.</p>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="animate-fade-up" style="background: #d1fae5; color: #047857; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'welcome'): ?>
                <div class="animate-fade-up" style="background: #e0e7ff; color: #4338ca; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #4f46e5;">
                    Welcome aboard! Please complete your profile to enable personalized scheme matching.
                </div>
            <?php endif; ?>

            <div class="profile-container">
                <!-- Left Sidebar: Overview & Stats -->
                <div class="card profile-card animate-fade-up delay-100" style="align-self: start; text-align: center;">
                    <div class="avatar-box">
                        <?= $initials ?>
                    </div>
                    <h3 style="margin-bottom: 0.25rem;"><?= htmlspecialchars($user['full_name']) ?></h3>
                    <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1.5rem;"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div style="text-align: left; background: var(--bg-main); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600;">
                            <span>Profile Completion</span>
                            <span style="color: <?= $completionPct == 100 ? '#10b981' : '#f59e0b' ?>"><?= $completionPct ?>%</span>
                        </div>
                        <div class="progress-bg">
                            <!-- Animating inline style using variable -->
                            <div class="progress-bar" style="width: 0%;" onload="this.style.width='<?= $completionPct ?>%';"></div>
                        </div>
                        <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.5rem; line-height: 1.3;">
                            <?php if($completionPct == 100): ?>
                                All set! Your AI matches are highly accurate.
                            <?php else: ?>
                                Complete your profile to unlock precise scheme recommendations!
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: left; font-size: 0.85rem;">
                        <strong>Member Since:</strong><br>
                        <span class="text-muted"><?= date('F j, Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>

                <!-- Right Content: The Form -->
                <div class="card profile-card animate-fade-up delay-200">
                    <form method="POST" action="">
                        <div class="animate-fade-up delay-100">
                            <div class="profile-section-title" style="margin-top: 0; border-bottom: none; border-left: 4px solid var(--primary-color); padding-left: 0.75rem; background: linear-gradient(to right, rgba(59,130,246,0.1), transparent);">Personal Details</div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" disabled style="background-color: #f1f5f9; cursor: not-allowed; border: 1px dashed #cbd5e1;">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="age" class="form-control" value="<?= $user['age'] ?>" placeholder="e.g. 35">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                    <select name="state" class="form-control">
                                        <option value="">Select Region...</option>
                                        <option value="Gujarat" <?= $user['state'] == 'Gujarat' ? 'selected' : '' ?>>Gujarat</option>
                                        <option value="Maharashtra" <?= $user['state'] == 'Maharashtra' ? 'selected' : '' ?>>Maharashtra</option>
                                        <option value="Delhi" <?= $user['state'] == 'Delhi' ? 'selected' : '' ?>>Delhi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Religion</label>
                                    <input type="text" name="religion" class="form-control" value="<?= htmlspecialchars($user['religion'] ?? '') ?>" placeholder="Optional">
                                </div>
                            </div>
                            
                            <div class="form-group mt-1">
                                <label class="form-label">Full Residential Address</label>
                                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($user['address']) ?></textarea>
                            </div>
                        </div>

                        <div class="animate-fade-up delay-200">
                            <div class="profile-section-title" style="border-bottom: none; border-left: 4px solid var(--accent-color); padding-left: 0.75rem; background: linear-gradient(to right, rgba(139,92,246,0.1), transparent);">Socio-Economic Criteria <span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted);">(Used by AI)</span></div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Annual Family Income (₹)</label>
                                    <input type="number" step="0.01" name="annual_income" class="form-control" value="<?= $user['annual_income'] ?>" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Category (Caste)</label>
                                    <select name="category" class="form-control">
                                        <option value="">Select Designation...</option>
                                        <option value="General" <?= $user['category'] == 'General' ? 'selected' : '' ?>>General (Unreserved)</option>
                                        <option value="OBC" <?= $user['category'] == 'OBC' ? 'selected' : '' ?>>Other Backward Class (OBC)</option>
                                        <option value="SC" <?= $user['category'] == 'SC' ? 'selected' : '' ?>>Scheduled Caste (SC)</option>
                                        <option value="ST" <?= $user['category'] == 'ST' ? 'selected' : '' ?>>Scheduled Tribe (ST)</option>
                                    </select>
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Occupation / Profession / Student Status</label>
                                    <input type="text" name="occupation" class="form-control" value="<?= htmlspecialchars($user['occupation']) ?>" placeholder="e.g. Farmer, Student, or Software Engineer">
                                </div>
                            </div>
                        </div>

                        <div class="animate-fade-up delay-300">
                            <div class="profile-section-title" style="border-bottom: none; border-left: 4px solid #10b981; padding-left: 0.75rem; background: linear-gradient(to right, rgba(16,185,129,0.1), transparent);">Targeted Interests</div>
                            <p class="text-muted" style="font-size: 0.85rem; margin-top: -0.5rem; margin-bottom: 1rem;">Select the specific programmatic sectors you are interested in parsing algorithms for.</p>
                            
                            <style>
                                .interests-grid {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 0.75rem;
                                    margin-bottom: 1rem;
                                }
                                .interest-pill {
                                    position: relative;
                                }
                                .interest-pill input[type="checkbox"] {
                                    position: absolute;
                                    opacity: 0;
                                    cursor: pointer;
                                    height: 0;
                                    width: 0;
                                    margin: 0;
                                }
                                .interest-pill label {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 0.6rem 1.25rem;
                                    background: #f8fafc;
                                    border: 1px solid #cbd5e1;
                                    color: #475569;
                                    border-radius: 50px;
                                    font-size: 0.9rem;
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                                }
                                .interest-pill input[type="checkbox"]:checked ~ label {
                                    background: #10b981;
                                    color: white;
                                    border-color: #10b981;
                                    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
                                    transform: translateY(-2px);
                                }
                                .interest-pill input[type="checkbox"]:focus-visible ~ label {
                                    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
                                }
                                .interest-pill label:hover {
                                    background: #f1f5f9;
                                    border-color: #94a3b8;
                                }
                                .interest-pill input[type="checkbox"]:checked ~ label:hover {
                                    background: #059669;
                                    border-color: #059669;
                                }
                            </style>
                            
                            <div class="interests-grid">
                                <?php foreach($categoriesList as $cat): ?>
                                    <div class="interest-pill">
                                        <input type="checkbox" id="cat_<?= md5($cat) ?>" name="interests[]" value="<?= $cat ?>" <?= in_array($cat, $userInterests) ? 'checked' : '' ?>>
                                        <label for="cat_<?= md5($cat) ?>">
                                            <?= $cat ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <hr style="border:0; border-top: 1px solid var(--border-color); margin: 2rem 0;">
                        <button type="submit" class="btn btn-primary animate-fade-up delay-300" style="padding: 0.75rem 2rem; font-size: 1.05rem;">Save Profile & Recalibrate AI</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        // Trigger progress bar animation after load
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const bar = document.querySelector('.progress-bar');
                if(bar) bar.style.width = '<?= $completionPct ?>%';
            }, 300);
        });
    </script>
</body>
</html>
