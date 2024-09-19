<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // Έλεγχος σύνδεσης


if (isset($_POST['update_quantity']) && isset($_POST['item'])) // Γίνεται έλεγχος αν έχει οριστεί ποσότητα και αν έχει επιλεγεί το αντικείμενο
{
	$quantity = $_POST['update_quantity'];
    $item = $_POST['item'];
    try
    {   // Δημιουργία query για να γίνει τροποποίηση ποσότητας από τον διαχειριστή
        $stmt = $conn->prepare("UPDATE items SET item_quantity = ? WHERE item_stuff = ?");
        $stmt->execute([$quantity , $item]);
        header("Location: add_item.php?error=Η ποσότητα προστέθηκε επιτυχώς στην βάση!");
    }
    catch (PDOException $e) //Σε περίπτωση που προκληθεί σφάλμα εμφανίζεται κατάλληλο μήνυμα
    {
        $errorMessage = $e->getMessage();
        print($errorMessage);
        header("Location: add_item.php?error=Η ποσότητα δεν προστέθηκε στην βάση!");
    }   
}
?>