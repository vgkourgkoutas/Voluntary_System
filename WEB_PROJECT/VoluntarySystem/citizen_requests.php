<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in3'])) // Έλεγχος εάν υπάρχει σύνδεση πολίτη
{
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αιτήματα Πολιτών</title>
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
            background-color: #C41E3A;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #9A2A2A;
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

        .error {
            color: #e74c3c;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }

       /* Για το πεδίο autocomplete */
        input[type="text"]#autocomplete-input {
            width: 95%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            border: 2px solid #2980b9;
            border-radius: 4px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        input[type="text"]#autocomplete-input:focus {
            border-color: #3498db;
            outline: none;
        }

        /* Για το dropdown του autocomplete */
        #autocomplete-list {
            width: 99%;
            border: 2px solid #2980b9;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background-color: #fff;
            color: #2c3e50;
            border-radius: 0 0 4px 4px;
        }

        #autocomplete-list div {
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #autocomplete-list div:hover {
            background-color: #f0f0f0;
        }

        /* Για το κουμπί υποβολής */
        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            padding: 12px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2ecc71;
        }

        #quantity {
            width: 99%;
            border: 2px solid #2980b9;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background-color: #fff;
            color: #2c3e50;
            border-radius: 0 0 4px 4px;
        }

    </style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>
<body>

    <h2>Αιτήματα Πολιτών</h2>
     <!-- Σύνδεσμος για επιστροφή στο κεντρικό μενού του πολίτη-->
     <a href="citizen_menu.php">Επιστροφή στο Κεντρικό Μενού</a>


     <?php if(isset($_GET['error'])) { ?> 
        <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
        <!-- Εμφανίζει μήνυμα λάθους εάν υπάρχει -->
    <?php } ?> 

<!-- Φόρμα για αναζήτηση αντικειμένων -->
<form method="POST" action="cit_res.php">
        <h2> Αναζήτηση του επιθυμητού αντικειμένου: </h2>
        <!-- Πεδίο κειμένου για αναζήτηση αντικειμένων με αυτόματη συμπλήρωση -->
        <input type="text" id="autocomplete-input" name="autocomplete-input" oninput="fetchData()">
        
        <div id="autocomplete-list"></div>

     
     <script>
        // Συνάρτηση που χρησιμοποιείται για το autocomplete πεδίο για την αναζήτηση ενός αντικειμένου από την βάση
            function fetchData() {
                // Λήψη της τιμής από το πεδίο κειμένου της αναζήτησης
                var input = document.getElementById('autocomplete-input').value;

                // Δημιουργία νέας αίτησης AJAX
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    // Έλεγχος αν η αίτηση είναι επιτυχής
                    if (this.readyState == 4 && this.status == 200) {
                        var data = JSON.parse(this.responseText); // Ανάλυση της απόκρισης JSON
                        displayAutocomplete(data); // Εμφάνιση των αποτελεσμάτων της αναζήτησης
                    }
                };
                // Αποστολή της αίτησης GET στο PHP script με την τιμή της αναζήτησης ως παράμετρο
                xhr.open("GET", "select_items.php?q=" + input, true);
                xhr.send();
            } 

            function displayAutocomplete(data) {
                var list = document.getElementById('autocomplete-list'); // Επιλογή του στοιχείου που θα περιέχει τα αποτελέσματα
                list.innerHTML = ''; // Καθαρισμός προηγούμενων αποτελεσμάτων

                // Δημιουργία και εμφάνιση στοιχείων αυτόματης συμπλήρωσης
                data.forEach(function (item) {
                    var option = document.createElement('div'); // Δημιουργία νέου στοιχείου <div>
                    option.innerHTML = item; // Ορισμός του περιεχομένου του στοιχείου
                    // Προσθήκη event listener για την επιλογή του στοιχείου
                    option.addEventListener('click', function () {
                        document.getElementById('autocomplete-input').value = item; // Ορισμός της τιμής του πεδίου κειμένου
                        list.innerHTML = ''; // Καθαρισμός των αποτελεσμάτων
                    });
                    list.appendChild(option); // Προσθήκη του στοιχείου στη λίστα
                });
            }
            </script>
        <br>
        <label for="quantity">Αριθμός ατόμων:</label>
        <!-- Πεδίο εισαγωγής αριθμού με προεπιλεγμένη τιμή 1 και ελάχιστο 1 -->
        <input type="number" id="quantity" name="quantity" value="1" min="1">
        <br>
        <!-- Κουμπί υποβολής της φόρμας αιήματος-->
        <input type="submit" value="Υποβολή Αιτήματος">
    </form>
