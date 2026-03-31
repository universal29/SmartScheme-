<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? 'Support Inquiry');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Name, email, and message are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO support_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        echo json_encode(['status' => 'success', 'message' => 'Message successfully sent to our Support Team! We will get back to you shortly.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message over the network. Please try again.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method.']);
}
?>
