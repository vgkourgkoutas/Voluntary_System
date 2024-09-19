<?php 
error_reporting(E_ALL); //Εμφάνιση σφαλμάτων στην κονσόλα
ini_set('display_errors', 1);

session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων

if (isset($_POST['itname']) && isset($_POST['dropdown'])) //Γίνεται έλεγχος αν έχει γίνει επιλογή κατηγορίας και έχει συμπληρωθεί το πλαίσιο για το όνομα του αντικειμένου
{
	$itemstuff = $_POST['itname']; // τοποθετούμε σε μεταβλητές τα ορίσματα που λαμβάνουμε από το POST
	$category = $_POST['dropdown'];

	if (empty($itemstuff)) //Αν το πλαίσιο ονόματος αντικειμένου είναι κενό τότε εμφανίζεται κατάλληλο μήνυμα
	{
		header("Location: add_item.php?error=Παρακαλώ εισάγετε ένα αντικείμενο");
	}
	
	elseif (empty($category)) //Αν στο dropdown δεν έχει επιλεχθεί κάποια κατηγορία τότε εμφανίζεται κατάλληλο μήνυμα
	{
		header("Location: add_item.php?error=Παρακαλώ εισάγετε μία κατηγορία");
	}
	else
	{
		try
		{
			//Προστίθεται στην βάση για το αντικείμενο ο κωδικός του, το όνομά του,
			//η κατηγορία του και ως προεπιλογή μια random τιμή με ανώτατο όριο τον αριθμό 100.
			$stmt = $conn->prepare("INSERT INTO items (item_id, item_stuff, item_quantity, item_category) VALUES (null,?,FLOOR(1 + (RAND() * (50 - 10))),?)");
		    $stmt->execute([$itemstuff, $category]);
			header("Location: add_item.php?error=Το αντικείμενο εισήχθη επιτυχώς στην επιθυμητή κατηγορία!");
		}
		catch (PDOException $e)
		{
			$errorMessage = $e->getMessage();  // Λαμβάνουμε μήνυμα σφάλματος εάν προκληθεί
			header("Location: add_item.php?error=Το αντικείμενο αυτό υπάρχει ήδη στην συγκεκριμένη κατηγορία!");  // Εμφανίζουμε το μήνυμα σφάλματος στη σελίδα
		}

	}
}
?>
