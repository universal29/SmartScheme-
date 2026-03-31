<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartScheme Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="brand">SmartAdmin</div>
            <nav class="user-nav">
                <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Dashboard</a>
                <a href="manage_citizens.php" class="<?= $current_page == 'manage_citizens.php' ? 'active' : '' ?>">Manage Citizens</a>
                <a href="support_tickets.php" class="<?= $current_page == 'support_tickets.php' ? 'active' : '' ?>">Support Tickets</a>
                <a href="analytics.php" class="<?= $current_page == 'analytics.php' ? 'active' : '' ?>">Scheme Analytics</a>
                <a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">Platform Settings</a>
                <a href="../api/logout.php" class="logout" style="margin-top: 2rem;">Logout</a>
            </nav>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar">
                <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
            </header>
            <div class="admin-content">
