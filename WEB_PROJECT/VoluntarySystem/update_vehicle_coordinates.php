<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

ini_set('display_errors', 1); // Εμφάνιση όλων των σφαλμάτων κατά την εκτέλεση
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in2'])) { //Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit();
}

// Λήψη συντεταγμένων από την αίτηση POST
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$user = $_SESSION['username'];
$rescuer_id = $_SESSION['id'];

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
// Ερώτημα SQL για την ενημέρωση των συντεταγμένων του οχήματος
$sql = "UPDATE vehicles SET vehicle_latitude = ?, vehicle_longitude = ? WHERE vehicle_name = 'Vehicle of $user'";
$stmt = $conn->prepare($sql); //Δημιουργεί ερώτημα για την ενημέρωση
$stmt->bind_param("dd", $latitude, $longitude); // Συνδέει τις συντεταγμένες (γεωγραφικό πλάτος και μήκος) στο ερώτημα
// Εκτέλεση του ερωτήματος
if ($stmt->execute()) {
    echo "Coordinates updated successfully"; // Εμφανίζει μήνυμα επιτυχίας αν η εκτέλεση του ερωτήματος ήταν επιτυχής

    // Ερώτημα SQL για την ενημέρωση των συντεταγμένων του οχήματος όσον αφορά τον πίνακα με τις γραμμές 
    $sql2 = "UPDATE task_connections SET vehicle_latitude = ?, vehicle_longitude = ? WHERE rescuer_id = ?";
    $stmt2 = $conn->prepare($sql2); //Δημιουργεί ερώτημα για την ενημέρωση
    $stmt2->bind_param("ddi", $latitude, $longitude, $rescuer_id); // Συνδέει τις συντεταγμένες (γεωγραφικό πλάτος και μήκος) στο ερώτημα
    $stmt2->execute();
    $stmt2->close(); // Κλείνει την δήλωση
} else {
    echo "Error updating coordinates: " . $conn->error; // Εμφανίζει μήνυμα λάθους αν η εκτέλεση του ερωτήματος αποτύχει
}

$stmt->close(); // Κλείνει την δήλωση
$conn->close(); // Κλείνει τη σύνδεση με τη βάση δεδομένων
?>
