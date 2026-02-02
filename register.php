<?php include 'includes/header.php'; ?>

<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem 0;">
    <div class="glass-panel" style="width: 100%; max-width: 500px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
        
        <form action="api/auth/register.php" method="POST">
            <div class="form-group">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" id="fullname" name="fullname" class="form-control" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
            
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1234567890">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Sign Up</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary-color);">Log in</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
