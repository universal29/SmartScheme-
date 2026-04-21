<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireLogin();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT s.* 
    FROM schemes s 
    JOIN saved_schemes ss ON s.id = ss.scheme_id 
    WHERE ss.user_id = ?
    ORDER BY s.created_at DESC
");
$stmt->execute([$userId]);
$savedSchemes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Schemes - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .saved-header {
            background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
            color: white;
            padding: 3rem 2.5rem;
            border-radius: var(--radius-xl);
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.15), 0 10px 10px -5px rgba(245, 158, 11, 0.05);
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .saved-header::after {
            content: '📌';
            position: absolute;
            right: 0px;
            bottom: -30px;
            font-size: 10rem;
            opacity: 0.15;
            transform: rotate(15deg);
        }
        .btn-saved-hover {
            transition: all 0.2s ease;
        }
        .btn-saved-hover:hover {
            background: #fef2f2 !important;
            color: #ef4444 !important;
            border-color: #fca5a5 !important;
            content: "Unsave";
        }
        .empty-icon {
            font-size: 5rem;
            opacity: 0.3;
            margin-bottom: 1rem;
            animation: bounce-subtle 2s infinite ease-in-out;
        }
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="saved-header">
                    <span style="display:inline-block; background:rgba(255,255,255,0.25); backdrop-filter:blur(5px); padding:0.25rem 0.75rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase; color: #fffbeb;">My Library</span>
                    <h1 class="page-title" style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem; line-height: 1.1;">Bookmarked Schemes</h1>
                    <p style="color: #fef3c7; font-size: 1.1rem; max-width: 650px; line-height: 1.5;">Access your personally curated list of schemes for quick application reference and tracking.</p>
                </div>
            </header>

            <?php if(empty($savedSchemes)): ?>
                <div class="card text-center animate-fade-up delay-100" style="padding: 5rem 2rem; border: 2px dashed var(--border-color); background: rgba(255,255,255,0.5);">
                    <div class="empty-icon">📂</div>
                    <h3 class="text-muted" style="margin-bottom: 0.5rem; font-size: 1.5rem;">Your library is empty.</h3>
                    <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.05rem;">You have not bookmarked any government schemes yet. Explore the repository or check your AI recommendations to start building your collection.</p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="recommendations.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">View AI Matches</a>
                        <a href="schemes.php" class="btn btn-outline" style="padding: 0.75rem 1.5rem;">Browse All Schemes</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="scheme-grid animate-fade-up delay-100">
                    <?php 
                        $delayCounter = 100;
                        foreach($savedSchemes as $scheme): 
                    ?>
                    <div class="card scheme-card" id="scheme-card-<?= $scheme['id'] ?>" style="animation-delay: <?= $delayCounter ?>ms; border-left: 4px solid #f59e0b;">
                        <div class="scheme-card-body">
                            <div class="scheme-meta">
                                <span class="badge" style="background: #fef3c7; color: #b45309;"><?= htmlspecialchars($scheme['category']) ?></span>
                                <span class="badge badge-green"><?= htmlspecialchars($scheme['state']) ?></span>
                            </div>
                            <h3 class="scheme-title" style="font-size: 1.25rem; line-height: 1.3; margin-bottom: 0.75rem;"><?= htmlspecialchars($scheme['name']) ?></h3>
                            <p class="scheme-desc" style="color: #64748b; line-height: 1.5;"><?= htmlspecialchars($scheme['description']) ?></p>
                        </div>
                        <div class="scheme-actions">
                            <!-- Button UX logic natively integrated -->
                            <button class="btn btn-primary btn-saved-hover" style="flex: 1; background: #f59e0b; border-color: #f59e0b;" 
                                    onmouseover="this.textContent='Remove ✖'" 
                                    onmouseout="this.textContent='Saved ✓'" 
                                    onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Saved ✓</button>
                            <a href="scheme_details.php?id=<?= $scheme['id'] ?>" class="btn btn-outline" style="flex: 1;">View Details &rarr;</a>
                        </div>
                    </div>
                    <?php 
                        $delayCounter += 50;
                        endforeach; 
                    ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
    <script>
        // Override local delete behavior specifically for the Saved Schemes page
        // When user un-saves a scheme here, we want the card to smoothly disappear
        const originalToggle = window.toggleSaveScheme;
        window.toggleSaveScheme = async function(schemeId, btn) {
            try {
                const fd = new FormData();
                fd.append('scheme_id', schemeId);
                const res = await fetch('api_save_scheme.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if(data.success && data.action === 'unsaved') {
                    // Smoothly fade out and remove the card
                    const card = document.getElementById('scheme-card-' + schemeId);
                    if(card) {
                        card.style.transition = 'all 0.4s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            // Optional: If nothing left, reload to show empty state
                            if(document.querySelectorAll('.scheme-card').length === 0) {
                                location.reload();
                            }
                        }, 400);
                    }
                }
            } catch (e) {
                console.error(e);
            }
        };
    </script>
</body>
</html>
