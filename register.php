<?php include 'includes/header.php'; ?>

<div class="container"
    style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding-top: 100px; padding-bottom: 4rem;">
    <div class="auth-card animate-fade-in" style="width: 100%; max-width: 500px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div
                style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; margin-bottom: 1rem; color: var(--success-color); font-size: 1.5rem;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Create Account</h2>
            <p style="color: var(--text-muted);">Join SmartSecure Solutions today</p>
        </div>

        <form action="api/auth/register.php" method="POST">
            <div class="form-group">
                <label for="fullname" class="form-label">Full Name</label>
                <div style="position: relative;">
                    <input type="text" id="fullname" name="fullname" class="form-control" required
                        placeholder="John Doe" style="padding-left: 2.5rem;">
                    <i class="fas fa-user"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div style="position: relative;">
                    <input type="email" id="email" name="email" class="form-control" required
                        placeholder="name@example.com" style="padding-left: 2.5rem;">
                    <i class="fas fa-envelope"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <div style="position: relative;">
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+1234567890"
                        style="padding-left: 2.5rem;">
                    <i class="fas fa-phone"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" required
                        placeholder="••••••••" style="padding-left: 2.5rem;">
                    <i class="fas fa-lock"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                        required placeholder="••••••••" style="padding-left: 2.5rem;">
                    <i class="fas fa-lock"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Sign Up <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Log
                in</a>
        </p>
    </div>
</div>

<script>
    // Check for password match
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    form.addEventListener('submit', function (e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        let msg = '';
        if (error === 'empty_fields') msg = 'Please fill in all fields.';
        else if (error === 'password_mismatch') msg = 'Passwords do not match.';
        else if (error === 'email_taken') msg = 'Email is already registered.';
        else if (error === 'registration_failed') msg = 'Registration failed. Try again.';
        else if (error === 'server_error') msg = 'Server error. Please try again later.';

        if (msg) {
            const popup = document.createElement('div');
            popup.style.position = 'fixed';
            popup.style.top = '20px';
            popup.style.left = '50%';
            popup.style.transform = 'translateX(-50%)';
            popup.style.backgroundColor = '#ef4444';
            popup.style.color = 'white';
            popup.style.padding = '1rem 2rem';
            popup.style.borderRadius = '8px';
            popup.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            popup.style.zIndex = '1000';
            popup.style.display = 'flex';
            popup.style.alignItems = 'center';

            popup.innerHTML = `<i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> ${msg}`;
            document.body.appendChild(popup);
            setTimeout(() => popup.remove(), 5000);
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
