<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

// Get data
$data = json_decode(file_get_contents("php://input"), true);

$admin_id = $data['admin_id'];
$password = $data['password'];
$confirm_password = $data['confirm_password'];

if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin_id exists
$check = $conn->prepare("SELECT * FROM admin_signup WHERE admin_id = ?");
$check->bind_param("s", $admin_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Admin ID already exists"]);
} else {
    // Insert admin data
    $stmt = $conn->prepare("INSERT INTO admin_signup (admin_id, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $admin_id, $hashed_password);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Admin registered successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed"]);
    }
}

$conn->close();
?>
