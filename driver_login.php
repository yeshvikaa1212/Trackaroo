<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$driver_id = $data['driver_id'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT password FROM driver_signup WHERE driver_id = ?");
$stmt->bind_param("s", $driver_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    
    if (password_verify($password, $hashed_password)) {
        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Driver ID not found"]);
}

$stmt->close();
$conn->close();
?>
