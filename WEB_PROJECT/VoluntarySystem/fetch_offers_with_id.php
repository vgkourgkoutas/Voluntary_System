<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
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
// Ανάκτηση του ID του πολίτη από τη συνεδρία 
$citizen_id = $_SESSION['id'];
// SQL ερώτημα για την ανάκτηση όλων των προσφορών από τον πίνακα 'citizen_offers'
// που σχετίζονται με τον πολίτη που έχει το ID που αποθηκεύεται στη μεταβλητή $citizen_id
$sql = "SELECT * FROM citizen_offers WHERE citoff_citizen_id = $citizen_id";
$result = $conn->query($sql);


$offer = [];
// Έλεγχος αν υπάρχουν αποτελέσματα από το ερώτημα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) { // Αν υπάρχουν αποτελέσματα γίνεται ανάκτηση κάθε γραμμής και προσθήκη της στον πίνακα $offer
        $offer[] = $row;
    }
}

$conn->close(); // Κλείσιμο της σύνδεσης

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($offer);
?>
