<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

// Get raw input data
$data = json_decode(file_get_contents("php://input"), true);

$driver_id = $data['driver_id'];
$driver_name = $data['driver_name'];
$phone_number = $data['phone_number'];
$license_number = $data['license_number'];
$password = $data['password'];
$confirm_password = $data['confirm_password'];

// Check if passwords match
if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute insert
$stmt = $conn->prepare("INSERT INTO driver_signup (driver_id, driver_name, phone_number, license_number, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $driver_id, $driver_name, $phone_number, $license_number, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Driver registered successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to register driver: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
