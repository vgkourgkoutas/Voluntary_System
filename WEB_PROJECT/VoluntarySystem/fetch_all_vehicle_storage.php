<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in1'])) {  // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή
    header('Location: logout.php');
    exit();
}
// Ενεργοποίηση εμφάνισης σφαλμάτων PHP
ini_set('display_errors', 1);  // Εμφάνιση όλων των σφαλμάτων κατά την εκτέλεση
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);  

// Σύνδεση με τη βάση δεδομένων MySQL
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con)); // Αν η σύνδεση αποτύχει, εμφανίζεται μήνυμα σφάλματος και διακόπτεται η εκτέλεση
}
// Επιλογή της βάσης δεδομένων "voluntary_system" και επιλογή όλων των εγγραφών από τον πίνακα vehicle_storage
mysqli_select_db($con, "voluntary_system");
$sql = "SELECT * FROM vehicle_storage";
$results = mysqli_query($con, $sql);

if (!$results) {
    die('Error in SQL query: ' . mysqli_error($con)); // Αν το ερώτημα αποτύχει, εμφανίζεται μήνυμα σφάλματος και διακόπτεται η εκτέλεση
}
// Αποθήκευση των αποτελεσμάτων σε έναν πίνακα data
$data = array();
while ($row = $results->fetch_assoc()) {
    $data[] = $row;
}

mysqli_close($con);
// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
