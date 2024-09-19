<?php
session_start(); // Ξεκινάει το session

// Ενεργοποίηση εμφάνισης σφαλμάτων 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in1'])) { // Έλεγχος αν είναι συνδεδεμένος ο διασώστης
    header('Location: logout.php');
    exit();
}

// Σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

// Ανάκτηση γραμμών από τον πίνακα task_connections
$sql = "SELECT task_latitude, task_longitude, vehicle_latitude, vehicle_longitude FROM task_connections";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$coordinates = [];
while ($row = $result->fetch_assoc()) {
    $coordinates[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($coordinates); // Επιστροφή των δεδομένων σε μορφή JSON
?>
