<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once 'includes/header.php';

// Action logic
if(isset($_POST['delete_user_id'])) {
    $delId = (int)$_POST['delete_user_id'];
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role='user'")->execute([$delId]);
    echo "<div class='alert-success'>Citizen successfully deleted.</div>";
}
?>
<div class="form-card" style="margin-top:0;">
    <h3>Manage Citizens</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC")->fetchAll();
            foreach($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['mobile']) ?></td>
                <td style="font-size:0.85rem; color:#6b7280;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete citizen permanently?');">
                        <input type="hidden" name="delete_user_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
