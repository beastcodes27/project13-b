<?php
require_once 'config/db.php';

try {
    // 1. Add rejected to ENUM and add rejection_reason column
    // Note: Some MySQL versions don't support modifying ENUM easily, but this is the standard way.
    // We also use a try-catch for cases where columns might already exist.
    
    $pdo->exec("ALTER TABLE installation_requests MODIFY COLUMN status ENUM('pending', 'approved', 'assigned', 'in_progress', 'completed', 'cancelled', 'rejected') DEFAULT 'pending'");
    
    // Check if column exists first
    $check = $pdo->query("SHOW COLUMNS FROM installation_requests LIKE 'rejection_reason'");
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE installation_requests ADD COLUMN rejection_reason TEXT AFTER admin_notes");
    }

    echo "Database updated successfully!";
} catch (PDOException $e) {
    echo "Database update error: " . $e->getMessage();
}
?>
