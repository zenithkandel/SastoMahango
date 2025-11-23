<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include 'conn.php';

// Fetch contributors
$sql = "SELECT id, full_name, email FROM contributors ORDER BY full_name ASC";

$result = $conn->query($sql);

$contributors = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $contributors[] = [
            'id' => $row['id'],
            'name' => $row['full_name'],
            'email' => $row['email']
        ];
    }
}

echo json_encode($contributors);

$conn->close();
?>
