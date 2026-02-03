<?php
session_start();
require_once 'config/db.php'; // Include DB connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// 1. Fetch User Stats
// Count active requests (pending, approved, assigned, in_progress)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM installation_requests WHERE client_id = ? AND status IN ('pending', 'approved', 'assigned', 'in_progress')");
$stmt->execute([$user_id]);
$active_requests_count = $stmt->fetchColumn();

// Count total history (completed, cancelled)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM installation_requests WHERE client_id = ? AND status IN ('completed', 'cancelled')");
$stmt->execute([$user_id]);
$history_count = $stmt->fetchColumn();


// 2. Fetch Services for Dropdown
$stmt = $pdo->query("SELECT * FROM services ORDER BY name ASC");
$services = $stmt->fetchAll();

// 3. Fetch User's Requests (Recent 10)
$stmt = $pdo->prepare("
    SELECT r.*, s.name as service_name 
    FROM installation_requests r 
    JOIN services s ON r.service_id = s.id 
    WHERE r.client_id = ? 
    ORDER BY r.created_at DESC 
    LIMIT 10
");
$stmt->execute([$user_id]);
$my_requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="padding-top: 8rem;">
    <div class="glass-panel animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin-bottom: 0.5rem;">My Dashboard</h2>
                <p style="color: var(--text-muted);">Welcome back, <?php echo htmlspecialchars($user_name); ?></p>
            </div>
            <a href="api/auth/logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i> Logout</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'request_created'): ?>
        <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Your service request has been submitted successfully!
        </div>
        <?php endif; ?>

        <div class="card-grid" style="margin: 2rem 0;">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>Active Requests</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--success-color);"><?php echo $active_requests_count; ?></p>
                <p style="color: var(--text-muted);">Services in progress</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color);">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Total History</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?php echo $history_count; ?></p>
                <p style="color: var(--text-muted);">Past services</p>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3rem; margin-bottom: 1.5rem;">
            <h3>My Service Requests</h3>
            <button class="btn btn-primary" onclick="document.getElementById('newRequestForm').scrollIntoView({behavior: 'smooth'})">
                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> New Request
            </button>
        </div>

        <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem;">Service Type</th>
                        <th style="padding: 1rem;">Date Requested</th>
                        <th style="padding: 1rem;">Preferred Date</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($my_requests) > 0): ?>
                        <?php foreach ($my_requests as $req): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($req['service_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                <td style="padding: 1rem;"><?php echo $req['preferred_date'] ? date('M d, Y', strtotime($req['preferred_date'])) : 'N/A'; ?></td>
                                <td style="padding: 1rem;">
                                    <?php 
                                        $statusColor = '#fbbf24'; // Warning/Pending
                                        if ($req['status'] == 'completed') $statusColor = '#10b981';
                                        elseif ($req['status'] == 'cancelled') $statusColor = '#ef4444';
                                        elseif ($req['status'] == 'in_progress') $statusColor = '#3b82f6';
                                    ?>
                                    <span style="color: <?php echo $statusColor; ?>; text-transform: capitalize; font-weight: 600;">
                                        <?php echo htmlspecialchars(str_replace('_', ' ', $req['status'])); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--text-muted);">
                                    <?php echo htmlspecialchars(substr($req['description'], 0, 50)) . (strlen($req['description']) > 50 ? '...' : ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                No service requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="newRequestForm" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
            <h3 style="margin-bottom: 1.5rem;">Request New Service</h3>
            <form action="api/create_request.php" method="POST">
                <div class="card-grid" style="margin: 0; gap: 1.5rem; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                    <div class="form-group">
                        <label for="service_id" class="form-label">Service Type</label>
                        <select name="service_id" id="service_id" class="form-control" required>
                            <option value="">Select a service...</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="property_type" class="form-label">Property Type</label>
                        <select name="property_type" id="property_type" class="form-control" required>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="industrial">Industrial</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="preferred_date" class="form-label">Preferred Date</label>
                        <input type="date" id="preferred_date" name="preferred_date" class="form-control">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1.5rem;">
                    <label for="address" class="form-label">Installation Address</label>
                    <input type="text" id="address" name="address" class="form-control" required placeholder="Street Address, City, Region">
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Additional Details</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe your requirements..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
