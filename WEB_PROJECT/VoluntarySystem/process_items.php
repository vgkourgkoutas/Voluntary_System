<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php');

if (!isset($_SESSION['logged_in2'])) {  // Έλεγχος αν υπάρχει σύνδεση με διασώστη
    header('Location: logout.php');
    exit();
}

// Έλεγχος της σύνδεσης
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

// Επιλογή της βάσης δεδομένων
mysqli_select_db($con, "voluntary_system");

// Λήψη του ID του διασώστη από τη συνεδρία
$rescuer_id = $_SESSION['id'];

// Έλεγχος αν έχουν επιλεγεί αντικείμενα
if (!empty($_POST['select'])) {

    echo '<pre>';
    print_r($_POST); // Debug: Εκτύπωση των δεδομένων POST για εντοπισμό σφαλμάτων
    echo '</pre>';
    foreach ($_POST['select'] as $item_id => $value) {
        // Λήψη της ποσότητας που καθόρισε ο χρήστης.Αν δεν έχει καθοριστεί ποσότητα, ορίζουμε 0.
        $quantity = isset($_POST['quantity'][$item_id]) ? intval($_POST['quantity'][$item_id]) : 0;
        echo $quantity;
        // Εξασφάλιση ότι η ποσότητα είναι μεγαλύτερη από 0
        if ($quantity > 0) {
            // Λήψη λεπτομερειών του αντικειμένου και έλεγχος αν υπάρχει αρκετή ποσότητα διαθέσιμη
            $sql = "SELECT item_stuff, item_quantity FROM items WHERE item_id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->bind_result($item_name, $item_quantity);
            $stmt->fetch();
            $stmt->close();
            // Έλεγχος αν η διαθέσιμη ποσότητα είναι επαρκής
            if ($item_quantity >= $quantity) {
                //Εισαγωγή ή ενημέρωση της ποσότητας του αντικειμένου στον πίνακα vehicle_storage, αν το αντικείμενο ήδη υπάρχει.
                $insert_sql = "INSERT INTO vehicle_storage (vehicle_rescuer_id, item_id, item_name, item_quantity)
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE item_quantity = item_quantity + VALUES(item_quantity)";
                $insert_stmt = $con->prepare($insert_sql);
                $insert_stmt->bind_param('iisi', $rescuer_id, $item_id, $item_name, $quantity);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Ενημέρωση του πίνακα items για μείωση της ποσότητας από την βάση
                $update_sql = "UPDATE items SET item_quantity = item_quantity - ? WHERE item_id = ?";
                $update_stmt = $con->prepare($update_sql);
                $update_stmt->bind_param('ii', $quantity, $item_id);
                $update_stmt->execute();
                $update_stmt->close();

                 // Ανακατεύθυνση στη σελίδα map_rescuer
                header("Location: map_rescuer.php?error=Τα αντικείμενα φορτώθηκαν επιτυχώς!");


            } else {
                // Διαχείριση περίπτωσης όπου η ζητούμενη ποσότητα είναι μεγαλύτερη από την διαθέσιμη
                echo "Δεν υπάρχει αρκετή διαθέσιμη ποσότητα για το αντικείμενο: $item_name. Αιτήθηκε: $quantity, Διαθέσιμη: $item_quantity.";
            }
        }
    }
}

// Κλείσιμο της σύνδεσης με τη βάση δεδομένων
mysqli_close($con);


exit();
?>
