<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος εάν υπάρχει σύνδεση 
    header('Location: logout.php');
    exit();
}
// Ανάκτηση του ονόματος χρήστη από το session για τον τρέχοντα συνδεδεμένο χρήστη
$user = $_SESSION['username'];

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
// SQL ερώτημα για την ανάκτηση δεδομένων του οχήματος που αντιστοιχεί στον χρήστη
$sql = "SELECT vehicle_rescuer_id, vehicle_name, vehicle_tasks, vehicle_latitude, vehicle_longitude FROM vehicles WHERE vehicle_name='Vehicle of $user'";
$result = $conn->query($sql);

$vehicle = null;  // Αρχικοποίηση της μεταβλητής $vehicle ως null για αποθήκευση των δεδομένων του οχήματος

// Έλεγχος αν υπάρχουν αποτελέσματα από το ερώτημα
if ($result->num_rows > 0) {
    // Ανάκτηση των αποτελεσμάτων και αποθήκευση των δεδομένων σε πίνακα
    $row = $result->fetch_assoc();
    $vehicle = [
        'id' => $row['vehicle_rescuer_id'],
        'name' => $row['vehicle_name'],
        'tasks' => $row['vehicle_tasks'],
        'latitude' => $row['vehicle_latitude'],
        'longitude' => $row['vehicle_longitude']
    ];
}

$conn->close();  // Κλείσιμο της σύνδεσης

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($vehicle);
?>
