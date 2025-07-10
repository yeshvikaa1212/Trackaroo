<?php
include 'includes/config.php'; // Adjust path if needed
header('Content-Type: application/json');

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Extract fields
$student_id = $data['student_id'] ?? '';
$name       = $data['name']       ?? '';
$age        = $data['age']        ?? '';
$grade      = $data['grade']      ?? '';
$school     = $data['school']     ?? '';
$address    = $data['address']    ?? '';

// Validate
if (!$student_id || !$name || !$age || !$grade || !$school || !$address) {
    echo json_encode(["status"=>"error","message"=>"All required fields missing"]);
    exit;
}

// Insert into database
$sql  = "INSERT INTO student_signup (student_id, name, age, grade, school, address)
         VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisss", $student_id, $name, $age, $grade, $school, $address);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Signup successful"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Insert failed: ".$stmt->error]);
}

$stmt->close();
$conn->close();
?>
