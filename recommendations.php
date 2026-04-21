<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/AI_Engine.php';
requireLogin();

$aiEngine = new AIEngine($pdo, $_SESSION['user_id']);
$recommendedSchemes = $aiEngine->getRecommendations();

// Fetch saved to mark buttons correctly
$stmtSaved = $pdo->prepare("SELECT scheme_id FROM saved_schemes WHERE user_id = ?");
$stmtSaved->execute([$_SESSION['user_id']]);
$savedIds = $stmtSaved->fetchAll(PDO::FETCH_COLUMN);

$highMatchCount = 0;
$normalMatchCount = 0;
foreach($recommendedSchemes as $sc) {
    if($sc['match_score'] >= 50) $highMatchCount++;
    else $normalMatchCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Recommendations - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .ai-banner {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1);
            margin-bottom: 2.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .ai-icon {
            font-size: 3rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        .high-match-card {
            border: 2px solid #8b5cf6;
            background: linear-gradient(to right bottom, #ffffff, #f5f3ff);
            box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.15);
            position: relative;
            overflow: hidden;
        }
        .high-match-card::before {
            content: 'TOP MATCH';
            position: absolute;
            top: 15px;
            right: -30px;
            background: linear-gradient(90deg, #8b5cf6, #ec4899);
            color: white;
            font-size: 0.6rem;
            font-weight: 800;
            padding: 0.2rem 2rem;
            transform: rotate(45deg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .match-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
        }
        .match-high {
            background: #ede9fe;
            color: #6d28d9;
            border: 1px solid #c4b5fd;
        }
        .match-normal {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #8b5cf6;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7);
            animation: pulse-dot-anim 1.5s infinite;
        }
        @keyframes pulse-dot-anim {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(139, 92, 246, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header animate-fade-up">
                <div>
                    <h1 class="page-title">Personalized Recommendations</h1>
                    <p class="text-muted">Direct matches scored by the SmartScheme AI Engine.</p>
                </div>
            </header>

            <?php if(empty($recommendedSchemes)): ?>
                <div class="card text-center animate-fade-up delay-100" style="padding: 4rem 2rem;">
                    <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 1rem;">🔍</div>
                    <h3 class="text-muted" style="margin-bottom: 0.5rem;">No highly relevant schemes found yet.</h3>
                    <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem auto;">The AI Engine could not map any active state or central schemes directly to your exact demographics right now. Ensure your profile is fully complete so we can accurately match you when new datasets arrive.</p>
                    <a href="profile.php" class="btn btn-primary mt-2">Check My Profile Settings</a>
                </div>
            <?php else: ?>
            
                <div class="ai-banner animate-fade-up delay-100">
                    <div class="ai-icon">🤖</div>
                    <div>
                        <h3 style="margin-bottom:0.35rem; font-size: 1.3rem;">AI Insight Dashboard</h3>
                        <p style="margin-bottom:0; line-height: 1.5; font-size: 0.95rem;">
                            We evaluated your demographics against active scheme constraints (State, Age limits, Income brackets, Category). 
                            <br>We discovered <strong><?= $highMatchCount ?> Top Priority matches</strong> directly aligned with your targeted interests, and <strong><?= $normalMatchCount ?> secondary matches</strong> you are fully eligible for!
                        </p>
                    </div>
                </div>
                
                <div class="scheme-grid animate-fade-up delay-200">
                    <?php 
                        $delayCounter = 200;
                        foreach($recommendedSchemes as $scheme): 
                            $isSaved = in_array($scheme['id'], $savedIds); 
                            $isHighMatch = ($scheme['match_score'] >= 50);
                    ?>
                    <div class="card scheme-card <?= $isHighMatch ? 'high-match-card' : '' ?>" style="animation-delay: <?= $delayCounter ?>ms;">
                        <div class="scheme-card-body">
                            <div class="scheme-meta" style="justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <div>
                                    <span class="badge"><?= htmlspecialchars($scheme['category']) ?></span>
                                    <span class="badge badge-green"><?= htmlspecialchars($scheme['state']) ?></span>
                                </div>
                                <?php if($isHighMatch): ?>
                                    <span class="match-badge match-high">
                                        <span class="pulse-dot"></span> TOP MATCH
                                    </span>
                                <?php else: ?>
                                    <span class="match-badge match-normal">
                                        ELIGIBLE
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="scheme-title" style="margin-bottom: 0.75rem; font-size: 1.25rem; line-height: 1.3;">
                                <?= htmlspecialchars($scheme['name']) ?>
                            </h3>
                            <p class="scheme-desc" style="color: #475569; font-size: 0.95rem; line-height: 1.5;">
                                <?= htmlspecialchars($scheme['description']) ?>
                            </p>
                            
                            <div style="background: var(--bg-main); padding: 0.75rem; border-radius: var(--radius-sm); font-size: 0.8rem; border: 1px dashed var(--border-color); margin-bottom: 1.5rem; color: #64748b;">
                                <strong>Why this matches:</strong> Your profile strictly meets the age, income limits, and geographic constraints for this scheme.
                                <?= $isHighMatch ? ' Additionally, it aligns with your explicit interest areas.' : '' ?>
                            </div>
                        </div>
                        <div class="scheme-actions" style="margin-top:auto;">
                            <?php if($isSaved): ?>
                                <button class="btn btn-primary" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Saved</button>
                            <?php else: ?>
                                <button class="btn btn-outline" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Save to Bookmarks</button>
                            <?php endif; ?>
                            <a href="scheme_details.php?id=<?= $scheme['id'] ?>" class="btn btn-primary" style="flex: 1; background: <?= $isHighMatch ? 'linear-gradient(90deg, #8b5cf6, #6d28d9)' : 'var(--primary-color)' ?>; box-shadow: <?= $isHighMatch ? '0 4px 10px rgba(139, 92, 246, 0.3)' : '' ?>;">View Details &rarr;</a>
                        </div>
                    </div>
                    <?php 
                        $delayCounter += 50; // Stagger each card
                        endforeach; 
                    ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
