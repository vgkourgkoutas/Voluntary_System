<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php'); // php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων
// Ορισμός παραμέτρων για τη σύνδεση με τη βαση
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";

// Δημιουργία σύνδεσης με τη βάση
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης με την βάση
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Λαμβάνουμε τον όρο αναζήτης από το autocomplete. 
//Η παράμετρος q υπάρχει στο URL. 
//Οταν η τιμη της παραμέτρου περνάει στο URL (π.χ. select_items.php?q=shirt) 
//η php λαμβάνει αυτη τη τιμή μέσω της $_GET['q']
//και την αποθηκεύει στη μεταβλητή $searchTerm.

$searchTerm = $_GET['q'];

// Ανάκτηση δεδομένων απο το πίνακα items
$sql = "SELECT item_stuff FROM items WHERE item_stuff LIKE '%$searchTerm%'";
$result = $conn->query($sql);

// Τοποθέτηση των δεδομένων σε πίνακα data για την χρήση του σε μορφή json
$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['item_stuff'];
    }
}

// Έξοδος των αποτελεσμάτων ως json κωδικοποίηση
echo json_encode($data);

$conn->close();
?>