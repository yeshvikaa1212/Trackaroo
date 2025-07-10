<?php
include 'includes/config.php'; // Adjust path if needed
?>

<?php
header("Content-Type: application/json");
// Decode JSON
$data = json_decode(file_get_contents("php://input"), true);

$result = $conn->query("SELECT route_number, route, time, bus_number FROM add_routes");

$routes = [];
while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}

echo json_encode(["status" => "success", "routes" => $routes]);

$conn->close();
?>
