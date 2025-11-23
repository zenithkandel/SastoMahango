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

if (!isset($data['name']) || !isset($data['price'])) {
    echo json_encode(['success' => false, 'message' => 'Name and Price are required']);
    exit;
}

$name = $data['name'];
$category = $data['category'] ?? 'Uncategorized';
$unit = $data['unit'] ?? 'pc';
$price = floatval($data['price']);
$icon = $data['icon'] ?? 'fa-box';
$tags = $data['tags'] ?? '';

// 3. Insert into updateItems table (Request for New Item)
// We use targetID = 0 to indicate a NEW item request
$targetID = 0;
$previousPrice = 0; // No previous price for new item

$sql = "INSERT INTO updateItems (
            targetID, name, category, unit, price, previous_price, icon, tags, modified_by, status, last_updated
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$insertStmt = $conn->prepare($sql);
if (!$insertStmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$insertStmt->bind_param("isssddssi", 
    $targetID, 
    $name, 
    $category, 
    $unit, 
    $price, 
    $previousPrice, 
    $icon, 
    $tags, 
    $userId
);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'New item request submitted successfully. Pending admin approval.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit request: ' . $insertStmt->error]);
}

$insertStmt->close();
$conn->close();
?>
