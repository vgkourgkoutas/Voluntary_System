<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων
// Έλεγχος αν έχουν συμπληρωθεί τα πεδία όνομα χρήστη, κωδικός καθώς και οι συντεταγμένες γεωγραφικό μήκος και πλάτος
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['latitude']) && isset($_POST['longitude']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    $latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
    
        try
        {
            //Ο χρήστης καταχωρείται ως διασώστης στην Βάση
            $stmt = $conn->prepare("INSERT INTO allusers VALUES (null,?,?,'rescuer')");
            $stmt->execute([$username, $password]);

            //Συνάρτηση που καλεί τον τελευταίο αριθμό που προστέθηκε στον πίνακα
            //allusers ώστε να περαστεί ακριβώς ο ίδιος και στον πίκακα rescuers.
            $user_id = $conn->lastInsertId();

            //Εισαγωγή του νέου διασώστη με χρήστη του id που μπήκε και στον
            //πίνακα allusers
            $stmt2 = $conn->prepare("INSERT INTO rescuers VALUES (?,?,?)");
            $stmt2->execute([$user_id, $username, "Vehicle of $username"]);

            //Εισαγωγή του οχήματος του διασώστη στον πίνακα vehicles με το όνομα
            //να έχει την μορφή "vehicle of user"
            $stmt3 = $conn->prepare("INSERT INTO vehicles (vehicle_rescuer_id, vehicle_name, vehicle_latitude, vehicle_longitude) VALUES (?,?,?,?)");
            $stmt3->execute([$user_id, "Vehicle of $username", $latitude, $longitude]);

            header("Location: admin_registers_rescuers.php?error=Επιτυχής Εγγραφή!");
        } 
        catch(PDOException $e) 
        {
            $errorMessage = $e->getMessage(); // Αν υπάρξη κάποιο error εμφανίζεται κατάλληλο μήνυμα 
            header("Location: admin_registers_rescuers.php?error=Υπήρξε κάποιο πρόβλημα, παρακαλώ δοκιμάστε ξανά.");
        }
    
}
?>
