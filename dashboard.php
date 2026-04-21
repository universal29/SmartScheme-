<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireLogin();

$userId = $_SESSION['user_id'];

// Fetch basic info
$stmt = $pdo->prepare("SELECT full_name, age, annual_income, state FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$profileIncomplete = (empty($user['age']) || empty($user['annual_income']) || empty($user['state']));

// Fetch Stats
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM schemes");
$totalSchemes = $stmtTotal->fetchColumn();

$stmtSaved = $pdo->prepare("SELECT COUNT(*) FROM saved_schemes WHERE user_id = ?");
$stmtSaved->execute([$userId]);
$totalSaved = $stmtSaved->fetchColumn();

$stmtTickets = $pdo->prepare("SELECT COUNT(*) FROM support_tickets WHERE user_id = ? AND status = 'open'");
$stmtTickets->execute([$userId]);
$openTickets = $stmtTickets->fetchColumn();

// Fetch recently viewed/saved schemes context to check toggle save style
$stmtSavedIds = $pdo->prepare("SELECT scheme_id FROM saved_schemes WHERE user_id = ?");
$stmtSavedIds->execute([$userId]);
$savedIds = $stmtSavedIds->fetchAll(PDO::FETCH_COLUMN);

// Top 6 recent schemes overall
$stmtSchemes = $pdo->query("SELECT * FROM schemes ORDER BY created_at DESC LIMIT 6");
$recentSchemes = $stmtSchemes->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1);
        }
        .stat-info {
            display: flex;
            flex-direction: column;
        }
        .stat-val {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header animate-fade-up">
                <div>
                    <h1 class="page-title">Welcome back, <?= htmlspecialchars($user['full_name']) ?>!</h1>
                    <p class="text-muted" style="font-size: 1.05rem;">Your personalized government scheme control center.</p>
                </div>
            </header>
            
            <?php if($profileIncomplete): ?>
            <div class="animate-fade-up" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; padding: 1rem 1.5rem; border-radius: var(--radius-md); border-left: 5px solid #f59e0b; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="display: block; font-size: 1.05rem; margin-bottom: 0.25rem;">Action Required: Missing Profile Info</strong>
                    <span style="font-size: 0.95rem;">Please update your age, income, and state to activate the AI Recommendation Engine accurately.</span>
                </div>
                <a href="profile.php" class="btn" style="background: white; color: #b45309; border: 1px solid #fcd34d;">Complete Profile</a>
            </div>
            <?php endif; ?>

            <!-- Statistics Overview -->
            <section class="stat-grid animate-fade-up delay-100">
                <div class="stat-card" style="border-top: 4px solid var(--primary-color);">
                    <div class="stat-info">
                        <span class="stat-val text-primary"><?= $totalSchemes ?></span>
                        <span class="stat-label">Total Schemes Indexed</span>
                    </div>
                    <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">🏛️</div>
                </div>
                <div class="stat-card" style="border-top: 4px solid #10b981;">
                    <div class="stat-info">
                        <span class="stat-val"><?= $totalSaved ?></span>
                        <span class="stat-label">Bookmarked Schemes</span>
                    </div>
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">📌</div>
                </div>
                <div class="stat-card" style="border-top: 4px solid #f59e0b;">
                    <div class="stat-info">
                        <span class="stat-val"><?= $openTickets ?></span>
                        <span class="stat-label">Pending Support Queries</span>
                    </div>
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">💬</div>
                </div>
            </section>

            <!-- Quick Category Filters -->
            <section class="animate-fade-up delay-150 mb-4" style="margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1.25rem;">Browse by Category</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1.25rem;">
                    <a href="schemes.php?category=Education" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #3b82f6;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">📚</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Education</h3>
                    </a>
                    <a href="schemes.php?category=Health" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #10b981;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">🏥</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Health</h3>
                    </a>
                    <a href="schemes.php?category=Agriculture" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #f59e0b;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">🌾</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Agriculture</h3>
                    </a>
                    <a href="schemes.php?category=Women" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #ec4899;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">👩‍🎓</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Women</h3>
                    </a>
                    <a href="schemes.php?category=Employment" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #6366f1;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">💼</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Employment</h3>
                    </a>
                    <a href="schemes.php?category=Senior+Citizen" class="card category-card" style="text-decoration:none; text-align:center; padding:1.5rem 1rem; transition: all 0.2s; border: 1px solid var(--border-color); border-bottom: 3px solid #8b5cf6;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem; transform: scale(1); transition: transform 0.2s;" class="cat-icon">👴</div>
                        <h3 style="color:#1e293b; font-size:1.05rem; margin:0;">Senior Citizen</h3>
                    </a>
                </div>
                <style>
                    .category-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1); background: #fafaf9; }
                    .category-card:hover .cat-icon { transform: scale(1.15); }
                </style>
            </section>

            <section class="animate-fade-up delay-200 mb-4">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h2>Explore Highlights</h2>
                    <a href="recommendations.php" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.85rem;">View Personal AI Matches &rarr;</a>
                </div>
                
                <div class="scheme-grid">
                    <?php foreach($recentSchemes as $scheme): ?>
                        <?php $isSaved = in_array($scheme['id'], $savedIds); ?>
                    <div class="card scheme-card">
                        <div class="scheme-card-body">
                            <div class="scheme-meta">
                                <span class="badge"><?= htmlspecialchars($scheme['category']) ?></span>
                                <span class="badge badge-green"><?= htmlspecialchars($scheme['state']) ?></span>
                            </div>
                            <h3 class="scheme-title"><?= htmlspecialchars($scheme['name']) ?></h3>
                            <p class="scheme-desc"><?= htmlspecialchars($scheme['description']) ?></p>
                        </div>
                        <div class="scheme-actions">
                            <?php if($isSaved): ?>
                                <button class="btn btn-primary" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Saved</button>
                            <?php else: ?>
                                <button class="btn btn-outline" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Save</button>
                            <?php endif; ?>
                            <a href="scheme_details.php?id=<?= $scheme['id'] ?>" class="btn btn-primary" style="flex: 1;">Details</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
