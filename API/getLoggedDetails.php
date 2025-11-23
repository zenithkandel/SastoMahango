<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'isLoggedIn' => true,
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_role' => $_SESSION['user_role']
    ]);
} else {
    echo json_encode([
        'isLoggedIn' => false,
        'message' => 'User not logged in'
    ]);
}
?>
