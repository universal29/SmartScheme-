<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireAdmin();

// Handle Delete/Block
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId != $_SESSION['user_id']) { // Don't delete self
        $stmtDel = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmtDel->execute([$delId]);
    }
    header("Location: admin_users.php");
    exit;
}

$search = $_GET['search'] ?? '';
$query = "SELECT id, full_name, email, role, created_at FROM users WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Citizens - Admin Hub</title>
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
            content: '👥';
            position: absolute;
            right: 10px;
            bottom: -30px;
            font-size: 12rem;
            opacity: 0.05;
            transform: rotate(-10deg);
        }
        
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255,255,255,0.6);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            max-width: 600px;
        }

        .user-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }
        .user-table th {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .user-table th:first-child { border-top-left-radius: var(--radius-lg); }
        .user-table th:last-child { border-top-right-radius: var(--radius-lg); }
        
        .user-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            transition: background 0.2s;
        }
        .user-table tr:hover td {
            background: #f8fafc;
        }
        .user-table tr:last-child td { border-bottom: none; }
        
        .avatar-mini {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }
        
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="admin-hero">
                    <span style="display:inline-block; background:rgba(255,255,255,0.15); backdrop-filter:blur(5px); padding:0.35rem 1rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase; color: #94a3b8;">User Database</span>
                    <h1 class="page-title" style="color: white; font-size: 2.8rem; margin-bottom: 0.5rem; line-height: 1.2;">Manage Citizens</h1>
                    <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 600px; line-height: 1.5;">Monitor and administer registered citizen accounts, assign roles, and revoke platform access if necessary.</p>
                </div>
            </header>

            <form method="GET" class="filter-bar animate-fade-up delay-100">
                <div style="flex: 1; position: relative;">
                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.4;">🔍</span>
                    <input type="text" name="search" class="form-control" placeholder="Search accounts by name or email..." value="<?= htmlspecialchars($search) ?>" style="padding-left: 2.5rem; margin-bottom: 0;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0 1.5rem;">Lookup</button>
                <?php if($search): ?>
                    <a href="admin_users.php" class="btn btn-outline" style="background: white;">Clear</a>
                <?php endif; ?>
            </form>

            <div class="card animate-fade-up delay-200" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                <div style="overflow-x: auto;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Citizen</th>
                                <th>Account Role</th>
                                <th>Registration Date</th>
                                <th style="text-align: right;">Administrative Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                                <?php 
                                    // Generate initials for avatar
                                    $words = explode(" ", trim($u['full_name']));
                                    $initials = strtoupper(mb_substr($words[0], 0, 1));
                                    if(count($words) > 1) {
                                        $initials .= strtoupper(mb_substr(end($words), 0, 1));
                                    } elseif(strlen($words[0]) > 1) {
                                        $initials .= strtoupper(mb_substr($words[0], 1, 1));
                                    }
                                ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div class="avatar-mini" style="<?= $u['role'] == 'admin' ? 'background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);' : '' ?>">
                                            <?= $initials ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 1.05rem;"><?= htmlspecialchars($u['full_name']) ?></div>
                                            <div style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($u['email']) ?> <span style="color: #cbd5e1;">(ID: <?= $u['id'] ?>)</span></div>

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="<?= $u['role'] == 'admin' ? 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;' : 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;' ?>">
                                        <?= strtoupper(htmlspecialchars($u['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="color: #475569; font-size: 0.95rem;">
                                        <?= date('M d, Y', strtotime($u['created_at'])) ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;">
                                        <?= date('h:i A', strtotime($u['created_at'])) ?>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <?php if($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?= $u['id'] ?>" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 0.4rem 1rem; font-size: 0.85rem;" onclick="return confirm('WARNING: Are you sure you wish to permanently delete this citizen record? This action cannot be undone.');">Revoke Access & Delete</a>
                                    <?php else: ?>
                                        <span style="font-size: 0.85rem; color: #94a3b8; font-style: italic;">Current Session</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(empty($users)): ?>
                    <div style="padding: 5rem 2rem; text-align: center;">
                        <span style="font-size: 4rem; opacity: 0.2; margin-bottom: 1rem; display: block;">🚫</span>
                        <h3 style="color: #475569;">No citizens found matching your query.</h3>
                        <p style="color: #94a3b8;">Return to the main directory or try a different spelling.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
