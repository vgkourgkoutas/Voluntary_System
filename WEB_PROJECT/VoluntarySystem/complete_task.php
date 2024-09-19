<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
// Εμφάνιση σφαλμάτων για debugging κατά τη διάρκεια της ανάπτυξης
ini_set('display_errors', 1); // Ενεργοποίηση εμφάνισης σφαλμάτων
ini_set('display_startup_errors', 1); // Εμφάνιση σφαλμάτων κατά την εκκίνηση του PHP
error_reporting(E_ALL); // Ρύθμιση για εμφάνιση όλων των τύπων σφαλμάτων

include('database.php');
// Έλεγχος αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['logged_in2'])) {
    header('Location: logout.php'); // Ανακατεύθυνση σε σελίδα αποσύνδεσης αν δεν είναι συνδεδεμένος
    exit();
}

// Στοιχεία σύνδεσης με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος της σύνδεσης
if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

try { // Δημιουργία σύνδεσης με τη βάση δεδομένων μέσω PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Ενεργοποίηση αναφορών σφαλμάτων για PDO
    // Λήψη του ονόματος χρήστη του διασώστη από τη συνεδρία
    $rescuer_username = $_SESSION['username'];
    // Ερώτημα για λήψη του ID του διασώστη
    $stmt = $pdo->prepare("SELECT rescuer_id FROM rescuers WHERE rescuer_username = ?");
    $stmt->execute([$rescuer_username]);
    $rescuer = $stmt->fetch(PDO::FETCH_ASSOC);
    // Αποθήκευση του ID του διασώστη
    $rescuer_id = $rescuer['rescuer_id'];
    // Έλεγχος αν το αίτημα είναι POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Λήψη δεδομένων από τη φόρμα POST
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

            if ($type === 'request') { // Επεξεργασία αιτήματος
                // Έλεγχος διαθεσιμότητας αντικειμένου στο όχημα
                $sql_check = "SELECT item_quantity FROM vehicle_storage WHERE vehicle_rescuer_id = :rescuer_id AND item_name = :item";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);
                $stmt_check->bindParam(':item', $item);
                $stmt_check->execute();
                $item_info = $stmt_check->fetch(PDO::FETCH_ASSOC);

                if ($item_info) {
                    $item_quantity = $item_info['item_quantity'];

                    if ($item_quantity >= $quantity) {
                        if ($item_quantity == $quantity) {
                            // Διαγραφή αντικειμένου από το όχημα αν η ποσότητα είναι ακριβής
                            $sql_delete_item = "DELETE FROM vehicle_storage WHERE vehicle_rescuer_id = :rescuer_id AND item_name = :item";
                            $stmt_delete_item = $pdo->prepare($sql_delete_item);
                            $stmt_delete_item->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);
                            $stmt_delete_item->bindParam(':item', $item);
                            
                            if ($stmt_delete_item->execute()) {
                                echo "Request processed and item removed from vehicle_storage.";
                            } else {
                                echo "Error removing item from vehicle_storage.";
                            }
                        } else {
                            // Ενημέρωση ποσότητας του αντικειμένου στο όχημα
                            $sql_update_vehicle = "UPDATE vehicle_storage SET item_quantity = item_quantity - :quantity 
                                                   WHERE vehicle_rescuer_id = :rescuer_id AND item_name = :item";
                            $stmt_update_vehicle = $pdo->prepare($sql_update_vehicle);
                            $stmt_update_vehicle->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);
                            $stmt_update_vehicle->bindParam(':item', $item);
                            $stmt_update_vehicle->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                            
                            if ($stmt_update_vehicle->execute()) {
                                echo "Request processed and item quantity updated.";
                            } else {
                                echo "Error updating vehicle_storage.";
                            }
                        }

                        // Καταγραφή αιτήματος ως ολοκληρωμένο
                        $sql_insert_request = "INSERT INTO old_requests (oldres_citizen_id, oldres_stuff, oldres_people, oldres_date_added)
                                               VALUES (:citizen_id, :item, :quantity, NOW())";
                        $stmt_insert_request = $pdo->prepare($sql_insert_request);
                        $stmt_insert_request->bindParam(':citizen_id', $citizen_id, PDO::PARAM_INT);
                        $stmt_insert_request->bindParam(':item', $item);
                        $stmt_insert_request->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                        if ($stmt_insert_request->execute()) {
                            echo "Old request recorded.";
                        } else {
                            echo "Error inserting into old_requests.";
                        }

                        // Διαγραφή της εργασίας από τον πίνακα current_tasks
                        $sql = "DELETE FROM current_tasks WHERE task_id = :task_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            echo "Task deleted.";

                            // Ενημέρωση του πλήθους εργασιών του οχήματος
                            $sql_update_vehicle = "UPDATE vehicles 
                                                   SET vehicle_tasks = vehicle_tasks - 1 
                                                   WHERE vehicle_rescuer_id = :rescuer_id AND vehicle_tasks > 0";
                            $stmt_update_vehicle = $pdo->prepare($sql_update_vehicle);
                            $stmt_update_vehicle->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);

                            if ($stmt_update_vehicle->execute()) {
                                echo "Vehicle task count updated.";
                            } else {
                                echo "Error updating vehicles table.";
                            }
                            
                            // Διαγραφή από τον πίνακα task_connections των γραμμών
                            $deleteConnectionSql = "DELETE FROM task_connections WHERE rescuer_id = ? AND task_latitude = ? AND task_longitude = ? AND task_type = ?";
                            $stmt = $conn->prepare($deleteConnectionSql);
                            $stmt->bind_param("idds", $rescuer_id, $task_latitude, $task_longitude, $type);
                            $stmt->execute();

                        } else {
                            echo "Error deleting task.";
                        }

                    } else {
                        echo "Insufficient quantity."; // Μήνυμα αν η ποσότητα είναι ανεπαρκής
                    }
                } else {
                    echo "Item not available.";  // Μήνυμα αν το αντικείμενο δεν είναι διαθέσιμο
                }

            } else if ($type === 'offer') { // Επεξεργασία προσφοράς
                // Εύρεση ID αντικειμένου βάσει του ονόματος
                $sql_item = "SELECT item_id FROM items WHERE item_stuff = :item";
                $stmt_item = $pdo->prepare($sql_item);
                $stmt_item->bindParam(':item', $item);
                $stmt_item->execute();
                $id = $stmt_item->fetch(PDO::FETCH_ASSOC);

                if ($id) {
                    $item_id = $id['item_id'];
                    // Εισαγωγή ή ενημέρωση αντικειμένου στην αποθήκευση του οχήματος
                    $sql_vehicle = "INSERT INTO vehicle_storage (vehicle_rescuer_id, item_id, item_name, item_quantity) 
                                    VALUES (:rescuer_id, :item_id, :item, :quantity)
                                    ON DUPLICATE KEY UPDATE item_quantity = item_quantity + :quantity";
                    $stmt_vehicle = $pdo->prepare($sql_vehicle);
                    $stmt_vehicle->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);
                    $stmt_vehicle->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                    $stmt_vehicle->bindParam(':item', $item);
                    $stmt_vehicle->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                    if ($stmt_vehicle->execute()) {
                        echo "Offer processed.";

                        // Καταγραφή της προσφοράς ως ολοκληρωμένη
                        $sql_insert_offer = "INSERT INTO old_offers (oldoff_citizen_id, oldoff_stuff, oldoff_quantity, oldoff_date_added)
                                             VALUES (:citizen_id, :item, :quantity, NOW())";
                        $stmt_insert_offer = $pdo->prepare($sql_insert_offer);
                        $stmt_insert_offer->bindParam(':citizen_id', $citizen_id, PDO::PARAM_INT);
                        $stmt_insert_offer->bindParam(':item', $item);
                        $stmt_insert_offer->bindParam(':quantity', $quantity, PDO::PARAM_INT);

                        if ($stmt_insert_offer->execute()) {
                            echo "Old offer recorded.";
                        } else {
                            echo "Error inserting into old_offers.";
                        }

                        // Διαγραφή της εργασίας από τον πίνακα current_tasks
                        $sql = "DELETE FROM current_tasks WHERE task_id = :task_id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            echo "Task deleted.";

                            // Ενημέρωση του πλήθους εργασιών του οχήματος
                            $sql_update_vehicle = "UPDATE vehicles 
                                                   SET vehicle_tasks = vehicle_tasks - 1 
                                                   WHERE vehicle_rescuer_id = :rescuer_id AND vehicle_tasks > 0";
                            $stmt_update_vehicle = $pdo->prepare($sql_update_vehicle);
                            $stmt_update_vehicle->bindParam(':rescuer_id', $rescuer_id, PDO::PARAM_INT);

                            if ($stmt_update_vehicle->execute()) {
                                echo "Vehicle task count updated.";
                            } else {
                                echo "Error updating vehicles table.";
                            }
                            
                            // Διαγραφή από τον πίνακα task_connections των γραμμών
                            $deleteConnectionSql = "DELETE FROM task_connections WHERE rescuer_id = ? AND task_latitude = ? AND task_longitude = ? AND task_type = ?";
                            $stmt = $conn->prepare($deleteConnectionSql);
                            $stmt->bind_param("idds", $rescuer_id, $task_latitude, $task_longitude, $type);
                            $stmt->execute();
                            


                        } else {
                            echo "Error deleting task.";
                        }

                    } else {
                        echo "Error inserting into vehicle_storage.";
                    }
                } else {
                    echo "Item ID not found for the provided item."; // Μήνυμα αν το ID αντικειμένου δεν βρεθεί
                }

            }

        } else {
            echo "Task ID not submitted."; // Μήνυμα αν δεν έχει υποβληθεί το task_id
        }
    } else {
        echo json_encode(['status' => 'error', 'error' => 'Invalid request method']); // Μήνυμα για μη έγκυρη μέθοδο αίτησης
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage(); // Εμφάνιση σφάλματος σε περίπτωση αποτυχίας σύνδεσης
}
?>
