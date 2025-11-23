<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../conn.php';
include 'checkSession.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name']) || !isset($data['category']) || !isset($data['price']) || !isset($data['unit'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

$name = $data['name'];
$category = $data['category'];
$price = $data['price'];
$unit = $data['unit'];
$icon = isset($data['icon']) ? $data['icon'] : 'fa-box';
$status = isset($data['status']) ? $data['status'] : 'active';
$created_by = $_SESSION['admin_id']; // Assuming admins can create items directly

try {
    $sql = "INSERT INTO items (name, category, price, unit, icon, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsssi", $name, $category, $price, $unit, $icon, $status, $created_by);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Item added successfully",
            "id" => $stmt->insert_id
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to add item"
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