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

// Ερώτημα SQL για την ανάκτηση όλων των ενεργών εργασιών από τον πίνακα current_tasks
$sql = "SELECT task_rescuer_id, citizen_id, citizen_fullname, citizen_telephone, item_stuff, item_quantity, offer_request_date_added, task_date_received, task_latitude, task_longitude, task_type 
        FROM current_tasks";
$stmt = $conn->prepare($sql);

$stmt->execute(); // Εκτέλεση

$result = $stmt->get_result(); // Ανάκτηση των αποτελεσμάτων της δήλωσης

$tasks = [];
// Έλεγχος αν υπάρχουν αποτελέσματα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Προετοιμασία και εκτέλεση της SQL δήλωσης για την ανάκτηση του username του διασώστη με βάση το task_rescuer_id
        $stmt2 = $conn->prepare("SELECT rescuer_username FROM rescuers WHERE rescuer_id = ?");
        $stmt2->bind_param("i", $row['task_rescuer_id']);
        $stmt2->execute();
        $rescuer = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        // Προσθήκη των δεδομένων της εργασίας και το username του διασώστη στον πίνακα tasks
        $tasks[] = [
            'task_rescuer_id' => $row['task_rescuer_id'],
            'rescuer_username' => $rescuer['rescuer_username'],
            'citizen_id' => $row['citizen_id'],
            'citizen_fullname' => $row['citizen_fullname'],
            'citizen_telephone' => $row['citizen_telephone'],
            'item_stuff' => $row['item_stuff'], 
            'item_quantity' => $row['item_quantity'],
            'offer_request_date_added' => $row['offer_request_date_added'],
            'task_date_received' => $row['task_date_received'],
            'task_latitude' => $row['task_latitude'],
            'task_longitude' => $row['task_longitude'],
            'task_type' => $row['task_type']
        ];
    }
}

$stmt->close(); // Κλείσιμο της αρχικής δήλωσης
$conn->close(); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($tasks);
?>
