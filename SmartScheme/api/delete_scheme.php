<?php
// api/delete_scheme.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schemeId = (int)($_POST['scheme_id'] ?? 0);
    if ($schemeId) {
        $pdo->prepare("DELETE FROM schemes WHERE id = ?")->execute([$schemeId]);
        echo json_encode(['status' => 'success', 'message' => 'Scheme deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
    }
}
?>
