<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include '../conn.php';
include 'checkSession.php';

try {
    // Count Update Requests (targetID > 0)
    $updateSql = "SELECT COUNT(*) as count FROM updateItems WHERE targetID > 0";
    $updateResult = $conn->query($updateSql);
    $updateCount = $updateResult->fetch_assoc()['count'];

    // Count New Item Requests (targetID == 0)
    $newSql = "SELECT COUNT(*) as count FROM updateItems WHERE targetID = 0";
    $newResult = $conn->query($newSql);
    $newCount = $newResult->fetch_assoc()['count'];

    echo json_encode([
        "success" => true,
        "counts" => [
            "updates" => intval($updateCount),
            "new_items" => intval($newCount)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching counts: " . $e->getMessage()
    ]);
}

$conn->close();
?>
