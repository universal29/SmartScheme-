<?php
require_once 'includes/config.php';
require_once 'includes/config.php';
require_once 'includes/session.php';
// Publicly accessible route

$search = $_GET['search'] ?? '';
$filterCategory = $_GET['category'] ?? '';

$query = "SELECT * FROM schemes WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filterCategory) {
    $query .= " AND category = ?";
    $params[] = $filterCategory;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schemes = $stmt->fetchAll();

$categoriesList = ['Education', 'Health', 'Agriculture', 'Women', 'Senior Citizen', 'Employment'];

$savedIds = [];
if (isLoggedIn()) {
    $stmtSaved = $pdo->prepare("SELECT scheme_id FROM saved_schemes WHERE user_id = ?");
    $stmtSaved->execute([$_SESSION['user_id']]);
    $savedIds = $stmtSaved->fetchAll(PDO::FETCH_COLUMN);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Schemes - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 3rem 2.5rem;
            border-radius: var(--radius-xl);
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-header::after {
            content: '🏛️';
            position: absolute;
            right: -20px;
            bottom: -40px;
            font-size: 10rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        }
        .filter-bar input, .filter-bar select {
            flex: 1;
            margin-bottom: 0;
            border: 1px solid #e2e8f0;
            background: #fff;
        }
        .search-icon-wrapper {
            position: relative;
            flex: 2;
        }
        .search-icon-wrapper input {
            padding-left: 2.5rem;
            width: 100%;
        }
        .search-icon-wrapper::before {
            content: '🔍';
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            opacity: 0.5;
        }

        /* Offcanvas Panel CSS */
        .offcanvas-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        .offcanvas-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .offcanvas-panel {
            position: fixed;
            top: 0; right: -600px;
            width: 100%;
            max-width: 550px;
            height: 100vh;
            background: #ffffff;
            z-index: 1001;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }
        .offcanvas-panel.active {
            right: 0;
        }
        .offcanvas-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        .offcanvas-body {
            padding: 2rem;
            overflow-y: auto;
            flex: 1;
            position: relative;
        }
        .offcanvas-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            gap: 1rem;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .close-btn:hover { background: #e2e8f0; color: #0f172a; }
        
        /* Blur class for guests */
        .blur-content {
            filter: blur(8px);
            opacity: 0.4;
            pointer-events: none;
            user-select: none;
        }
        .auth-lock {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: rgba(255,255,255,0.85);
            z-index: 10;
            text-align: center;
            padding: 2rem;
        }
        
        .info-card {
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            border: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }
        .info-label { display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 0.25rem; }
        .info-value { font-weight: 700; color: #1e293b; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="hero-header">
                    <span style="display:inline-block; background:rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding:0.25rem 0.75rem; border-radius:999px; font-size:0.8rem; font-weight:600; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase;">Master Database</span>
                    <h1 class="page-title" style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem; line-height: 1.1;">All Schemes Repository</h1>
                    <p style="color: #cbd5e1; font-size: 1.1rem; max-width: 650px; line-height: 1.5;">Browse the comprehensive catalog of government initiatives. Filter by category or search by keywords to find exactly what you're looking for.</p>
                </div>
            </header>

            <form method="GET" class="filter-bar animate-fade-up delay-100">
                <div class="search-icon-wrapper">
                    <input type="text" name="search" class="form-control" placeholder="Search schemes by name or keywords..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="category" class="form-control">
                    <option value="">All Functional Categories</option>
                    <?php foreach($categoriesList as $cat): ?>
                        <option value="<?= $cat ?>" <?= $filterCategory == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" style="padding: 0 2rem; font-weight: 600;">Search</button>
                <?php if($search || $filterCategory): ?>
                    <a href="schemes.php" class="btn btn-outline" style="background: white;">Clear Filters</a>
                <?php endif; ?>
            </form>

            <div class="scheme-grid animate-fade-up delay-200">
                <?php 
                    $delayCounter = 200;
                    foreach($schemes as $scheme): 
                        $isSaved = in_array($scheme['id'], $savedIds); 
                ?>
                <div class="card scheme-card" style="animation-delay: <?= $delayCounter ?>ms;">
                    <div class="scheme-card-body">
                        <div class="scheme-meta">
                            <span class="badge"><?= htmlspecialchars($scheme['category']) ?></span>
                            <span class="badge badge-green"><?= htmlspecialchars($scheme['state']) ?></span>
                        </div>
                        <h3 class="scheme-title" style="margin-bottom: 0.75rem; font-size: 1.2rem; line-height: 1.3;"><?= htmlspecialchars($scheme['name']) ?></h3>
                        <p class="scheme-desc" style="color: #64748b; line-height: 1.5;"><?= htmlspecialchars($scheme['description']) ?></p>
                    </div>
                    <div class="scheme-actions">
                        <?php if(!isLoggedIn()): ?>
                            <a href="auth_login.php" class="btn btn-outline" style="flex: 1; text-align:center; text-decoration:none;">Save</a>
                        <?php elseif($isSaved): ?>
                            <button class="btn btn-primary" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Saved</button>
                        <?php else: ?>
                            <button class="btn btn-outline" style="flex: 1;" onclick="toggleSaveScheme(<?= $scheme['id'] ?>, this)">Save</button>
                        <?php endif; ?>
                        <button class="btn btn-primary" style="flex: 1;" onclick="openSchemePanel(<?= $scheme['id'] ?>)">Details &rarr;</button>
                    </div>
                </div>
                <?php 
                    $delayCounter += 50;
                    endforeach; 
                ?>
            </div>
            
            <?php if(empty($schemes)): ?>
                <div class="text-center mt-4 animate-fade-up delay-200" style="padding: 4rem 2rem;">
                    <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">📭</div>
                    <h3 class="text-muted" style="margin-bottom: 0.5rem;">No schemes found matching your criteria.</h3>
                    <p style="color: var(--text-muted);">Try adjusting your search terms or clearing your current filters.</p>
                    <a href="schemes.php" class="btn btn-primary mt-2">Clear All Filters</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Offcanvas Overlay & Panel -->
    <div class="offcanvas-overlay" onclick="closeSchemePanel()"></div>
    <div class="offcanvas-panel" id="schemePanel">
        <div class="offcanvas-header">
            <h2 id="oc_title" style="margin: 0; font-size: 1.25rem; color: #0f172a;">Loading...</h2>
            <button class="close-btn" onclick="closeSchemePanel()">&times;</button>
        </div>
        
        <div class="offcanvas-body">
            <!-- Loading State -->
            <div id="oc_loader" style="text-align: center; padding: 3rem;">
                <div style="font-size: 2rem; animation: spin 1s linear infinite;">🔄</div>
                <p style="color: #64748b; margin-top: 1rem;">Fetching scheme matrix...</p>
            </div>

            <!-- Content State -->
            <div id="oc_content" style="display: none;">
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <span class="badge" id="oc_category"></span>
                    <span class="badge badge-green" id="oc_state"></span>
                </div>
                
                <p id="oc_desc" style="color: #475569; line-height: 1.6; margin-bottom: 2rem; font-size: 1.05rem;"></p>
                
                <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Eligibility & Constraints</h3>
                
                <div style="position: relative;">
                    <div id="oc_constraints">
                        <div class="info-card"><span class="info-label">Geographic Constraint</span><span class="info-value" id="oc_geo"></span></div>
                        <div class="info-card"><span class="info-label">Age Requirements</span><span class="info-value" id="oc_age"></span></div>
                        <div class="info-card"><span class="info-label">Max Family Income</span><span class="info-value" id="oc_income"></span></div>
                        <div class="info-card"><span class="info-label">Social Category</span><span class="info-value" id="oc_target"></span></div>
                    </div>
                
                    <!-- Guest Lock Overlay (injected via JS if not logged in) -->
                    <div id="oc_lock" class="auth-lock" style="display: none;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
                        <h3 style="margin-bottom: 0.5rem;">Restricted Information</h3>
                        <p style="color: #64748b; margin-bottom: 1.5rem;">Please log in to unlock this eligibility matrix.</p>
                        <a href="auth_login.php" class="btn btn-primary" style="width: 100%;">Sign In Securely</a>
                        <a href="auth_register.php" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem;">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="offcanvas-footer" id="oc_footer" style="display: none;">
            <button id="oc_save_btn" class="btn btn-outline" style="flex: 1;" onclick="">Save</button>
            <button id="oc_apply_btn" class="btn btn-primary" style="flex: 1; background: #10b981; border-color: #10b981;" onclick="">Apply via AI Assistant &rarr;</button>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        const overlay = document.querySelector('.offcanvas-overlay');
        const panel = document.getElementById('schemePanel');
        
        async function openSchemePanel(id) {
            // Show offcanvas UI
            overlay.classList.add('active');
            panel.classList.add('active');
            document.body.style.overflow = 'hidden'; // prevent bg scroll
            
            // Reset UI to loading
            document.getElementById('oc_title').textContent = "Loading Schema...";
            document.getElementById('oc_loader').style.display = 'block';
            document.getElementById('oc_content').style.display = 'none';
            document.getElementById('oc_footer').style.display = 'none';
            document.getElementById('oc_lock').style.display = 'none';
            document.getElementById('oc_constraints').classList.remove('blur-content');

            try {
                // Fetch data
                const response = await fetch('api/get_scheme.php?id=' + id);
                const data = await response.json();
                
                if (data.success) {
                    const s = data.scheme;
                    
                    // Populate Header & Body
                    document.getElementById('oc_title').textContent = s.name;
                    document.getElementById('oc_category').textContent = s.category;
                    document.getElementById('oc_state').textContent = s.state;
                    document.getElementById('oc_desc').textContent = s.description;
                    
                    // Constraints
                    document.getElementById('oc_geo').textContent = (s.state === 'All' || s.state === 'Central') ? 'Nationwide / Central' : s.state + ' Only';
                    document.getElementById('oc_age').textContent = (s.min_age == 0 && s.max_age == 200) ? 'No Age Limit' : s.min_age + ' to ' + s.max_age + ' years';
                    document.getElementById('oc_income').textContent = s.max_income ? '₹' + parseFloat(s.max_income).toLocaleString() : 'No Income Limit';
                    document.getElementById('oc_target').textContent = s.target_category;

                    // Display Logic Based on Auth
                    if (!data.is_logged_in) {
                        document.getElementById('oc_constraints').classList.add('blur-content');
                        document.getElementById('oc_lock').style.display = 'flex';
                        
                        // Footer buttons for guests
                        document.getElementById('oc_save_btn').textContent = 'Save';
                        document.getElementById('oc_save_btn').onclick = () => window.location.href='auth_login.php';
                        document.getElementById('oc_apply_btn').style.display = 'none';
                    } else {
                        // Footer buttons for logged-in
                        document.getElementById('oc_save_btn').textContent = data.is_saved ? 'Saved' : 'Save to Bookmarks';
                        document.getElementById('oc_save_btn').onclick = function() { toggleSaveScheme(s.id, this); };
                        
                        document.getElementById('oc_apply_btn').style.display = 'flex';
                        document.getElementById('oc_apply_btn').onclick = () => window.location.href = 'apply_scheme.php?id=' + s.id;
                    }

                    // Swap visibility
                    document.getElementById('oc_loader').style.display = 'none';
                    document.getElementById('oc_content').style.display = 'block';
                    document.getElementById('oc_footer').style.display = 'flex';
                } else {
                    document.getElementById('oc_title').textContent = "Error";
                    document.getElementById('oc_desc').textContent = data.error;
                    document.getElementById('oc_loader').style.display = 'none';
                    document.getElementById('oc_content').style.display = 'block';
                }
            } catch (e) {
                console.error(e);
            }
        }

        function closeSchemePanel() {
            overlay.classList.remove('active');
            panel.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && panel.classList.contains('active')) {
                closeSchemePanel();
            }
        });
    </script>
</body>
</html>
