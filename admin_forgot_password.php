<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

// Get raw input
$data = json_decode(file_get_contents("php://input"),true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $data['admin_id'];
    $new_password = $data['new_password'];
    $confirm_password = $data['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE admin_signup SET password = ? WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $admin_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password."]);
    }

    $stmt->close();
    $conn->close();
}
?>
