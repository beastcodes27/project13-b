<?php
session_start();
require_once 'config/db.php';

$message = '';
$message_type = '';
$valid_token = false;
$email = '';

// Check for token in URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token_hash = hash('sha256', $token);

    // Verify token and expiry
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token_hash = ? AND expires_at > NOW()");
    $stmt->execute([$token_hash]);
    $reset_request = $stmt->fetch();

    if ($reset_request) {
        $valid_token = true;
        $email = $reset_request['email'];
    } else {
        $message = "Invalid or expired reset link. Please request a new one.";
        $message_type = "error";
    }
} else {
    $message = "No reset token provided.";
    $message_type = "error";
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $message_type = "error";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } else {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            // Update user's password
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $email]);

            // Invalidate the token (remove it)
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);

            $pdo->commit();

            // Clear debug info
            unset($_SESSION['debug_reset_link']);

            $message = "Your password has been successfully reset. You can now log in.";
            $message_type = "success";
            $valid_token = false; // Hide the form
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "An error occurred. Please try again later.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SmartSecure Solutions</title>
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
        </div>
    </nav>

    <div class="container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding-top: 80px;">
        <div class="auth-card animate-fade-in" style="width: 100%; max-width: 400px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; margin-bottom: 1rem; color: var(--primary-color); font-size: 1.5rem;">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h2>Reset Password</h2>
                <?php if ($valid_token): ?>
                    <p style="color: var(--text-muted);">Set a new password for <strong><?php echo htmlspecialchars($email); ?></strong></p>
                <?php endif; ?>
            </div>

            <?php if ($message): ?>
                <div style="padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; background: <?php echo $message_type === 'success' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'; ?>; color: <?php echo $message_type === 'success' ? '#059669' : '#dc2626'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#10b981' : '#ef4444'; ?>;">
                    <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="margin-right: 0.5rem;"></i>
                    <?php echo $message; ?>
                </div>
                <?php if ($message_type === 'success'): ?>
                    <a href="login.html" class="btn btn-primary" style="width: 100%; display: block; text-align: center;">Go to Login</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($valid_token): ?>
                <form action="reset-password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="POST">
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••" style="padding-left: 2.5rem;">
                            <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div style="position: relative;">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="••••••••" style="padding-left: 2.5rem;">
                            <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Update Password <i class="fas fa-save" style="margin-left: 0.5rem;"></i>
                    </button>
                </form>
            <?php elseif ($message_type === 'error' && !isset($_GET['token'])): ?>
                 <a href="forgot-password.php" class="btn btn-outline" style="width: 100%; display: block; text-align: center;">Request New Link</a>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SmartSecure Solutions. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
