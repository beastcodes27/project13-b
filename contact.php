<?php
include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero" style="padding-top: 8rem; padding-bottom: 2rem;">
        <div class="container animate-fade-in">
            <h1>By Your Side,<br>Anytime, Anywhere.</h1>
            <p style="margin-bottom: 0;">We’d love to hear from you. Reach out to us for inquiries, support, or a
                free consultation.</p>
        </div>
    </section>

    <section class="container" style="padding-bottom: 4rem;">
        <div class="glass-panel">
            <div class="card-grid"
                style="margin: 0; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">

                <!-- Contact Info -->
                <div class="animate-fade-in delay-100">
                    <h3 style="margin-bottom: 2rem;">Get In Touch</h3>

                    <div style="display: flex; align-items: start; margin-bottom: 2rem;">
                        <div
                            style="width: 50px; height: 50px; border-radius: 12px; background: rgba(211, 47, 47, 0.1); display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 1.25rem; margin-right: 1rem; flex-shrink: 0;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem; color: #fff;">WhatsApp / Phone</h4>
                            <p style="color: var(--text-muted);">+255 617 800 426</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; margin-bottom: 2rem;">
                        <div
                            style="width: 50px; height: 50px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: var(--success-color); font-size: 1.25rem; margin-right: 1rem; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem; color: #fff;">Location</h4>
                            <p style="color: var(--text-muted);">Magomeni, Dar es Salaam</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; margin-bottom: 2rem;">
                        <div
                            style="width: 50px; height: 50px; border-radius: 12px; background: rgba(93, 64, 55, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-color); font-size: 1.25rem; margin-right: 1rem; flex-shrink: 0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem; color: #fff;">Email Address</h4>
                            <p style="color: var(--text-muted);">beastcodes27@gmail.com</p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start;">
                        <div
                            style="width: 50px; height: 50px; border-radius: 12px; background: rgba(236, 72, 153, 0.1); display: flex; align-items: center; justify-content: center; color: #ec4899; font-size: 1.25rem; margin-right: 1rem; flex-shrink: 0;">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem; color: #fff;">Instagram</h4>
                            <a href="https://instagram.com/iman_ibraah" target="_blank"
                                style="color: var(--text-muted); transition: color 0.3s;"
                                onmouseover="this.style.color='var(--primary-color)'"
                                onmouseout="this.style.color='var(--text-muted)'">Iman Ibraah</a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="animate-fade-in delay-200">
                    <div id="form-alert">
                         <?php 
                            if (isset($_GET['msg']) && $_GET['msg'] === 'sent') {
                                echo '<div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                                        <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Message sent successfully! We will get back to you soon.
                                      </div>';
                            } elseif (isset($_GET['error'])) {
                                echo '<div style="background: rgba(239, 68, 68, 0.2); border: 1px solid var(--danger-color); color: #ef4444; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                                        <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> Error sending message. Please try again.
                                      </div>';
                            }
                         ?>
                    </div>
                    <form action="api/submit_contact.php" method="POST"
                        style="background: rgba(0,0,0,0.2); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--glass-border);">
                        <h3 style="margin-bottom: 1.5rem;">Send a Message</h3>

                        <div class="form-group">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="John Doe"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="name@example.com" required>
                        </div>

                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="4"
                                placeholder="How can we help you?" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section style="height: 300px; width: 100%; filter: grayscale(100%) invert(90%);">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15847.604562725546!2d39.2758!3d-6.8163!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185c4b1a1c93a007%3A0x6b4f7e5272a56767!2sMagomeni%2C%20Dar%20es%20Salaam%2C%20Tanzania!5e0!3m2!1sen!2sus!4v1699999999999!5m2!1sen!2sus"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
