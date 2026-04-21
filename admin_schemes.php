<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireAdmin();

if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM schemes WHERE id = ?")->execute([$delId]);
    header("Location: admin_schemes.php");
    exit;
}

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM schemes WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND name LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schemes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Schemes - Admin Hub</title>
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
            content: '📝';
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
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
        }
        .admin-table th {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .admin-table th:first-child { border-top-left-radius: var(--radius-lg); }
        .admin-table th:last-child { border-top-right-radius: var(--radius-lg); }
        
        .admin-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            transition: background 0.2s;
        }
        .admin-table tr:hover td {
            background: #f8fafc;
        }
        .admin-table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="admin-hero">
                    <span style="display:inline-block; background:rgba(255,255,255,0.15); backdrop-filter:blur(5px); padding:0.35rem 1rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase; color: #94a3b8;">Operations Registry</span>
                    <h1 class="page-title" style="color: white; font-size: 2.8rem; margin-bottom: 0.5rem; line-height: 1.2;">Manage Schemes</h1>
                    <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 600px; line-height: 1.5;">Oversee the live database of government schemes. View analytics, edit policies, or revoke outdated initiatives.</p>
                </div>
            </header>

            <div class="filter-bar animate-fade-up delay-100">
                <form method="GET" style="display: flex; gap: 0.5rem; flex: 1; max-width: 500px;">
                    <div style="flex: 1; position: relative;">
                        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.4;">🔍</span>
                        <input type="text" name="search" class="form-control" placeholder="Search operational schemes..." value="<?= htmlspecialchars($search) ?>" style="padding-left: 2.5rem; margin-bottom: 0;">
                    </div>
                    <button type="submit" class="btn btn-primary">Find</button>
                    <?php if($search): ?>
                        <a href="admin_schemes.php" class="btn btn-outline" style="background: white;">Clear</a>
                    <?php endif; ?>
                </form>
                
                <button class="btn btn-primary" style="background: #10b981; border-color: #10b981;" onclick="alert('Module Integration Pending: Scheme Creation Engine.');">+ Create New Scheme</button>
            </div>

            <div class="card animate-fade-up delay-200" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>UID Focus</th>
                                <th>Scheme Name & Details</th>
                                <th>Demographics</th>
                                <th>Live Status</th>
                                <th style="text-align: right;">Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($schemes as $s): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #cbd5e1; font-size: 1rem; font-family: monospace;">
                                        #<?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 1.05rem; margin-bottom: 0.25rem;">
                                        <?= htmlspecialchars($s['name']) ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem; display: flex; gap: 0.5rem; align-items: center;">
                                        <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;"><?= htmlspecialchars($s['category']) ?></span>
                                        <span class="badge badge-green" style="background: #ecfdf5; color: #047857; border: 1px solid #d1fae5;"><?= htmlspecialchars($s['state']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div style="color: #475569; font-size: 0.85rem;">
                                        <strong>Age:</strong> <?= ($s['min_age']==0 && $s['max_age']==200) ? 'Any' : $s['min_age'].'-'.$s['max_age'] ?> | 
                                        <strong>Caste:</strong> <?= htmlspecialchars($s['target_category']) ?>
                                    </div>
                                    <div style="color: #475569; font-size: 0.85rem; margin-top: 0.25rem;">
                                        <strong>Income:</strong> <?= $s['max_income'] ? '≤ ₹'.number_format($s['max_income']) : 'No Limit' ?> | 
                                        <strong>Gender:</strong> <?= htmlspecialchars($s['gender'] ?? 'All') ?>
                                    </div>
                                </td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; font-weight: 600; color: #10b981;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Active
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="scheme_details.php?id=<?= $s['id'] ?>" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; margin-right: 0.5rem; background: white;">View</a>
                                    <a href="?delete=<?= $s['id'] ?>" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('WARNING: Are you sure you wish to pull this scheme offline and delete its records?');">Revoke</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(empty($schemes)): ?>
                    <div style="padding: 5rem 2rem; text-align: center;">
                        <span style="font-size: 4rem; opacity: 0.2; margin-bottom: 1rem; display: block;">📂</span>
                        <h3 style="color: #475569;">No active schemes registered.</h3>
                        <p style="color: #94a3b8;">Click "+ Create New Scheme" to begin building the operational database.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
