<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος εάν υπάρχει σύνδεση
    header('Location: logout.php');
}
// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// SQL ερώτημα που ενώνει τους πίνακες 'citizen_registration' και 'citizen_offers'
// Το ερώτημα ανακτά δεδομένα από αυτούς τους πίνακες με βάση το 'citizen_id' που συνδέει τους δύο πίνακες
$sql = "SELECT citoff_citizen_id,citizen_name,citizen_phone,citizen_latitude,citizen_longitude,citoff_stuff,citoff_quantity,citoff_date_added,citoff_state FROM citizen_registration INNER JOIN citizen_offers ON citizen_registration.citizen_id=citizen_offers.citoff_citizen_id";
$result = $conn->query($sql);



$offers = [];
// Έλεγχος αν υπάρχουν αποτελέσματα από το ερώτημα
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offers[] = [    // Προσθήκη των δεδομένων της κάθε γραμμής στον πίνακα $offers
            'citizen_id' => $row['citoff_citizen_id'], 
            'citizen_name' => $row['citizen_name'],
            'citizen_phone' => $row['citizen_phone'],
            'latitude' => $row['citizen_latitude'],
            'longitude' => $row['citizen_longitude'],
            'item' => $row['citoff_stuff'], 
            'citizen_quantity' => $row['citoff_quantity'], // Ο αριθμός των ανθρώπων που αιτούνται ισοδυναμεί με την ποσότητα ενός αντικειμένου
            'date_added' => $row['citoff_date_added'],
            'state' => $row['citoff_state']
        ];
    }
}

$conn->close();  // Κλείσιμο της σύνδεσης

// Επιστρέφει τα στοιχεία σε json κωδικοποίηση
header('Content-Type: application/json');
echo json_encode($offers);
?>
