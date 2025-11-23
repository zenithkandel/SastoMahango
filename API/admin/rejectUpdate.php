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
        "message" => "Missing request ID"
    ]);
    exit;
}

$requestId = $data['id'];

try {
    // 1. Fetch request details for logging
    $sql = "SELECT u.name as item_name, u.targetID, c.full_name as contributor_name 
            FROM updateItems u 
            LEFT JOIN contributors c ON u.modified_by = c.id 
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Request not found"
        ]);
        exit;
    }

    $row = $result->fetch_assoc();
    $itemName = $row['item_name'];
    $targetID = intval($row['targetID']);
    $contributorName = $row['contributor_name'] ?? 'Unknown';
    $stmt->close();

    // 2. Delete from updateItems
    $deleteSql = "DELETE FROM updateItems WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $requestId);
    
    if ($deleteStmt->execute()) {
        // 3. Log to admin.log
        $actionType = ($targetID > 0) ? "rejected the update for" : "rejected the creation of";
        $logMessage = "[" . date('Y-m-d H:i:s') . "] Admin {$actionType} item '{$itemName}' requested by {$contributorName}.\n";
        $logFile = '../../admin.log'; // Root directory relative to API/admin/
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        echo json_encode([
            "success" => true,
            "message" => "Request rejected successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete request"
        ]);
    }
    $deleteStmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>