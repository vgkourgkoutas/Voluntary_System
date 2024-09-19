<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων

// Υποθέτοουμε ότι έχουμε ένα μοναδικό αναγνωριστικό για τον πολίτη (π.χ. citizen_username)
$citizen_username = $_SESSION['username'];

// Επιλογή του citizen_id από τη βάση δεδομένων με βάση το όνομα χρήστη
$stmt = $conn->prepare("SELECT citizen_id FROM citizen_registration WHERE citizen_username = ?");
$stmt->execute([$citizen_username]);
$citizen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$citizen)
{
    // Ανακατεύθυνση σε περίπτωση όπου το citizen_id δεν βρέθηκε
    header('Location: logout.php?error=Citizen ID not found');
    exit();
}

$citizen_id = $citizen['citizen_id'];


if (isset($_POST['item']) && isset($_POST['update_quantity'])) // Έλεγχος αν έχει επιλεχθεί το αντικείμενο και η ποσότητα
{
	$itemstuff = $_POST['item'];
	$quantity = $_POST['update_quantity'];
		try
		{
			//Προστίθεται στην βάση για το αντικείμενο ο κωδικός του, το όνομά του,
			//η κατηγορία του και ως προεπιλεγμένη τιμή 1 για την ποσότητά του.
			$stmt = $conn->prepare("INSERT INTO citizen_offers (citoff_id, citoff_citizen_id, citoff_stuff, citoff_quantity, citoff_date_added) VALUES (null,?,?,?, CURRENT_TIMESTAMP)");
		    $stmt->execute([$citizen_id , $itemstuff, $quantity]);
			header("Location: citizen_offers.php?error= Η προσφορά σας καταχωρήθηκε με επιτυχία!");
		}
		catch (PDOException $e) // Εμφάνιση κατάλληλου μηνύματος σε περίπτωση error
		{
			$errorMessage = $e->getMessage();
			print($errorMessage);

		}   
	
}
?>