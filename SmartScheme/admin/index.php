<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once 'includes/header.php';

$usersStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
$totalUsers = $usersStmt->fetchColumn();

$schemesStmt = $pdo->query("SELECT COUNT(*) FROM schemes");
$totalSchemes = $schemesStmt->fetchColumn();

// Fetch new stats
$savesStmt = $pdo->query("SELECT COUNT(*) FROM saved_schemes");
$totalSaves = $savesStmt->fetchColumn();

$adminsStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$totalAdmins = $adminsStmt->fetchColumn();
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Registered Citizens</h3>
        <p><?= htmlspecialchars($totalUsers) ?></p>
    </div>
    <div class="stat-card">
        <h3>Active Schemes</h3>
        <p><?= htmlspecialchars($totalSchemes) ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Bookmarks</h3>
        <p><?= htmlspecialchars($totalSaves) ?></p>
    </div>
    <div class="stat-card">
        <h3>Administrators</h3>
        <p><?= htmlspecialchars($totalAdmins) ?></p>
    </div>
</div>

<div class="dashboard-grid" style="margin-top: 2.5rem; gap: 2rem;">
    <!-- Recent Users Table -->
    <div class="form-card" style="margin-top: 0;">
        <h3 style="margin-bottom:1rem;">Recent Citizens</h3>
        <table style="margin-top:0;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recentUsers = $pdo->query("SELECT id, full_name, created_at FROM users WHERE role = 'user' ORDER BY id DESC LIMIT 5")->fetchAll();
                if(count($recentUsers) > 0) {
                    foreach($recentUsers as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td style="font-size:0.85rem; color:#6b7280;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endforeach;
                } else {
                    echo '<tr><td colspan="3" style="text-align:center; color:#6b7280;">No citizens joined yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Right Side Placeholder or Mini Graph Area if needed (Using same card style) -->
    <div class="form-card" style="margin-top: 0; background: linear-gradient(135deg, #111827 0%, #1f2937 100%); color:white; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
        <h3 style="color:white; margin-bottom:0.5rem;">System Health</h3>
        <p style="color:#9ca3af; margin-bottom:2rem;">All backend services are operating normally.</p>
        <div style="width: 120px; height: 120px; border-radius: 50%; border: 8px solid #3b82f6; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:bold; color:white;">
            100%
        </div>
        <p style="color:#60a5fa; margin-top:1.5rem; font-weight:600;">Recommendation Engine Active</p>
    </div>
</div>

<div class="form-card">
    <h3>Publish New Scheme</h3>
    <form id="addSchemeForm" action="../api/add_scheme.php" method="POST">
        <div id="form-msg" style="display:none;" class="alert-success"></div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Scheme Name</label>
                <input type="text" name="name" required placeholder="Ex: PM Kisan Samman">
            </div>
            <div class="form-group">
                <label>Ministry</label>
                <input type="text" name="ministry" required>
            </div>
            <div class="form-group">
                <label>Module</label>
                <select name="module_category" required>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Education">Education</option>
                    <option value="Women">Women</option>
                    <option value="Senior Citizen">Senior Citizen</option>
                    <option value="Business">Business</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Launch Date</label>
                <input type="date" name="launch_date" required>
            </div>
            <div class="form-group">
                <label>Benefit Detail</label>
                <input type="text" name="benefits" required>
            </div>
            <div class="form-group">
                <label>Benefit Value</label>
                <input type="number" name="benefit_value" required value="0">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:1.5rem;">
            <label>Description</label>
            <textarea name="description" rows="3" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Required Documents</label>
                <textarea name="required_docs" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>Steps</label>
                <textarea name="application_steps" rows="2" required></textarea>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 2rem;">
            <label>AI Rules JSON</label>
            <textarea name="rules_json" rows="3" placeholder="JSON Rules..." required></textarea>
        </div>
        
        <button type="submit" class="btn-primary">Publish Scheme</button>
    </form>
</div>

<div class="form-card">
    <h3 style="margin-bottom:1rem;">Manage Schemes</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Ministry</th>
                <th>Module</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $allSchemes = $pdo->query("SELECT id, name, ministry, module_category FROM schemes ORDER BY id DESC")->fetchAll();
            foreach($allSchemes as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td style="font-weight:500;"><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['ministry']) ?></td>
                <td><span style="background:#dbeafe; color:#1e40af; padding:4px 8px; border-radius:4px; font-size:0.8rem; font-weight:600;"><?= htmlspecialchars($s['module_category'] ?? 'General') ?></span></td>
                <td>
                    <button class="btn-danger" onclick="deleteScheme(<?= $s['id'] ?>)">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
async function deleteScheme(id) {
    if(!confirm('Delete this scheme?')) return;
    const formData = new FormData();
    formData.append('scheme_id', id);
    const res = await fetch('../api/delete_scheme.php', { method: 'POST', body: formData });
    const data = await res.json();
    if(data.status === 'success') {
        location.reload();
    } else {
        alert(data.message);
    }
}

document.getElementById('addSchemeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('../api/add_scheme.php', { method: 'POST', body: formData });
    const data = await res.json();
    
    const msgDiv = document.getElementById('form-msg');
    msgDiv.style.display = 'block';
    
    if(data.status === 'success') {
        msgDiv.className = 'alert-success';
        msgDiv.innerText = data.message;
        e.target.reset();
        setTimeout(() => location.reload(), 1500);
    } else {
        msgDiv.className = 'alert-error';
        msgDiv.innerText = data.message;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
