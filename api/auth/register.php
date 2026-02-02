<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: ../../register.php?error=empty_fields");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: ../../register.php?error=password_mismatch");
        exit;
    }

    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: ../../register.php?error=email_taken");
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'client')");
        if ($stmt->execute([$fullname, $email, $hashed_password, $phone])) {
            header("Location: ../../login.php?success=registered");
            exit;
        } else {
            header("Location: ../../register.php?error=registration_failed");
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: ../../register.php?error=server_error");
        exit;
    }
} else {
    header("Location: ../../register.php");
    exit;
}
?>
