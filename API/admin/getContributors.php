<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include '../conn.php';

try {
    $sql = "SELECT id, full_name, email, phone, last_login FROM contributors ORDER BY id DESC";
    $result = $conn->query($sql);

    $contributors = array();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $contributors[] = $row;
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $contributors
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching contributors: " . $e->getMessage()
    ]);
}

$conn->close();
?>