<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος αν υπάρχει σύνδεση
    header('Location: logout.php');
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

// Παίρνουμε τις συντεταγμένες από το POST request
$base_id = $_POST['base_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Ενημέρωση των νέων συντεταγμένων στη βάση
$stmt_update = $conn->prepare("UPDATE base SET base_latitude = ?, base_longitude = ? WHERE base_id = ?");
$stmt_update->bind_param("ddi", $latitude, $longitude, $base_id);

// Εκτέλεση του query και εμφάνιση μηνύματος σε περίπτωση σφάλματος
if ($stmt_update->execute()) {
    echo "Οι συντεταγμένες προστέθηκαν με επιτυχία!";
} else {
    echo "Σφάλμα κατά την προσθήκη των συντεταγμένων: " . $stmt_update->error;
}

// Κλείσιμο της σύνδεσης
$stmt_update->close();
$conn->close();
?>
