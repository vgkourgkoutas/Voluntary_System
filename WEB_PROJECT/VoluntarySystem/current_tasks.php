<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php');

if (!isset($_SESSION['logged_in2'])) /// Έλεγχος αν υπάρχει σύνδεση διασώστη
{
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') // Έλεγχος αν η μέθοδος του αιτήματος είναι POST
{   // Ανάκτηση του ονόματος χρήστη του διασώστη από τη συνεδρία
    $rescuer_username = $_SESSION['username'];
    // Προετοιμασία και εκτέλεση δήλωσης για να ανακτηθεί το ID και το όχημα του διασώστη βάσει του ονόματος χρήστη
    $stmt = $conn->prepare("SELECT rescuer_id, rescuer_vehicle FROM rescuers WHERE rescuer_username = ?");
    $stmt->execute([$rescuer_username]);
    // Αποθήκευση του αποτελέσματος της ερώτησης σε πίνακα rescuer
    $rescuer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rescuer) // Έλεγχος αν ο διασώστης δεν βρέθηκε
    {
        exit(); // Εάν δεν βρέθηκε ο διασώστης, τερματίζεται η εκτέλεση του κώδικα
    }
    // Αποθήκευση του ID του διασώστη και άλλων δεδομένων που στάλθηκαν μέσω της φόρμας POST
    $rescuer_id = $rescuer['rescuer_id'];
    $currtask_fullname = $_POST['currtask_fullname'];
    $currtask_phone = $_POST['currtask_phone'];
    $currtask_stuff = $_POST['currtask_stuff'];
    $currtask_quantity = $_POST['currtask_quantity'];

    // Το όνομα του οχήματος του διασώστη αποθηκεύεται σε μία μεταβλητή
    $rescuer_vehicle_name = $rescuer['rescuer_vehicle'];

    // Έλεγχος αν υπάρχει ήδη καταχώρηση με τα ίδια στοιχεία
    $stmt_check_duplicate = $conn->prepare("SELECT COUNT(*) FROM current_tasks WHERE currtask_fullname = ? AND currtask_phone = ? AND currtask_stuff = ? AND currtask_quantity = ? AND currtask_vehicle_name = ?");
    $stmt_check_duplicate->execute([$currtask_fullname, $currtask_phone, $currtask_stuff, $currtask_quantity, $rescuer_vehicle_name]);

    if ($stmt_check_duplicate->fetchColumn() > 0) {
        // Εμφάνιση μηνύματος σφάλματος αν έχει ήδη παραληφθεί, 
        echo 'Έχετε ήδη παραλάβει αυτό το αίτημα/προσφορά.';
    } 
    else
    {
        // Εισαγωγή στη βάση δεδομένων στο πίνακα current_tasks
        $stmt_insert = $conn->prepare("INSERT INTO current_tasks (currtask_rescuer_id, currtask_fullname, currtask_phone, currtask_stuff, currtask_quantity, currtask_vehicle_name) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->execute([$rescuer_id, $currtask_fullname, $currtask_phone, $currtask_stuff, $currtask_quantity, $rescuer_vehicle_name]);

        // Απάντηση με μήνυμα επιτυχίας
        echo 'Το είδος παραλήφθηκε επιτυχώς.';
    }
} 
else 
{
    exit();
}
?>
