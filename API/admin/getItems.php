<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include '../conn.php';
include 'checkSession.php';

try {
    $sql = "SELECT * FROM items ORDER BY id DESC";
    $result = $conn->query($sql);

    $items = array();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $items
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching items: " . $e->getMessage()
    ]);
}

$conn->close();
?>