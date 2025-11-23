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
    $sql = "SELECT u.*, c.full_name as contributor_name 
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

    $request = $result->fetch_assoc();
    $stmt->close();

    $targetID = intval($request['targetID']);

    if ($targetID > 0) {
        // UPDATE Existing Item
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
        $updateStmt->bind_param("sssddssisi", 
            $request['name'],
            $request['category'],
            $request['unit'],
            $request['price'],
            $request['previous_price'],
            $request['icon'],
            $request['tags'],
            $request['modified_by'],
            $request['last_updated'],
            $targetID
        );

        if ($updateStmt->execute()) {
            $success = true;
            $message = "Request approved and item updated successfully";
        } else {
            $success = false;
            $message = "Failed to update item";
        }
        $updateStmt->close();

    } else {
        // CREATE New Item
        // We use the contributor's ID as both created_by and modified_by (or just created_by)
        // The items table has created_by. Let's check if it has modified_by. Yes, we added it.
        
        $insertSql = "INSERT INTO items (
                        name, category, unit, price, previous_price, icon, tags, created_by, modified_by, last_updated, status
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";
        
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sssddssiis", 
            $request['name'],
            $request['category'],
            $request['unit'],
            $request['price'],
            $request['previous_price'],
            $request['icon'],
            $request['tags'],
            $request['modified_by'], // created_by
            $request['modified_by'], // modified_by
            $request['last_updated']
        );

        if ($insertStmt->execute()) {
            $success = true;
            $message = "Request approved and new item created successfully";
        } else {
            $success = false;
            $message = "Failed to create item: " . $insertStmt->error;
        }
        $insertStmt->close();
    }

    if ($success) {
        // 3. Log to admin.log
        $itemName = $request['name'];
        $contributorName = $request['contributor_name'] ?? 'Unknown';
        $actionType = ($targetID > 0) ? "approved the update for" : "approved the creation of";
        
        $logMessage = "[" . date('Y-m-d H:i:s') . "] Admin {$actionType} item '{$itemName}' requested by {$contributorName}.\n";
        $logFile = '../../admin.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        // 4. Delete from updateItems
        $deleteSql = "DELETE FROM updateItems WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $requestId);
        $deleteStmt->execute();
        $deleteStmt->close();

        echo json_encode([
            "success" => true,
            "message" => $message
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => $message
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>