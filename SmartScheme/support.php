<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>
<div class="hero-section text-center text-white" style="padding: 4rem 2rem; margin-bottom: 2rem;">
    <div class="container hero-content">
        <h2>How can we help you today?</h2>
        <div class="search-bar-wrapper mt-4">
            <input type="text" id="faqSearch" class="form-control rounded-pill" placeholder="Search FAQs..." style="max-width: 500px; margin: 0 auto; padding-left: 1.5rem;">
        </div>
    </div>
</div>

<div class="container my-5 dashboard-grid" style="align-items: start;">
    <div style="grid-column: span 1;">
        <div class="card glassmorphism">
            <h3 class="mb-4">Contact Support</h3>
            <form id="contactForm" action="api/submit_support.php" method="POST">
                <div id="contact-msg" class="alert-success" style="display:none; margin-bottom:1rem; padding:1rem; border-radius:8px;"></div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required value="<?= isLoggedIn() ? htmlspecialchars($_SESSION['full_name'] ?? '') : '' ?>">
                </div>
                <div class="form-group">
                    <label>Email ID</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Support Subject</label>
                    <input type="text" name="subject" required placeholder="Ex: Need help with Registration">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-primary w-100 mt-3">Send Message</button>
            </form>
        </div>
    </div>

    <div style="grid-column: span 1;">
        <div class="card glassmorphism">
            <h3 class="mb-4" id="faqTitle">Frequently Asked Questions</h3>
            <div id="faqList">
                <div class="faq-item p-3 mb-3" style="background: rgba(255,255,255,0.6); border-radius: 8px;">
                    <h5 class="text-primary mb-1">How does the AI Recommendation work?</h5>
                    <p class="text-sm">We analyze your profile's age, income, category, and other fields against the strict eligibility criteria of hundreds of schemes. The match score helps you find the most suitable benefits instantly.</p>
                </div>
                <div class="faq-item p-3 mb-3" style="background: rgba(255,255,255,0.6); border-radius: 8px;">
                    <h5 class="text-primary mb-1">Is my data secure?</h5>
                    <p class="text-sm">Yes, your data is securely stored and passwords are cryptographically hashed. We never share your personal information with third parties.</p>
                </div>
                <div class="faq-item p-3 mb-3" style="background: rgba(255,255,255,0.6); border-radius: 8px;">
                    <h5 class="text-primary mb-1">How frequently are new schemes added?</h5>
                    <p class="text-sm">New schemes are typically updated weekly directly from official government sources.</p>
                </div>
                <div class="faq-item p-3 mb-3" style="background: rgba(255,255,255,0.6); border-radius: 8px;">
                    <h5 class="text-primary mb-1">Why do I need to verify my email/mobile?</h5>
                    <p class="text-sm">Verification prevents spam accounts and guarantees that you can securely recover your account through OTPs if needed.</p>
                </div>
                
                <div id="noFaq" class="text-muted" style="display:none; padding:1rem;">No FAQs match your search. Try adjusting keywords or contacting support.</div>
            </div>
        </div>
    </div>
</div>

<script>
// Actual contact form submission
document.getElementById('contactForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerText = 'Sending over secure channels...';
    btn.disabled = true;
    
    const formData = new FormData(e.target);
    try {
        const res = await fetch('api/submit_support.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        const msg = document.getElementById('contact-msg');
        msg.style.display = 'block';
        msg.innerText = data.message;
        
        if(data.status === 'success') {
            msg.className = 'alert-success';
            e.target.reset();
        } else {
            msg.className = 'alert-error';
        }
    } catch(err) {
        alert("Failed to reach server");
    }
    
    btn.innerText = 'Send Message';
    btn.disabled = false;
});

// Auto-suggest FAQ logic
document.getElementById('faqSearch').addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.faq-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        const text = item.innerText.toLowerCase();
        if(text.includes(term)) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    if(term.length > 0) {
        document.getElementById('faqTitle').innerText = "Search Results for FAQs";
    } else {
        document.getElementById('faqTitle').innerText = "Frequently Asked Questions";
    }
    
    document.getElementById('noFaq').style.display = visibleCount === 0 ? 'block' : 'none';
});
</script>
<?php require_once 'includes/footer.php'; ?>
