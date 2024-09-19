<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων
//Ελέγχει αν ο χρήστης έχει εισάγει όνομα, τον κωδικό, το πλήρες ονοματεπώνυμο του και το τηλέφωνο επικοινωνίας του
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['fullname']) && isset($_POST['telephone']))
{   // Αποθηκεύουμε τα δεδομένα του χρήστη σε μεταβλητές
	$username = $_POST['username'];
	$password = $_POST['password'];
	$fullname = $_POST['fullname'];
    $telephone = $_POST['telephone'];
    $latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	
	//Εάν κάποιο απο τα στοιχεία λείπει, τότε δίνεται το αντίστοιχο μήνυμα σφάλματος για το συγκεκριμένο
	//πεδίο
	if (empty($username))
	{ 
		header("Location: registration.php?error=Παρακαλώ εισάγετε όνομα χρήστη");

	}                  
	else if (empty($password))
	{
		header("Location: registration.php?error=Παρακαλώ εισάγετε κωδικό χρήστη ");
	}
	else if (empty($fullname))
	{
		header("Location: registration.php?error=Παρακαλώ εισάγετέ ένα ονοματεπώνυμο ");     
	}
    else if (empty($telephone))
	{
		header("Location: registration.php?error=Παρακαλώ εισάγετε έναν τηλεφωνικό αριθμό");     
	}
	else
	{
		//Ελέγχεται με query αν το όνομα που πληκτρολογήθηκε υπάρχει ήδη
		$stmt = $conn->prepare("SELECT * FROM allusers WHERE user_username=?");
		$stmt->execute([$username]);
		if($stmt->rowCount() === 1) 
		{
			header("Location: registration.php?error=Το Όνομα χρήστη υπάρχει ήδη!");
		}
		else
		{
			try
			{
				//Καταγράφονται στην βάση τα στοιχεία που εισήγαγε ο νέος χρήστης στον πίνακα allusers
				$stmt3 = $conn->prepare("INSERT INTO allusers VALUES (null,?,?,'citizen')");
				$stmt3->execute([$username,$password]);

				//Ανάκτηση του id που μολις προστέθηκε στον πίνακα allusers
				$citizen_id = $conn->lastInsertId();

				//Καταγράφονται στην βάση τα στοιχεία που εισήγαγε ο νέος χρήστης στον πίνακα citizen_registration
				$stmt2 = $conn->prepare("INSERT INTO citizen_registration VALUES (?,?,?,?,?,?,?)");
				$stmt2->execute([$citizen_id,$username,$password,$fullname,$telephone,$latitude,$longitude]);
				header("Location: registration.php?error=Επιτυχής εγγραφή!");
			}
			catch (PDOException $e) 
			{
				//Εμφανίζεται μήνυμα λάθος σε περίπτωση που το όνομα χρήστη υπάρχει ήδη
				$errorMessage = $e->getMessage();
				header("Location: registration.php?error=Το συγκεκριμένο username υπάρχει ήδη!");
			}
		}
	}
}

?>
