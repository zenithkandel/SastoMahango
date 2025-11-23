<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'conn.php';
session_start();

// 1. Check Authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

$userId = $_SESSION['user_id'];

// 2. Get Input Data
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$category = $data['category'] ?? '';
$unit = $data['unit'] ?? '';
$price = floatval($data['price'] ?? 0);
$icon = $data['icon'] ?? 'fa-box';
$tags = $data['tags'] ?? '';

if (empty($name) || empty($category) || empty($unit) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields correctly.']);
    exit;
}

// 3. Insert into newItem table (Request for New Item)
// Instead of inserting into `items` directly, we insert a request into `newItem`
$sql = "INSERT INTO newItem (
            name, category, unit, price, icon, tags, created_by, status, last_updated
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssdssi", 
    $name, 
    $category, 
    $unit, 
    $price, 
    $icon, 
    $tags, 
    $userId
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'New item request submitted successfully. Pending admin approval.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit new item request: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
