<?php
include 'includes/config.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$driver_id = $data["driver_id"] ?? '';
$current_password = $data["current_password"] ?? '';
$new_password = $data["new_password"] ?? '';
$confirm_password = $data["confirm_password"] ?? '';

// Step 1: Check if all fields are filled
if (empty($driver_id) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Step 2: Confirm new password matches
if ($new_password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "New passwords do not match."]);
    exit;
}

// Step 3: Fetch current hashed password from DB
$stmt = $conn->prepare("SELECT password FROM driver_signup WHERE driver_id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Driver ID not found."]);
    exit;
}

$row = $result->fetch_assoc();
$stored_password = $row["password"];

// Step 4: Verify current password
if (!password_verify($current_password, $stored_password)) {
    echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
    exit;
}

// Step 5: Hash new password and update
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE driver_signup SET password = ? WHERE driver_id = ?");
$update->bind_param("ss", $hashed_new_password, $driver_id);

if ($update->execute()) {
    echo json_encode(["status" => "success", "message" => "Password changed successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update password."]);
}

$conn->close();
?>
