<?php 
// Ελέγχει αν η συνεδρία έχει ξεκινήσει και αν ο χρήστης είναι συνδεδεμένος
// Αν η συνεδρία δεν έχει ξεκινήσει (δηλαδή αν το 'logged_in' δεν είναι ορισμένο)
// Ανακατευθύνει τον χρήστη στη σελίδα logout.php
if (!isset($_SESSION['logged_in']))
{
  header('Location: logout.php');
}
//Αυτή είναι η βάση με την οποία συνδέεται το σύστημα
//Ότι στοιχεία εισάγονται από τα αρχεία .php αποθηκεύονται
//στην βάση voluntary_system στους ανάλογους πίνακες
$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "voluntary_system";

// Προσπαθεί να δημιουργήσει μια νέα σύνδεση στη βάση δεδομένων με PDO
try
{
  // Δημιουργεί μια νέα σύνδεση PDO με τη βάση δεδομένων
  $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);

  // Ρυθμίζει την PDO να εκτελεί εξαιρέσεις (exceptions) σε περίπτωση σφαλμάτων
  // Αυτό σημαίνει ότι θα προκαλέσει εξαιρέσεις (PDOException) αν υπάρξει σφάλμα κατά τη σύνδεση ή την εκτέλεση ερωτημάτων
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
  // Αν υπάρχει σφάλμα κατά την προσπάθεια σύνδεσης, το μήνυμα σφάλματος εκτυπώνεται
  // με $e->getMessage()
  echo "Connection failed : ". $e->getMessage();
}
?>