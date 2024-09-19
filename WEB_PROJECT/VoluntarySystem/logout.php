<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
session_unset();  //Αφαίρεση όλων των μεταβλητών που έχουν αποθηκευτεί στο τρέχον session.
session_destroy(); // Καταστροφή του session, διαγράφοντας όλα τα δεδομένα του.
// Αποσύνδεση των χρηστών από όλες τις συνεδρίες.
$_SESSION['logged_in'] = false;
$_SESSION['logged_in1'] = false;
$_SESSION['logged_in2'] = false;
$_SESSION['logged_in3'] = false;

// Ανακατεύθυνση του χρήστη στη σελίδα εισόδου μετά την αποσύνδεση.
header('location:login_page.php');
?>