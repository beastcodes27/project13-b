<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
    if (empty($email) || empty($password)) {
        header("Location: ../../login.html?error=empty_fields");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../../admin_dashboard.php"); 
            } elseif ($user['role'] === 'technician') {
                 // Placeholder for technician dashboard
                 header("Location: ../../technician_dashboard.php");
            } else {
                header("Location: ../../client_dashboard.php");
            }
            exit;
        } else {
            // Invalid credentials
            header("Location: ../../login.html?error=invalid_credentials");
            exit;
        }
    } catch (PDOException $e) {
        // Log error and redirect
        error_log($e->getMessage());
        header("Location: ../../login.html?error=server_error");
        exit;
    }
} else {
    // If not POST, redirect to login
    header("Location: ../../login.html");
    exit;
}
?>
