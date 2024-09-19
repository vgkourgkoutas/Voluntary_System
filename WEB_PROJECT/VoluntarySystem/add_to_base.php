<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php');

if (!isset($_SESSION['logged_in2'])) // Έλεγχος εάν υπάρχει σύνδεση διασώστη
{
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{   // Παίρνουμε το όνομα χρήστη του διασώστη από τη συνεδρία (session)
    $rescuer_username = $_SESSION['username'];
    // Προετοιμασία και εκτέλεση της εντολής SQL για να πάρουμε το ID του διασώστη και το όνομα του οχήματος του διασώστη
    $stmt = $conn->prepare("SELECT rescuer_id, rescuer_vehicle FROM rescuers WHERE rescuer_username = ?");
    $stmt->execute([$rescuer_username]);
    $rescuer = $stmt->fetch(PDO::FETCH_ASSOC);
    // Έλεγχος αν η ερώτηση δεν επέστρεψε αποτελέσματα
    if (!$rescuer) 
    {
        exit(); // Αν δεν βρεθεί ο διασώστης, διακόπτεται η εκτέλεση
    }
    // Παίρνουμε το όνομα του αντικειμένου και της ποσότητας από τη φόρμα
    $itname = $_POST['itname'];
    $quantity = $_POST['posothta'];

     
    try
    {
        // Προετοιμασία SQL για έλεγχο αν υπάρχει ήδη καταχώρηση του αντικειμένου στη βάση δεδομένων
        $stmt2 = $conn->prepare("SELECT item_quantity FROM items WHERE item_stuff = ?");
        $stmt2->execute([$itname]);
        // Έλεγχος αν υπάρχει ήδη ποσότητα για το αντικείμενο
        if ($stmt2->fetchColumn() > 0) 
        {
            // Fetch του αποτελέσματος στην μεταβλητή add_quantity
            $result = $stmt2->fetch(PDO::FETCH_ASSOC);
            $add_quantity = $result['item_quantity'] + $quantity;
            // Ενημέρωση της ποσότητας στη βάση δεδομένων αν το αντικείμενο υπάρχει ήδη στην βάση δεδομένων
            $stmt_update = $conn->prepare("UPDATE items SET item_quantity = ? WHERE item_stuff = ?");
            $stmt_update->execute([$add_quantity, $itname]);
        }
    }
    catch (PDOException $e)
    {   // Αν προκύψει κάποιο σφάλμα, τυπώνεται το μήνυμα του σφάλματος
        $errorMessage = $e->getMessage();
        print($errorMessage);
    } 
} 
else 
{
    exit();  // Αν το αίτημα δεν είναι POST, διακόπτεται η εκτέλεση
}

?>
