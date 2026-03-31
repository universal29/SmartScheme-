<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
$searchQuery = $_GET['q'] ?? '';
$moduleQuery = $_GET['module'] ?? '';
?>
<style>
/* Page Specific Animations & Premium Styles */
.hero-schemes {
    padding: 6rem 2rem;
    background: linear-gradient(135deg, #1e3a8a 0%, #6366f1 100%);
    color: white;
    border-radius: 0 0 50px 50px;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
    animation: fadeIn 0.8s ease-out;
}
.hero-schemes::after {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    border-radius: 50%;
    animation: pulseBg 15s infinite alternate;
}
@keyframes pulseBg {
    from { transform: scale(1); opacity: 0.5; }
    to { transform: scale(1.1); opacity: 0.9; }
}

.filter-sidebar {
    background: white;
    padding: 2.5rem 2rem;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08);
    position: sticky;
    top: 100px;
    border: 1px solid rgba(255,255,255,0.8);
    z-index: 10;
}

.scheme-card-animated {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #f3f4f6;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(30px);
}
.scheme-card-animated::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 5px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.scheme-card-animated:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px -12px rgba(59, 130, 246, 0.2);
}
.scheme-card-animated:hover::before {
    transform: scaleX(1);
}

.badge-module {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1;
}
.badge-state {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    padding: 0.4rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1;
}

@keyframes slideUpFade {
    to { opacity: 1; transform: translateY(0); }
}

.input-modern {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    padding: 0.9rem 1.1rem;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    outline: none;
    color: #334155;
}
.input-modern:focus {
    background: white;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
}
</style>

<div class="hero-schemes" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
    <div class="container hero-content" style="position:relative; z-index:2; display:flex; flex-direction:column; align-items:center; text-align:center;">
        <h1 class="fw-bold mb-3" style="font-size:3.5rem; text-align:center; width:100%;">Explore All Public Schemes</h1>
        <p class="lead mx-auto" style="max-width: 700px; font-size:1.2rem; opacity:0.9; text-align:center; margin-left:auto; margin-right:auto;">Leverage our deep filtering engine to isolate the exact government programs designed for your demographic.</p>
    </div>
</div>

<div class="container my-5">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid #e5e7eb; padding-bottom: 1.5rem; margin-bottom:3rem;">
        <div>
            <h2 style="margin:0; font-weight:800; color:#1f2937; letter-spacing:-0.03em;">Database Index</h2>
        </div>
        <div id="resultsCount" class="text-primary fw-bold" style="font-size: 1.1rem; background:rgba(59, 130, 246, 0.1); padding:0.6rem 1.2rem; border-radius:50px;">
            Initializing Protocol...
        </div>
    </div>

    <div class="row" style="display: flex; gap: 3rem;">
        <!-- Sidebar Filters -->
        <div class="col-md-3" style="flex: 1; max-width: 330px;">
            <div class="filter-sidebar">
                <h4 class="mb-4 fw-bold" style="display:flex; align-items:center; gap:12px; font-size:1.6rem; color:#1e293b;">
                    <svg width="28" height="28" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Refine Results
                </h4>
                <form id="filterForm">
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; font-size:0.95rem; color:#475569; display:block; margin-bottom:0.5rem;">Keyword Search</label>
                        <input type="text" name="q" class="input-modern w-100" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="e.g. Pension, Farm...">
                    </div>
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; font-size:0.95rem; color:#475569; display:block; margin-bottom:0.5rem;">Geographic Scope</label>
                        <select name="state" class="input-modern w-100">
                            <option value="">National & State Combined</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Maharashtra">Maharashtra</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; font-size:0.95rem; color:#475569; display:block; margin-bottom:0.5rem;">Module Category</label>
                        <select name="module" class="input-modern w-100">
                            <option value="">All Categories</option>
                            <option value="Agriculture" <?= $moduleQuery == 'Agriculture'?'selected':'' ?>>Agriculture</option>
                            <option value="Healthcare" <?= $moduleQuery == 'Healthcare'?'selected':'' ?>>Healthcare</option>
                            <option value="Education" <?= $moduleQuery == 'Education'?'selected':'' ?>>Education</option>
                            <option value="Women" <?= $moduleQuery == 'Women'?'selected':'' ?>>Women</option>
                            <option value="Senior Citizen" <?= $moduleQuery == 'Senior Citizen'?'selected':'' ?>>Senior Citizen</option>
                            <option value="General" <?= $moduleQuery == 'General'?'selected':'' ?>>General Federal</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:2rem;">
                        <label style="font-weight:600; font-size:0.95rem; color:#475569; display:block; margin-bottom:0.5rem;">Algorithmic Sorting</label>
                        <select name="sort" class="input-modern w-100">
                            <option value="">Latest Additions</option>
                            <option value="popular">Most Popular / Trending</option>
                            <option value="benefit">Highest Financial Benefit</option>
                        </select>
                    </div>
                    <button type="submit" class="w-100" style="background:#4f46e5; color:white; border:none; border-radius:50px; font-size:1.15rem; font-weight:700; padding:1.1rem; box-shadow:0 10px 20px -5px rgba(79, 70, 229, 0.4); margin-bottom:1rem; cursor:pointer; transition:all 0.3s ease;">Engage Array</button>
                    <button type="button" class="w-100" style="background:transparent; color:#4f46e5; border:2px solid #4f46e5; border-radius:50px; font-size:1.15rem; font-weight:700; padding:1.05rem; cursor:pointer; transition:all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.02);" onclick="document.getElementById('filterForm').reset(); fetchSchemes();">Clear Matrix</button>
                </form>
            </div>
        </div>

        <!-- Schemes Grid -->
        <div class="col-md-9" style="flex: 2.5;">
            <div id="loading" class="text-center" style="display:none; padding: 6rem 0;">
                <div style="font-size: 4rem; animation: pulseBg 1s infinite alternate;">📡</div>
                <h4 class="mt-4 text-muted" style="font-weight:600;">Scanning Federal Databases...</h4>
            </div>
            <div id="schemesGrid" class="grid-schemes" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <!-- Schemes injected via JS -->
            </div>
            <div id="noData" class="text-center mt-5" style="display:none; padding:5rem 2rem; background:white; border-radius:30px; border: 1px dashed #d1d5db;">
                <div style="font-size:5rem; opacity:0.3; margin-bottom:1.5rem;">📭</div>
                <h2 class="text-main fw-bold">Zero Hits Found</h2>
                <p class="text-muted" style="font-size:1.15rem; max-width:400px; margin:0 auto;">Try broadening your keyword search term or clearing the state filter isolation.</p>
                <button class="btn-outline mt-4" onclick="document.getElementById('filterForm').reset(); fetchSchemes();">Reset Protocols</button>
            </div>
        </div>
    </div>
