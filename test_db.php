<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Starting DB Test...<br>";

require_once 'config/db.php';

echo "Connected to DB.<br>";

try {
    echo "Checking users table...<br>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    echo "Users: " . $stmt->fetchColumn() . "<br>";

    echo "Checking installation_requests table...<br>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM installation_requests");
    echo "Requests: " . $stmt->fetchColumn() . "<br>";
    
    echo "Checking contact_messages table...<br>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
    echo "Messages: " . $stmt->fetchColumn() . "<br>";

} catch (PDOException $e) {
    echo "DataBase Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
?>
