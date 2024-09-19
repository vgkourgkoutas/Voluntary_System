<?php 
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων
//Ελέγχει αν ο χρήστης έχει εισάγει όνομα, τον κωδικό και έχει επιλέξει κατηγορία χρήστη
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['category']))
{
	$admin = $_POST['username'];
	$password = $_POST['password'];
	$category = $_POST['category'];
	
	
	//εάν κάποιο από τα τρία στοιχεία δεν έχει εισαχθεί, τότε ο χρήστης λαμβάνει το ανάλογο
	//μήνυμα λάθους και γίνεται ανακατεύθυνση στην σελίδα login_page.php
	if (empty($admin))
	{ 
		header("Location:login_page.php?error=Παρακαλώ εισάγετε όνομα χρήστη");

	}                 
	else if (empty($password))
	{
		header("Location: login_page.php?error=Παρακαλώ εισάγετε κωδικό χρήστη ");
	}
	else if (empty($category))
	{
		header("Location: login_page.php?error=Παρακαλώ επιλέξτε μία κατηγορία");     
	}
	else
	{
		
		// Ετοιμάζει μια SQL ερώτηση με παραμέτρους για να ελέγξει αν υπάρχει χρήστης με τα δεδομένα (username, password, user_role)
		$stmt = $conn->prepare("SELECT * FROM allusers WHERE user_username=? AND user_password=? AND user_role=?");

		// Εκτελεί την ερώτηση με τα δεδομένα που παρέχονται (username, password, user_role) μέσω του πίνακα
		$stmt->execute([$admin,$password,$category]);
        // Ελέγχει αν υπάρχει ακριβώς μία εγγραφή που ταιριάζει με τα δεδομένα
		if ($stmt->rowCount() === 1)
		{  // Αν υπάρχει μία μόνο εγγραφή, την ανακτά
			$user = $stmt->fetch();
             // Αποθηκεύουμε τα δεδομένα του χρήστη σε μεταβλητές
			$usern = $user['user_username'];
			$user_password = $user['user_password'];
			$user_category = $user['user_role'];
			$userID = $user['user_id'];
			// Αποθηκεύουμε τα δεδομένα του χρήστη στις μεταβλητές της συνεδρίας (session) για μελλοντική χρήση
			$_SESSION['username'] = $usern;
            $_SESSION['password'] = $user_password;
			$_SESSION['category'] = $user_category;
			$_SESSION['id'] = $userID;   
			
			//Ανάλογα με την κατηγορία χρήστη γίνεται ανακατατεύθυνση στην αντίστοιχη σελίδα
			if($category == 'admin')
			{ // Αν η κατηγορία είναι 'admin', ρυθμίζει τις μεταβλητές συνεδρίας για το admin και ανακατευθύνει στη σελίδα admin_menu.php
				$_SESSION["logged_in1"] = true;
				$_SESSION["logged_in"] = true;
				header("Location:admin_menu.php");
			}

			if($category == 'rescuer')
			{ // Αν η κατηγορία είναι 'rescuer', ρυθμίζει τις μεταβλητές συνεδρίας για το rescuer και ανακατευθύνει στη σελίδα rescuer_menu.php
				$_SESSION["logged_in2"] = true;
				$_SESSION["logged_in"] = true;
				header("Location:rescuer_menu.php"); 
			}

			if($category == 'citizen')
			{ // Αν η κατηγορία είναι 'citizen', ρυθμίζει τις μεταβλητές συνεδρίας για το citizen και ανακατευθύνει στη σελίδα citizen_menu.php
				$_SESSION["logged_in3"] = true;
				$_SESSION["logged_in"] = true;
				header("Location:citizen_menu.php");
			}
		}
		else
		{   // Αν δεν υπάρχει ακριβώς μία εγγραφή, ανακατευθύνει τον χρήστη πίσω στη σελίδα σύνδεσης με μήνυμα σφάλματος
			header("Location: login_page.php?error=Λανθασμένο Όνομα χρήστη ή κωδικός&username=$admin");
		}
	}
}

?>