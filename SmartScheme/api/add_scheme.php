<?php
// api/add_scheme.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $ministry = sanitize($_POST['ministry'] ?? '');
    $launch_date = $_POST['launch_date'] ?? '';
    $description = sanitize($_POST['description'] ?? '');
    $benefits = sanitize($_POST['benefits'] ?? '');
    $required_docs = sanitize($_POST['required_docs'] ?? '');
    $application_steps = sanitize($_POST['application_steps'] ?? '');
    $rules_json = $_POST['rules_json'] ?? '';
    $benefit_value = (int)($_POST['benefit_value'] ?? 0);
    $module_category = sanitize($_POST['module_category'] ?? 'General');

    // Validate JSON
    if (!empty($rules_json)) {
        json_decode($rules_json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON format for rules.']);
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO schemes (name, ministry, launch_date, description, benefits, required_docs, application_steps, rules_json, benefit_value, module_category) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $name, $ministry, $launch_date, $description, $benefits, 
            $required_docs, $application_steps, $rules_json, $benefit_value, $module_category
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'Scheme published successfully!']);
    } catch (\Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'System error: ' . $e->getMessage()]);
    }
}
?>
