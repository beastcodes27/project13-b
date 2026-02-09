<?php include 'includes/header.php'; ?>

<div class="container"
    style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding-top: 80px;">
    <div class="auth-card animate-fade-in" style="width: 100%; max-width: 400px;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div
                style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(211, 47, 47, 0.1); border-radius: 50%; margin-bottom: 1rem; color: var(--primary-color); font-size: 1.5rem;">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2>Welcome Back</h2>
            <p style="color: var(--text-muted);">Sign in to access your dashboard</p>
        </div>

        <form action="api/auth/login.php" method="POST">
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
                <label for="password" class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" required
                        placeholder="••••••••" style="padding-left: 2.5rem;">
                    <i class="fas fa-lock"
                        style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            <div style="text-align: right; margin-bottom: 1.5rem;">
                <a href="forgot-password.php" style="font-size: 0.875rem; color: var(--primary-color);">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Log In <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php"
                style="color: var(--primary-color); font-weight: 600;">Sign up</a>
        </p>
    </div>
</div>

<!-- Simple script to handle URL parameters for errors -->
<script>
    const form = document.querySelector('form');
    const email = document.getElementById('email');

    function showPopup(message) {
        const popup = document.createElement('div');
        popup.style.position = 'fixed';
        popup.style.top = '20px';
        popup.style.left = '50%';
        popup.style.transform = 'translateX(-50%)';
        popup.style.backgroundColor = '#ef4444';
        popup.style.color = 'white';
        popup.style.padding = '1rem 2rem';
        popup.style.borderRadius = '8px';
        popup.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1)';
        popup.style.zIndex = '1000';
        popup.style.display = 'flex';
        popup.style.alignItems = 'center';
        popup.style.animation = 'animate-fade-in 0.3s ease-out';

        popup.innerHTML = `<i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> ${message}`;
        document.body.appendChild(popup);
        setTimeout(() => {
            popup.style.opacity = '0';
            popup.style.transition = 'opacity 0.5s ease-out';
            setTimeout(() => popup.remove(), 500);
        }, 4000);
    }

    form.addEventListener('submit', function (e) {
        const emailValue = email.value.toLowerCase();
        if (!emailValue.endsWith('@gmail.com')) {
            e.preventDefault();
            showPopup('Please use your valid @gmail.com address.');
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if (error) {
        let msg = '';
        if (error === 'empty_fields') msg = 'Please fill in all fields.';
        else if (error === 'invalid_credentials') msg = 'Invalid email or password.';
        else if (error === 'server_error') msg = 'Server error. Please try again later.';

        if (msg) showPopup(msg);
    }
</script>

<?php include 'includes/footer.php'; ?>
