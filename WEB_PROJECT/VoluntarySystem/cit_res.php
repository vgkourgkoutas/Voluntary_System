<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php');

if (!isset($_SESSION['logged_in'])) // Έλεγχος αν υπάρχει σύνδεση
{
	header('Location: login_page.php');
}

// Υποθέτουμε ότι έχουμε ένα αναγνωριστικό για τον πολίτη
$citizen_username = $_SESSION['username'];

// Ανάκτηση του citizen_id από τη βάση δεδομένων βάσει του ονόματος χρήστη
$stmt = $conn->prepare("SELECT citizen_id FROM citizen_registration WHERE citizen_username = ?");
$stmt->execute([$citizen_username]);
$citizen = $stmt->fetch(PDO::FETCH_ASSOC); // Λήψη του citizen_id από τα αποτελέσματα
// Έλεγχος αν το citizen_id δεν βρέθηκε στη βάση δεδομένων
if (!$citizen)
{
   // Ανακατεύθυνση ή χειρισμός της περίπτωσης που δεν βρέθηκε το citizen_id
    header('Location: login_page.php?error=Citizen ID not found');
    exit(); // Διακοπή εκτέλεσης του κώδικα
}

$citizen_id = $citizen['citizen_id']; // Αποθήκευση του citizen_id για μελλοντική χρήση
// Έλεγχος αν έχουν υποβληθεί τα πεδία 'autocomplete-input' και 'quantity' μέσω POST
if (isset($_POST['autocomplete-input']) && isset($_POST['quantity']))
{
	$item = $_POST['autocomplete-input']; // Λήψη του επιλεγμένου αντικειμένου
	$quantity = $_POST['quantity']; // Λήψη της ποσότητας

	// Έλεγχος αν το πεδίο του αντικειμένου είναι κενό
	if (empty($item))
	{   // Ανακατεύθυνση με μήνυμα σφάλματος
		header("Location:citizen_requests.php?error=Παρακαλώ επιλέξτε ένα από τα διαθέσιμα αντικείμενα!");
	}                 
	else
	{
		try
		{
			// Εισαγωγή του αιτήματος στη βάση δεδομένων στον πίνακα citizen_requests
			$stmt = $conn->prepare("INSERT INTO citizen_requests (citres_citizen_id, citres_stuff, citres_people, citres_date_added) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
			$stmt->execute([$citizen_id, $item, $quantity]); // Εκτέλεση της εισαγωγής με τα δεδομένα που υποβλήθηκαν

			// Ανακατεύθυνση με μήνυμα επιτυχίας
			header("Location: citizen_requests.php?error=Το αίτημα σας καταχωρήθηκε επιτυχώς στην Βάση!");
		}
		catch (PDOException $e)
		{   // Χειρισμός τυχόν σφάλματος κατά την εισαγωγή
			$errorMessage = $e->getMessage(); // Αποθήκευση του μηνύματος σφάλματος
			print($errorMessage); // Εκτύπωση του σφάλματος για debugging 
			header("Location: citizen_requests.php?error=Το αντικείμενο που ζητήσατε δεν υπάρχει στην Βάση!");
		}   
	}
}
?>
