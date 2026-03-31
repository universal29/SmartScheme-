<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once 'includes/header.php';
?>
<div class="dashboard-grid">
    <div class="form-card" style="margin-top:0;">
        <h3 style="margin-bottom:0.5rem;">Platform Configuration</h3>
        <p style="color:#6b7280; font-size:0.9rem; margin-bottom:1.5rem;">Update the core configuration details of the SmartScheme platform.</p>
        <form onsubmit="event.preventDefault(); alert('Settings successfully updated!');">
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label>Platform Name</label>
                <input type="text" value="SmartScheme Initiative" disabled style="background:#f3f4f6; color:#9ca3af; cursor:not-allowed;">
                <small style="color:#9ca3af; margin-top:5px;">System label locked for MVP phase.</small>
            </div>
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label>Administrative Support Email</label>
                <input type="email" value="support@smartscheme.gov.in" required>
            </div>
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label>Global Maintenance Mode</label>
                <select>
                    <option value="0" selected>Disabled - Platform is Live</option>
                    <option value="1">Enabled - Platform is Offline</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:1rem; width:auto;">Save Settings</button>
        </form>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
