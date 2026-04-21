<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

if (!isLoggedIn()) {
    header("Location: auth_login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM schemes WHERE id = ?");
$stmt->execute([$id]);
$scheme = $stmt->fetch();

if (!$scheme) {
    header("Location: schemes.php");
    exit;
}

// Fallback logic for AI insights if DB is empty for this scheme
$applyUrl = !empty($scheme['apply_url']) ? $scheme['apply_url'] : 'https://www.india.gov.in';

$documents = [];
if (!empty($scheme['required_documents'])) {
    $documents = explode("\n", $scheme['required_documents']);
} else {
    // Generate AI-like fallback docs based on category
    $documents[] = "💳 Aadhaar Card or Valid Government ID";
    $documents[] = "🏦 Bank Account Passbook (For DBT Transfers)";
    $documents[] = "📄 Residential / Domicile Certificate";
    
    if ($scheme['category'] == 'Education') {
        $documents[] = "🎓 Previous Year Marksheets / Certificates";
        $documents[] = "🏫 Admission Proof or Fee Receipt";
    } elseif ($scheme['category'] == 'Agriculture') {
        $documents[] = "🌾 Land Ownership Records (7/12 Extract)";
        $documents[] = "🚜 Farmer Registration Certificate";
    } elseif ($scheme['max_income'] !== null) {
        $documents[] = "💰 Income Certificate (Ensuring income < ₹" . number_format($scheme['max_income']) . ")";
    }
    
    if ($scheme['target_category'] !== 'All' && $scheme['target_category'] !== 'General') {
        $documents[] = "📜 Caste / Category Certificate (" . $scheme['target_category'] . ")";
    }
}

$aiSummary = "";
if (!empty($scheme['ai_eligibility_summary'])) {
    $aiSummary = $scheme['ai_eligibility_summary'];
} else {
    // Generate AI-like summary
    $aiSummary = "My analysis confirms this scheme targets primarily those in the <b>" . htmlspecialchars($scheme['category']) . "</b> sector in <b>" . ($scheme['state'] == 'All' ? 'any region of India' : htmlspecialchars($scheme['state'])) . "</b>. ";
    
    if ($scheme['min_age'] > 0 || $scheme['max_age'] < 200) {
        $aiSummary .= "Applicants must be between <b>" . $scheme['min_age'] . " and " . $scheme['max_age'] . " years old</b>. ";
    }
    
    if ($scheme['max_income'] !== null) {
        $aiSummary .= "It is designed for applicants with a total family annual income of strictly <b>less than ₹" . number_format($scheme['max_income'], 2) . "</b>. ";
    } else {
        $aiSummary .= "There is <b>no upper income cap</b> to apply for this scheme. ";
    }
    
    if ($scheme['target_category'] !== 'All') {
        $aiSummary .= "Crucially, you must belong to the <b>" . htmlspecialchars($scheme['target_category']) . "</b> demographic to qualify.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Application Assistant | <?= htmlspecialchars($scheme['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #8b5cf6;
            --brand-secondary: #0ea5e9;
            --surface-color: rgba(255, 255, 255, 0.95);
            --text-dark: #0f172a;
            --text-gray: #475569;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #020617 0%, #172554 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            overflow: hidden;
        }

        .magic-bg {
            position: absolute;
            width: 100vw;
            height: 100vh;
            top: 0; left: 0;
            overflow: hidden;
            z-index: 0;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            animation: float 15s infinite ease-in-out alternate;
        }
        .orb-1 { width: 500px; height: 500px; background: rgba(139, 92, 246, 0.6); top: -100px; left: -100px; }
        .orb-2 { width: 600px; height: 600px; background: rgba(14, 165, 233, 0.5); bottom: -200px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -50px) scale(1.1); }
            100% { transform: translate(-30px, 30px) scale(0.9); }
        }

        .onboarding-grid {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            margin: 2rem;
            display: grid;
            grid-template-columns: 350px 1fr;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: popIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Branding Panel (Left) */
        .character-panel {
            background: radial-gradient(circle at bottom right, rgba(139, 92, 246, 0.4), rgba(2, 6, 23, 0.8));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            color: white;
            border-right: 1px solid rgba(255,255,255,0.1);
        }

        .assistant-character {
            width: 220px;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.6));
            animation: hover-character 4s infinite ease-in-out;
            margin-bottom: 2rem;
            z-index: 2;
        }

        @keyframes hover-character {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Form Panel (Right) */
        .details-panel {
            background: #ffffff;
            padding: 3.5rem 4rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            max-height: 80vh;
        }

        .details-header {
            margin-bottom: 2rem;
        }
        
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(90deg, #e0e7ff, #dbeafe);
            color: #4f46e5;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .scheme-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            line-height: 1.3;
        }

        /* AI Analysis Card */
        .analysis-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--brand-primary);
            position: relative;
            animation: slideUp 0.5s ease 0.1s both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .analysis-card h3 {
            margin: 0 0 0.75rem 0;
            font-size: 1.1rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .analysis-card p {
            margin: 0;
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
        }

        /* Checklist */
        .checklist-section {
            margin-bottom: 2.5rem;
            animation: slideUp 0.5s ease 0.2s both;
        }

        .checklist-section h3 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .doc-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .doc-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }
        
        .doc-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Apply Button */
        .btn-apply {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            color: white;
            text-decoration: none;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4);
            animation: slideUp 0.5s ease 0.3s both;
        }

        .btn-apply:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -5px rgba(139, 92, 246, 0.5);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #64748b;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: color 0.2s;
            animation: slideUp 0.5s ease 0.4s both;
        }
        .btn-cancel:hover { color: #0f172a; }

        @media (max-width: 850px) {
            .onboarding-grid { grid-template-columns: 1fr; }
            .character-panel { padding: 2rem; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); }
            .details-panel { padding: 2rem; max-height: none; }
            .assistant-character { width: 120px; margin-bottom: 1rem; }
        }
    </style>
</head>
<body>

    <div class="magic-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="onboarding-grid">
        
        <div class="character-panel">
            <img src="assets/img/assistant.png" alt="Smart Assistant" class="assistant-character">
            <h2 style="font-size: 1.5rem; margin:0 0 0.5rem 0;">Analysis Complete</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 0.95rem; line-height: 1.5; margin:0; max-width: 250px;">I've collected the exact application parameters you'll need.</p>
        </div>

        <div class="details-panel">
            
            <div class="details-header">
                <div class="ai-badge">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Sahay AI Insights
                </div>
                <h1 class="scheme-title">Application Pack for <br><span style="color:var(--brand-primary);"><?= htmlspecialchars($scheme['name']) ?></span></h1>
            </div>

            <div class="analysis-card">
                <h3><i class="fa-solid fa-list-check" style="color:var(--brand-primary);"></i> Eligibility Verdict</h3>
                <p><?= $aiSummary ?></p>
            </div>

            <div class="checklist-section">
                <h3><i class="fa-solid fa-file-shield" style="color:var(--brand-primary); margin-right: 0.5rem;"></i> Required Documentation</h3>
                <ul class="doc-list">
                    <?php foreach($documents as $doc): ?>
                        <?php if(trim($doc) !== ''): ?>
                            <li class="doc-item"><?= htmlspecialchars(trim($doc)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="margin-top: auto;">
                <a href="<?= htmlspecialchars($applyUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn-apply">
                    <span>Transact on Official Portal</span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <a href="scheme_details.php?id=<?= $id ?>" class="btn-cancel">Return to Details</a>
            </div>

        </div>

    </div>

</body>
</html>
