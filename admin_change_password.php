<?php
include 'includes/config.php';
header("Content-Type: application/json");

// Get raw input as object
$data = json_decode(file_get_contents("php://input")); // returns stdClass

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id         = $data->admin_id ?? '';
    $current_password = $data->current_password ?? '';
    $new_password     = $data->new_password ?? '';
    $confirm_password = $data->confirm_password ?? '';

    if (empty($admin_id) || empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT password FROM admin_signup WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    if (!$stored_password || !password_verify($current_password, $stored_password)) {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "New passwords do not match."]);
        exit;
    }

    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE admin_signup SET password = ? WHERE admin_id = ?");
    $stmt->bind_param("ss", $hashed_new_password, $admin_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password changed successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to change password."]);
    }

    $stmt->close();
    $conn->close();
}
?>
