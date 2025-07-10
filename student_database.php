<?php
include 'includes/config.php';
header("Content-Type: application/json");

$sql = "SELECT student_id, grade, route_number FROM student_signup";
$result = $conn->query($sql);

$students = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            "student_id" => $row['student_id'],
            "grade" => $row['grade'],
            "route_number" => $row['route_number']
        ];
    }

    echo json_encode([
        "status" => "success",
        "data" => $students
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No students found"
    ]);
}

$conn->close();
?>
