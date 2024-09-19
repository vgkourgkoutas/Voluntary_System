<?php

session_start(); // Έναρξη του session

if (!isset($_SESSION['logged_in'])) { // Έλεγχος αν υπάρχει σύνδεση
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
    die("Σφάλμα σύνδεσης στη βάση δεδομένων: " . $conn->connect_error);
}

// Παίρνουμε τις συντεταγμένες από τον πελάτη (POST request)
$vehicle_name = $_POST['vehicle_name'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Ενημέρωση των νέων συντεταγμένων στο πίνακα vehicles
$sql = "UPDATE vehicles SET vehicle_latitude=$latitude, vehicle_longitude=$longitude WHERE vehicle_name='$vehicle_name'";

if ($conn->query($sql) === TRUE)
{
    echo "Οι συντεταγμένες προστέθηκαν με επιτυχία!";

    // Λήψη του rescuer_id από τον πίνακα vehicles
    $sql_rescuer_id = "SELECT vehicle_rescuer_id FROM vehicles WHERE vehicle_name='$vehicle_name'";
    $result = $conn->query($sql_rescuer_id);

    if ($result->num_rows > 0) {
        // Λαμβάνουμε το rescuer_id
        $row = $result->fetch_assoc();
        $rescuer_id = $row['vehicle_rescuer_id'];

        // Ενημέρωση των συντεταγμένων στον πίνακα task_connections με βάση το rescuer_id
        $sql_update_task = "UPDATE task_connections SET vehicle_latitude=$latitude, vehicle_longitude=$longitude WHERE rescuer_id=$rescuer_id";
        
        if ($conn->query($sql_update_task) === TRUE) {
            echo "Οι συντεταγμένες ενημερώθηκαν επιτυχώς στον πίνακα task_connections!";
        } else {
            echo "Σφάλμα κατά την ενημέρωση των συντεταγμένων στον πίνακα task_connections: " . $conn->error;
        }
    } else {
        echo "Δεν βρέθηκε rescuer_id για το όχημα με όνομα '$vehicle_name'.";
    }
} 
else
{
    echo "Σφάλμα κατά την προσθήκη των συντεταγμένων: " . $conn->error;
}

// Κλείνουμε τη σύνδεση με τη βάση δεδομένων
$conn->close();
?>
