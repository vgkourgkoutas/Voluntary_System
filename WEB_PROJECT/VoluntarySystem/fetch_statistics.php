<?php
session_start();

if (!isset($_SESSION['logged_in1'])) {
    header('Location: logout.php');
    exit();
}

// Ενεργοποίηση εμφάνισης όλων των σφαλμάτων και προειδοποιήσεων
error_reporting(E_ALL); // Ενεργοποίηση αναφορών σφαλμάτων για όλα τα επίπεδα
ini_set('display_errors', 1);  // Ρύθμιση της εμφάνισης σφαλμάτων στην οθόνη

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
// Ανάκτηση και διαμόρφωση ημερομηνιών από τα δεδομένα της GET
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] . ' 00:00:00' : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] . ' 23:59:59' : null;

// Έλεγχος αν οι ημερομηνίες είναι έγκυρες
if (!$start_date || !$end_date) {
    echo json_encode(['error' => 'Invalid date range']); // Επιστροφή σφάλματος αν οι ημερομηνίες δεν έχουν καθοριστεί
    exit;
}

// SQL ερωτήματα για την ανάκτηση των μετρήσεων
$new_requests_query = "SELECT COUNT(*) as count FROM citizen_requests WHERE citres_date_added BETWEEN ? AND ?";
$new_offers_query = "SELECT COUNT(*) as count FROM citizen_offers WHERE citoff_date_added BETWEEN ? AND ?";

$completed_requests_query = "SELECT COUNT(*) as count FROM old_requests WHERE oldres_date_added BETWEEN ? AND ?";
$completed_offers_query = "SELECT COUNT(*) as count FROM old_offers WHERE oldoff_date_added BETWEEN ? AND ?";

try {
    // Προετοιμασία και εκτέλεση του ερωτήματος για νέες αιτήσεις
    $stmt = $conn->prepare($new_requests_query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $stmt->bind_result($new_requests_count);
    $stmt->fetch();
    $stmt->close();

    // Προετοιμασία και εκτέλεση του ερωτήματος για νέες προσφορές
    $stmt = $conn->prepare($new_offers_query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $stmt->bind_result($new_offers_count);
    $stmt->fetch();
    $stmt->close();

    // Προετοιμασία και εκτέλεση του ερωτήματος για ολοκληρωμένες αιτήσεις
    $stmt = $conn->prepare($completed_requests_query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $stmt->bind_result($completed_requests_count);
    $stmt->fetch();
    $stmt->close();

    // Προετοιμασία και εκτέλεση του ερωτήματος για ολοκληρωμένες προσφορές
    $stmt = $conn->prepare($completed_offers_query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $stmt->bind_result($completed_offers_count);
    $stmt->fetch();
    $stmt->close();

    //Επιστρέφει τα αποτελέσματα σε json κωδικοποίηση
    echo json_encode([
        'new_requests' => $new_requests_count ?? 0,
        'new_offers' => $new_offers_count ?? 0,
        'completed_requests' => $completed_requests_count ?? 0,
        'completed_offers' => $completed_offers_count ?? 0,
    ]);

} catch (Exception $e) {  
    // Διαχείριση εξαιρέσεων
    echo json_encode(['error' => $e->getMessage()]); // Επιστροφή μηνύματος σφάλματος
}

// Κλείσιμο της σύνδεσης
$conn->close();
?>
