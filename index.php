<?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container animate-fade-in">
            <h1>Advanced Security for<br>Modern Life</h1>
            <p>SmartSecure Solutions provides cutting-edge installation and maintenance services for CCTV, alarm systems, and access control. Secure your world today.</p>
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
                    High-definition surveillance systems with remote monitoring capabilities to keep your property safe 24/7.
                </p>
            </div>

            <!-- Service 2 -->
            <div class="feature-card animate-fade-in delay-200">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3>Alarm Systems</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    State-of-the-art intruder detection systems that alert you and authorities instantly upon authorized entry.
                </p>
            </div>

            <!-- Service 3 -->
            <div class="feature-card animate-fade-in delay-300">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Electric Fences</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">
                    Powerful perimeter security solutions designed to deter and detect any attempts to breach your property.
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
</main>

<?php include 'includes/footer.php'; ?>
