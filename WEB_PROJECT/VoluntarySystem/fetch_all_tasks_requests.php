<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) // Έλεγχος εάν υπάρχει σύνδεση
{
    header('Location: logout.php');
}
// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης με τη βάση δεδομένων
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ερώτημα SQL για επιλογή όλων των εγγραφών από τον πίνακα current_tasks όπου το task_type είναι request και αποθήκευση στο result
$sql = "SELECT * FROM current_tasks WHERE task_type = 'request'";
$result = $conn->query($sql);



$requests = [];
// Έλεγχος αν υπάρχουν εγγραφές στο αποτέλεσμα του ερωτήματος
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

$conn->close();

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($requests);
?>
