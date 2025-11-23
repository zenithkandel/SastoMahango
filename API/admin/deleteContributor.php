<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing contributor ID"
    ]);
    exit;
}

$id = $data['id'];

try {
    $sql = "DELETE FROM contributors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Contributor deleted successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Contributor not found"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete contributor"
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>