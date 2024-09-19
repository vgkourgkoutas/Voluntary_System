<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
// Ενεργοποίηση εμφάνισης σφαλμάτων 
ini_set('display_errors', 1); // Εμφάνιση σφαλμάτων
ini_set('display_startup_errors', 1); // Εμφάνιση σφαλμάτων κατά την εκκίνηση
error_reporting(E_ALL); // Εμφάνιση όλων των τύπων σφαλμάτων

// Συμπερίληψη αρχείου σύνδεσης με τη βάση δεδομένων
include('database.php');

if (!isset($_SESSION['logged_in2'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit(); // Διασφαλίζει ότι δεν θα εκτελούνται περαιτέρω εντολές
}

// Συμπερίληψη κώδικα σύνδεσης στη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}
// Ανακτά το όνομα χρήστη του διασώστη από τη συνεδρία
$rescuer_username = $_SESSION['username'];

// Αλλαγή από mysqli σε PDO για συνέπεια με τον τύπο σύνδεσης της βάσης δεδομένων
try {
    // Δημιουργία σύνδεσης με PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ρύθμιση του PDO να ρίχνει εξαιρέσεις σε περίπτωση σφαλμάτων
    // Ερώτηση για την ανάκτηση του ID του διασώστη με βάση το όνομα χρήστη
    $stmt = $pdo->prepare("SELECT rescuer_id FROM rescuers WHERE rescuer_username = ?");
    $stmt->execute([$rescuer_username]);
    $rescuer = $stmt->fetch(PDO::FETCH_ASSOC);  // Ανάκτηση των αποτελεσμάτων

    $rescuer_id = $rescuer['rescuer_id']; // Αποθήκευση του ID του διασώστη
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage()); // Διακοπή εκτέλεσης αν υπάρξει σφάλμα PDO
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Έλεγχος αν η αίτηση είναι τύπου POST
    // Ανακτά τα δεδομένα απο τη φόρμα
    $citizen_id = $_POST['citizen_id'];
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $date_added = $_POST['date_added'];
    $type = $_POST['type'];
    $task_latitude = $_POST['task_latitude'];
    $task_longitude = $_POST['task_longitude'];

    // Έλεγχος αν έχει υποβληθεί το task_id
    if (isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];

        if ($type === 'request') {
            // Εισαγωγή νέου αιτήματος στον πίνακα citizen_requests
            $sql_request = "INSERT INTO citizen_requests (citres_citizen_id, citres_stuff, citres_people, citres_date_added) 
                            VALUES (?, ?, ?, ?)";

            $stmt_request = $conn->prepare($sql_request);
            $stmt_request->bind_param("ssis", $citizen_id, $item, $quantity, $date_added);

            if ($stmt_request->execute()) {
                echo "success";  // Επιτυχής εισαγωγή
                // Διαγραφή από τον πίνακα task_connections
                $deleteConnectionSql = "DELETE FROM task_connections WHERE rescuer_id = ? AND task_latitude = ? AND task_longitude = ? AND task_type = ?";
                $stmt = $conn->prepare($deleteConnectionSql);
                $stmt->bind_param("idds", $rescuer_id, $task_latitude, $task_longitude, $type);
                $stmt->execute();
            } else {
                echo "Σφάλμα κατά την εισαγωγή στον πίνακα citizen_requests: " . $stmt_request->error;
            }

            $stmt_request->close(); // Κλείσιμο του statement

            

        } else if ($type === 'offer') {
            $sql_offer = "INSERT INTO citizen_offers (citoff_citizen_id, citoff_stuff, citoff_quantity, citoff_date_added) 
                          VALUES (?, ?, ?, ?)";

            $stmt_offer = $conn->prepare($sql_offer);
            $stmt_offer->bind_param("ssis", $citizen_id, $item, $quantity, $date_added);

            if ($stmt_offer->execute()) {
                echo "success";  // Επιτυχής εισαγωγή
                // Διαγραφή από τον πίνακα task_connections
                $deleteConnectionSql = "DELETE FROM task_connections WHERE rescuer_id = ? AND task_latitude = ? AND task_longitude = ? AND task_type = ?";
                $stmt = $conn->prepare($deleteConnectionSql);
                $stmt->bind_param("idds", $rescuer_id, $task_latitude, $task_longitude, $type);
                $stmt->execute();
            } else {
                echo "Σφάλμα κατά την εισαγωγή στον πίνακα citizen_offers: " . $stmt_offer->error; // Σφάλμα εισαγωγής
            }

            $stmt_offer->close(); // Κλείσιμο του statement

        }

        // Ερώτημα SQL για διαγραφή του συγκεκριμένου task με βάση το task_id
        $sql = "DELETE FROM current_tasks WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            echo "success";

            // Ενημέρωση του αριθμού των tasks του οχήματος
            $sql_update_vehicle = "UPDATE vehicles 
                                   SET vehicle_tasks = vehicle_tasks - 1 
                                   WHERE vehicle_rescuer_id = ? 
                                   AND vehicle_tasks > 0"; // Εξασφάλιση ότι ο αριθμός των tasks δεν πάει κάτω από το μηδέν
            $stmt_update_vehicle = $conn->prepare($sql_update_vehicle);
            $stmt_update_vehicle->bind_param("i", $rescuer_id);

            // Εκτέλεση της εντολής ενημέρωσης
            if ($stmt_update_vehicle->execute()) {
                echo "Vehicle task count updated successfully."; // Επιτυχής ενημέρωση
            } else {
                echo "Σφάλμα κατά την ενημέρωση του πίνακα vehicles: " . $stmt_update_vehicle->error; // Σφάλμα ενημέρωσης
            }

            $stmt_update_vehicle->close(); // Κλείσιμο του statement

        } else {
            echo "Σφάλμα κατά τη διαγραφή του task: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Δεν υποβλήθηκε το task_id.";
    }

    $conn->close(); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

} else {
    echo json_encode(['status' => 'error', 'error' => 'Invalid request method']);  // Μήνυμα σφάλματος αν η μέθοδος δεν είναι POST
}
