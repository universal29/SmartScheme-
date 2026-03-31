<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartScheme - Govt Scheme Recommendations</title>
    <!-- Google Fonts for Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🏛️ SmartScheme</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="schemes.php">Schemes</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="support.php">Support</a></li>
                <?php if(isLoggedIn()): ?>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin/index.php" class="text-primary fw-bold">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="api/logout.php" class="btn-primary" style="padding: 0.5rem 1rem;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn-primary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <main class="main-content">
