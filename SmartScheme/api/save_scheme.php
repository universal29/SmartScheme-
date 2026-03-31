<?php
// api/save_scheme.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $schemeId = (int)($_POST['scheme_id'] ?? 0);

    if (!$schemeId) {
        echo json_encode(['status' => 'error', 'message' => 'Scheme ID required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO saved_schemes (user_id, scheme_id) VALUES (?, ?)");
        $stmt->execute([$userId, $schemeId]);
        echo json_encode(['status' => 'success', 'message' => 'Scheme saved successfully.']);
    } catch (\PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Duplicate key
            echo json_encode(['status' => 'error', 'message' => 'Scheme is already saved.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'System error.']);
        }
    }
}
?>
