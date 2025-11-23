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
            $targetID = $row['targetID'];
            $itemSql = "SELECT name, price, category, unit, icon FROM items WHERE id = $targetID";
            $itemResult = $conn->query($itemSql);
            $currentItem = $itemResult->fetch_assoc();
            
            $row['current_item'] = $currentItem;
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