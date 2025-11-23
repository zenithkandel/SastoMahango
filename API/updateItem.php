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

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Item ID is required']);
    exit;
}

$id = intval($data['id']);
$name = $data['name'] ?? '';
$category = $data['category'] ?? '';
$unit = $data['unit'] ?? '';
$price = floatval($data['price'] ?? 0);
$icon = $data['icon'] ?? '';
$tags = $data['tags'] ?? '';

// 3. Fetch Current Data (to calculate trend/change)
// We need previous values to determine if price changed
$stmt = $conn->prepare("SELECT price, previous_price FROM items WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$row = $result->fetch_assoc();
$currentPrice = floatval($row['price']);
$oldPreviousPrice = floatval($row['previous_price']);

// Calculate Change & Trend
// Only update price history if the price actually changed
if (abs($price - $currentPrice) > 0.001) {
    $previousPrice = $currentPrice;
} else {
    // Price didn't change, keep old history
    $previousPrice = $oldPreviousPrice;
}

// 4. Insert into updateItems table (Request for Update)
// Instead of updating `items` directly, we insert a request into `updateItems`
// We store the original item's ID as `targetID`
$sql = "INSERT INTO updateItems (
            targetID, name, category, unit, price, previous_price, icon, tags, modified_by, status, last_updated
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$insertStmt = $conn->prepare($sql);
if (!$insertStmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$insertStmt->bind_param("isssddssi", 
    $id, // This is the targetID (the ID of the item being edited)
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
    echo json_encode(['success' => true, 'message' => 'Update request submitted successfully. Pending admin approval.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit update request: ' . $insertStmt->error]);
}

$stmt->close();
$insertStmt->close();
$conn->close();
?>
