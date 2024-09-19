<?php

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος αν υπάρχει σύνδεση
    header('Location: logout.php');
}

// Ενεργοποίηση εμφάνισης σφαλμάτων PHP
ini_set('display_errors', 1); // Εμφάνιση όλων των σφαλμάτων κατά την εκτέλεση
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";

// Δημιουργία σύνδεσης με τη βάση δεδομένων 
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ανάκτηση του username του διασώστη απο τη συνεδρία 
$rescuer_username = $_SESSION['username'];

// Προετοιμασία και εκτέλεση της SQL δήλωσης για την ανάκτηση του rescuer_id με βάση το username του rescuer
$stmt = $conn->prepare("SELECT rescuer_id FROM rescuers WHERE rescuer_username = ?");
$stmt->bind_param("s", $rescuer_username);
$stmt->execute();
$rescuer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rescuer) {  // Έλεγχος αν βρέθηκε ο διασώστης
    die("No rescuer found with this username.");
}

$rescuer_id = $rescuer['rescuer_id'];

// Προετοιμασία της SQL δήλωσης για την ανάκτηση των tasks του διασώστη με βάση το rescuer_id
$sql = "SELECT citizen_id, citizen_fullname, citizen_telephone, item_stuff, item_quantity, offer_request_date_added, task_date_received, task_latitude, task_longitude, task_type 
        FROM current_tasks 
        WHERE task_rescuer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rescuer_id);
$stmt->execute();

$result = $stmt->get_result();  // Ανάκτηση των αποτελεσμάτων
$tasks = [];
// Έλεγχος αν υπάρχουν αποτελέσματα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = [  // Προσθήκη των δεδομένων της εργασίας στον πίνακα $tasks
            'citizen_id' => $row['citizen_id'],
            'citizen_fullname' => $row['citizen_fullname'],
            'citizen_telephone' => $row['citizen_telephone'],
            'item_stuff' => $row['item_stuff'], 
            'item_quantity' => $row['item_quantity'],
            'offer_request_date_added' => $row['offer_request_date_added'],
            'task_date_received' => $row['task_date_received'],
            'task_latitude' => $row['task_latitude'],
            'task_longitude' => $row['task_longitude'],
            'task_type' => $row['task_type'],
            'vehicle_username' => $rescuer_username
        ];
    }
}

$stmt->close();  // Κλείσιμο της δήλωσης
$conn->close();  // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($tasks);
?>
