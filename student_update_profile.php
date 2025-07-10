<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
$data = json_decode(file_get_contents("php://input"));

if (
    isset($data->student_id) &&
    isset($data->name) &&
    isset($data->age) &&
    isset($data->school) &&
    isset($data->grade)
) {
    $student_id = $data->student_id;
    $name = $data->name;
    $age = $data->age;
    $school = $data->school;
    $grade = $data->grade;

    $stmt = $conn->prepare("UPDATE student_signup SET name=?, age=?, school=?, grade=? WHERE student_id=?");
    $stmt->bind_param("sisss", $name, $age, $school, $grade, $student_id);

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
