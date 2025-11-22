<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include 'conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        // Update view count
        $sql = "UPDATE items SET views = views + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'View count updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update view count']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID parameter missing']);
}

$conn->close();
?>
