<?php
include 'includes/config.php'; // DB connection
header('Content-Type: application/json');

// 1. Decode raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// 2. Extract and validate fields
$student_id       = trim($data['student_id'] ?? '');
$new_password     = trim($data['new_password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

if (!$student_id || !$new_password || !$confirm_password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// 3. Check if student exists
$stmt = $conn->prepare("SELECT * FROM student_account_setup WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Student ID not found"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close(); // Close select statement

// 4. Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// 5. Update the password
$update = $conn->prepare("UPDATE student_account_setup SET password = ? WHERE student_id = ?");
$update->bind_param("ss", $hashed_password, $student_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Password updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Password update failed"]);
}

$update->close();
$conn->close();
?>
