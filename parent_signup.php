<?php
include 'includes/config.php';
header("Content-Type: application/json");

// Get input
$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data["student_id"] ?? '';
$phone_number = $data["phone_number"] ?? '';
$password = $data["password"] ?? '';
$confirm_password = $data["confirm_password"] ?? '';

// Validate input
if (empty($student_id) || empty($phone_number) || empty($password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
    exit;
}

// Check if student ID exists
$check = $conn->prepare("SELECT student_id FROM student_signup WHERE student_id = ?");
$check->bind_param("s", $student_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid Student ID."]);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into parent_signup
$insert = $conn->prepare("INSERT INTO parent_signup (student_id, phone_number, password) VALUES (?, ?, ?)");
$insert->bind_param("sss", $student_id, $phone_number, $hashed_password);

if ($insert->execute()) {
    echo json_encode(["status" => "success", "message" => "Parent registered successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to register parent."]);
}

$conn->close();
?>
