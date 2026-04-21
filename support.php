<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
requireLogin();

$userId = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ticket'])) {
    $subject = trim($_POST['subject']);
    $content = trim($_POST['message']);

    if (empty($subject) || empty($content)) {
        $error = "Subject and message are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$userId, $subject, $content])) {
            $message = "Your support ticket has been submitted successfully. Our team will review it shortly.";
        } else {
            $error = "Failed to submit ticket. Please try again.";
        }
    }
}

// Fetch user's existing tickets
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk - SmartScheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .support-header {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: white;
            padding: 3rem 2.5rem;
            border-radius: var(--radius-xl);
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(4, 120, 87, 0.15), 0 10px 10px -5px rgba(4, 120, 87, 0.05);
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .support-header::after {
            content: '🎧';
            position: absolute;
            right: 10px;
            bottom: -30px;
            font-size: 10rem;
            opacity: 0.1;
            transform: rotate(-10deg);
        }
        
        .ticket-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }
        
        /* Custom Scrollbar for ticket list */
        .ticket-list::-webkit-scrollbar {
            width: 6px;
        }
        .ticket-list::-webkit-scrollbar-track {
            background: #f1f5f9; 
            border-radius: 10px;
        }
        .ticket-list::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        
        .ticket-item {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            transition: all 0.2s ease;
        }
        .ticket-item:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .ticket-bubble-user {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 0 var(--radius-md) var(--radius-md) var(--radius-md);
            margin-bottom: 0.5rem;
            color: #334155;
            font-size: 0.95rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .ticket-bubble-admin {
            margin-top: 1rem;
            background: linear-gradient(to right, #eff6ff, #dbeafe);
            padding: 1rem;
            border-radius: var(--radius-md) 0 var(--radius-md) var(--radius-md);
            border: 1px solid #bfdbfe;
            position: relative;
        }
        .ticket-bubble-admin::before {
            content: 'Admin Support';
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="animate-fade-up">
                <div class="support-header">
                    <span style="display:inline-block; background:rgba(255,255,255,0.2); backdrop-filter:blur(5px); padding:0.25rem 0.75rem; border-radius:999px; font-size:0.8rem; font-weight:700; margin-bottom:1rem; letter-spacing:1px; text-transform:uppercase;">Resolution Center</span>
                    <h1 class="page-title" style="color: white; font-size: 2.5rem; margin-bottom: 0.5rem; line-height: 1.1;">Help & Support Desk</h1>
                    <p style="color: #d1fae5; font-size: 1.1rem; max-width: 650px; line-height: 1.5;">Facing an issue mapping a scheme or navigating the portal? Submit a ticket globally and our administrative team will assist you.</p>
                </div>
            </header>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Submit Ticket Form -->
                <div class="card animate-fade-up delay-100" style="align-self: start;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px dashed var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <span style="font-size: 1.5rem;">✉️</span>
                        <h3 style="margin: 0; font-size: 1.25rem;">Open a New Ticket</h3>
                    </div>
                    
                    <?php if ($message): ?>
                        <div style="background: #d1fae5; color: #047857; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div style="background: #fee2e2; color: #ef4444; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="submit_ticket" value="1">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Subject</label>
                            <input type="text" name="subject" class="form-control" required placeholder="Brief description of the issue" style="background: #f8fafc;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600;">Detailed Message</label>
                            <textarea name="message" class="form-control" rows="8" required placeholder="Please describe your issue, error code, or specific question in detail..." style="background: #f8fafc; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 1.05rem;">Submit Ticket to Admins</button>
                    </form>
                </div>

                <!-- Existing Tickets -->
                <div class="card animate-fade-up delay-200" style="background: #ffffff;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; border-bottom: 2px dashed var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <span style="font-size: 1.5rem;">🕒</span>
                        <h3 style="margin: 0; font-size: 1.25rem;">Ticket History</h3>
                    </div>
                    
                    <div class="ticket-list">
                        <?php if (empty($tickets)): ?>
                            <div style="text-center; padding: 3rem 1rem; opacity: 0.5; display: flex; flex-direction: column; align-items: center;">
                                <span style="font-size: 3rem; margin-bottom: 1rem;">🍃</span>
                                <p style="font-weight: 500;">Your ticket history is clear.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($tickets as $ticket): ?>
                                <div class="ticket-item">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                        <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600; font-family: monospace;">
                                            TICKET #<?= str_pad($ticket['id'], 4, '0', STR_PAD_LEFT) ?>
                                        </div>
                                        <span class="badge <?= $ticket['status'] == 'open' ? 'badge-yellow' : 'badge-green' ?>" style="font-size: 0.7rem;">
                                            <?= $ticket['status'] == 'open' ? '🔴 OPEN' : '🟢 RESOLVED' ?>
                                        </span>
                                    </div>
                                    
                                    <h4 style="margin-bottom: 0.75rem; color: #1e293b;"><?= htmlspecialchars($ticket['subject']) ?></h4>
                                    
                                    <div class="ticket-bubble-user">
                                        <?= nl2br(htmlspecialchars($ticket['message'])) ?>
                                        <div style="font-size: 0.7rem; color: #9ca3af; margin-top: 0.5rem; text-align: right;">
                                            Sent on <?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($ticket['admin_reply'])): ?>
                                        <div class="ticket-bubble-admin">
                                            <p style="font-size: 0.95rem; margin: 0; color: #1e3a8a;"><?= nl2br(htmlspecialchars($ticket['admin_reply'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
