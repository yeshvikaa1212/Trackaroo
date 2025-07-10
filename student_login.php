<?php
include 'includes/config.php';
header('Content-Type: application/json');

// 1. Decode raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// 2. Get POST data from JSON
$student_id = $data['student_id'] ?? '';
$password   = $data['password']   ?? '';

if (!$student_id || !$password) {
    echo json_encode(["status" => "error", "message" => "Student ID and password are required"]);
    exit;
}

// 3. Check if student exists
$sql = "SELECT * FROM student_account_setup WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid student ID"]);
    exit;
}

$row = $result->fetch_assoc();
$hashed_password = $row['password'];

// 4. Verify password
if (password_verify($password, $hashed_password)) {
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "student_id" => $row['student_id']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Incorrect password"]);
}

$stmt->close();
$conn->close();
?>
