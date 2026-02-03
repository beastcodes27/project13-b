<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and is a technician
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'technician') {
    header("Location: login.php");
    exit;
}

$tech_id = $_SESSION['user_id'];
$tech_name = $_SESSION['user_name'] ?? 'Technician';

// Handle job status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $job_id = $_POST['job_id'];
    $action = $_POST['action'];
    $new_status = null;
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');

    switch ($action) {
        case 'accept':
            $new_status = 'in_progress';
            break;
        case 'reject':
            if (empty($rejection_reason)) {
                $error = "Please provide a reason for rejection.";
            } else {
                $new_status = 'rejected';
            }
            break;
        case 'complete':
            $new_status = 'completed';
            break;
        case 'cancel':
            $new_status = 'cancelled';
            break;
    }

    if ($new_status) {
        // Ensure the job actually belongs to this technician
        try {
            if ($new_status === 'rejected') {
                $stmt = $pdo->prepare("UPDATE installation_requests SET status = ?, rejection_reason = ? WHERE id = ? AND technician_id = ?");
                $stmt->execute([$new_status, $rejection_reason, $job_id, $tech_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE installation_requests SET status = ? WHERE id = ? AND technician_id = ?");
                $stmt->execute([$new_status, $job_id, $tech_id]);
            }
            $msg = "Job status successfully updated to " . ucfirst(str_replace('_', ' ', $new_status)) . ".";
        } catch (PDOException $e) {
            $error = "Failed to update status: " . $e->getMessage();
        }
    }
}

// 1. Fetch NEW Assignments (Assigned - waiting for accept/reject)
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as client_name, u.phone as client_phone, s.name as service_name 
    FROM installation_requests r 
    JOIN users u ON r.client_id = u.id 
    JOIN services s ON r.service_id = s.id 
    WHERE r.technician_id = ? AND r.status = 'assigned'
    ORDER BY r.created_at DESC
");
$stmt->execute([$tech_id]);
$new_assignments = $stmt->fetchAll();

// 2. Fetch Ongoing Jobs (In Progress)
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as client_name, u.phone as client_phone, s.name as service_name 
    FROM installation_requests r 
    JOIN users u ON r.client_id = u.id 
    JOIN services s ON r.service_id = s.id 
    WHERE r.technician_id = ? AND r.status = 'in_progress'
    ORDER BY r.preferred_date ASC
");
$stmt->execute([$tech_id]);
$ongoing_jobs = $stmt->fetchAll();

// 3. Fetch Job History (Completed, Cancelled, Rejected)
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as client_name, s.name as service_name 
    FROM installation_requests r 
    JOIN users u ON r.client_id = u.id 
    JOIN services s ON r.service_id = s.id 
    WHERE r.technician_id = ? AND r.status IN ('completed', 'cancelled', 'rejected')
    ORDER BY r.updated_at DESC
    LIMIT 20
");
$stmt->execute([$tech_id]);
$job_history = $stmt->fetchAll();

include 'includes/header.php';
?>

<style>
    .job-card {
        border-radius: var(--radius-lg);
        background: var(--surface-color);
        border: 1px solid var(--glass-border);
        padding: 2rem;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .job-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .rejection-form {
        display: none;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--glass-border);
    }
</style>

<div class="container" style="padding-top: 8rem; padding-bottom: 4rem;">
    <div class="glass-panel animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin-bottom: 0.5rem;">Technician Dashboard</h2>
                <p style="color: var(--text-muted);">Welcome back, <?php echo htmlspecialchars($tech_name); ?>.</p>
            </div>
            <a href="api/auth/logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i> Logout</a>
        </div>

        <?php if (isset($msg)): ?>
            <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- New Assignments Section -->
        <section style="margin-bottom: 4rem;">
            <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; color: var(--primary-color);">
                <i class="fas fa-bell" style="margin-right: 0.75rem;"></i> New Job Requests
            </h3>
            
            <?php if (count($new_assignments) > 0): ?>
                <div class="card-grid">
                    <?php foreach ($new_assignments as $job): ?>
                        <div class="job-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                                <span style="background: var(--primary-color); color: white; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">New Request</span>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">#<?php echo $job['id']; ?></span>
                            </div>

                            <h4 style="margin-bottom: 1.25rem; font-size: 1.3rem;"><?php echo htmlspecialchars($job['service_name']); ?></h4>
                            
                            <div style="margin-bottom: 1.5rem; font-size: 0.95rem; flex-grow: 1;">
                                <p style="margin-bottom: 0.75rem;"><i class="fas fa-map-marker-alt" style="width: 25px; color: var(--primary-color);"></i> <?php echo htmlspecialchars($job['address']); ?></p>
                                <p style="margin-bottom: 0.75rem;"><i class="fas fa-calendar-day" style="width: 25px; color: var(--primary-color);"></i> Preferred: <?php echo $job['preferred_date'] ? date('M d, Y', strtotime($job['preferred_date'])) : 'Flexible'; ?></p>
                                <p style="margin-bottom: 0.75rem;"><i class="fas fa-building" style="width: 25px; color: var(--primary-color);"></i> <?php echo ucfirst($job['property_type']); ?></p>
                            </div>

                            <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; border-left: 3px solid var(--primary-color);">
                                <strong style="display: block; margin-bottom: 0.25rem;">Admin Notes:</strong>
                                <?php echo nl2br(htmlspecialchars($job['admin_notes'] ?: 'No specific instructions.')); ?>
                            </div>

                            <div id="actions-<?php echo $job['id']; ?>">
                                <div style="display: flex; gap: 0.5rem;">
                                    <form action="" method="POST" style="flex: 1;">
                                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-primary" style="width: 100%;">Accept Job</button>
                                    </form>
                                    <button onclick="toggleRejection(<?php echo $job['id']; ?>)" class="btn btn-outline" style="flex: 1; border-color: #ef4444; color: #ef4444;">Reject</button>
                                </div>

                                <div id="reject-form-<?php echo $job['id']; ?>" class="rejection-form">
                                    <form action="" method="POST">
                                        <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                        <label class="form-label" style="font-size: 0.85rem;">Reason for rejection:</label>
                                        <textarea name="rejection_reason" class="form-control" rows="2" required placeholder="Too busy, distance, etc..."></textarea>
                                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                            <button type="submit" name="action" value="reject" class="btn btn-primary" style="flex: 1; padding: 0.5rem; background: #ef4444; border-color: #ef4444;">Submit Rejection</button>
                                            <button type="button" onclick="toggleRejection(<?php echo $job['id']; ?>)" class="btn btn-outline" style="flex: 1; padding: 0.5rem;">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; background: rgba(0,0,0,0.1); border-radius: 12px; border: 1px dashed var(--glass-border);">
                    <p style="color: var(--text-muted);">No new job requests at the moment.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Ongoing Jobs Section -->
        <section style="margin-bottom: 4rem;">
            <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; color: var(--success-color);">
                <i class="fas fa-spinner" style="margin-right: 0.75rem;"></i> Ongoing Jobs
            </h3>

            <?php if (count($ongoing_jobs) > 0): ?>
                <div class="card-grid">
                    <?php foreach ($ongoing_jobs as $job): ?>
                        <div class="job-card" style="border-left: 4px solid var(--success-color);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                                <span style="background: #3b82f6; color: white; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">In Progress</span>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">#<?php echo $job['id']; ?></span>
                            </div>

                            <h4 style="margin-bottom: 1.25rem; font-size: 1.3rem;"><?php echo htmlspecialchars($job['service_name']); ?></h4>
                            
                            <div style="margin-bottom: 1.5rem; font-size: 0.95rem; flex-grow: 1;">
                                <p style="margin-bottom: 0.5rem;"><i class="fas fa-user" style="width: 25px; color: var(--text-muted);"></i> <?php echo htmlspecialchars($job['client_name']); ?></p>
                                <p style="margin-bottom: 0.5rem;"><i class="fas fa-phone" style="width: 25px; color: var(--text-muted);"></i> <?php echo htmlspecialchars($job['client_phone']); ?></p>
                                <p style="margin-bottom: 0.5rem;"><i class="fas fa-map-marker-alt" style="width: 25px; color: var(--text-muted);"></i> <?php echo htmlspecialchars($job['address']); ?></p>
                            </div>

                            <form action="" method="POST" style="display: flex; gap: 0.5rem;">
                                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                <button type="submit" name="action" value="complete" class="btn btn-primary" style="flex: 2; background: var(--success-color); border-color: var(--success-color);">Mark Complete</button>
                                <button type="submit" name="action" value="cancel" class="btn btn-outline" style="flex: 1; border-color: #ef4444; color: #ef4444;">Cancel</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; background: rgba(0,0,0,0.1); border-radius: 12px; border: 1px dashed var(--glass-border);">
                    <p style="color: var(--text-muted);">No ongoing jobs currently.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- History Section -->
        <section>
            <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center;">
                <i class="fas fa-history" style="margin-right: 0.75rem;"></i> Job History
            </h3>

            <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                            <th style="padding: 1rem;">ID</th>
                            <th style="padding: 1rem;">Client</th>
                            <th style="padding: 1rem;">Service</th>
                            <th style="padding: 1rem;">Updated On</th>
                            <th style="padding: 1rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($job_history) > 0): ?>
                            <?php foreach ($job_history as $his): ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 1rem; color: var(--text-muted);">#<?php echo $his['id']; ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($his['client_name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($his['service_name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($his['updated_at'])); ?></td>
                                    <td style="padding: 1rem;">
                                        <?php 
                                            $color = 'var(--text-muted)';
                                            if ($his['status'] === 'completed') $color = 'var(--success-color)';
                                            elseif ($his['status'] === 'rejected') $color = '#ef4444';
                                            elseif ($his['status'] === 'cancelled') $color = '#f59e0b';
                                        ?>
                                        <span style="color: <?php echo $color; ?>; font-weight: 600; text-transform: capitalize;">
                                            <?php echo htmlspecialchars($his['status']); ?>
                                            <?php if ($his['status'] === 'rejected'): ?>
                                                <i class="fas fa-info-circle" title="<?php echo htmlspecialchars($his['rejection_reason']); ?>" style="margin-left: 0.25rem; font-size: 0.8rem; cursor: help;"></i>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">No historical records.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    function toggleRejection(jobId) {
        const form = document.getElementById('reject-form-' + jobId);
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    }
</script>

<?php include 'includes/header.php'; ?>
