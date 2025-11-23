<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include 'conn.php';

session_start();

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];

    // Check in admins table
    $sql = "SELECT id, full_name, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $full_name, $hashed_password);
            $stmt->fetch();
            
            // Verify password
            if (password_verify($password, $hashed_password)) {
                
                // Set session variables
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_name'] = $full_name;
                $_SESSION['user_role'] = 'admin';

                // Update last login
                $updateSql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("i", $id);
                $updateStmt->execute();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful',
                    'redirect' => 'admin-dashboard.html'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing email or password']);
}

$conn->close();
?>
