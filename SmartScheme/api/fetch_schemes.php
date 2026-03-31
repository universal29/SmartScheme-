<?php
// api/fetch_schemes.php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$query = "SELECT * FROM schemes WHERE 1=1";
$params = [];

if (!empty($_GET['q'])) {
    $search = "%" . $_GET['q'] . "%";
    $query .= " AND (name LIKE ? OR description LIKE ? OR ministry LIKE ?)";
    array_push($params, $search, $search, $search);
}

if (!empty($_GET['module'])) {
    $query .= " AND module_category = ?";
    array_push($params, $_GET['module']);
}

if (!empty($_GET['state'])) {
    $stateFilter = sanitize($_GET['state']);
    if ($stateFilter !== 'All India') {
        // If they pick a specific state, show Schemes for that state OR centralized All India schemes
        $query .= " AND (state = ? OR state = 'All India' OR state IS NULL)";
        array_push($params, $stateFilter);
    }
}

// Extra filters: category, income_range etc. if provided via URL
if (!empty($_GET['sort'])) {
    if($_GET['sort'] === 'popular') $query .= " ORDER BY popularity DESC";
    else if($_GET['sort'] === 'benefit') $query .= " ORDER BY benefit_value DESC";
    else if($_GET['sort'] === 'recent') $query .= " ORDER BY launch_date DESC";
} else {
    $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schemes = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'data' => $schemes]);
?>
