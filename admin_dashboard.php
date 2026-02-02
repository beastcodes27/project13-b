<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container" style="padding-top: 8rem;">
    <div class="glass-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Admin Dashboard</h2>
            <div>
                <span style="margin-right: 1rem; color: var(--text-muted);">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="api/auth/logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>

        <div class="card-grid">
            <div class="feature-card">
                <h3>Total Users</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">12</p>
                <p style="color: var(--text-muted);">Registered Client Accounts</p>
            </div>
            
            <div class="feature-card">
                <h3>Pending Requests</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--accent-color);">4</p>
                <p style="color: var(--text-muted);">Awaiting Approval</p>
            </div>
            
            <div class="feature-card">
                <h3>Active Jobs</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--success-color);">7</p>
                <p style="color: var(--text-muted);">Currently In Progress</p>
            </div>
        </div>

        <h3 style="margin-top: 3rem; margin-bottom: 1.5rem;">Recent Installation Requests</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem;">ID</th>
                        <th style="padding: 1rem;">Client</th>
                        <th style="padding: 1rem;">Service</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Date</th>
                        <th style="padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder Data -->
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 1rem;">#1001</td>
                        <td style="padding: 1rem;">Jane Smith</td>
                        <td style="padding: 1rem;">CCTV Installation</td>
                        <td style="padding: 1rem;"><span style="color: #fbbf24;">Pending</span></td>
                        <td style="padding: 1rem;">2023-10-25</td>
                        <td style="padding: 1rem;">
                            <button class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</button>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 1rem;">#1002</td>
                        <td style="padding: 1rem;">Robert Johnson</td>
                        <td style="padding: 1rem;">Electric Fence</td>
                        <td style="padding: 1rem;"><span style="color: #3b82f6;">Assigned</span></td>
                        <td style="padding: 1rem;">2023-10-24</td>
                        <td style="padding: 1rem;">
                            <button class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
