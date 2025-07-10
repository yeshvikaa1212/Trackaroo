<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

// Get raw input
$data = json_decode(file_get_contents("php://input"));

$admin_id = isset($data->admin_id) ? $data->admin_id : '';
$password = isset($data->password) ? $data->password : '';

if (empty($admin_id) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Admin ID and password are required"]);
    exit;
}

// Check if admin exists
$stmt = $conn->prepare("SELECT * FROM admin_signup WHERE admin_id = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verify the password using password_verify
    if (password_verify($password, $admin['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "admin_id" => $admin['admin_id']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Admin not found"]);
}

$conn->close();
?>
