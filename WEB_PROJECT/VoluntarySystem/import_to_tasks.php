<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
// Ενεργοποίηση εμφάνισης σφαλμάτων PHP
ini_set('display_errors', 1); // Εμφάνιση όλων των σφαλμάτων κατά την εκτέλεση
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in2'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit;
}
// Έλεγχος αν το αίτημα είναι POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Λήψη δεδομένων
    $type = $_POST['type'];
    $data = $_POST['data'];
    $vehicle_latitude = $_POST['vehicle_latitude'];
    $vehicle_longitude = $_POST['vehicle_longitude'];
    $task_latitude = $_POST['task_latitude'];
    $task_longitude = $_POST['task_longitude'];
    $rescuer_id = $_SESSION['id'];

    // Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "voluntary_system";

    try {
         // Δημιουργία σύνδεσης με τη βάση δεδομένων με χρήση PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
         // Θέτουμε το PDO error mode σε exception για καλύτερο έλεγχο σφαλμάτων
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Έλεγχος του τρέχοντος αριθμού tasks για το όχημα του διασώστη
        $sql_check = "SELECT vehicle_tasks FROM vehicles WHERE vehicle_rescuer_id = :rescuer_id";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindValue(':rescuer_id', $rescuer_id);
        $stmt_check->execute();
        $vehicle = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        // Έλεγχος αν το όχημα έχει λιγότερες από 4 αναθέσεις
        if ($vehicle['vehicle_tasks'] < 4) {
            if ($type === 'request') {  // Έλεγχος αν το αίτημα αφορά request
                // Εισαγωγή νέας ανάθεσης στο πίνακα current_tasks
                $sql = "INSERT INTO current_tasks (task_rescuer_id, citizen_id, citizen_fullname, citizen_telephone, offer_request_date_added, item_stuff, item_quantity, task_latitude, task_longitude, task_type) 
                        VALUES (:rescuer_id, :citizen_id, :citizen_name, :citizen_phone, :date_added, :item, :citizen_quantity, :task_latitude, :task_longitude, :task_type)";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':rescuer_id', $rescuer_id);
                $stmt->bindValue(':citizen_id', $data['citizen_id']);
                $stmt->bindValue(':citizen_name', $data['citizen_name']);
                $stmt->bindValue(':citizen_phone', $data['citizen_phone']);
                $stmt->bindValue(':date_added', $data['date_added']);
                $stmt->bindValue(':item', $data['item']);
                $stmt->bindValue(':citizen_quantity', $data['citizen_quantity']);
                $stmt->bindValue(':task_latitude', $task_latitude);
                $stmt->bindValue(':task_longitude', $task_longitude);
                $stmt->bindValue(':task_type', $type);

                $stmt->execute();
                // Διαγραφή του αντίστοιχου αιτήματος από τον πίνακα citizen_requests
                $sql2 = "DELETE FROM citizen_requests WHERE citres_citizen_id=:citizen_id AND citres_stuff=:item AND citres_people=:citizen_quantity";

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindValue(':citizen_id', $data['citizen_id']);
                $stmt2->bindValue(':item', $data['item']);
                $stmt2->bindValue(':citizen_quantity', $data['citizen_quantity']);

                $stmt2->execute();

                // Ενημέρωση του αριθμού αναθέσεων στο πίνακα vehicles και  αύξηση των αριθμών των task κατά 1
                $sql3 = "UPDATE vehicles 
                         SET vehicle_tasks = vehicle_tasks + 1  
                         WHERE vehicle_rescuer_id = :rescuer_id";

                $stmt3 = $conn->prepare($sql3);
                $stmt3->bindValue(':rescuer_id', $rescuer_id);

                $stmt3->execute();


                $sql4 = "INSERT INTO task_connections (rescuer_id, task_latitude, task_longitude, vehicle_latitude, vehicle_longitude, task_type) VALUES (:rescuer_id, :task_latitude, :task_longitude, :vehicle_latitude, :vehicle_longitude, :task_type)";

                $stmt4 = $conn->prepare($sql4);
                $stmt4->bindValue(':rescuer_id', $rescuer_id);
                $stmt4->bindValue(':task_latitude', $task_latitude);
                $stmt4->bindValue(':task_longitude', $task_longitude);
                $stmt4->bindValue(':vehicle_latitude', $vehicle_latitude);
                $stmt4->bindValue(':vehicle_longitude', $vehicle_longitude);
                $stmt4->bindValue(':task_type', $type);

                $stmt4->execute();
            } else if ($type === 'offer') {
                // Εισαγωγή νέας ανάθεσης για offer στο πίνακα current_tasks
                $sql = "INSERT INTO current_tasks (task_rescuer_id, citizen_id, citizen_fullname, citizen_telephone, offer_request_date_added, item_stuff, item_quantity, task_latitude, task_longitude, task_type) 
                        VALUES (:rescuer_id, :citizen_id, :citizen_name, :citizen_phone, :date_added, :item, :citizen_quantity, :task_latitude, :task_longitude, :task_type)";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':rescuer_id', $rescuer_id);
                $stmt->bindValue(':citizen_id', $data['citizen_id']);
                $stmt->bindValue(':citizen_name', $data['citizen_name']);
                $stmt->bindValue(':citizen_phone', $data['citizen_phone']);
                $stmt->bindValue(':date_added', $data['date_added']);
                $stmt->bindValue(':item', $data['item']);
                $stmt->bindValue(':citizen_quantity', $data['citizen_quantity']);
                $stmt->bindValue(':task_latitude', $task_latitude);
                $stmt->bindValue(':task_longitude', $task_longitude);
                $stmt->bindValue(':task_type', $type);

                $stmt->execute();
                // Διαγραφή της αντίστοιχης προσφοράς από τον πίνακα citizen_offers
                $sql2 = "DELETE FROM citizen_offers WHERE citoff_citizen_id=:citizen_id AND citoff_stuff=:item AND citoff_quantity=:citizen_quantity";

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindValue(':citizen_id', $data['citizen_id']);
                $stmt2->bindValue(':item', $data['item']);
                $stmt2->bindValue(':citizen_quantity', $data['citizen_quantity']);

                $stmt2->execute();

                // Ενημέρωση του αριθμού αναθέσεων στο πίνακα vehicles και αύξηση των task κατά 1
                $sql3 = "UPDATE vehicles 
                         SET vehicle_tasks = vehicle_tasks + 1 
                         WHERE vehicle_rescuer_id = :rescuer_id";

                $stmt3 = $conn->prepare($sql3);
                $stmt3->bindValue(':rescuer_id', $rescuer_id);

                $stmt3->execute();

                $sql4 = "INSERT INTO task_connections (rescuer_id, task_latitude, task_longitude, vehicle_latitude, vehicle_longitude, task_type) VALUES (:rescuer_id, :task_latitude, :task_longitude, :vehicle_latitude, :vehicle_longitude, :task_type)";

                $stmt4 = $conn->prepare($sql4);
                $stmt4->bindValue(':rescuer_id', $rescuer_id);
                $stmt4->bindValue(':task_latitude', $task_latitude);
                $stmt4->bindValue(':task_longitude', $task_longitude);
                $stmt4->bindValue(':vehicle_latitude', $vehicle_latitude);
                $stmt4->bindValue(':vehicle_longitude', $vehicle_longitude);
                $stmt4->bindValue(':task_type', $type);

                $stmt4->execute();
            }

            // Επιστροφή JSON με status success
            echo json_encode([
                'status' => 'success'
            ]);
        } else {  // Επιστροφή JSON με μήνυμα σφάλματος αν ο διασώστης έχει ήδη 4 αναθέσεις
            echo json_encode(['status' => 'error', 'error' => 'The rescuer already has 4 tasks assigned.']);
        }
    } catch (PDOException $e) {
        // Καταγραφή σφάλματος σε περίπτωση αποτυχίας σύνδεσης με τη βάση δεδομένων
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
    } finally {
        $conn = null; // Κλείσιμο σύνδεσης με τη βάση δεδομένων
    }
} else {   // Επιστροφή JSON με μήνυμα σφάλματος αν η μέθοδος αιτήματος δεν είναι POST
    echo json_encode(['status' => 'error', 'error' => 'Invalid request method']);
}
?>
