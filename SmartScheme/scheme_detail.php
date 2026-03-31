<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('schemes.php');

$stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$stmt->execute([$id]);
$scheme = $stmt->fetch();

if (!$scheme) {
    echo "<div class='container my-5'><h2>Scheme not found</h2></div>";
    require_once 'includes/footer.php';
    exit;
}

// Increment popularity minimally for viewing (mocking logic)
$pdo->prepare("UPDATE schemes SET popularity = popularity + 1 WHERE id = ?")->execute([$id]);
?>
<div class="container my-5 dashboard-grid">
    <div style="grid-column: span 2;">
        <div class="card glassmorphism">
            <h1 class="text-primary"><?= htmlspecialchars($scheme['name']) ?></h1>
            <p class="text-muted"><strong>Ministry:</strong> <?= htmlspecialchars($scheme[' मंत्रालय'] ?? $scheme['ministry']) ?> | <strong>Launched:</strong> <?= htmlspecialchars($scheme['launch_date']) ?></p>
            
            <div class="mt-4">
                <h3>About the Scheme</h3>
                <p><?= nl2br(htmlspecialchars($scheme['description'])) ?></p>
            </div>
            
            <div class="mt-4">
                <h3 class="text-success">Benefits</h3>
                <div class="benefits-tag text-lg p-3">🎁 <?= htmlspecialchars($scheme['benefits']) ?></div>
            </div>

            <div class="mt-4" style="background: rgba(255,255,255,0.5); padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb;">
                <h4>Required Documents</h4>
                <p><?= nl2br(htmlspecialchars($scheme['required_docs'])) ?></p>
            </div>
            
            <div class="mt-4">
                <h4>Application Steps</h4>
                <p><?= nl2br(htmlspecialchars($scheme['application_steps'])) ?></p>
            </div>

            <?php if(isLoggedIn()): ?>
                <div class="mt-5 text-center" style="padding: 2rem; background: #e0e7ff; border-radius: 16px;">
                    <h3>Am I Eligible?</h3>
                    <p>Click below to use AI to check your eligibility based on your profile.</p>
                    <button id="checkEligibilityBtn" class="btn-primary" data-id="<?= $scheme['id'] ?>">Check Eligibility Now</button>
                    <div id="eligibilityResult" class="mt-3" style="display:none; font-size: 1.1rem; font-weight: bold;"></div>
                </div>
            <?php else: ?>
                <div class="mt-5 text-center">
                    <p class="text-muted">Please <a href="login.php" class="text-primary fw-bold">Login</a> to check AI eligibility and save schemes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const btn = document.getElementById('checkEligibilityBtn');
if(btn) {
    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.innerText = 'Checking...';
        const res = await fetch(`api/check_eligibility.php?scheme_id=${btn.dataset.id}`);
        const data = await res.json();
        
        const resultDiv = document.getElementById('eligibilityResult');
        resultDiv.style.display = 'block';
        
        if(data.status === 'success') {
            if(data.eligible) {
                resultDiv.className = 'alert-success mt-3';
                resultDiv.innerHTML = `✅ You are Eligible!<br><span style="font-size:0.9rem; font-weight:normal;">${data.reason}</span>`;
            } else {
                resultDiv.className = 'alert-error mt-3';
                resultDiv.innerHTML = `❌ You are Not Eligible.<br><span style="font-size:0.9rem; font-weight:normal;">Missing criteria: ${data.missing.join(', ')}</span>`;
            }
        } else {
            resultDiv.className = 'alert-error mt-3';
            resultDiv.innerText = data.message || 'Error checking eligibility. Have you completed your profile?';
        }
        btn.innerText = 'Check Again';
        btn.disabled = false;
    });
}
</script>
<?php require_once 'includes/footer.php'; ?>
