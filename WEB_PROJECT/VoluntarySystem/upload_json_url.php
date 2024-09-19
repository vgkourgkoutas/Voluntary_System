<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

$url = $_POST['urlInput']; // Λήψη της διεύθυνσης URL του αρχείου JSON από το αίτημα POST

// Έλεγχος αν το πεδίο URL είναι κενό
if (empty($url)) {
    header("Location: get_json_url.php?error=Το πεδίο URL δεν είναι συμπληρωμένο. Παρακαλώ εισάγετε μια έγκυρη διεύθυνση URL."); // Εμφάνιση μηνύματος αν δεν έχει συμπληρωθεί το URL
}

// Ανάγνωση του αρχείου JSON από την καθορισμένη διεύθυνση URL
$jsonData = file_get_contents($url);

// Έλεγχος αν τα δεδομένα JSON φορτώθηκαν με επιτυχία
if ($jsonData === false) {
    die('Error reading JSON file');
}

// Ανάλυση των δεδομένων JSON
$data = json_decode($jsonData, true); // Μετατρέπει τα δεδομένα JSON σε πίνακα PHP

// Έλεγχος αν η ανάλυση JSON ήταν επιτυχής
if ($data === null) {
    die('Error decoding JSON data');
}

// Ορισμός παραμέτρων για τη σύνδεση με τη βάση δεδομένων
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voluntary_system";

// Δημιουργία σύνδεσης με τη βάση δεδομένων
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης με τη βάση δεδομένων
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Εισαγωγή δεδομένων στον πίνακα add_category
foreach ($data['categories'] as $category) {
    $categoryName = $category['category_name']; // Λαμβάνει το όνομα της κατηγορίας από το JSON

    // Εισαγωγή μόνο αν το όνομα της κατηγορίας δεν είναι κενό
    if (!empty($categoryName)) {
        // Ερώτημα SQL για εισαγωγή κατηγορίας (αγνοεί διπλότυπα)
        $sql = "INSERT IGNORE INTO add_category (category_items) VALUES ('$categoryName')";
        if ($conn->query($sql) === false) {
            echo "Error inserting category: " . $conn->error; // Εμφανίζει μήνυμα λάθους αν η εισαγωγή αποτύχει
        }
    }
}

// Εισαγωγή δεδομένων στον πίνακα items
foreach ($data['items'] as $item) {
    $categoryId = $item['category'];
    $itemName = $item['name'];

    // Ανάκτηση του ονόματος της κατηγορίας με βάση το ID
    $categoryName = '';
    foreach ($data['categories'] as $category) {
        if ($category['id'] == $categoryId) {
            $categoryName = $category['category_name'];
            break;
        }
    }

    // Εισαγωγή δεδομένων στον πίνακα items
    $sql = "INSERT IGNORE INTO items (item_category, item_stuff, item_quantity, item_category_id) VALUES ('$categoryName', '$itemName', FLOOR(1 + (RAND() * (50 - 10))), $categoryId)";

    if ($conn->query($sql) === false) {
        echo "Error inserting item: " . $conn->error;
    } else {
        header("Location: get_json_url.php?error=Τα δεδομένα αναρτήθηκαν επιτυχώς!"); // Ανακατεύθυνση αν η εισαγωγή είναι επιτυχής με εμφάνιση μηνύματος
    }
}

// Κλείσιμο σύνδεσης με τη βάση δεδομένων
$conn->close();

echo "Data inserted successfully!"; // Εμφάνιση μηνύματος επιτυχίας
?>