</div>

<script>
async function fetchSchemes(page = 1) {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('schemesGrid').innerHTML = '';
    document.getElementById('noData').style.display = 'none';

    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData).toString();

    try {
        const res = await fetch(`api/fetch_schemes.php?${params}`);
        const data = await res.json();
        
        // Slight artificial delay for UX feel of 'processing'
        setTimeout(() => {
            document.getElementById('loading').style.display = 'none';
            if(data.status === 'success' && data.data.length > 0) {
                document.getElementById('resultsCount').innerText = `${data.data.length} Match${data.data.length!==1?'es':''} Found`;
                
                let html = '';
                data.data.forEach((s, index) => {
                    // Calculate animation delay for crisp staggering effect
                    const delay = index * 0.12;
                    
                    html += `
                    <div class="scheme-card-animated" style="animation: slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards ${delay}s;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                            <h3 class="fw-bold" style="color:var(--text-main); margin:0; flex:1; font-size:1.3rem; line-height:1.4;">${s.name}</h3>
                        </div>
                        
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.2rem;">
                            ${s.state && s.state !== 'All India' ? `<span class="badge-state">📍 ${s.state}</span>` : ''}
                            <span class="badge-module">${s.module_category || 'General Federal'}</span>
                        </div>
                        
                        <p class="text-muted small" style="font-weight:600; display:flex; align-items:center; gap:0.4rem; margin-bottom:1rem;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.6;"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                            ${s.ministry}
                        </p>
                        
                        <div style="flex:1;">
                            <p style="color:#4b5563; font-size:0.95rem; line-height:1.6;">${s.description.substring(0, 120)}...</p>
                        </div>
                        
                        <div style="background: #f9fafb; padding: 1.2rem; border-radius:12px; margin-bottom: 1.5rem; border: 1px solid #f3f4f6;">
                            <div style="font-weight:700; color:#059669; font-size:0.9rem; margin-bottom:0.4rem; display:flex; align-items:center; gap:0.4rem;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                Guaranteed Core Benefit
                            </div>
                            <div style="color:#374151; font-size:0.9rem; line-height:1.5;">${s.benefits}</div>
                        </div>
                        
                        <a href="scheme_detail.php?id=${s.id}" class="btn-primary w-100 text-center" style="display:block; padding:0.9rem; font-size:1.05rem; box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3);">View Full Directive</a>
                    </div>`;
                });
                document.getElementById('schemesGrid').innerHTML = html;
            } else {
                document.getElementById('resultsCount').innerText = `Zero Return Matrix`;
                document.getElementById('noData').style.display = 'block';
            }
        }, 400); // UI aesthetic timeout
    } catch (err) {
        document.getElementById('loading').style.display = 'none';
        alert("Failed to connect to the scheme algorithm.");
    }
}

// Bind live filtering with anti-flicker debounce
let debounceTimer;
document.querySelector('input[name="q"]').addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchSchemes(1), 500);
});

const selects = document.querySelectorAll('select');
selects.forEach(s => s.addEventListener('change', () => fetchSchemes(1)));

document.getElementById('filterForm').addEventListener('submit', (e) => {
    e.preventDefault();
    fetchSchemes(1);
});

// Boot ignition
fetchSchemes(1);
</script>
<?php require_once 'includes/footer.php'; ?>
