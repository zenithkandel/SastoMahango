<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access. Please login as admin.",
        "redirect" => "../login.html"
    ]);
    exit;
}
?>