<?php
include 'includes/config.php';

$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->parent_id) &&
    isset($data->student_id) &&
    isset($data->phone_number)
) {
    $parent_id = $data->parent_id;
    $student_id = $data->student_id;
    $phone_number = $data->phone_number;

    $stmt = $conn->prepare("UPDATE parent_signup SET student_id=?, phone=? WHERE parent_id=?");
    $stmt->bind_param("sss", $student_id, $phone_number, $parent_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
