<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$student_id       = trim($data['student_id'] ?? '');
$new_password     = trim($data['new_password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

if (!$student_id || !$new_password || !$confirm_password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// 1. Check if student_id exists in parent_signup
$stmt = $conn->prepare("SELECT student_id FROM parent_signup WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid student ID"]);
    exit;
}

// 2. Check if new and confirm passwords match
if ($new_password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// 3. Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

// 4. Update the password
$update = $conn->prepare("UPDATE parent_signup SET password = ? WHERE student_id = ?");
$update->bind_param("ss", $hashed_password, $student_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password"]);
}

$conn->close();
?>
