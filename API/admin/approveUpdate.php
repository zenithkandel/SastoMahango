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
    // 1. Fetch request details
    $sql = "SELECT * FROM updateItems WHERE id = ?";
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

    $request = $result->fetch_assoc();
    $stmt->close();

    // 2. Update items table
    $updateSql = "UPDATE items SET 
                    name = ?, 
                    category = ?, 
                    unit = ?, 
                    price = ?, 
                    previous_price = ?, 
                    icon = ?, 
                    tags = ?, 
                    modified_by = ?, 
                    last_updated = ? 
                  WHERE id = ?";
                  
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssdissisi", 
        $request['name'],
        $request['category'],
        $request['unit'],
        $request['price'],
        $request['previous_price'],
        $request['icon'],
        $request['tags'],
        $request['modified_by'],
        $request['last_updated'],
        $request['targetID']
    );

    if ($updateStmt->execute()) {
        // 3. Delete from updateItems
        $deleteSql = "DELETE FROM updateItems WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $requestId);
        $deleteStmt->execute();
        $deleteStmt->close();

        echo json_encode([
            "success" => true,
            "message" => "Request approved and item updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update item"
        ]);
    }
    $updateStmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>