<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireAdmin();

$ticketId = $_GET['id'] ?? 0;
if (!$ticketId) {
    header("Location: admin_support.php");
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, u.full_name, u.email FROM support_tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header("Location: admin_support.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $reply = trim($_POST['reply']);
    
    if (empty($reply)) {
        $error = "Reply message cannot be empty.";
    } else {
        $updateStmt = $pdo->prepare("UPDATE support_tickets SET admin_reply = ?, status = 'resolved' WHERE id = ?");
        if ($updateStmt->execute([$reply, $ticketId])) {
            header("Location: admin_support.php?msg=replied");
            exit;
        } else {
            $error = "Failed to submit reply.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handle Ticket - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="page-header">
                <div>
                    <h1 class="page-title">Handle Support Ticket #<?= $ticket['id'] ?></h1>
                    <a href="admin_support.php" style="color: var(--text-muted); font-size: 0.9rem;">&larr; Back to Tickets</a>
                </div>
            </header>

            <div class="card" style="max-width: 800px;">
                <div style="background: var(--bg-main); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <strong>Citizen:</strong> <?= htmlspecialchars($ticket['full_name']) ?> (<?= htmlspecialchars($ticket['email']) ?>)<br>
                            <strong>Date:</strong> <?= date('F d, Y h:i A', strtotime($ticket['created_at'])) ?>
                        </div>
                        <div>
                            <span class="badge <?= $ticket['status'] == 'open' ? 'badge-yellow' : 'badge-green' ?>">
                                <?= ucfirst($ticket['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div style="margin-bottom: 0.5rem; font-size: 1.1rem; font-weight: 600;">
                        Subject: <?= htmlspecialchars($ticket['subject']) ?>
                    </div>
                    <div style="color: var(--text-main); font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($ticket['message'])) ?>
                    </div>
                </div>

                <?php if($ticket['status'] == 'open' || empty($ticket['admin_reply'])): ?>
                    <h3>Your Reply</h3>
                    <?php if ($error): ?>
                        <div style="background: #fee2e2; color: #ef4444; padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="form-group">
                            <textarea name="reply" class="form-control" rows="6" required placeholder="Type your response to the citizen here..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Reply & Resolve Ticket</button>
                    </form>
                <?php else: ?>
                    <h3>Your Previous Reply</h3>
                    <div style="background: #e0e7ff; padding: 1.5rem; border-radius: var(--radius-md); border-left: 4px solid #4338ca;">
                        <?= nl2br(htmlspecialchars($ticket['admin_reply'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
