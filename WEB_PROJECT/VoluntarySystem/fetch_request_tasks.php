<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος εάν υπάρχει σύνδεση
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
// Ανάκτηση του ID του πολίτη από τη συνεδρία 
$citizen_id = $_SESSION['id'];
// SQL ερώτημα για την ανάκτηση των τρεχουσών εργασιών που ανήκουν στο συγκεκριμένο πολίτη
// και που ανήκουν στον τύπο 'request'
$sql = "SELECT * FROM current_tasks WHERE citizen_id = $citizen_id AND task_type = 'request'";
$result = $conn->query($sql);



$requests = [];
// Έλεγχος αν υπάρχουν αποτελέσματα από το ερώτημα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) { // Αν υπάρχουν αποτελέσματα γίνεται ανάκτηση κάθε γραμμής και προσθήκη της στον πίνακα $requests
        $requests[] = $row;
    }
}

$conn->close(); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($requests);
?>
