<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$student_id        = trim($data['student_id'] ?? '');
$current_password  = trim($data['current_password'] ?? '');
$new_password      = trim($data['new_password'] ?? '');
$confirm_password  = trim($data['confirm_password'] ?? '');

// 1. Check for empty fields
if (!$student_id || !$current_password || !$new_password || !$confirm_password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// 2. Get existing password from DB
$stmt = $conn->prepare("SELECT password FROM parent_signup WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid student ID"]);
    exit;
}

$row = $result->fetch_assoc();
$stored_password = $row['password'];

// 3. Verify current password
if (!password_verify($current_password, $stored_password)) {
    echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
    exit;
}

// 4. Check new password match
if ($new_password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "New passwords do not match"]);
    exit;
}

// 5. Hash new password and update
$new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
$update = $conn->prepare("UPDATE parent_signup SET password = ? WHERE student_id = ?");
$update->bind_param("ss", $new_hashed_password, $student_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Password changed successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password"]);
}

$conn->close();
?>
