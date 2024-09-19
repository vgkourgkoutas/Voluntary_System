<?php 
session_start(); //Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php');  //Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 

if (isset($_POST['announcement']))
{
	$submit = $_POST['announcement'];
	// Ελέγχει αν ο διαχειριστής έχει πληκτρολογήσει ένα κείμενο το 
	// οποίο αποτελεί και την ανακοίνωσή.
	if (empty($submit)) //Αν δεν έχει συμπληρωθεί το πεδίο κειμένου, εμφανίζεται κατάλληλο μήνυμα.
	{ 
		header("Location: admin_announcement.php?error=Παρακαλώ γράψτε μία ανακοίνωση!");
	}
	else //Εισάγεται στον πίνακα admin_announcements η ανακοίνωση 
	{
			$stmt = $conn->prepare("INSERT INTO admin_announcements VALUES (null,?)");
		    $stmt->execute([$submit]);
			header("Location: admin_announcement.php");	
    }
} 
?>