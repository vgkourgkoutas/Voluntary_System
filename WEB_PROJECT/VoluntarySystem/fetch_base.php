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

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}
// Δημιουργία SQL ερωτήματος για επιλογή των συντεταγμένων της βάσης
$sql = "SELECT * FROM base WHERE base_id = 1";
$result = $conn->query($sql);  // Aποθήκευση αποτελέσματος στο result

$baseData = [];

if ($result->num_rows > 0)
{
    // Έλεγχος αν υπάρχουν εγγραφές στο αποτέλεσμα. Αν υπάρχουν, γίνεται ανάκτηση της πρώτης σειράς
    $row = $result->fetch_assoc();
    // Αποθήκευση των γεωγραφικών συντεταγμένων
    $baseData = [
        'latitude' => $row['base_latitude'],
        'longitude' => $row['base_longitude']
    ];
}

// Κλείσιμο σύνδεσης
$conn->close();

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($baseData);
?>
