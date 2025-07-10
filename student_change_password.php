<?php
include 'includes/config.php';
header('Content-Type: application/json');

// 1. Decode raw JSON input as associative array
$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data['student_id']) &&
    isset($data['password']) &&
    isset($data['new_password']) &&
    isset($data['confirm_password'])
) {
    $student_id = $data['student_id'];
    $password = $data['password'];
    $new_password = $data['new_password'];
    $confirm_password = $data['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "New passwords do not match"]);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM student_account_setup WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
    } else {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            // ✅ Fix: Update correct table name `student_account_setup` (not `students`)
            $update_stmt = $conn->prepare("UPDATE student_account_setup SET password = ? WHERE student_id = ?");
            $update_stmt->bind_param("ss", $new_hashed, $student_id);
            if ($update_stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update password"]);
            }
            $update_stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Incomplete input"]);
}
?>
