<?php
// api/recommend.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Fetch Profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

if (!$profile) {
    echo json_encode(['status' => 'success', 'data' => []]);
    exit;
}

// Age calc
$age = 0;
if ($profile['dob']) {
    $dob = new DateTime($profile['dob']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

$incomeNumeric = 0;
if($profile['income_range'] == 'Below 1 Lakh') $incomeNumeric = 50000;
else if($profile['income_range'] == '1-5 Lakhs') $incomeNumeric = 250000;
else if($profile['income_range'] == 'Above 5 Lakhs') $incomeNumeric = 600000;

// Fetch all schemes
$stmt = $pdo->query("SELECT * FROM schemes");
$allSchemes = $stmt->fetchAll();

$recommended = [];
// Get Max Popularity and Benefit for normalization
$maxPop = 1; $maxBen = 1;
foreach($allSchemes as $s) {
    if($s['popularity'] > $maxPop) $maxPop = $s['popularity'];
    if($s['benefit_value'] > $maxBen) $maxBen = $s['benefit_value'];
}

foreach ($allSchemes as $scheme) {
    $rules = json_decode($scheme['rules_json'], true) ?: [];
    
    $eligibilityScore = 1.0; // Starts at 100%
    $misses = 0;
    
    // Penalize score for each missed rule
    if (isset($rules['age_min']) && $age < $rules['age_min']) $misses++;
    if (isset($rules['gender']) && !in_array($profile['gender'], $rules['gender'])) $misses++;
    if (isset($rules['category']) && !in_array($profile['category'], $rules['category'])) $misses++;
    if (isset($rules['is_farmer']) && $rules['is_farmer'] && !$profile['is_farmer']) $misses++;
    if (isset($rules['bpl_status']) && $rules['bpl_status'] && !$profile['bpl_status']) $misses++;
    if (isset($rules['income_max']) && $incomeNumeric > $rules['income_max']) $misses++;

    $totalRules = count($rules) ?: 1;
    $eligibilityPercent = max(0, 1 - ($misses / $totalRules));
    
    // The Match algorithm: 50% eligibility, 20% pop, 20% benefit, 10% preference
    // Normalized factors
    $popFactor = $scheme['popularity'] / $maxPop;
    $benFactor = $scheme['benefit_value'] / $maxBen;
    
    // For preference: Give a 10% boost if state or some arbitrary metric matches. Assumed 1 for now if profile is complete.
    $prefFactor = ($profile['completeness_score'] > 50) ? 1.0 : 0.5;

    $finalScore = ($eligibilityPercent * 50) + ($popFactor * 20) + ($benFactor * 20) + ($prefFactor * 10);
    
    if ($eligibilityPercent > 0.5) { // Only recommend if at least 50% eligible
        // Add reason
        $reason = "Based on your Profile match.";
        if($eligibilityPercent == 1.0) $reason = "Highest eligibility match!";
        else if ($scheme['benefit_value'] > 100000) $reason = "High benefit value for your category.";
        
        $recommended[] = [
            'scheme' => $scheme,
            'match_percent' => round($finalScore),
            'reason' => $reason
        ];
    }
}

// Sort by match_percent DESC
usort($recommended, function($a, $b) {
    return $b['match_percent'] <=> $a['match_percent'];
});

// Take top 10
$top10 = array_slice($recommended, 0, 10);

echo json_encode(['status' => 'success', 'data' => $top10]);
?>
