<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION['user_id'];
    $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
    $property_type = trim($_POST['property_type']);
    $address = trim($_POST['address']);
    $preferred_date = !empty($_POST['preferred_date']) ? $_POST['preferred_date'] : null;
    $description = trim($_POST['description']);

    // Validation
    if (!$service_id || empty($address)) {
        header("Location: ../../client_dashboard.php?error=missing_fields");
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO installation_requests 
            (client_id, service_id, property_type, address, preferred_date, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        $stmt->execute([
            $client_id, 
            $service_id, 
            $property_type, 
            $address, 
            $preferred_date, 
            $description
        ]);

        header("Location: ../../client_dashboard.php?msg=request_created");
        exit;

    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        header("Location: ../../client_dashboard.php?error=db_error");
        exit;
    }
} else {
    // If not POST, redirect back
    header("Location: ../../client_dashboard.php");
    exit;
}
?>
