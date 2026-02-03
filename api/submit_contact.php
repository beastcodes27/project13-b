<?php
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($message)) {
        header("Location: ../contact.html?error=empty_fields");
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        
        // Redirect with success message
        header("Location: ../contact.html?msg=sent");
        exit;
    } catch (PDOException $e) {
        header("Location: ../contact.html?error=server_error");
        exit;
    }
} else {
    header("Location: ../contact.html");
    exit;
}
?>
