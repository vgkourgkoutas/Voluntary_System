<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) {  // Έλεγχος αν υπάρχει σύνδεση
    header('Location: logout.php');
    exit();
}
// Λήψη του ID του διασώστη από τη συνεδρία
$vehicle_rescuer_id = $_SESSION['id'];
// Ενεργοποίηση εμφάνισης σφαλμάτων PHP
ini_set('display_errors', 1); // Εμφάνιση όλων των σφαλμάτων κατά την εκτέλεση
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Δημιουργία σύνδεσης με τη βάση δεδομένων
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con)); // Έλεγχος αν η σύνδεση απέτυχε
}
 // Επιλογή της βάσης δεδομένων "voluntary_system"
mysqli_select_db($con, "voluntary_system");
// Προετοιμασία της SQL δήλωσης για την ανάκτηση δεδομένων από τον πίνακα vehicle_storage
$sql = "SELECT * FROM vehicle_storage WHERE vehicle_rescuer_id = ?";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($con));
}

$stmt->bind_param('i', $vehicle_rescuer_id); // Σύνδεση της παραμέτρου vehicle_rescuer_id με την SQL δήλωση
$stmt->execute();
$results = $stmt->get_result(); // Λήψη των αποτελεσμάτων της εκτέλεσης της δήλωσης

if (!$results) {
    die('Error in SQL query: ' . mysqli_error($con));
}
// Δημιουργία πίνακα για αποθήκευση των αποτελεσμάτων
$data = array();
while ($row = $results->fetch_assoc()) {
    $data[] = $row; // Προσθήκη κάθε γραμμής αποτελέσματος στον πίνακα
}

mysqli_close($con);  // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

// Επιστροφή των αποτελεσμάτων σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
?>
