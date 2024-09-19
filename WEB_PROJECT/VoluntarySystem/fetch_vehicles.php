<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) {  // Έλεγχος αν υπάρχει σύνδεση
    header('Location: logout.php');
    exit();
}

// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ερώτημα SQL για την ανάκτηση δεδομένων του οχήματος μαζί με τα αντικείμενα αποθήκευσης 
// Τα αποτελέμστα απο το query εχουν την εξής μορφή πχ Orange juice:15, Chocolate:10
$sql = "SELECT v.*, GROUP_CONCAT(CONCAT_WS(': ', s.item_name, s.item_quantity) SEPARATOR ', ') AS storage_items 
        FROM vehicles v
        LEFT JOIN vehicle_storage s ON v.vehicle_rescuer_id = s.vehicle_rescuer_id
        GROUP BY v.vehicle_rescuer_id";

$result = $conn->query($sql);

$vehicles = [];
// Έλεγχος αν υπάρχουν αποτελέσματα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = [   // Προσθήκη των δεδομένων του οχήματος στον πίνακα vehicles
            'name' => $row['vehicle_name'],
            'tasks' => $row['vehicle_tasks'],
            'vehicle_latitude' => $row['vehicle_latitude'],
            'vehicle_longitude' => $row['vehicle_longitude'],
            'storage_items' => $row['storage_items']
        ];
    }
}

$conn->close(); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($vehicles);
?>
