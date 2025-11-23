<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include '../conn.php';
include 'checkSession.php';

try {
    $sql = "SELECT u.*, c.full_name as contributor_name 
            FROM updateItems u 
            LEFT JOIN contributors c ON u.modified_by = c.id 
            ORDER BY u.id DESC";
            
    $result = $conn->query($sql);

    $requests = array();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Fetch current item details for comparison
            $targetID = intval($row['targetID']);
            
            if ($targetID > 0) {
                $itemSql = "SELECT name, price, category, unit, icon, tags FROM items WHERE id = $targetID";
                $itemResult = $conn->query($itemSql);
                if ($itemResult && $itemResult->num_rows > 0) {
                    $currentItem = $itemResult->fetch_assoc();
                    $row['current_item'] = $currentItem;
                    $row['type'] = 'update';
                } else {
                    // Item might have been deleted?
                    $row['current_item'] = null;
                    $row['type'] = 'unknown';
                }
            } else {
                // New Item Request
                $row['current_item'] = null;
                $row['type'] = 'create';
            }
            
            $requests[] = $row;
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $requests
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching requests: " . $e->getMessage()
    ]);
}

$conn->close();
?>