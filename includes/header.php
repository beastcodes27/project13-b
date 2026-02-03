<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartSecure Solutions</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
</head>
<body>
    
    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.php" class="nav-logo">
                <i class="fas fa-shield-alt"></i> SmartSecure
            </a>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
            <ul class="nav-links">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#about" class="nav-link">About Us</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
            </ul>
            <?php endif; ?>
            
            <div class="nav-auth">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Hi, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                    <?php 
                        $dashboardLink = 'client_dashboard.php';
                        if (isset($_SESSION['user_role'])) {
                            if ($_SESSION['user_role'] === 'admin') {
                                $dashboardLink = 'admin_dashboard.php';
                            } elseif ($_SESSION['user_role'] === 'technician') {
                                $dashboardLink = 'technician_dashboard.php';
                            }
                        }
                    ?>
                    <a href="<?php echo $dashboardLink; ?>" class="btn btn-outline" style="margin-right: 0.5rem;">Dashboard</a>
                    <a href="api/auth/logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline" style="margin-right: 1rem;">Log In</a>
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                <?php endif; ?>
            </div>

            <div class="menu-toggle" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        if (menuBtn && navLinks) {
            menuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                const icon = menuBtn.querySelector('i');
                if (navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }

        // Close menu when clicking links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (navLinks) navLinks.classList.remove('active');
                const icon = menuBtn ? menuBtn.querySelector('i') : null;
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    </script>
