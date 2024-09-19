<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
if (!isset($_SESSION['logged_in1'])) // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
{
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Προσθήκη Κατηγορίας</title>
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
            background-color: #34495e; 
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
</head>

<body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function (){ //διασφαλίζουμε ότι ο κώδικας JavaScript θα εκτελεστεί μόνο αφού έχει φορτωθεί πλήρως το DOM της σελίδας.

    function fetchAllCategories() { // Συνάρτηση η οποία λαμβάνει όλες τις κατηγορίες για εκτύπωση με τη χρήση ajax. 
        $.ajax({
            url: 'fetch_categories.php', // λαμβάνουμε τις κατηγορίες από το αρχείο fetch_categories.php
            type: 'GET',
            dataType: 'json',
            success: function(data) { // Αν η αίτηση είναι επιτυχής, τα δεδομένα θα αποθηκευτούν.
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Έλεγχος αν τα δεδομένα έχουν τη σωστή μορφή.
                    return;
                }
                
                renderAllCategories(data); //Συνάρτηση εμφάνισης των κατηγοριών σε πίνακες
            },
            error: function(xhr, status, error) { // Διαχείριση σφαλμάτων AJAX.
                console.error('AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);
            }
        });
    }

    function renderAllCategories(data) {
        let tableContent = '<tr><th>ID Κατηγορίας</th><th>Όνομα Κατηγορίας</th><th>Ημερομηνία Καταχώρησης</th></tr>';
        $.each(data, function(index, item) { // Χρησιμοποιείται η jQuery $.each για να διατρέξει τις κατηγορίες.
            tableContent += '<tr>';
            tableContent += '<td>' + (item.category_id || 'N/A') + '</td>';
            tableContent += '<td>' + (item.category_items || 'N/A') + '</td>';
            tableContent += '<td>' + (item.category_date_added || 'N/A') + '</td>';
            tableContent += '</tr>';
        });
        $('#allcategories').html(tableContent); // Εισαγωγή των δεδομένων στον πίνακα με ID allcategories.
    }

    fetchAllCategories();  // Εκκίνηση της φόρτωσης κατηγοριών μόλις φορτωθεί η σελίδα.

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
<h2> Προσθήκη Νέας Κατηγορίας </h2>
<a href="storage_management.php">Επιστροφή στο Μενού</a>
<?php if(isset($_GET['error'])) { ?> 
    <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
    <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->
<form method="POST" action="add_cat.php">
    <table>
        <tr>
            <td>Όνομα Κατηγορίας:</td>
            <td><input type="text" name="catname"></td>  <!-- Πεδίο εισαγωγής ονόματος κατηγορίας. -->
        </tr>
    </table>
    <input type="submit" value="Προσθήκη"/> <!-- Κουμπί για υποβολή φόρμας. -->
</form>

<h2>Κατηγορίες</h2>

<table id="allcategories"> <!--Εμφάνιση στοιχείων υπό την μορφή πίνακα-->
    <tr>
        <th></th>
    </tr>
</table>

</body>
</html>
