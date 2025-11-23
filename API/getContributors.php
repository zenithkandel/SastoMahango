<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include 'conn.php';

// Fetch contributors and their contribution count
$sql = "SELECT c.id, c.full_name, c.email, COUNT(i.id) as contributions 
        FROM contributors c 
        LEFT JOIN items i ON c.id = i.created_by 
        GROUP BY c.id 
        ORDER BY contributions DESC";

$result = $conn->query($sql);

$contributors = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $contributors[] = [
            'id' => $row['id'],
            'name' => $row['full_name'],
            'email' => $row['email'],
            'contributions' => intval($row['contributions'])
        ];
    }
}

echo json_encode($contributors);

$conn->close();
?>
