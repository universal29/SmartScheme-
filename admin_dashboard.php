<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireAdmin();

// Fetch metrics
$stmtUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'");
$totalUsers = $stmtUsers->fetchColumn();

$stmtSchemes = $pdo->query("SELECT COUNT(*) FROM schemes");
$totalSchemes = $stmtSchemes->fetchColumn();

$stmtSaves = $pdo->query("SELECT COUNT(*) FROM saved_schemes");
$totalSaves = $stmtSaves->fetchColumn();

$stmtTickets = $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status='open'");
$openTickets = $stmtTickets->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: white;
            padding: 3.5rem 2.5rem;
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.5);
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }
        .admin-hero::after {
            content: '⚙️';
            position: absolute;
            right: 20px;
            bottom: -50px;
            font-size: 14rem;
            opacity: 0.05;
            transform: rotate(-15deg);
        }
        
        .metric-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: var(--radius-lg);
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .metric-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
            line-height: 1;
            letter-spacing: -2px;
        }
        
        .quick-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; }
        .bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; }
        .bg-gradient-amber { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color: white; }
        .bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; }
        
        .action-btn:hover {
            transform: scale(1.02);
            opacity: 0.95;
            color: white;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="admin-hero">
                    <span style="display:inline-block; background:rgba(255,255,255,0.15); backdrop-filter:blur(5px); padding:0.35rem 1rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase; color: #94a3b8;">Command Center</span>
                    <h1 class="page-title" style="color: white; font-size: 2.8rem; margin-bottom: 0.5rem; line-height: 1.2;">System Analytics</h1>
                    <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 600px; line-height: 1.5;">Monitor platform traffic, manage the citizen database, and oversee the government operational repository.</p>
                </div>
            </header>

            <h3 class="animate-fade-up delay-100" style="margin-bottom: 1.5rem; color: #334155;">Live Platform Metrics</h3>
            <div class="scheme-grid animate-fade-up delay-100">
                <div class="card metric-card" style="border-top: 4px solid #3b82f6;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">👥</div>
                    <h3 style="color: #64748b; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Registered Citizens</h3>
                    <div class="metric-number" style="color: #1e293b;"><?= $totalUsers ?></div>
                    <p style="color: #94a3b8; font-size: 0.9rem;">Active profile accounts</p>
                </div>
                
                <div class="card metric-card" style="border-top: 4px solid #10b981;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏛️</div>
                    <h3 style="color: #64748b; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Active Schemes</h3>
                    <div class="metric-number" style="color: #10b981;"><?= $totalSchemes ?></div>
                    <p style="color: #94a3b8; font-size: 0.9rem;">Government programs indexed</p>
                </div>
                
                <div class="card metric-card" style="border-top: 4px solid #f59e0b;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📌</div>
                    <h3 style="color: #64748b; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Total Bookmarks</h3>
                    <div class="metric-number" style="color: #f59e0b;"><?= $totalSaves ?></div>
                    <p style="color: #94a3b8; font-size: 0.9rem;">Schemes saved by citizens</p>
                </div>
            </div>

            <div class="animate-fade-up delay-200" style="margin-top: 3rem;">
                <h3 style="margin-bottom: 0.5rem; color: #334155;">Quick Operational Actions</h3>
                <p class="text-muted" style="margin-bottom: 1rem;">Jump directly to administrative hubs to manage the platform.</p>
                
                <div class="quick-action-grid">
                    <a href="admin_users.php" class="action-btn bg-gradient-blue">
                        <span>👥</span> Manage Citizens
                    </a>
                    <a href="admin_schemes.php" class="action-btn bg-gradient-green">
                        <span>📝</span> Platform Schemes
                    </a>
                    <a href="admin_support.php" class="action-btn <?= $openTickets > 0 ? 'bg-gradient-amber' : 'bg-gradient-purple' ?>" style="position: relative;">
                        <span>💬</span> Help Desk
                        <?php if($openTickets > 0): ?>
                            <span style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                <?= $openTickets ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
