<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in3'])) { // Έλεγχος εάν υπάρχει σύνδεση πολίτη
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
    die("Connection failed: " . $conn->connect_error);
}
// Ανάκτηση του citizen_id από το session, που αντιστοιχεί στον τρέχοντα συνδεδεμένο χρήστη
$citizen_id = $_SESSION['id'];
// SQL ερώτημα για την επιλογή όλων των εγγραφών από τον πίνακα 'old_offers'
// που αντιστοιχούν στον τρέχοντα πολίτη με βάση το citizen_id
$sql = "SELECT * FROM old_offers WHERE oldoff_citizen_id = $citizen_id";
$result = $conn->query($sql); // Αποθήκευση των αποτελεσμάτων στη μεταβλητή $result



$old_offers = [];
// Έλεγχος αν υπάρχουν αποτελέσματα από το ερώτημα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $old_offers[] = $row; // Προσθήκη των εγγραφών στον πίνακα $old_requests
    }
}

$conn->close(); // Κλείσιμο της σύνδεσης

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($old_offers);
?>
