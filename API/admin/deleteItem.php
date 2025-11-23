<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../conn.php';
include 'checkSession.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing item ID"
    ]);
    exit;
}

$id = $data['id'];

try {
    // Fetch item name for logging
    $nameSql = "SELECT name FROM items WHERE id = ?";
    $nameStmt = $conn->prepare($nameSql);
    $nameStmt->bind_param("i", $id);
    $nameStmt->execute();
    $nameResult = $nameStmt->get_result();
    $itemName = ($nameResult->num_rows > 0) ? $nameResult->fetch_assoc()['name'] : "Unknown Item";
    $nameStmt->close();

    $sql = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Log to admin.log
            $logMessage = "[" . date('Y-m-d H:i:s') . "] Admin deleted item '{$itemName}' (ID: {$id}).\n";
            $logFile = '../../admin.log';
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            echo json_encode([
                "success" => true,
                "message" => "Item deleted successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Item not found"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete item"
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