<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php'); // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
if (!isset($_SESSION['logged_in1'])) {
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Διαχείριση Αντικειμένων</title>
    <style>
      body {
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: #fff;
        }

        h2 {
            text-align: center;
            color: #fff;
            font-size: 24px;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: #fff;
            background-color: #3498db;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #2980b9;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #34495e; /* Σκούρο background για το φόρμα */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2ecc71;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Εισάγουμε τη βιβλιοθήκη jQuery για τη χρήση της AJAX και άλλων λειτουργιών. -->
    <script>
    $(document).ready(function () { // Καλούμε τη συνάρτηση μόλις η σελίδα είναι έτοιμη.
        function fetchAllItems() { // Ορίζουμε μια συνάρτηση για να ανακτήσει όλα τα αντικείμενα από τον διακομιστή.
            $.ajax({
                url: 'fetch_items.php', // Ορίζουμε το URL από το οποίο θα γίνει η ανάκτηση δεδομένων.
                type: 'GET', // Ορίζουμε τη μέθοδο HTTP ως GET.
                dataType: 'json', // Ορίζουμε τον τύπο δεδομένων ως JSON.
                success: function(data) { // Καθορίζουμε τι θα κάνουμε αν η AJAX κλήση είναι επιτυχής.
                    if (!Array.isArray(data)) { // Ελέγχουμε αν τα δεδομένα είναι πίνακας.
                        console.error('Unexpected data format:', data); // Εμφανίζουμε σφάλμα στη κονσόλα αν τα δεδομένα δεν είναι σε μορφή πίνακα.
                        return;
                    }
                    
                    renderAllItems(data); // Καλούμε τη συνάρτηση για την εμφάνιση των αντικειμένων.
                    fetchAllItems();
                },
                error: function(xhr, status, error) { 
                    console.error('AJAX Error:', status, error); // Εμφανίζουμε σφάλμα στη κονσόλα με πληροφορίες για το σφάλμα.
                    console.error('Response Text:', xhr.responseText); // Εμφανίζουμε το κείμενο της απάντησης.
                }
            });
        }

        function renderAllItems(data) { // Ορίζουμε μια συνάρτηση για την αποτύπωση των αντικειμένων σε πίνακα.
            let tableContent = '<tr><th>Κατηγορία</th><th>Αντικείμενο</th><th>Ποσότητα</th></tr>'; // Δημιουργούμε τις κεφαλίδες του πίνακα.
            $.each(data, function(index, item) { // Επαναλαμβάνουμε για κάθε στοιχείο του πίνακα δεδομένων.
                tableContent += '<tr>'; 
                tableContent += '<td>' + (item.item_category || 'N/A') + '</td>'; // Προσθέτουμε την κατηγορία του αντικειμένου ή 'N/A' αν δεν υπάρχει.
                tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>'; // Προσθέτουμε το όνομα του αντικειμένου ή 'N/A' αν δεν υπάρχει.
                tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>'; // Προσθέτουμε την ποσότητα του αντικειμένου ή 'N/A' αν δεν υπάρχει.
                tableContent += '</tr>';
            });
            $('#allitems').html(tableContent); // Ενημερώνουμε το περιεχόμενο του πίνακα με τα δεδομένα.
        }

        fetchAllItems(); // Καλουμε τη συνάρτηση για την ανάκτηση των αντικειμένων.

        // Ελέγχει αν υπάρχει το μήνυμα σφάλματος
        var errorMessage = $('#error-message');
        if (errorMessage.length > 0) {
            // Εμφανίζει το μήνυμα
            errorMessage.show();
            
            // Κρύβει το μήνυμα μετά από 3 δευτερόλεπτα (3000 ms)
            setTimeout(function() {
                errorMessage.fadeOut(); // Χρησιμοποιεί fadeOut για ομαλή απόκρυψη
            }, 3000); // Χρόνος σε ms
        }
    });
    </script>
</head>

<body>
    <h2>Προσθήκη Νέου Προϊόντος/Αντικειμένου στην Βάση</h2>
    <a href="storage_management.php">Επιστροφή στο Μενού</a>

    <?php if(isset($_GET['error'])) { ?> 
    <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
    <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->


    <form method="POST" action="add_it.php">
        <label for="dropdown">Κατηγορία Αντικειμένου:</label>
        <select id="dropdown" name="dropdown">
            <?php
            //Στοιχεία Βάσης
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "voluntary_system";

            //Δημιουργία σύνδεσης με την βάση
            $conn = new mysqli($servername, $username, $password, $dbname);

            //Έλεγχος σύνδεσης
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //Κάλεσμα των δεδομένων από την βάση.
            $sql = "SELECT * FROM add_category";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["category_items"] . "'>" . $row["category_items"] . "</option>";
                }
            }
            $conn->close();
            ?>
        </select>
        <br>
        <br>
        <label for="itname">Όνομα Αντικειμένου:</label>
        <input type="text" id="itname" name="itname">

        <input type="submit" value="Υποβολή">
    </form>
    
    <br>

    <form method="POST" action="update_it.php">
        
        <label for="dropdown1">Επίλεξε Αντικείμενο:</label>
        <select id="dropdown1" name="item">
            <?php
            //Κώδικας που χρησιμοποιείται για την τροποποίηση μίας ποσότητας αντικειμένου στην βάση
            //Στοιχεία Βάσης
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "voluntary_system";

            //Δημιουργία σύνδεσης με την βάση
            $conn = new mysqli($servername, $username, $password, $dbname);

            //Έλεγχος σύνδεσης
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            //Κάλεσμα των δεδομένων από την βάση
            $sql = "SELECT * FROM items";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["item_stuff"] . "'>" . $row["item_stuff"] . "</option>";
                }
            }
            $conn->close();
            ?>
        </select>
        <br>
        <br>
        <label for="update_quantity">Ποσότητα:</label>
        <input type="number" id="update_quantity" min="1" name="update_quantity" value="1">
        <br>
        <br>
        <input type="submit" value="Τροποποίηση Ποσότητας">
    </form>

    <h2>Διαθέσιμα Αντικείμενα στη Βάση</h2>
    <table id="item_table"></table>

    <table id="allitems">
    </table>
</body>
</html>
