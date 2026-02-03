<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Fetch Key Metrics
// Total Clients
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'");
$total_clients = $stmt->fetchColumn();

// Pending Requests
$stmt = $pdo->query("SELECT COUNT(*) FROM installation_requests WHERE status = 'pending'");
$pending_requests = $stmt->fetchColumn();

// Active Jobs (Approved, Assigned, In Progress)
$stmt = $pdo->query("SELECT COUNT(*) FROM installation_requests WHERE status IN ('approved', 'assigned', 'in_progress')");
$active_jobs = $stmt->fetchColumn();

// Unread Messages
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unread_messages = $stmt->fetchColumn();

// 2. Fetch Recent Requests
$stmt = $pdo->query("
    SELECT r.*, u.full_name as client_name, s.name as service_name, tech.full_name as tech_name
    FROM installation_requests r 
    JOIN users u ON r.client_id = u.id 
    JOIN services s ON r.service_id = s.id 
    LEFT JOIN users tech ON r.technician_id = tech.id
    ORDER BY r.created_at DESC 
    LIMIT 20
");
$recent_requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="padding-top: 8rem;">
    <div class="glass-panel animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 style="margin-bottom: 0.5rem;">Admin Dashboard</h2>
                <p style="color: var(--text-muted);">Overview of system performance and requests</p>
            </div>
            <div>
                <span style="margin-right: 1rem; color: var(--text-muted);">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                <a href="api/auth/logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>

        <div class="card-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary-color);">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Total Clients</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?php echo $total_clients; ?></p>
                <p style="color: var(--text-muted);">Registered Accounts</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(251, 191, 36, 0.1); color: #fbbf24;">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Pending Requests</h3>
                <p style="font-size: 2rem; font-weight: 700; color: #fbbf24;"><?php echo $pending_requests; ?></p>
                <p style="color: var(--text-muted);">Awaiting Approval</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Active Jobs</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--success-color);"><?php echo $active_jobs; ?></p>
                <p style="color: var(--text-muted);">Currently In Progress</p>
            </div>

            <a href="admin_messages.php" style="text-decoration: none; display: block;">
                <div class="feature-card" style="transition: transform 0.2s; cursor: pointer;">
                    <div class="feature-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Unread Messages</h3>
                    <p style="font-size: 2rem; font-weight: 700; color: #ef4444;"><?php echo $unread_messages; ?></p>
                    <p style="color: var(--text-muted);">From Contact Form</p>
                </div>
            </a>
        </div>

        <h3 style="margin-top: 3rem; margin-bottom: 1.5rem;">Recent Installation Requests</h3>
        <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem;">ID</th>
                        <th style="padding: 1rem;">Client</th>
                        <th style="padding: 1rem;">Service</th>
                        <th style="padding: 1rem;">Assigned To</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Date</th>
                        <th style="padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_requests) > 0): ?>
                        <?php foreach ($recent_requests as $req): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 1rem;">#<?php echo $req['id']; ?></td>
                                <td style="padding: 1rem;">
                                    <?php echo htmlspecialchars($req['client_name']); ?>
                                </td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($req['service_name']); ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($req['tech_name']): ?>
                                        <span style="color: var(--text-color); font-size: 0.9rem;">
                                            <i class="fas fa-user-cog" style="margin-right: 0.3rem; color: var(--primary-color);"></i>
                                            <?php echo htmlspecialchars($req['tech_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php 
                                        $statusColor = '#fbbf24'; // Pending
                                        if ($req['status'] == 'completed') $statusColor = '#10b981';
                                        elseif ($req['status'] == 'cancelled' || $req['status'] == 'rejected') $statusColor = '#ef4444';
                                        elseif ($req['status'] == 'in_progress') $statusColor = '#3b82f6';
                                        elseif ($req['status'] == 'assigned') $statusColor = '#8b5cf6';
                                        elseif ($req['status'] == 'approved') $statusColor = '#3b82f6';
                                    ?>
                                    <span style="color: <?php echo $statusColor; ?>; text-transform: capitalize; font-weight: 600;">
                                        <?php echo htmlspecialchars(str_replace('_', ' ', $req['status'])); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <a href="admin_request_details.php?id=<?php echo $req['id']; ?>" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; text-decoration: none;">Manage</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                No installation requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
