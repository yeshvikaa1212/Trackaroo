<?php
include 'includes/config.php'; // Adjust path if needed
header('Content-Type: application/json');

// 1. Decode raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

// 2. Only proceed if POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Safely extract values
    $student_id       = $data['student_id'] ?? '';
    $password         = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    // 4. Validate input
    if (!$student_id || !$password || !$confirm_password) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }

    // 5. Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 6. Insert into DB
    $stmt = $conn->prepare("INSERT INTO student_account_setup (student_id, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $student_id, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Account setup successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
