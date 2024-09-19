<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων

//Γίνεται έλεγχος εάν ο χρήστης έχει είσαγει στο πλάισιο ένα όνομα για την νέα κατηγορία
if (isset($_POST['catname']))
{
	$catname = $_POST['catname'];
	if (empty($catname)) //Εάν δεν έχει εισαχθεί κάποιο όνομα κατηγορίας προβάλετε μήνυμα σφάλματος
	{ 
		header("Location: add_category.php?error=Παρακαλώ εισάγετε κατηγορία");
	} 
	else
	{
		try
		{
			//Εισάγεται στον πίνακα add_category ο αριθμός της κατηγορίας, το όνομα και ημερομηνία/ώρα που προστέθηκε
			$stmt = $conn->prepare("INSERT INTO add_category VALUES (null,?,CURRENT_TIMESTAMP)");
		    $stmt->execute([$catname]);
			header("Location: add_category.php?error=Η κατηγορία εισήχθη επιτυχώς!");
		}
		catch (PDOException $e) // Εκτύπωση σε περίπτωση που προκληθεί κάποιο σφάλμα
		{
			$errorMessage = $e->getMessage();
			header("Location: add_category.php?error=Η κατηγορία υπάρχει ήδη!");
		}   
	}
}
?>
