<?php
// api/update_profile.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    
    $dob = $_POST['dob'] ?: null;
    $gender = $_POST['gender'] ?: null;
    $state = sanitize($_POST['state'] ?? '');
    $district = sanitize($_POST['district'] ?? '');
    $income_range = $_POST['income_range'] ?: null;
    $category = $_POST['category'] ?: null;
    $is_farmer = (int)($_POST['is_farmer'] ?? 0);
    $bpl_status = (int)($_POST['bpl_status'] ?? 0);
    $looking_for_module = sanitize($_POST['looking_for_module'] ?? '');

    // Calculate Completeness Score out of 9 core fields
    $fields = [$dob, $gender, $state, $district, $income_range, $category, $looking_for_module, $is_farmer !== null ? true : false, $bpl_status !== null ? true : false];
    $filled = 0;
    foreach($fields as $f) {
        if(!empty($f)) $filled++;
    }
    $completeness = round(($filled / count($fields)) * 100);

    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, dob, gender, state, district, income_range, category, is_farmer, bpl_status, looking_for_module, completeness_score) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE 
                           dob=VALUES(dob), gender=VALUES(gender), state=VALUES(state), district=VALUES(district), 
                           income_range=VALUES(income_range), category=VALUES(category), 
                           is_farmer=VALUES(is_farmer), bpl_status=VALUES(bpl_status), looking_for_module=VALUES(looking_for_module), completeness_score=VALUES(completeness_score)");
                           
    $success = $stmt->execute([$userId, $dob, $gender, $state, $district, $income_range, $category, $is_farmer, $bpl_status, $looking_for_module, $completeness]);

    if ($success) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Profile updated successfully and recommendations refreshed!',
            'completeness' => $completeness
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update profile.']);
    }
}
?>
