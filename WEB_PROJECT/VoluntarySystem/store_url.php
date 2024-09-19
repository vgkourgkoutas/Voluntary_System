<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
}

// Ανάκτηση της διαδρομής του αρχείου JSON από την POST αίτηση
$jsonFilePath =  $_POST['urlInput']; // Υποθέτει ότι η URL είναι μέσα στο πεδίο 'urlInput' της POST αίτησης
$jsonFilePath = $_FILES['fileInput']['tmp_name']; // Αντικαθιστά την παραπάνω γραμμή για να χρησιμοποιήσει το προσωρινό όνομα του αρχείου που ανέβηκε
$jsonData = file_get_contents($jsonFilePath); // Ανάγνωση του περιεχομένου του αρχείου JSON

// Έλεγχος αν η ανάγνωση του αρχείου JSON ήταν επιτυχής
if ($jsonData === false) {
    die('Error reading JSON file');
}


// Ανάλυση του JSON δεδομένων σε πίνακα PHP
$data = json_decode($jsonData, true);

// Έλεγχος αν η ανάλυση του JSON ήταν επιτυχής
if ($data === null) {
    die('Error decoding JSON data');
}

// Στοιχεία σύνδεσης με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Εισαγωγή κατηγοριών στη βάση δεδομένων
foreach ($data['categories'] as $category) {
    $categoryName = $category['category_name'];
    // Εισαγωγή κατηγορίας μόνο αν το όνομα δεν είναι κενό
    if (!empty($categoryName)) {
        $sql = "INSERT IGNORE INTO add_category (category_items) VALUES ('$categoryName')";
        // Εκτέλεση του ερωτήματος και έλεγχος αν η εισαγωγή ήταν επιτυχής
        if ($conn->query($sql) === false) {
            echo "Error inserting category: " . $conn->error;
        }
    }
}
// Εισαγωγή αντικειμένων στη βάση δεδομένων
foreach ($data['items'] as $item) {
    
    $itemName = $item['name'];
    $categoryID = $item['category']; // Υποθέτουμε ότι το ID κατηγορίας αποθηκεύεται στο πεδίο 'category'

    // Λήψη του ονόματος της κατηγορίας με βάση το ID της
    $categoryName = getCategoryName($data['categories'], $categoryID);
    // Εισαγωγή αντικειμένου μόνο αν το όνομα του αντικειμένου και η κατηγορία δεν είναι κενά
    if (!empty($itemName) && !empty($categoryName)) {
        $sql = "INSERT IGNORE INTO items (item_category, item_stuff, item_quantity, item_category_id) VALUES ('$categoryName', '$itemName', FLOOR(1 + (RAND() * (50 - 10))), $categoryID)";
        // Εκτέλεση του ερωτήματος και έλεγχος αν η εισαγωγή ήταν επιτυχής
        if ($conn->query($sql) === false) {
            echo "Error inserting item: " . $conn->error;
        }
    }
}

// Συνάρτηση για την απόκτηση του ονόματος της κατηγορίας με βάση το ID της
function getCategoryName($categories, $categoryID) {
    foreach ($categories as $category) {
        if ($category['id'] == $categoryID) {
            return $category['category_name'];
        }
    }
    return ''; // Επιστροφή κενής συμβολοσειράς αν δεν βρεθεί το ID
}

$conn->close(); // Κλείσιμο της σύνδεσης με τη βάση δεδομένων

header("Location: upload_json.php"); // Ανακατεύθυνση στην σελίδα upload_json.php μετά την ολοκλήρωση της επεξεργασίας
?>

