<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'role' => null
];

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
    $response['role'] = $_SESSION['user_role'] ?? 'client';
}

echo json_encode($response);
?>
