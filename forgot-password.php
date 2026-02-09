<?php
session_start();
require_once 'config/db.php';

$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        $message = "Please enter your email address.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {
        // Find user by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Security best practice: Don't reveal if email exists
        // We always show the same success message
        if ($user) {
            // Generate a secure random token
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $expiry = date('Y-m-d H:i:s', time() + 1800); // 30 minutes

            // Store in database (overwrite any existing token for this email)
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);

            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token_hash, $expiry]);

            // Simulate sending email (in a real app, use PHPMailer or mail())
            // For this demo, we'll just log it or provide the link if in debug mode
            $base_url = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $reset_link = $base_url . "/reset-password.php?token=" . $token;
            
            // In a real scenario, you would do:
            // mail($email, "Password Reset", "Click here to reset: " . $reset_link);
            
            // For development/demo purposes, we'll store the link in session to show it
            $_SESSION['debug_reset_link'] = $reset_link;
        }

        $message = "If an account exists with that email, you will receive a password reset link shortly.";
        $message_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SmartSecure Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-content">
            <a href="index.html" class="nav-logo">
                <i class="fas fa-shield-alt"></i> SmartSecure
            </a>
            <div class="nav-auth">
                <a href="login.html" class="btn btn-outline">Log In</a>
            </div>
        </div>
    </nav>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding-top: 80px;">
        <div class="auth-card animate-fade-in" style="width: 100%; max-width: 400px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; margin-bottom: 1rem; color: var(--primary-color); font-size: 1.5rem;">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Forgot Password</h2>
                <p style="color: var(--text-muted);">Enter your email to reset your password</p>
            </div>

            <?php if ($message): ?>
                <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background: <?php echo $message_type === 'success' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; color: <?php echo $message_type === 'success' ? '#059669' : '#dc2626'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>;">
                    <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="margin-right: 0.5rem;"></i>
                    <?php echo $message; ?>
                </div>
                <?php if (isset($_SESSION['debug_reset_link'])): ?>
                    <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; font-size: 0.875rem;">
                        <strong>Debug Info (Simulation):</strong><br>
                        Email would be sent with this link:<br>
                        <a href="<?php echo $_SESSION['debug_reset_link']; ?>" style="word-break: break-all; color: #b45309; text-decoration: underline;">
                            <?php echo $_SESSION['debug_reset_link']; ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="forgot-password.php" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div style="position: relative;">
                        <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com" style="padding-left: 2.5rem;">
                        <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Send Reset Link <i class="fas fa-paper-plane" style="margin-left: 0.5rem;"></i>
                </button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
                Remember your password? <a href="login.html" style="color: var(--primary-color); font-weight: 600;">Log in</a>
            </p>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SmartSecure Solutions. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
