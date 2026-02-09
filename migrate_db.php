<?php
require_once 'config/db.php';

try {
    
    $pdo->exec("ALTER TABLE installation_requests MODIFY COLUMN status ENUM('pending', 'approved', 'assigned', 'in_progress', 'completed', 'cancelled', 'rejected') DEFAULT 'pending'");
    
    
    $check = $pdo->query("SHOW COLUMNS FROM installation_requests LIKE 'rejection_reason'");
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE installation_requests ADD COLUMN rejection_reason TEXT AFTER admin_notes");
    }

    echo "Database updated successfully!";
} catch (PDOException $e) {
    echo "Database update error: " . $e->getMessage();
}
?>