<body>
<script>
$(document).ready(function() {
            // Συνάρτηση για την ανάκτηση όλων των αιτημάτων
            function fetchAllRequests() {
                $.ajax({
                    url: 'fetch_requests.php', // Διεύθυνση του PHP script που θα επεξεργαστεί την αίτηση
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(data) {
                        if (!Array.isArray(data)) { // Έλεγχος αν τα δεδομένα είναι σε μορφή πίνακα
                            console.error('Unexpected data format:', data); // Εμφάνιση σφάλματος εάν η μορφή των δεδομένων δεν είναι η αναμενόμενη
                            return;
                        }
                        
                        renderAllRequests(data); // Εμφάνιση όλων των αιτημάτων
                    },
                    error: function(xhr, status, error) {
                        // Εμφάνιση σφάλματος εάν η αίτηση αποτύχει
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                    }
            });
    }

    function renderAllRequests(data) {
                let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th></tr>'; 
                $.each(data, function(index, item) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + (item.item || 'N/A') + '</td>'; // Επιλογή αντικειμένου
                    tableContent += '<td>' + (item.citizen_quantity || 'N/A') + '</td>'; // Επιλογή ποσότητας ατόμων
                    tableContent += '</tr>';
                });
                $('#allrequests').html(tableContent); // Εισαγωγή του περιεχομένου στον πίνακα με id="allrequests"
            }

    // Συνάρτηση για την ανάκτηση αιτημάτων με ID
    function fetchRequestsWithID() {
                $.ajax({
                    url: 'fetch_requests_with_id.php', // Διεύθυνση του PHP script για αιτήματα με ID
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(data) {
                        if (!Array.isArray(data)) { // Έλεγχος αν τα δεδομένα είναι σε μορφή πίνακα
                            console.error('Unexpected data format:', data); // Εμφάνιση σφάλματος εάν η μορφή των δεδομένων δεν είναι αναμενόμενη
                            return;
                        }
                        
                        renderRequestsWithID(data); // Εμφάνιση αιτημάτων με ID
                    },
                    error: function(xhr, status, error) {
                        // Εμφάνιση σφάλματος εάν η αίτηση αποτύχει
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                    }
                });
            }
     // Συνάρτηση για την εμφάνιση αιτημάτων με ID σε πίνακα
     function renderRequestsWithID(data) {
                let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Ημερομηνία Καταχώρησης</th><th>Κατάσταση</th></tr>'; 
                $.each(data, function(index, item) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + (item.citres_stuff || 'N/A') + '</td>'; // Προσθήκη δεδομένων αντικειμένου
                    tableContent += '<td>' + (item.citres_people || 'N/A') + '</td>'; // Προσθήκη δεδομένων ποσότητας ατόμων
                    tableContent += '<td>' + (item.citres_date_added || 'N/A') + '</td>'; // Προσθήκη ημερομηνίας καταχώρησης
                    tableContent += '<td>' + (item.citres_state || 'N/A') + '</td>'; // Προσθήκη κατάστασης
                    tableContent += '</tr>';
                });
                $('#requestsWithID').html(tableContent); // Εισαγωγή του περιεχομένου στον πίνακα με id="requestsWithID"
            }
    // Συνάρτηση για την ανάκτηση tasks
    function fetchTasks() {
                $.ajax({
                    url: 'fetch_request_tasks.php', // Διεύθυνση του PHP script για εργασίες
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(data) {
                        if (!Array.isArray(data)) { // Έλεγχος αν τα δεδομένα είναι σε μορφή πίνακα
                            console.error('Unexpected data format:', data); // Εμφάνιση σφάλματος εάν η μορφή των δεδομένων δεν είναι αναμενόμενη
                            return;
                        }
                        
                        renderTasks(data); // Εμφάνιση των εργασιών
                    },
                    error: function(xhr, status, error) {
                        // Εμφάνιση σφάλματος εάν η αίτηση αποτύχει
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                    }
                });
            }

     // Συνάρτηση για την εμφάνιση των tasks σε πίνακα
     function renderTasks(data) {
                let tableContent = '<tr><th>ID Διασώστη</th><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Καταχώρηση Αιτήματος</th><th>Ανάληψη Αιτήματος</th><th>Κατάσταση</th></tr>';
                $.each(data, function(index, item) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + (item.task_rescuer_id || 'N/A') + '</td>'; // Προσθήκη ID διασώστη
                    tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>'; // Προσθήκη αντικειμένου
                    tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>'; // Προσθήκη ποσότητας ατόμων
                    tableContent += '<td>' + (item.offer_request_date_added || 'N/A') + '</td>'; // Προσθήκη ημερομηνίας καταχώρησης αιτήματος
                    tableContent += '<td>' + (item.task_date_received || 'N/A') + '</td>'; // Προσθήκη ημερομηνίας ανάληψης αιτήματος
                    tableContent += '<td>' + 'ACCEPTED' + '</td>'; // Προσθήκη κατάστασης αποδοχή
                    tableContent += '</tr>';
                });
                $('#tasks').html(tableContent); // Εισαγωγή του περιεχομένου στον πίνακα με id="tasks"
            }

    // Συνάρτηση για την ανάκτηση όλων των tasks
    function fetchAllTasks() {
                $.ajax({
                    url: 'fetch_all_tasks_requests.php', // Διεύθυνση του PHP script για όλες τις εργασίες
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(data) {
                        if (!Array.isArray(data)) { // Έλεγχος αν τα δεδομένα είναι σε μορφή πίνακα
                            console.error('Unexpected data format:', data); // ΕΕμφάνιση σφάλματος εάν η μορφή των δεδομένων δεν είναι αναμενόμενη
                            return;
                        }
                        
                        renderAllTasks(data); // Εμφάνιση όλων των εργασιών
                    },
                    error: function(xhr, status, error) {
                        // Εμφάνιση σφάλματος εάν η αίτηση αποτύχει
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                    }
                });
            }

    // Συνάρτηση για την εμφάνιση όλων των tasks σε πίνακα
    function renderAllTasks(data) {
                let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th></tr>'; 
                $.each(data, function(index, item) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>'; // Προσθήκη αντικειμένου
                    tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>'; // Προσθήκη ποσότητας ατόμων
                    tableContent += '</tr>';
                });
                $('#alltasks').html(tableContent); // Εισαγωγή του περιεχομένου στον πίνακα με id="alltasks"
            }

     // Συνάρτηση για την ανάκτηση παλαιών αιτημάτων με ID
     function fetchOldRequestsWithID() {
                $.ajax({
                    url: 'fetch_old_requests.php', // Διεύθυνση του PHP script για παλαιά αιτήματα
                    type: 'GET', 
                    dataType: 'json', 
                    success: function(data) {
                        if (!Array.isArray(data)) { // Έλεγχος αν τα δεδομένα είναι σε μορφή πίνακα
                            console.error('Unexpected data format:', data); // Εμφάνιση σφάλματος εάν η μορφή των δεδομένων δεν είναι αναμενόμενη
                            return;
                        }
                        
                        renderOldRequestsWithID(data); // Εμφάνιση παλαιών αιτημάτων με ID
                    },
                    error: function(xhr, status, error) {
                        // Εμφάνιση σφάλματος εάν η αίτηση αποτύχει
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                    }
                });
            }

    // Συνάρτηση για την εμφάνιση παλαιών αιτημάτων με ID σε πίνακα
    function renderOldRequestsWithID(data) {
                let tableContent = '<tr><th>ID Πολίτη</th><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Ημερομηνία Ολοκλήρωσης</th></tr>'; 
                $.each(data, function(index, item) {
                    tableContent += '<tr>';
                    tableContent += '<td>' + (item.oldres_citizen_id || 'N/A') + '</td>'; // Προσθήκη ID πολίτη
                    tableContent += '<td>' + (item.oldres_stuff || 'N/A') + '</td>'; // Προσθήκη αντικειμένου
                    tableContent += '<td>' + (item.oldres_people || 'N/A') + '</td>'; // Προσθήκη ποσότητας ατόμων
                    tableContent += '<td>' + (item.oldres_date_added || 'N/A') + '</td>'; // Προσθήκη ημερομηνίας ολοκλήρωσης
                    tableContent += '</tr>';
                });
                $('#oldrequestsWithID').html(tableContent); // Εισαγωγή του περιεχομένου στον πίνακα με id="oldrequestsWithID"
            }
    // Κλήση των συναρτήσεων για την ανάκτηση δεδομένων όταν η σελίδα φορτωθεί
    fetchAllRequests();
    fetchRequestsWithID();
    fetchTasks();
    fetchAllTasks();
    fetchOldRequestsWithID();

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

<h2>Όλα τα αιτήματα</h2>
<table id="allrequests">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
    </tr>
</table>

<h2>Aιτήματα πολιτών που έχουν αναληφθεί</h2>
<table id="alltasks">
<tr>
        <th></th>
        <th></th>
 </tr>
</table>

<h2>Τα τρέχοντα αιτήματά μου</h2>
<table id="requestsWithID">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
        <th>Ημερομηνία Καταχώρησης</th>
        <th>Κατάσταση</th>
    </tr>
</table>

<h2>Τα προς διεκπαιρέωση αιτήματά μου</h2>
<table id="tasks">
    <tr>
        <th>ID Διασώστη</th>
        <th>Αντικείμενο</th>
        <th>Πλήθος ατόμων</th>
        <th>Καταχώρηση Αιτήμαατος</th>
        <th>Ανάληψη Αιτήματος</th>
        <th>Κατάσταση</th>
    </tr>
</table>

<h2>Τα παρελθόνα αιτήματά μου</h2>
<table id="oldrequestsWithID">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
        <th>Ημερομηνία Καταχώρησης</th>
        <th>Κατάσταση</th>
    </tr>
</table>

</body>
</html>
