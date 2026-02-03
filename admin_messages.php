<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Mark as read if requested
if (isset($_GET['mark_read'])) {
    $msg_id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$msg_id]);
    header("Location: admin_messages.php");
    exit;
}

// Fetch Messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container" style="padding-top: 8rem; padding-bottom: 4rem;">
    <div class="glass-panel animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Contact Messages</h2>
            <a href="admin_dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div style="overflow-x: auto; background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-color);">
                <thead>
                    <tr style="text-align: left; border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem;">Date</th>
                        <th style="padding: 1rem;">Sender</th>
                        <th style="padding: 1rem;">Message</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) > 0): ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); <?php echo !$msg['is_read'] ? 'background: rgba(59, 130, 246, 0.05);' : ''; ?>">
                                <td style="padding: 1rem; white-space: nowrap;"><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($msg['name']); ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($msg['email']); ?></div>
                                </td>
                                <td style="padding: 1rem; max-width: 400px;">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php if (!$msg['is_read']): ?>
                                        <span style="color: var(--accent-color); font-weight: 600;">New</span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <?php if (!$msg['is_read']): ?>
                                        <a href="admin_messages.php?mark_read=<?php echo $msg['id']; ?>" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">Mark Read</a>
                                    <?php else: ?>
                                        <span style="color: var(--success-color);"><i class="fas fa-check"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                No messages found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
