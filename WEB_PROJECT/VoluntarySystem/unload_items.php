<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php');


if (!isset($_SESSION['logged_in2'])) { //Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit();
}

// Σύνδεση με τη βάση δεδομένων
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

// Επιλογή της βάσης δεδομένων
mysqli_select_db($con, "voluntary_system");

// Λήψη του ID του διασώστη από τη συνεδρία
$rescuer_id = $_SESSION['id'];

// Έλεγχος αν το κουμπί "unload_all" (ξεφόρτωμα όλων) έχει πατηθεί
if (isset($_POST['unload_all'])) {
    // Ανάκτηση όλων των αντικειμένων από την αποθήκη οχήματος για τον συγκεκριμένο διασώστη
    $sql_vehicle = "SELECT item_id, item_quantity FROM vehicle_storage WHERE vehicle_rescuer_id = ?";
    $stmt_vehicle = $con->prepare($sql_vehicle); // Δημιουργεί ερώτημα για την ανάκτηση αντικειμένων
    $stmt_vehicle->bind_param('i', $rescuer_id); // Συνδέει το ID του διασώστη στο ερώτημα
    $stmt_vehicle->execute();  // Εκτελεί το ερώτημα
    $result_vehicle = $stmt_vehicle->get_result(); // Παίρνει τα αποτελέσματα του ερωτήματος

    while ($row = $result_vehicle->fetch_assoc()) { // Επεξεργασία των αποτελεσμάτων
        $item_id = $row['item_id']; // Λήψη του ID του αντικειμένου
        $quantity_to_add = $row['item_quantity']; // Λήψη της ποσότητας του αντικειμένου

         // Ενημέρωση του πίνακα αντικειμένων για να αυξήσει την ποσότητα
        $update_items_sql = "UPDATE items SET item_quantity = item_quantity + ? WHERE item_id = ?";
        $update_items_stmt = $con->prepare($update_items_sql); // Δημιουργεί ερώτημα για την ενημέρωση της ποσότητας
        $update_items_stmt->bind_param('ii', $quantity_to_add, $item_id); // Συνδέει την ποσότητα και το ID του αντικειμένου στο ερώτημα
        $update_items_stmt->execute(); // Εκτελεί το ερώτημα
        $update_items_stmt->close(); // Κλείνει την δήλωση
    }

    // Διαγραφή όλων των αντικειμένων από την αποθήκη οχήματος για τον συγκεκριμένο διασώστη
    $delete_vehicle_sql = "DELETE FROM vehicle_storage WHERE vehicle_rescuer_id = ?";
    $delete_vehicle_stmt = $con->prepare($delete_vehicle_sql); // Δημιουργεί ερώτημα για τη διαγραφή αντικειμένων
    $delete_vehicle_stmt->bind_param('i', $rescuer_id); // Συνδέει το ID του διασώστη στο ερώτημα
    $delete_vehicle_stmt->execute(); // Εκτελεί το ερώτημα
    $delete_vehicle_stmt->close(); // Κλείνει την προετοιμασμένη δήλωση

    // Ανακατεύθυνση με μήνυμα επιτυχίας
    header("Location:load_unload_to_base.php?message=Τα αντικείμενα ξεφορτώθηκαν επιτυχώς!"); // Ανακατευθύνει στη σελίδα με μήνυμα επιτυχίας
    exit();  // Τερματισμός της εκτέλεσης του script
}

// Κλείσιμο της σύνδεσης με τη βάση δεδομένων
mysqli_close($con);
?>
