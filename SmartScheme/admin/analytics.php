<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once 'includes/header.php';
?>
<div class="dashboard-grid">
    <div class="form-card" style="margin-top:0;">
        <h3 style="margin-bottom:1rem;">Top Saved Schemes</h3>
        <table>
            <thead>
                <tr>
                    <th>Scheme Name</th>
                    <th>Total Community Saves</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $saves = $pdo->query("SELECT s.name, COUNT(ss.id) as saves FROM saved_schemes ss JOIN schemes s ON ss.scheme_id = s.id GROUP BY s.id ORDER BY saves DESC LIMIT 10")->fetchAll();
                if(count($saves) > 0) {
                    foreach($saves as $s): ?>
                    <tr>
                        <td style="font-weight:500; color:#3b82f6;"><?= htmlspecialchars($s['name']) ?></td>
                        <td style="font-weight:bold; font-size:1.1rem;"><?= $s['saves'] ?></td>
                    </tr>
                    <?php endforeach;
                } else {
                    echo '<tr><td colspan="2" style="text-align:center; color:#6b7280;">No data available yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="form-card" style="margin-top:0;">
        <h3 style="margin-bottom:1rem;">Active Schemes by Module</h3>
        <table>
            <thead>
                <tr>
                    <th>Module Category</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $modules = $pdo->query("SELECT module_category, COUNT(id) as cnt FROM schemes GROUP BY module_category ORDER BY cnt DESC")->fetchAll();
                if(count($modules) > 0) {
                    foreach($modules as $m): ?>
                    <tr>
                        <td><span style="background:#dbeafe; color:#1e40af; padding:4px 8px; border-radius:4px; font-weight:600;"><?= htmlspecialchars($m['module_category'] ?? 'General') ?></span></td>
                        <td style="font-weight:bold; font-size:1.1rem;"><?= $m['cnt'] ?></td>
                    </tr>
                    <?php endforeach;
                } else {
                    echo '<tr><td colspan="2" style="text-align:center; color:#6b7280;">No data available yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
