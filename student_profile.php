<?php
include 'includes/config.php';
header('Content-Type: application/json');

// 1. Decode raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// ✅ Get student_id from decoded JSON
$student_id = $data['student_id'] ?? '';

if (empty($student_id)) {
    echo json_encode(["status" => "error", "message" => "student_id is required"]);
    exit;
}

// Fetch only name, age, school, grade
$sql = "SELECT name, age, school, grade FROM student_signup WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Student not found"]);
} else {
    $profile = $result->fetch_assoc();
    echo json_encode(["status" => "success", "profile" => $profile]);
}

$stmt->close();
$conn->close();
?>
