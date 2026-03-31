<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once 'includes/header.php';

// Mark as resolved action
if(isset($_POST['resolve_id'])) {
    $resId = (int)$_POST['resolve_id'];
    $pdo->prepare("UPDATE support_messages SET status='resolved' WHERE id=?")->execute([$resId]);
    echo "<div class='alert-success'>Ticket officially marked as resolved.</div>";
}
?>
<div class="form-card" style="margin-top:0;">
    <h3>Citizen Support Tickets</h3>
    <p style="color:#6b7280; font-size:0.9rem; margin-bottom:1.5rem;">View and respond natively via email to citizens experiencing issues.</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Citizen Context</th>
                <th>Subject & Content</th>
                <th>Status</th>
                <th>Administration</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $tickets = $pdo->query("SELECT * FROM support_messages ORDER BY status ASC, id DESC")->fetchAll();
            if(count($tickets) > 0) {
                foreach($tickets as $t): ?>
                <tr>
                    <td style="color:#6b7280;">#<?= $t['id'] ?></td>
                    <td>
                        <div style="font-weight:600; color:#1f2937;"><?= htmlspecialchars($t['name']) ?></div>
                        <div style="font-size:0.85rem; color:#3b82f6;"><?= htmlspecialchars($t['email']) ?></div>
                        <div style="font-size:0.8rem; color:#9ca3af; margin-top:0.25rem;">Joined: <?= date('M d, Y', strtotime($t['created_at'])) ?></div>
                    </td>
                    <td style="max-width:320px;">
                        <div style="font-weight:600; color:#111827;"><?= htmlspecialchars($t['subject']) ?></div>
                        <div style="font-size:0.85rem; color:#4b5563; margin-top:6px; line-height:1.4;"><?= nl2br(htmlspecialchars($t['message'])) ?></div>
                    </td>
                    <td>
                        <?php if($t['status'] === 'open'): ?>
                            <span style="background:#fee2e2; color:#b91c1c; padding:6px 10px; border-radius:6px; font-size:0.75rem; font-weight:700; letter-spacing:0.05em;">OPEN</span>
                        <?php else: ?>
                            <span style="background:#d1fae5; color:#065f46; padding:6px 10px; border-radius:6px; font-size:0.75rem; font-weight:700; letter-spacing:0.05em;">RESOLVED</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:0.5rem; max-width: 140px;">
                            <a href="mailto:<?= urlencode($t['email']) ?>?subject=Re: <?= urlencode($t['subject']) ?>" class="btn-primary" style="text-decoration:none; padding: 0.5rem; font-size:0.8rem; text-align:center;">Reply Mail</a>
                            <?php if($t['status'] === 'open'): ?>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="resolve_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn-outline" style="padding: 0.5rem; border-color:#10b981; color:#10b981; font-size:0.8rem; width:100%;">Mark Resolved</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach;
            } else {
                echo '<tr><td colspan="5" style="text-align:center; padding: 3rem; color:#6b7280;">Inbox is clear! No support tickets have been opened yet.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<?php require_once 'includes/footer.php'; ?>
