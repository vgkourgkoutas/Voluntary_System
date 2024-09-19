<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in3'])) { // Έλεγχος αν υπάρχει σύνδεση πολίτη
    header('Location: logout.php');
    exit();
}

// Ελέγχουμε αν η μέθοδος του αιτήματος είναι POST και αν έχει παρασχεθεί το ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $offerId = $_POST['id']; // Αποθήκευση του ID της προσφοράς που θέλουμε να διαγράψουμε

    // Στοιχεία σύνδεσης με τη βάση δεδομένων
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

    // Προετοιμασία του statement για διαγραφή της προσφοράς
    $stmt = $conn->prepare("DELETE FROM citizen_offers WHERE citoff_id = ?");
    $stmt->bind_param("i", $offerId); // Δέσμευση της παραμέτρου ID ως ακέραιος αριθμός
    // Εκτέλεση της εντολής διαγραφής
    if ($stmt->execute()) {
        echo "Η προσφορά ακυρώθηκε επιτυχώς."; // Εμφάνιση μηνύματος επιτυχίας αν η διαγραφή ολοκληρωθεί
    } else {
        echo "Error: " . $stmt->error; // Εμφάνιση μηνύματος σφάλματος αν κάτι πάει στραβά
    }
    // Κλείσιμο του statement και της σύνδεσης
    $stmt->close();
    $conn->close();
}
?>
