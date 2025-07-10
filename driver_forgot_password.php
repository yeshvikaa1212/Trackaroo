<?php
include 'includes/config.php'; // DB connection
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$driver_id = $data["driver_id"] ?? '';
$new_password = $data["new_password"] ?? '';
$confirm_password = $data["confirm_password"] ?? '';

// Validate input
if (empty($driver_id) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
    exit;
}

// Check if driver ID exists
$check = $conn->prepare("SELECT driver_id FROM driver_signup WHERE driver_id = ?");
$check->bind_param("s", $driver_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "Driver ID not found."]);
    exit;
}

// Hash password and update
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$update = $conn->prepare("UPDATE driver_signup SET password = ? WHERE driver_id = ?");
$update->bind_param("ss", $hashed_password, $driver_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password."]);
}

$conn->close();
?>
