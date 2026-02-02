<?php include 'includes/header.php'; ?>

<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="glass-panel" style="width: 100%; max-width: 400px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back</h2>
        
        <form action="api/auth/login.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Log In</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php" style="color: var(--primary-color);">Sign up</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
