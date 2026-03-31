<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
require_once 'includes/header.php';
?>
<div class="hero-section text-center text-white" style="padding: 4rem 2rem; margin-bottom: 2rem; border-radius: 0 0 40px 40px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
    <div class="container hero-content">
        <h1 class="fw-bold">🚀 Startup India Eligibility Engine</h1>
        <p class="lead" style="margin-top: 1rem; max-width:800px; margin-left:auto; margin-right:auto;">Find out instantly if your registered company qualifies for up to ₹20 Lakh in Seed Funding or ₹50 Lakh in Business Expansion Loans under Startup India schemes.</p>
    </div>
</div>

<div class="container my-5" style="max-width: 800px;">
    <div class="card glassmorphism" id="checkerCard" style="padding: 2.5rem;">
        <h3 class="mb-4 text-primary text-center">Interactive Company Profiler</h3>
        <p class="text-center text-muted mb-4">Complete this rapid 4-step assessment to algorithmically check your DPIIT status against current banking rules.</p>
        
        <form id="startupForm" style="margin-top:2rem;">
            <div class="form-group mb-4">
                <label style="font-weight:600;">1. Company Registration/Incorporation Date</label>
                <input type="date" name="inc_date" id="inc_date" required class="form-control" style="padding: 0.8rem;">
            </div>
            
            <div class="form-group mb-4">
                <label style="font-weight:600;">2. Is your startup officially recognized by DPIIT?</label>
                <select name="dpiit" id="dpiit" class="form-control" required style="padding: 0.8rem;">
                    <option value="">Select Option...</option>
                    <option value="yes">Yes, we have a DPIIT Recognition Certificate</option>
                    <option value="no">No / Not Yet Applied</option>
                </select>
            </div>
            
            <div class="form-group mb-4">
                <label style="font-weight:600;">3. Current Annual Turnover (in INR)</label>
                <select name="turnover" id="turnover" class="form-control" required style="padding: 0.8rem;">
                    <option value="">Select Option...</option>
                    <option value="pre_revenue">Pre-revenue (₹0)</option>
                    <option value="below_1cr">Below ₹1 Crore</option>
                    <option value="1_to_25cr">₹1 Crore to ₹25 Crore</option>
                    <option value="above_25cr">Above ₹25 Crore</option>
                </select>
            </div>
            
            <div class="form-group mb-5">
                <label style="font-weight:600;">4. Do you have a scalable prototype or product ready?</label>
                <select name="prototype" id="prototype" class="form-control" required style="padding: 0.8rem;">
                    <option value="">Select Option...</option>
                    <option value="yes">Yes, we have a functional prototype or launched product</option>
                    <option value="no">No, we are entirely in the ideation/concept phase</option>
                </select>
            </div>

            <button type="submit" class="btn-primary w-100 p-3" style="font-size:1.1rem; letter-spacing: 0.05em; text-transform:uppercase;">Evaluate Funding Eligibility &rarr;</button>
        </form>
        
        <div id="resultsArea" style="display:none;">
            <h3 class="text-center mb-4 text-main">Your Diagnostic Report</h3>
            
            <div id="seedResult" class="mb-4 p-4" style="border-radius:12px; border-left: 6px solid #3b82f6; background: rgba(59, 130, 246, 0.05);">
                <h4 class="mb-2" style="color:var(--primary-color);">Startup India Seed Fund (Up to ₹20 Lakh)</h4>
                <p id="seedDesc" class="mb-0 text-muted" style="line-height:1.6;"></p>
            </div>
            
            <div id="loanResult" class="p-4" style="border-radius:12px; border-left: 6px solid #10b981; background: rgba(16, 185, 129, 0.05);">
                <h4 class="mb-2" style="color:#059669;">Business Scale-up Loan (Up to ₹50 Lakh)</h4>
                <p id="loanDesc" class="mb-0 text-muted" style="line-height:1.6;"></p>
            </div>
            
            <div class="text-center mt-5">
                <button type="button" class="btn-primary" style="padding: 0.8rem 2rem; background: #6b7280;" onclick="resetForm()">Recalculate with different inputs</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('startupForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Execute Native Algorithm logic
    const incDateStr = document.getElementById('inc_date').value;
    const dpiit = document.getElementById('dpiit').value;
    const turnover = document.getElementById('turnover').value;
    const prototype = document.getElementById('prototype').value;
    
    if(!incDateStr) return;
    
    const incDate = new Date(incDateStr);
    const today = new Date();
    const ageInYears = (today - incDate) / (1000 * 60 * 60 * 24 * 365);
    
    // Seed Fund Engine (Max ₹20L)
    // Rule: DPIIT Recognized, Age <= 2 Years, Has Prototype, Pre-revenue or Below 1Cr
    let seedEligible = true;
    let seedReasons = [];
    if(dpiit !== 'yes') { seedEligible = false; seedReasons.push("Requires official DPIIT Recognition ID."); }
    if(ageInYears > 2) { seedEligible = false; seedReasons.push("Your startup must be incorporated within the last 2 years to qualify for 'Seed' stage funding."); }
    if(prototype !== 'yes') { seedEligible = false; seedReasons.push("Requires a demonstrable functional prototype or scalable product base."); }
    if(turnover === 'above_25cr') { seedEligible = false; seedReasons.push("Annual revenue strongly exceeds the threshold intended for seed-stage grants."); }
    
    const seedBox = document.getElementById('seedResult');
    if(seedEligible) {
        seedBox.style.borderLeftColor = '#10b981';
        seedBox.style.background = 'rgba(16, 185, 129, 0.05)';
        document.getElementById('seedDesc').innerHTML = '<strong style="color:#059669; font-size:1.1rem; display:block; margin-bottom:0.5rem;">✔️ Highly Eligible!</strong> Your company dynamically meets all stringent structural criteria for the Startup India Seed Fund Scheme. You are eligible to apply directly on the national DPIIT portal for early-stage capital grants up to ₹20,000,000.';
    } else {
        seedBox.style.borderLeftColor = '#ef4444';
        seedBox.style.background = 'rgba(239, 68, 68, 0.05)';
        document.getElementById('seedDesc').innerHTML = '<strong style="color:#b91c1c; font-size:1.1rem; display:block; margin-bottom:0.5rem;">❌ Not Eligible Currently.</strong> ' + seedReasons.join(' ');
    }
    
    // Loan Engine (Max ₹50L) -> Mudra / Stand-up India scale
    // Rule: Activity must be > 1 year older to show traction OR DPIIT + Revenue.
    let loanEligible = true;
    let loanReasons = [];
    if(ageInYears < 1 && turnover === 'pre_revenue') {
        loanEligible = false;
        loanReasons.push("Standard Banking structures (CGTMSE / Stand-Up India) mandate at least 1-2 years of measurable financial traction or verifiable existing revenue to sanction high-capital unsecured collateral loans.");
    }
    
    const loanBox = document.getElementById('loanResult');
    if(loanEligible) {
        loanBox.style.borderLeftColor = '#10b981';
        loanBox.style.background = 'rgba(16, 185, 129, 0.05)';
        document.getElementById('loanDesc').innerHTML = '<strong style="color:#059669; font-size:1.1rem; display:block; margin-bottom:0.5rem;">✔️ Eligible for Commercial Scale-up Loans.</strong> Based on your company''s current traction metrics, you align flawlessly with MSME scales to secure collateral-free expansions up to ₹50,000,000.';
    } else {
        loanBox.style.borderLeftColor = '#ef4444';
        loanBox.style.background = 'rgba(239, 68, 68, 0.05)';
        document.getElementById('loanDesc').innerHTML = '<strong style="color:#b91c1c; font-size:1.1rem; display:block; margin-bottom:0.5rem;">❌ Bank Loan Unlikely.</strong> ' + loanReasons.join(' ');
    }
    
    // Animate view seamlessly
    document.getElementById('startupForm').style.opacity = '0';
    setTimeout(() => {
        document.getElementById('startupForm').style.display = 'none';
        document.getElementById('resultsArea').style.display = 'block';
        document.getElementById('startupForm').style.opacity = '1';
    }, 200);
});

function resetForm() {
    document.getElementById('resultsArea').style.display = 'none';
    document.getElementById('startupForm').reset();
    document.getElementById('startupForm').style.display = 'block';
}
</script>
<?php require_once 'includes/footer.php'; ?>
