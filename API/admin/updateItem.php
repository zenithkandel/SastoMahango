<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../conn.php';
include 'checkSession.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['price'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

$id = $data['id'];
$name = $data['name'];
$category = $data['category'];
$price = $data['price'];
$unit = $data['unit'];
$icon = isset($data['icon']) ? $data['icon'] : 'fa-box';
$status = isset($data['status']) ? $data['status'] : 'active';

try {
    // Get previous price for history
    $prevSql = "SELECT price FROM items WHERE id = ?";
    $prevStmt = $conn->prepare($prevSql);
    $prevStmt->bind_param("i", $id);
    $prevStmt->execute();
    $prevResult = $prevStmt->get_result();
    $previous_price = 0;
    
    if ($prevResult->num_rows > 0) {
        $row = $prevResult->fetch_assoc();
        $previous_price = $row['price'];
    }
    $prevStmt->close();

    // Update item
    $sql = "UPDATE items SET name = ?, category = ?, price = ?, unit = ?, icon = ?, status = ?, previous_price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssdi", $name, $category, $price, $unit, $icon, $status, $previous_price, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Item updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update item"
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>