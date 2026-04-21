<?php
require_once 'includes/config.php';
require_once 'includes/config.php';
require_once 'includes/session.php';
// Publicly accessible details route

$schemeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$schemeId) {
    header("Location: schemes.php");
    exit;
}

// Fetch scheme details
$stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$stmt->execute([$schemeId]);
$scheme = $stmt->fetch();

if (!$scheme) {
    echo "Scheme not found.";
    exit;
}

$isSaved = false;
if (isLoggedIn()) {
    $stmtSaved = $pdo->prepare("SELECT COUNT(*) FROM saved_schemes WHERE user_id = ? AND scheme_id = ?");
    $stmtSaved->execute([$_SESSION['user_id'], $schemeId]);
    $isSaved = $stmtSaved->fetchColumn() > 0;
}

function displayValue($val, $suffix = '') {
    if ($val === null || $val === '') return 'Not specified / No limit';
    return htmlspecialchars($val) . $suffix;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($scheme['name']) ?> - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .details-header {
            background: linear-gradient(135deg, var(--bg-main) 0%, #e0e7ff 100%);
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .info-card {
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }
        .info-label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="animate-fade-up">
                <a href="javascript:history.back()" style="display: inline-block; margin-bottom: 1rem; color: var(--text-muted);">&larr; Back to previous page</a>
                
                <div class="details-header">
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                        <span class="badge"><?= htmlspecialchars($scheme['category']) ?></span>
                        <span class="badge badge-green"><?= htmlspecialchars($scheme['state']) ?></span>
                    </div>
                    <h1 style="font-size: 2.5rem; margin-bottom: 1rem; line-height: 1.2; color: var(--primary-color);">
                        <?= htmlspecialchars($scheme['name']) ?>
                    </h1>
                    <p style="font-size: 1.1rem; color: var(--text-main); max-width: 800px; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($scheme['description'])) ?>
                    </p>
                    
                    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                        <?php if(!isLoggedIn()): ?>
                            <a href="auth_login.php" class="btn btn-outline" style="padding: 0.75rem 2rem; font-size: 1.05rem; background: white; text-decoration: none;">Save to Bookmarks</a>
                            <a href="auth_login.php" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.05rem; background: #10b981; text-decoration: none;">Log in to Apply &nearr;</a>
                        <?php else: ?>
                            <?php if($isSaved): ?>
                                <button class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.05rem;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Saved</button>
                            <?php else: ?>
                                <button class="btn btn-outline" style="padding: 0.75rem 2rem; font-size: 1.05rem; background: white;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Save to Bookmarks</button>
                            <?php endif; ?>

                                <button class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.05rem; background: #10b981;" onclick="window.location.href='apply_scheme.php?id=<?= $scheme['id'] ?>';">Submit Application &nearr;</button>
                        <?php endif; ?>
                    </div>
                </div>

                <h2 style="margin-bottom: 1.5rem;">Eligibility Criteria & Constraints</h2>
                
                <div style="position: relative;">
                    <div class="info-grid animate-fade-up delay-100" style="<?= !isLoggedIn() ? 'filter: blur(8px); opacity: 0.4; pointer-events: none; user-select: none;' : '' ?>">
                        <div class="info-card">
                            <span class="info-label">Geographic Constraint</span>
                            <span class="info-value"><?= htmlspecialchars($scheme['state']) == 'All' || htmlspecialchars($scheme['state']) == 'Central' ? 'Nationwide / Central' : htmlspecialchars($scheme['state']) . ' Only' ?></span>
                        </div>
                        
                        <div class="info-card">
                            <span class="info-label">Age Requirements</span>
                            <span class="info-value">
                                <?php 
                                    if($scheme['min_age'] == 0 && $scheme['max_age'] == 200) echo 'No Age Limit';
                                    else echo $scheme['min_age'] . ' to ' . $scheme['max_age'] . ' years';
                                ?>
                            </span>
                        </div>
                        
                        <div class="info-card">
                            <span class="info-label">Max Family Income</span>
                            <span class="info-value">
                                <?= $scheme['max_income'] ? '₹' . number_format($scheme['max_income'], 2) : 'No Income Limit' ?>
                            </span>
                        </div>
                        
                        <div class="info-card">
                            <span class="info-label">Social Category</span>
                            <span class="info-value"><?= htmlspecialchars($scheme['target_category']) ?></span>
                        </div>
                        
                        <div class="info-card">
                            <span class="info-label">Target Gender</span>
                            <span class="info-value"><?= htmlspecialchars($scheme['gender'] ?? 'All') ?></span>
                        </div>
                    </div>

                    <?php if(!isLoggedIn()): ?>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; z-index: 10;">
                            <div class="animate-fade-up delay-200" style="background: rgba(255, 255, 255, 0.95); padding: 2.5rem 3rem; border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center; border: 1px solid var(--border-color); max-width: 450px;">
                                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.8;">🔒</div>
                                <h3 style="margin-bottom: 0.75rem; color: #1e293b; font-size: 1.4rem;">Restricted Information</h3>
                                <p style="color: #64748b; margin-bottom: 1.75rem; font-size: 0.95rem; line-height: 1.5;">To protect algorithmic integrity, explicit eligibility constraints are hidden from external crawlers. Please log in or sign up below to unlock this matrix.</p>
                                <a href="auth_register.php" class="btn btn-primary" style="background: #10b981; border-color: #10b981; width: 100%; padding: 0.75rem;">Create Free Account</a>
                                <div style="margin-top: 1.25rem; font-size: 0.9rem;">
                                    Already registered? <a href="auth_login.php" style="color: var(--primary-color); font-weight: 700;">Sign in securely</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
