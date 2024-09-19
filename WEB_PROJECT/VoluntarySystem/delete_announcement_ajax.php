<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in1'])) {  // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
    header('Location: logout.php');
    exit();
}
// Ελέγχει αν υπάρχει το πεδίο 'announcement_id' στο POST αίτημα
if (isset($_POST['announcement_id'])) { 
    include('database.php');
    $con = mysqli_connect('localhost','root','','voluntary_system');
    
    
    // Λαμβάνει το 'announcement_id' από το POST αίτημα και το μετατρέπει σε ακέραιο αριθμό για χρήση στο SQL ερώτημα
    $announcement_id = intval($_POST['announcement_id']); 
    // Δημιουργεί SQL ερώτημα για διαγραφή της εγγραφής με το συγκεκριμένο 'announcement_id' από τον πίνακα 'admin_announcements'
    $sql = "DELETE FROM admin_announcements WHERE ann_id = $announcement_id";
    // Εκτελεί το SQL ερώτημα
    if (mysqli_query($con, $sql)) {
        // Αν η εκτέλεση του ερωτήματος είναι επιτυχής, επιστρέφει JSON με 'success' ως true
        echo json_encode(["success" => true]);
    } else {
        // Αν η εκτέλεση του ερωτήματος αποτύχει, επιστρέφει JSON με 'success' ως false και μήνυμα σφάλματος
        echo json_encode(["success" => false, "error" => mysqli_error($con)]);
    }
    // Κλείνει τη σύνδεση με τη βάση δεδομένων
    mysqli_close($con);
} else {
    // Αν το πεδίο 'announcement_id' δεν υπάρχει στο POST αίτημα, επιστρέφει JSON με success ως false και μήνυμα σφάλματος
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}
?>
