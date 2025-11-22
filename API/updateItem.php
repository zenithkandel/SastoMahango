<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'conn.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID is required']);
    exit;
}

$id = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$category = $conn->real_escape_string($data['category']);
$unit = $conn->real_escape_string($data['unit']);
$price = floatval($data['price']);
$icon = $conn->real_escape_string($data['icon']);
$tags = $conn->real_escape_string($data['tags']);

// Fetch current item to check price
$sql = "SELECT price FROM items WHERE id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentPrice = floatval($row['price']);
    
    $updateSql = "";
    
    // Check if price has changed (using a small epsilon for float comparison)
    if (abs($price - $currentPrice) > 0.01) {
        // Price changed - Update price related fields and timestamp
        $change = $price - $currentPrice;
        $trend = ($change > 0) ? 'up' : 'down';
        $previousPrice = $currentPrice;
        
        $updateSql = "UPDATE items SET 
            name = '$name', 
            category = '$category', 
            unit = '$unit', 
            price = $price, 
            previous_price = $previousPrice, 
            `change` = $change, 
            trend = '$trend', 
            icon = '$icon', 
            tags = '$tags', 
            last_updated = NOW() 
            WHERE id = $id";
    } else {
        // Price unchanged - Preserve last_updated
        $updateSql = "UPDATE items SET 
            name = '$name', 
            category = '$category', 
            unit = '$unit', 
            icon = '$icon', 
            tags = '$tags', 
            last_updated = last_updated 
            WHERE id = $id";
    }
    
    if ($conn->query($updateSql)) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
}

$conn->close();
?>
