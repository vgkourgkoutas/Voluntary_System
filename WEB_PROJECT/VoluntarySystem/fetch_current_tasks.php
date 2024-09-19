<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

include('database.php'); // Σύνδεση με τη βάση δεδόμενων

// Ανάκτηση του ονόματος του διασώστη
$rescuer_username = $_SESSION['username'];

// SQL ερώτηματα για εύρεση του διασώστη με βάση το όνομα του
$stmt = $conn->prepare("SELECT rescuer_id FROM rescuers WHERE rescuer_username = ?");
$stmt->execute([$rescuer_username]); // Εκτέλεση του ερωτήματος με τη χρήση του ονόματος του
$rescuer = $stmt->fetch(PDO::FETCH_ASSOC);
// Αποθήκευση του ID του διασώστη 
$rescuer_id = $rescuer['rescuer_id'];
// SQL ερωτήματα για εύρεση των τρέχουσων εργασιών που έχουν ανατεθεί στον συγκεκριμένο διασώστη
$stmt2 = $conn->prepare("SELECT * FROM current_tasks WHERE task_rescuer_id = ?");
$stmt2->execute([$rescuer_id]); // Εκτέλεση του ερωτήματος με τη χρήση του ID του διασώστη
$own_tasks = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Επιστρέφει τα στοιχεία ως json κωδικοποίηση
echo json_encode($own_tasks);
?>
