<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$is_logged_in = isLoggedIn();
$completeness = 0;
$profile = [];
$userId = null;

if($is_logged_in) {
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();
    $completeness = $profile['completeness_score'] ?? 0;
}
?>

<style>
.module-card {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: none !important;
    position: relative;
    overflow: hidden;
    color: white !important;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.module-card:hover {
    transform: translateY(-8px) scale(1.03);
}
.module-card::after {
    content: '';
    position: absolute;
    top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}
.module-card:hover::after {
    opacity: 1;
    animation: rotateBg 15s linear infinite;
}
@keyframes rotateBg {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<div class="container my-4 pt-4">
    <h3 class="mb-3" style="color:#1f2937; font-weight:800; letter-spacing:-0.03em;">Explore Schemes by Category</h3>
    <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem;">
        <?php
        $modules = [
            'Agriculture' => [
                'icon' => '🌾',
                'bg' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)',
                'shadow' => 'rgba(16, 185, 129, 0.4)'
            ],
            'Healthcare' => [
                'icon' => '⚕️',
                'bg' => 'linear-gradient(135deg, #4f46e5 0%, #818cf8 100%)',
                'shadow' => 'rgba(79, 70, 229, 0.4)'
            ],
            'Education' => [
                'icon' => '📚',
                'bg' => 'linear-gradient(135deg, #ea580c 0%, #f97316 100%)',
                'shadow' => 'rgba(249, 115, 22, 0.4)'
            ],
            'Women' => [
                'icon' => '👩',
                'bg' => 'linear-gradient(135deg, #db2777 0%, #f472b6 100%)',
                'shadow' => 'rgba(219, 39, 119, 0.4)'
            ],
            'Senior Citizen'=> [
                'icon' => '👴',
                'bg' => 'linear-gradient(135deg, #0d9488 0%, #2dd4bf 100%)',
                'shadow' => 'rgba(13, 148, 136, 0.4)'
            ],
            'General' => [
                'icon' => '🏢',
                'bg' => 'linear-gradient(135deg, #475569 0%, #94a3b8 100%)',
                'shadow' => 'rgba(71, 85, 105, 0.4)'
            ]
        ];
        foreach($modules as $m => $data): ?>
        <a href="schemes.php?module=<?= urlencode($m) ?>" class="card module-card text-center" style="min-width: 170px; flex: 0 0 auto; text-decoration: none; padding: 1.8rem 1.5rem; background: <?= $data['bg'] ?>; box-shadow: 0 8px 20px <?= $data['shadow'] ?>;">
            <div style="font-size: 3rem; margin-bottom: 0.8rem; text-shadow: 0 4px 10px rgba(0,0,0,0.15);"><?= $data['icon'] ?></div>
            <div class="fw-bold" style="font-size:1.15rem; letter-spacing:0.02em;"><?= $m ?></div>
        </a>
        <?php endforeach; ?>
        
        <!-- Startup India Special Module -->
        <a href="startup_india.php" class="card module-card text-center" style="min-width: 180px; flex: 0 0 auto; text-decoration: none; padding: 1.8rem 1.5rem; background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);">
            <div style="font-size: 3rem; margin-bottom: 0.8rem; text-shadow: 0 4px 10px rgba(0,0,0,0.15);">🚀</div>
            <div class="fw-bold" style="font-size:1.15rem; letter-spacing:0.02em;">Startup India<br><span style="font-size:0.85rem; font-weight:normal; opacity:0.9;">Check Eligibility</span></div>
        </a>
    </div>
</div>

<div class="dashboard-grid mt-2">
    <div style="grid-column: span 1;">
        <div class="card glassmorphism">
            <?php if($is_logged_in): ?>
                <h3>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h3>
                <p class="text-muted mb-4">Here is your customized scheme platform overview.</p>
                
                <div class="mb-4 p-3" style="background: rgba(255,255,255,0.5); border-radius: 12px; border: 1px solid rgba(255,255,255,0.8);">
                    <h4 class="text-primary mb-3" style="font-size: 1.1rem;">👤 Profile Snapshot</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem;" class="text-sm">
                        <div><strong>State:</strong> <?= htmlspecialchars($profile['state'] ?? 'Pending') ?></div>
                        <div><strong>District:</strong> <?= htmlspecialchars($profile['district'] ?? 'Pending') ?></div>
                        <div><strong>Category:</strong> <?= htmlspecialchars($profile['category'] ?? 'Pending') ?></div>
                        <div><strong>Income:</strong> <?= htmlspecialchars($profile['income_range'] ?? 'Pending') ?></div>
                        <div style="grid-column: span 2;"><strong>Primary Interest:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($profile['looking_for_module'] ?? 'None') ?></span></div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4>Profile Completeness</h4>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $completeness ?>%;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;" class="mt-2">
                        <p class="text-sm" style="margin:0; font-weight:bold;"><?= $completeness ?>% Complete</p>
                        <a href="profile.php" class="btn-outline text-sm" style="padding: 0.3rem 0.8rem;">Update Profile</a>
                    </div>
                </div>
                
                <div class="mt-5">
                    <h4>❤️ Saved Schemes</h4>
                    <div id="savedSchemesList" class="mt-3">
                        <?php
                        $stmt = $pdo->prepare("SELECT s.* FROM saved_schemes ss JOIN schemes s ON ss.scheme_id = s.id WHERE ss.user_id = ? ORDER BY ss.created_at DESC");
                        $stmt->execute([$userId]);
                        $saved = $stmt->fetchAll();
                        if(count($saved) > 0) {
                            foreach($saved as $ss) {
                                echo '<div class="mb-3 p-3" style="background: rgba(255,255,255,0.7); border-radius: 12px; border: 1px solid #e5e7eb;">';
                                echo '<div class="text-xs fw-bold mb-1" style="display:flex; justify-content:space-between; color:var(--primary-color);">';
                                echo '<span>'.htmlspecialchars($ss['module_category'] ?? 'General').' Module</span>';
                                echo '</div>';
                                echo '<strong style="font-size:1.1rem;"><a href="scheme_detail.php?id='.$ss['id'].'" class="text-main" style="text-decoration:none;">'.htmlspecialchars($ss['name']).'</a></strong>';
                                echo '<div class="mt-2 text-sm text-muted">🏛️ '.htmlspecialchars($ss['ministry']).'</div>';
                                echo '<div class="mt-1 text-sm font-weight-bold">💰 '.htmlspecialchars($ss['benefits']).'</div>';
                                echo '<button onclick="saveScheme('.$ss['id'].')" class="btn-outline text-xs mt-3" style="padding: 0.2rem 0.5rem; color:#ef4444; border-color:#ef4444;">Remove Save</button>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="text-muted text-sm mt-2">You haven\'t saved any schemes yet. Browse schemes to add them here.</p>';
                        }
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <h3>Welcome to SmartScheme Dashboard</h3>
                <p class="text-muted mb-4">You are browsing the dashboard as a guest.</p>
                <div class="mb-4 p-4 text-center" style="background: rgba(255,255,255,0.5); border-radius: 12px; border: 1px solid rgba(255,255,255,0.8);">
                    <h4 class="text-primary mb-2">Unlock Personalized Schemes</h4>
                    <p class="text-sm text-muted mb-4">Create a free account to track your AI profile matches and save your favorite schemes securely to your dashboard.</p>
                    <a href="register.php" class="btn-primary" style="display:inline-block; text-decoration:none; width: 100%;">Create Free Account</a>
                    <p class="text-xs mt-3">Already registered? <a href="login.php" class="text-primary fw-bold">Log in here</a></p>
                </div>
                <div style="margin-top:2rem;">
                    <h4>Recent Updates</h4>
                    <p class="text-sm text-muted mb-2">✅ AI Recommendation Engine v1.0 Live</p>
                    <p class="text-sm text-muted mb-2">✅ Deep Profile Analysis Added</p>
                    <p class="text-sm text-muted mb-2">✅ Healthcare & Agriculture Modules Expanded</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="grid-column: span 1;">
        <div class="card glassmorphism" style="min-height: 400px;">
            <?php if($is_logged_in): ?>
                <h3>🎯 Top Personalized Recommendations</h3>
                <?php if($completeness < 30): ?>
                    <div class="alert-error mt-4">
                        Your profile is incomplete. Please update your profile to get personalized recommendations.
                    </div>
                <?php else: ?>
                    <div id="recommendations-container" class="mt-4">
                        <div id="rec-loading" class="text-center text-muted">Calculating AI Matches...</div>
                        <div id="rec-list" style="display:none;" class="grid-schemes" style="grid-template-columns: 1fr;"></div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <h3 style="margin-bottom:0;">🔥 Trending Schemes Right Now</h3>
                <p class="text-sm text-muted mt-1">These are the most popular schemes globally.</p>
                <div class="mt-3" style="display:flex; flex-direction:column; gap:1rem;">
                    <?php
                    $trending = $pdo->query("SELECT * FROM schemes ORDER BY popularity DESC LIMIT 4")->fetchAll();
                    foreach($trending as $t): ?>
                        <div style="padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 12px; background: rgba(255,255,255,0.8); position:relative;">
                            <div style="position:absolute; right:1.5rem; top:1.5rem; color:var(--primary-color); font-size:0.8rem; font-weight:bold; background:#dbeafe; padding:4px 8px; border-radius:4px;"><?= htmlspecialchars($t['module_category'] ?? 'General') ?></div>
                            <h4 class="text-primary" style="margin-right:80px; margin-bottom:0.5rem;"><a href="scheme_detail.php?id=<?= $t['id'] ?>" style="text-decoration:none;"><?= htmlspecialchars($t['name']) ?></a></h4>
                            <div class="text-xs text-muted mb-2">🏛️ <?= htmlspecialchars($t['ministry']) ?></div>
                            <div class="benefits-tag mt-2">💰 <?= htmlspecialchars($t['benefits']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4" style="margin-bottom:1rem;">
                    <a href="schemes.php" class="btn-outline text-sm" style="padding: 0.5rem 2rem;">Browse All Schemes</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
<?php if($is_logged_in && $completeness >= 30): ?>
async function loadRecommendations() {
    const res = await fetch('api/recommend.php');
    const response = await res.json();
    
    document.getElementById('rec-loading').style.display = 'none';
    const list = document.getElementById('rec-list');
    list.style.display = 'flex';
    list.style.flexDirection = 'column';
    list.style.gap = '1.5rem';
    
    if(response.data && response.data.length > 0) {
        let html = '';
        response.data.forEach(item => {
            html += `
            <div style="padding: 1.5rem; border: 1px solid #e5e7eb; border-radius: 12px; background: rgba(255,255,255,0.8); position:relative;">
                <div style="position:absolute; right:1.5rem; top:1.5rem; font-weight:bold; color:var(--secondary-color);">Match: ${item.match_percent}%</div>
                <h4 class="text-primary" style="margin-right:80px;"><a href="scheme_detail.php?id=${item.scheme.id}">${item.scheme.name}</a></h4>
                <p class="text-sm text-muted mb-2">${item.reason}</p>
                <div class="benefits-tag mt-2 mb-2">💰 ${item.scheme.benefits}</div>
                <div class="mt-2 text-right">
                    <button onclick="saveScheme(${item.scheme.id})" class="btn-outline text-sm" style="padding: 0.3rem 0.8rem;">❤️ Save</button>
                    <a href="scheme_detail.php?id=${item.scheme.id}" class="btn-primary text-sm" style="padding: 0.3rem 0.8rem;">View</a>
                </div>
            </div>`;
        });
        list.innerHTML = html;
    } else {
        list.innerHTML = '<p class="text-muted">No specific recommendations right now. Expand your profile criteria.</p>';
    }
}

async function saveScheme(id) {
    const formData = new FormData();
    formData.append('scheme_id', id);
    const res = await fetch('api/save_scheme.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'success') {
        location.reload(); // Refresh to show saved scheme
    } else {
        alert(data.message);
    }
}

loadRecommendations();
<?php endif; ?>
</script>
<?php require_once 'includes/footer.php'; ?>
