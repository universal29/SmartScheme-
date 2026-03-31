<?php
// api/check_eligibility.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$scheme_id = (int)($_GET['scheme_id'] ?? 0);
if (!$scheme_id) {
    echo json_encode(['status' => 'error', 'message' => 'Scheme ID required']);
    exit;
}

// Fetch Profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

if (!$profile || $profile['completeness_score'] < 30) {
    echo json_encode(['status' => 'error', 'message' => 'Profile incomplete. Please fill out your profile first.']);
    exit;
}

// Fetch Scheme Rules
$stmt = $pdo->prepare("SELECT rules_json FROM schemes WHERE id = ?");
$stmt->execute([$scheme_id]);
$scheme = $stmt->fetch();

if (!$scheme || !$scheme['rules_json']) {
    // If no rules defined, assume eligible
    echo json_encode(['status' => 'success', 'eligible' => true, 'reason' => 'No specific restrictions for this scheme.']);
    exit;
}

$rules = json_decode($scheme['rules_json'], true);
$missing = [];

// Calculate Age
$age = 0;
if ($profile['dob']) {
    $dob = new DateTime($profile['dob']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

// Check Age
if (isset($rules['age_min']) && $age < $rules['age_min']) {
    $missing[] = "Minimum age is {$rules['age_min']} years.";
}

// Check Gender
if (isset($rules['gender']) && !in_array($profile['gender'], $rules['gender'])) {
    $missing[] = "Gender must be " . implode(' or ', $rules['gender']);
}

// Check Category
if (isset($rules['category']) && !in_array($profile['category'], $rules['category'])) {
    $missing[] = "Category must be " . implode(', ', $rules['category']);
}

// Check Farmer Status
if (isset($rules['is_farmer']) && $rules['is_farmer'] && !$profile['is_farmer']) {
    $missing[] = "Must be a registered farmer.";
}

// Check BPL Status
if (isset($rules['bpl_status']) && $rules['bpl_status'] && !$profile['bpl_status']) {
    $missing[] = "Must have Below Poverty Line (BPL) status.";
}

// Income Check logic (simplified for mockup)
if (isset($rules['income_max'])) {
    $incomeNumeric = 0;
    if($profile['income_range'] == 'Below 1 Lakh') $incomeNumeric = 50000;
    else if($profile['income_range'] == '1-5 Lakhs') $incomeNumeric = 250000;
    else if($profile['income_range'] == 'Above 5 Lakhs') $incomeNumeric = 600000;
    
    if ($incomeNumeric > $rules['income_max']) {
        $missing[] = "Income exceeds the maximum limit of ₹" . number_format($rules['income_max']) . " per year.";
    }
}

if (empty($missing)) {
    echo json_encode(['status' => 'success', 'eligible' => true, 'reason' => 'You meet all the criteria based on your profile!']);
} else {
    echo json_encode(['status' => 'success', 'eligible' => false, 'missing' => $missing]);
}
?>
