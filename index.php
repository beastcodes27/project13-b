<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'technician') {
        header("Location: technician_dashboard.php");
    } else {
        header("Location: client_dashboard.php");
    }
    exit;
}
include 'includes/header.php'; 
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container animate-fade-in">
            <h1>Advanced Security for<br>Modern Life</h1>
            <p>SmartSecure Solutions provides cutting-edge installation and maintenance services for CCTV, alarm
                systems, and access control. Secure your world today.</p>
            <div class="hero-actions">
                <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Request Installation</a>
                <a href="#services" class="btn btn-outline">Explore Services</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">Our Premier Services</h2>

        <div class="card-grid">
            <!-- Service 1 -->
            <div class="feature-card animate-fade-in delay-100">
                <div class="feature-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>CCTV Installation</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    High-definition surveillance systems with remote monitoring capabilities to keep your property
                    safe 24/7.
                </p>
            </div>

            <!-- Service 2 -->
            <div class="feature-card animate-fade-in delay-200">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3>Alarm Systems</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    State-of-the-art intruder detection systems that alert you and authorities instantly upon
                    authorized entry.
                </p>
            </div>

            <!-- Service 3 -->
            <div class="feature-card animate-fade-in delay-300">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Electric Fences</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    Powerful perimeter security solutions designed to deter and detect any attempts to breach your
                    property.
                </p>
            </div>

            <!-- Service 4 -->
            <div class="feature-card animate-fade-in delay-300">
                <div class="feature-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>Access Control</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    Manage who enters your premises with advanced biometric and card-based access control systems.
                </p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="container" style="padding: 4rem 1.5rem; text-align: center;">
        <h2 style="margin-bottom: 2rem;">Why Choose Us</h2>
        <div style="max-width: 800px; margin: 0 auto; color: var(--text-muted);">
            <p>We combine technical expertise with a commitment to customer satisfaction. Our certified technicians
                ensure every installation meets the highest standards of safety and reliability.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="container" style="padding: 4rem 1.5rem; margin-bottom: 4rem;">
        <div class="glass-panel" style="text-align: center;">
            <h2 style="margin-bottom: 1.5rem;">Ready to Secure Your Property?</h2>
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sent'): ?>
            <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem; max-width: 500px; margin-inline: auto;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Message sent successfully! We'll get back to you soon.
            </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; margin-bottom: 2rem; color: var(--text-muted);">
                <div><i class="fab fa-whatsapp" style="margin-right: 0.5rem; color: var(--success-color);"></i> +255
                    617 800 426</div>
                <div><i class="fas fa-map-marker-alt"
                        style="margin-right: 0.5rem; color: var(--primary-color);"></i> Magomeni, Dar es Salaam
                </div>
            </div>
            
            <!-- Contact Form Integrated -->
            <form action="api/submit_contact.php" method="POST" style="max-width: 600px; margin: 0 auto; text-align: left;">
                <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin: 0; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Your Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Your Email">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="4" required placeholder="How can we help?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
            </form>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
