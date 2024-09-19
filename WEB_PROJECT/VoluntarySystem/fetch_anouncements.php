<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) // Έλεγχος εάν υπάρχει σύνδεση 
{
    header('Location: logout.php');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης με την βάση
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ανάκτηση δεδομένων απο το πίνακα admin_announcements
$sql = "SELECT * FROM admin_announcements";
$result = $conn->query($sql);


// Τοποθέτηση των δεδομένων σε πίνακα anouncements για την χρήση του σε μορφή json
$anouncements = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $anouncements[] = $row;
    }
}

$conn->close();

// Έξοδος των αποτελεσμάτων ως json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($anouncements);
?>
