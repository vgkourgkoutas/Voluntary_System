<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
// Δημιουργία σύνδεσης με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

if (!isset($_SESSION['logged_in'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
}

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$citizen_id = $_SESSION['id'];  // Ανακτά το ID του πολίτη από τη συνεδρία (session)
// SQL ερώτηση για την ανάκτηση όλων των αιτημάτων του πολίτη από τον πίνακα citizen_requests
$sql = "SELECT * FROM citizen_requests WHERE citres_citizen_id = $citizen_id";
$result = $conn->query($sql); // Εκτελεί την SQL ερώτηση


$requests = []; // Αρχικοποίηση πίνακα για την αποθήκευση των αποτελεσμάτων

if ($result->num_rows > 0) { // Έλεγχος αν υπάρχουν αποτελέσματα
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row; // Προσθήκη κάθε γραμμής του αποτελέσματος στον πίνακα $requests
    }
}

$conn->close(); // Κλείνει τη σύνδεση με τη βάση δεδομένων

// Επιστροφή της απάντησης σε μορφή JSON
header('Content-Type: application/json');
echo json_encode($requests);
?>
