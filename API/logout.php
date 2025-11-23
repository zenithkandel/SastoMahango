<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully',
    'redirect' => 'login.html'
]);
?>