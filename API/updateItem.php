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

// 4. Update Database
// We explicitly update last_updated to NOW() and set updated_by to the user ID
$sql = "UPDATE items SET 
        name = ?, 
        category = ?, 
        unit = ?, 
        price = ?, 
        previous_price = ?, 
        icon = ?, 
        tags = ?, 
        last_updated = NOW(), 
        updated_by = ? 
        WHERE id = ?";

$updateStmt = $conn->prepare($sql);
if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$updateStmt->bind_param("sssdsssii", 
    $name, 
    $category, 
    $unit, 
    $price, 
    $previousPrice, 
    $icon, 
    $tags, 
    $userId, 
    $id
);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update item: ' . $updateStmt->error]);
}

$stmt->close();
$updateStmt->close();
$conn->close();
?>
