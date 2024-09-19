<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php')

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Μεταφόρτωση Αρχείου</title>
    <style>
      body {
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: #fff;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: #fff;
            background-color: #e74c3c;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 15%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        a:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
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

        button {
            font-size: 18px;
            background-color: #3498db;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 20%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        #upload {
            font-size: 18px;
            background-color: #097969;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 20%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #upload:hover {
            background-color: #4F7942;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

        <?php if(isset($_GET['error'])) { ?> 
        <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
        <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->


    <a href="storage_management.php">Επιστροφή στο Μενού</a>

    <form action="store_url.php" method="post" enctype="multipart/form-data">
        <label for="fileInput"><h2>Επιλογή αρχείου:</h2></label>
        <input type="file" name="fileInput" id="fileInput" style="display: none;" onchange="displayFileName()">
        <button type="button" onclick="document.getElementById('fileInput').click()">Άνοιγμα</button>
        <p id="selectedFileName"></p>
        <button id="upload" type="submit">Μεταφόρτωση</button>
    </form>
    <br>
    
    <script>
        function displayFileName()
        {   // Επιλέγει το στοιχείο HTML input με το id 'fileInput'
            var input = document.getElementById('fileInput');
             // Επιλέγει το στοιχείο HTML που θα εμφανίσει το όνομα του αρχείου με το id 'selectedFileName'
            var output = document.getElementById('selectedFileName');
            output.innerText = input.files[0].name; // Ενημερώνει το περιεχόμενο του στοιχείου output με το όνομα του πρώτου επιλεγμένου αρχείου
        }
    </script>
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
                        console.error('Unexpected data format:', data); // Εμφανίζουμε σφάλμα στη κονσόλα αν τα δεδομένα δεν είναι δε μορφή πίνακα.
                        return;
                    }
                    
                    renderAllItems(data); // Καλούμε τη συνάρτηση για την απόδοση των αντικειμένων.
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

    <h2>Αντικείμενα</h2>
    <table id="allitems">
    </table>
</body>
</html>

