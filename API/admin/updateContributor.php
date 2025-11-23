<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['full_name']) || !isset($data['email'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

$id = $data['id'];
$full_name = $data['full_name'];
$email = $data['email'];
$phone = isset($data['phone']) ? $data['phone'] : null;

try {
    // Check if email exists for other users
    $checkSql = "SELECT id FROM contributors WHERE email = ? AND id != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $email, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email already in use by another contributor"
        ]);
        exit;
    }

    $sql = "UPDATE contributors SET full_name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $email, $phone, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Contributor updated successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update contributor"
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