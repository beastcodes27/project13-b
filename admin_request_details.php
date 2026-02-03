<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$request_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$request_id) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle Status/Technician Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_request'])) {
    $new_status = $_POST['status'];
    $tech_id = !empty($_POST['technician_id']) ? $_POST['technician_id'] : null;
    $admin_notes = trim($_POST['admin_notes'] ?? '');
    
    // If a technician is selected but status is still pending/approved, auto-set to assigned
    if ($tech_id && in_array($new_status, ['pending', 'approved'])) {
        $new_status = 'assigned';
    }

    try {
        $stmt = $pdo->prepare("UPDATE installation_requests SET status = ?, technician_id = ?, admin_notes = ? WHERE id = ?");
        $stmt->execute([$new_status, $tech_id, $admin_notes, $request_id]);
        $msg = "Request updated successfully!";
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}

// Fetch all technicians
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role = 'technician' ORDER BY full_name ASC");
$technicians = $stmt->fetchAll();

// Fetch Request Details
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as client_name, u.email as client_email, u.phone as client_phone, s.name as service_name 
    FROM installation_requests r 
    JOIN users u ON r.client_id = u.id 
    JOIN services s ON r.service_id = s.id 
    WHERE r.id = ?
");
$stmt->execute([$request_id]);
$request = $stmt->fetch();

if (!$request) {
    header("Location: admin_dashboard.php?error=not_found");
    exit;
}

include 'includes/header.php';
?>

<div class="container" style="padding-top: 8rem; padding-bottom: 4rem;">
    <div class="glass-panel animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Request Details #<?php echo $request['id']; ?></h2>
            <a href="admin_dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <?php if (isset($msg)): ?>
            <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));">
            <!-- Client Info -->
            <div style="padding: 1.5rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Client Information</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($request['client_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($request['client_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($request['client_phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($request['address']); ?></p>
            </div>

            <!-- Request Info -->
            <div style="padding: 1.5rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Request Information</h3>
                <p><strong>Service:</strong> <?php echo htmlspecialchars($request['service_name']); ?></p>
                <p><strong>Property:</strong> <?php echo ucfirst($request['property_type']); ?></p>
                <p><strong>Preferred Date:</strong> <?php echo $request['preferred_date'] ? date('M d, Y', strtotime($request['preferred_date'])) : 'N/A'; ?></p>
                <p><strong>Requested On:</strong> <?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></p>
            </div>
        </div>

        <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
            <h3 style="margin-bottom: 1rem; color: var(--primary-color);">Description / Notes</h3>
            <p style="color: var(--text-color); line-height: 1.6; margin-bottom: 1.5rem;">
                <?php echo nl2br(htmlspecialchars($request['description'] ?: 'No additional details provided.')); ?>
            </p>

            <?php if ($request['status'] === 'rejected' && $request['rejection_reason']): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 1rem; border-radius: 8px;">
                    <strong style="color: #ef4444; display: block; margin-bottom: 0.5rem;"><i class="fas fa-exclamation-triangle"></i> Technician Rejection Reason:</strong>
                    <p style="color: var(--text-color);"><?php echo nl2br(htmlspecialchars($request['rejection_reason'])); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Management Actions -->
        <div style="margin-top: 2rem; padding: 2.0rem; background: rgba(255,255,255,0.05); border-radius: var(--radius-md); border: 1px solid var(--primary-color);">
            <h3 style="margin-bottom: 1.5rem;">Manage Request</h3>
            <form action="" method="POST">
                <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="status" class="form-label">Current Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" <?php echo $request['status'] == 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                            <option value="approved" <?php echo $request['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="assigned" <?php echo $request['status'] == 'assigned' ? 'selected' : ''; ?>>Assigned (Waiting for Technician)</option>
                            <option value="in_progress" <?php echo $request['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress (Accepted)</option>
                            <option value="completed" <?php echo $request['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $request['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="rejected" <?php echo $request['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected by Technician</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="technician_id" class="form-label">Assign Technician</label>
                        <select name="technician_id" id="technician_id" class="form-control">
                            <option value="">-- Not Assigned --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?php echo $tech['id']; ?>" <?php echo $request['technician_id'] == $tech['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tech['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="admin_notes" class="form-label">Admin Notes (visible to technician)</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" placeholder="Add instructions for the technician..."><?php echo htmlspecialchars($request['admin_notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="update_request" class="btn btn-primary">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Update Request Details
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/header.php'; ?>
