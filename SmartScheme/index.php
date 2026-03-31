<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
require_once 'includes/header.php';
?>
<section class="hero-section text-center text-white">
    <div class="container hero-content">
        <h1>Find the Right Government Schemes For You</h1>
        <p>Personalized AI recommendations based on your profile and needs.</p>
        <div class="search-bar-wrapper mt-4">
            <form action="schemes.php" method="GET" style="display: flex; gap: 10px; justify-content: center;">
                <input type="text" name="q" placeholder="Search for agriculture, health, education..." style="max-width: 400px; border-radius: 50px; padding-left: 1.5rem;">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </div>
    </div>
</section>

<section class="featured-schemes py-5">
    <div class="container">
        <h2 class="text-center mb-4">🔥 Trending Schemes</h2>
        <div class="grid-schemes">
            <?php
            $stmt = $pdo->query("SELECT * FROM schemes ORDER BY popularity DESC LIMIT 3");
            while($scheme = $stmt->fetch()):
            ?>
            <div class="scheme-card glassmorphism">
                <h3 class="text-primary"><?= htmlspecialchars($scheme['name']) ?></h3>
                <p class="text-muted small">🏛️ <?= htmlspecialchars($scheme['ministry']) ?></p>
                <p><?= substr(htmlspecialchars($scheme['description']), 0, 100) ?>...</p>
                <div class="benefits-tag mb-3">💰 <?= htmlspecialchars($scheme['benefits']) ?></div>
                <a href="scheme_detail.php?id=<?= $scheme['id'] ?>" class="btn-outline w-100 text-center" style="display: block;">View Details</a>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center" style="margin-top: 3rem;">
            <a href="schemes.php" class="btn-primary">Browse All Schemes</a>
        </div>
    </div>
</section>

<section class="modules-overview py-5 bg-light" style="background: rgba(255,255,255,0.6);">
    <div class="container text-center">
        <h2 class="mb-5 text-primary">Explore Schemes by Module</h2>
        <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 2rem; justify-content: center; flex-wrap: wrap;">
            <?php
            $modules = ['Agriculture' => '🌾', 'Healthcare' => '⚕️', 'Education' => '📚', 'Women' => '👩', 'Senior Citizen' => '👴', 'General' => '🏢'];
            foreach($modules as $m => $icon): ?>
            <a href="schemes.php?module=<?= urlencode($m) ?>" class="card glassmorphism text-center border-0" style="min-width: 150px; flex: 0 0 auto; text-decoration: none; padding: 2rem; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <div style="font-size: 3rem; margin-bottom: 1rem;"><?= $icon ?></div>
                <div class="fw-bold text-main" style="font-size: 1.1rem;"><?= $m ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="how-it-works py-5">
    <div class="container text-center">
        <h2 class="mb-5">How SmartScheme Works</h2>
        <div class="row" style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
            <div class="col-md-4 card glassmorphism border-0 px-4 py-5" style="flex:1; min-width: 300px;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👤</div>
                <h4 class="text-primary">1. Create Your Profile</h4>
                <p class="text-muted">Fill out your age, income, category, and occupation details securely.</p>
            </div>
            <div class="col-md-4 card glassmorphism border-0 px-4 py-5" style="flex:1; min-width: 300px;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🤖</div>
                <h4 class="text-primary">2. AI Recommendation</h4>
                <p class="text-muted">Our AI engine instantly matches your exact profile against strict government scheme rules.</p>
            </div>
            <div class="col-md-4 card glassmorphism border-0 px-4 py-5" style="flex:1; min-width: 300px;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📑</div>
                <h4 class="text-primary">3. Apply & Benefit</h4>
                <p class="text-muted">Review scheme details, verify eligibility, and apply directly via official links.</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials py-5" style="background: rgba(255,255,255,0.6);">
    <div class="container text-center">
        <h2 class="mb-5 text-primary">What Citizens Say</h2>
        <div class="grid-schemes" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));">
            <div class="card glassmorphism text-left p-5 border-0">
                <p class="lead" style="font-style: italic;">"I had no idea I was eligible for the Ayushman Bharat scheme until the AI engine matched me perfectly based on my BPL status."</p>
                <div class="mt-4 fw-bold text-main">- Ramesh K., Farmer</div>
            </div>
            <div class="card glassmorphism text-left p-5 border-0">
                <p class="lead" style="font-style: italic;">"The dashboard is so clean and easy to use. I found the exact scholarship my daughter needed for college in minutes."</p>
                <div class="mt-4 fw-bold text-main">- Sunita P., Teacher</div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section py-5 text-center mt-4">
    <div class="container card glassmorphism" style="padding: 4rem; background: linear-gradient(135deg, rgba(79,70,229,0.1), rgba(16,185,129,0.1));">
        <h2>Ready to find your schemes?</h2>
        <p class="lead mb-4">Join thousands of citizens maximizing their benefits.</p>
        <a href="register.php" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">Create Account</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
