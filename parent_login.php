<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$student_id = trim($data['student_id'] ?? '');
$password   = trim($data['password'] ?? '');

if (!$student_id || !$password) {
    echo json_encode(["status" => "error", "message" => "Missing student ID or password"]);
    exit;
}

// 1. Check if student_id exists in student_signup
$stmt1 = $conn->prepare("SELECT student_id FROM student_signup WHERE student_id = ?");
$stmt1->bind_param("s", $student_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Student ID not found"]);
    exit;
}

// 2. Get hashed password from parent_signup
$stmt2 = $conn->prepare("SELECT parent_id, password FROM parent_signup WHERE student_id = ?");
$stmt2->bind_param("s", $student_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Parent not registered"]);
    exit;
}

$parent = $result2->fetch_assoc();
$hashedPassword = $parent['password'];

// 3. Use password_verify to compare
if (password_verify($password, $hashedPassword)) {
    echo json_encode([
        "status"     => "success",
        "message"    => "Login successful",
        "parent_id"  => $parent['parent_id'],
        "student_id" => $student_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid password"]);
}

$conn->close();
?>
