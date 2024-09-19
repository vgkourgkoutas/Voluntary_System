<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in1'])) { // Έλεγχος αν υπάρχει σύνδεση διαχειριστή
    header('Location: logout.php');
    exit();
}
// Ενεργοποίηση εμφάνισης σφαλμάτων για αποσφαλμάτωση
ini_set('display_errors', 1); // Εμφάνιση σφαλμάτων
ini_set('display_startup_errors', 1); // Εμφάνιση σφαλμάτων κατά την εκκίνηση
error_reporting(E_ALL); // Εμφάνιση όλων των τύπων σφαλμάτων
// Δημιουργία σύνδεσης με τη βάση δεδομένων MySQL
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}
// Επιλογή της βάσης δεδομένων
mysqli_select_db($con, "voluntary_system");
// Δημιουργία SQL ερωτήματος για ανάκτηση δεδομένων
$sql = "SELECT * FROM add_category";
$results = mysqli_query($con, $sql);

if (!$results) {
    die('Error in SQL query: ' . mysqli_error($con)); // Εξαγωγή σφάλματος αν το SQL ερώτημα αποτύχει
}

$data = array(); // Δημιουργία πίνακα data για αποθήκευση των αποτελεσμάτων
while ($row = $results->fetch_assoc()) {
    $data[] = $row;
}

mysqli_close($con); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

header('Content-Type: application/json'); // Ρύθμιση του τύπου περιεχομένου της απάντησης ως JSON
echo json_encode($data, JSON_PRETTY_PRINT); // Επιστροφή των δεδομένων σε μορφή JSON
?>
