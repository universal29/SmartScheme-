<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireAdmin();

// Handle Delete (Only if resolved)
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    // Ensures only resolved tickets can be deleted
    $pdo->prepare("DELETE FROM support_tickets WHERE id = ? AND status = 'resolved'")->execute([$delId]);
    header("Location: admin_support.php");
    exit;
}

$stmt = $pdo->query("
    SELECT t.*, u.full_name, u.email 
    FROM support_tickets t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.status ASC, t.created_at DESC
");
$tickets = $stmt->fetchAll();

// Count Open Tickets for metrics
$openCount = 0;
foreach($tickets as $t) {
    if($t['status'] == 'open') $openCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Hub - Admin</title>
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
            content: '💬';
            position: absolute;
            right: 10px;
            bottom: -50px;
            font-size: 14rem;
            opacity: 0.05;
            transform: rotate(-15deg);
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
            vertical-align: top;
            transition: background 0.2s;
        }
        .admin-table tr:hover td {
            background: #f8fafc;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #f59e0b; /* Amber */
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            animation: pulse-dot-anim 1.5s infinite;
        }
        @keyframes pulse-dot-anim {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="admin-hero">
                    <span style="display:inline-block; background:rgba(255,255,255,0.15); backdrop-filter:blur(5px); padding:0.35rem 1rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase; color: #94a3b8;">Citizen Relations</span>
                    <h1 class="page-title" style="color: white; font-size: 2.8rem; margin-bottom: 0.5rem; line-height: 1.2;">Support Tickets</h1>
                    <p style="color: #cbd5e1; font-size: 1.15rem; max-width: 600px; line-height: 1.5;">Manage citizen inquiries, resolve technical platform hitches, and answer scheme-related queries directly.</p>
                </div>
            </header>

            <div class="animate-fade-up delay-100" style="margin-bottom: 1.5rem; font-weight: 600; color: #334155;">
                System Backlog: <span style="color: #ef4444;"><?= $openCount ?> open queries</span>
            </div>

            <div class="card animate-fade-up delay-200" style="padding: 0; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Ticket Details</th>
                                <th>Citizen Information</th>
                                <th>Issue Context</th>
                                <th>Current Status</th>
                                <th style="text-align: right;">Administrative Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tickets as $t): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #cbd5e1; font-size: 1rem; font-family: monospace;">
                                        #<?= str_pad($t['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.5rem;">
                                        <?= date('M d, Y', strtotime($t['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 1rem; margin-bottom: 0.25rem;">
                                        <?= htmlspecialchars($t['full_name']) ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #64748b;">
                                        <?= htmlspecialchars($t['email']) ?>
                                    </div>
                                </td>
                                <td style="max-width: 350px;">
                                    <strong style="color: #0f172a; display: block; margin-bottom: 0.35rem; font-size: 1.05rem;"><?= htmlspecialchars($t['subject']) ?></strong>
                                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0;">
                                        <?= htmlspecialchars($t['message']) ?>
                                    </p>
                                </td>
                                <td>
                                    <?php if($t['status'] == 'open'): ?>
                                        <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                            <span class="pulse-dot" style="margin-right: 0.2rem;"></span> OPEN
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: #ecfdf5; color: #047857; border: 1px solid #d1fae5;">
                                            RESOLVED
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if($t['status'] == 'open'): ?>
                                        <a href="admin_ticket_reply.php?id=<?= $t['id'] ?>" class="btn btn-primary" style="background: #3b82f6; border-color: #3b82f6; box-shadow: 0 2px 4px rgba(59,130,246,0.3); padding: 0.4rem 1rem; font-size: 0.85rem;">Resolve &rarr;</a>
                                    <?php else: ?>
                                        <a href="admin_ticket_reply.php?id=<?= $t['id'] ?>" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem; background: white; margin-right: 0.5rem;">Read Log</a>
                                        <a href="?delete=<?= $t['id'] ?>" class="btn btn-outline" style="color: #ef4444; border-color: #fecaca; padding: 0.4rem 1rem; font-size: 0.85rem; background: #fef2f2;" onclick="return confirm('WARNING: Are you sure you wish to permanently clear this resolved ticket log?');">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(empty($tickets)): ?>
                    <div style="padding: 5rem 2rem; text-align: center;">
                        <span style="font-size: 4rem; opacity: 0.2; margin-bottom: 1rem; display: block;">🍃</span>
                        <h3 style="color: #475569;">No support tickets on record.</h3>
                        <p style="color: #94a3b8;">The citizen support channel is currently empty.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
