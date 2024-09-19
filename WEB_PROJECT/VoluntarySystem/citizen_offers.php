<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in3'])) // Έλεγχος εάν υπάρχει σύνδεση πολίτη
{
    header('Location: logout.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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
        .tables-container
        {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .table-wrapper
        {
            margin-bottom: 20px;
            width: 80%;
            max-width: 800px; 
            margin: 20 px;
        }

        table {
        width: 100%;
        border-collapse: collapse;
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

        input[type="number"] {
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

        /* Στυλ για το dropdown menu */
        .custom-dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .custom-dropdown select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            appearance: none;
            background-color: #fff;
            color: #333;
            cursor: pointer;
        }

        .custom-dropdown::after {
            content: '\25BC'; /* Χρησιμοποιούμε ένα Unicode βέλος κάτω */
            position: absolute;
            top: 14px;
            right: 16px;
            pointer-events: none;
            font-size: 14px;
            color: #333;
        }

        .custom-dropdown select:hover {
            border-color: #2980b9;
        }
        
        .cancel-offer {
            background-color: #C41E3A;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .cancel-offer:hover {
            background-color: #9A2A2A;
        }


    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

        <a href="citizen_menu.php">Επιστροφή στο Κεντρικό Μενού</a>

        <form method="POST" action="cit_off.php">
        <?php if(isset($_GET['error'])) { ?> 
        <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
        <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->
            <h1 for="dropdown1">Κάνε την δικιά σου προσφορά:</h1>
            <div class="custom-dropdown">
            <select id="dropdown1" name="item">
                <?php
                //Στοιχεία Βάσης
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "voluntary_system";

                //Δημιουργία σύνδεσης με την βάση
                $conn = new mysqli($servername, $username, $password, $dbname);

                //Έλεγχος σύνδεσης
                if ($conn->connect_error)
                {
                    die("Connection failed: " . $conn->connect_error);
                }

                //Κάλεσμα των δεδομένων από την βάση
                $sql = "SELECT * FROM items";
                $result = $conn->query($sql);

                //Προβολή των αντικειμένων με μενού τύπου "dropdown"
                //Όταν προστεθεί ένα νέο αντικείμενο, ανανεώνεται και η
                //λίστα με τα αντικείμενα
                if ($result->num_rows > 0)
                {
                    while($row = $result->fetch_assoc())
                    {
                        echo "<option value='" . $row["item_stuff"] . "'>" . $row["item_stuff"] . "</option>";
                    }
                }

                $conn->close();
                ?>
            </select>
            </div>
            <input type="number" min="1" name="update_quantity" value="1">
            <input type="submit" value="Υποβολή Προσφοράς"/>
        </form>

    
    <script>
$(document).ready(function() {

    function fetchAnouncements() {
        $.ajax({
            url: 'fetch_anouncements.php', // URL για την ανάκτηση ανακοινώσεων
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderAnouncements(data); // Καλεί τη συνάρτηση για να εμφανίσει τις ανακοινώσεις
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderAnouncements(data) {
        let tableContent = '<tr></tr>'; // Ξεκινάει το περιεχόμενο του πίνακα
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.ann_announcement || 'N/A') + '</td>'; // Εμφανίζει την ανακοίνωση
            tableContent += '</tr>';
        });
        $('#anouncements').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
    }


    function fetchAllOffers() {
        $.ajax({
            url: 'fetch_offers.php', // URL για την ανάκτηση όλων των προσφορών
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderAllOffers(data); // Καλεί τη συνάρτηση για να εμφανίσει όλες τις προσφορές
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderAllOffers(data) {
        let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th></tr>'; 
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.item || 'N/A') + '</td>'; // Εμφανίζει το αντικείμενο
            tableContent += '<td>' + (item.citizen_quantity || 'N/A') + '</td>'; // Εμφανίζει την ποσότητα
            tableContent += '</tr>';
        });
        $('#alloffers').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
    }

    function fetchOffersWithID() {
        $.ajax({
            url: 'fetch_offers_with_id.php', // URL για την ανάκτηση προσφορών με ID
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderOffersWithID(data); // Καλεί τη συνάρτηση για να εμφανίσει τις προσφορές με ID
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderOffersWithID(data) {
    let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Ημερομηνία Καταχώρησης</th><th>Κατάσταση</th><th>Ενέργειες</th></tr>';
    $.each(data, function(index, item) {
        tableContent += '<tr>';
        tableContent += '<td>' + (item.citoff_stuff || 'N/A') + '</td>'; // Εμφανίζει το αντικείμενο
        tableContent += '<td>' + (item.citoff_quantity || 'N/A') + '</td>'; // Εμφανίζει την ποσότητα
        tableContent += '<td>' + (item.citoff_date_added || 'N/A') + '</td>'; // Εμφανίζει την ημερομηνία καταχώρησης
        tableContent += '<td>' + (item.citoff_state || 'N/A') + '</td>'; // Εμφανίζει την κατάσταση
        tableContent += '<td><button class="cancel-offer" data-id="' + item.citoff_id + '">Ακύρωση</button></td>'; // Δημιουργεί το κουμπί ακύρωσης με ID
        tableContent += '</tr>';
    });
    $('#offersWithID').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
}

 // Προσθήκη event listener στο κουμπί ακύρωσης
$('#offersWithID').on('click', '.cancel-offer', function() {
        var offerId = $(this).data('id'); // Παίρνει το ID της προσφοράς από το κουμπί
        $.ajax({
            url: 'cancel_offer.php', // URL για την ακύρωση της προσφοράς
            type: 'POST', // Μέθοδος AJAX request
            data: { id: offerId }, // Στέλνει το ID της προσφοράς
            success: function(response) {
                // Καλεί τις συναρτήσεις για να φορτώσουν τα δεδομένα
                fetchAllOffers();
                fetchOffersWithID(); 
                fetchTasks();
                fetchAllTasks();
                fetchOldOffersWithID();
                fetchAnouncements();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    });

    function fetchTasks() {
        $.ajax({
            url: 'fetch_offer_tasks.php', // URL για την ανάκτηση των καθηκόντων
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderTasks(data); // Καλεί τη συνάρτηση για να εμφανίσει τα καθήκοντα
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderTasks(data) {
        let tableContent = '<tr><th>ID Διασώστη</th><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Καταχώρηση Προσφοράς</th><th>Ανάληψη Προσφοράς</th><th>Κατάσταση</th></tr>';
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.task_rescuer_id || 'N/A') + '</td>'; // Εμφανίζει το ID του διασώστη
            tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>'; // Εμφανίζει το αντικείμενο
            tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>'; // Εμφανίζει την ποσότητα
            tableContent += '<td>' + (item.offer_request_date_added || 'N/A') + '</td>'; // Εμφανίζει την ημερομηνία καταχώρησης προσφοράς
            tableContent += '<td>' + (item.task_date_received || 'N/A') + '</td>'; // Εμφανίζει την ημερομηνία ανάληψης προσφοράς
            tableContent += '<td>' + 'ACCEPTED' + '</td>'; // Εμφανίζει την κατάσταση ως "ACCEPTED"
            tableContent += '</tr>';
        });
        $('#tasks').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
    }

    function fetchAllTasks() {
        $.ajax({
            url: 'fetch_all_tasks_offers.php', // URL για την ανάκτηση όλων των καθηκόντων προσφορών
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderAllTasks(data); // Καλεί τη συνάρτηση για να εμφανίσει όλα τα καθήκοντα
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderAllTasks(data) {
        let tableContent = '<tr><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th></tr>';
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>'; // Εμφανίζει το αντικείμενο
            tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>'; // Εμφανίζει την ποσότητα
            tableContent += '</tr>';
        });
        $('#alltasks').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
    }

    function fetchOldOffersWithID() {
        $.ajax({
            url: 'fetch_old_offers.php', // URL για την ανάκτηση παλαιών προσφορών
            type: 'GET', // Μέθοδος AJAX request
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εμφανίζει σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return;
                }
                
                renderOldOffersWithID(data); // Καλεί τη συνάρτηση για να εμφανίσει τις παλαιές προσφορές με ID
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφανίζει σφάλμα σε περίπτωση αποτυχίας του AJAX request
                console.error('Response Text:', xhr.responseText); // Εμφανίζει το κείμενο της απάντησης
            }
        });
    }

    function renderOldOffersWithID(data) {
        let tableContent = '<tr><th>ID Πολίτη</th><th>Αντικείμενο</th><th>Πλήθος Ατόμων</th><th>Ημερομηνία Ολοκλήρωσης</th></tr>'; 
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.oldoff_citizen_id || 'N/A') + '</td>'; // Εμφανίζει το ID του πολίτη
            tableContent += '<td>' + (item.oldoff_stuff || 'N/A') + '</td>'; // Εμφανίζει το αντικείμενο
            tableContent += '<td>' + (item.oldoff_quantity || 'N/A') + '</td>'; // Εμφανίζει την ποσότητα
            tableContent += '<td>' + (item.oldoff_date_added || 'N/A') + '</td>'; // Εμφανίζει την ημερομηνία ολοκλήρωσης
            tableContent += '</tr>';
        });
        $('#oldoffersWithID').html(tableContent); // Ενημερώνει το περιεχόμενο του πίνακα
    }

    // Καλεί τις συναρτήσεις για να φορτώσουν τα δεδομένα κατά την αρχική φόρτωση της σελίδας
    fetchAllOffers();
    fetchOffersWithID();
    fetchTasks();
    fetchAllTasks();
    fetchOldOffersWithID();
    fetchAnouncements();


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

<h2>Ανακοινώσεις</h2>
<table id="anouncements">
    <tr>
    </tr>
</table>

<h2>Όλες οι προσφορές</h2>
<table id="alloffers">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
    </tr>
</table>

<h2>Όλες οι προσφορές πολιτών που έχουν αναληφθεί</h2>

<table id="alltasks">
        <th></th>
        <th></th>
</table>

<h2>Οι τρέχουσες προσφορές μου</h2>
<table id="offersWithID">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
        <th>Ημερομηνία Καταχώρησης</th>
        <th>Κατάσταση</th>
    </tr>
</table>

<h2>Οι προς διεκπαιρέωση προσφορές μου</h2>
<table id="tasks">
    <tr>
        <th>ID Διασώστη</th>
        <th>Αντικείμενο</th>
        <th>Πλήθος ατόμων</th>
        <th>Καταχώρηση Προσφοράς</th>
        <th>Ανάληψη Προσφοράς</th>
        <th>Κατάσταση</th>
    </tr>
</table>

<h2>Οι παρελθούσες προσφορές μου</h2>
<table id="oldoffersWithID">
    <tr>
        <th>Αντικείμενα</th>
        <th>Πλήθος ατόμων</th>
        <th>Ημερομηνία Καταχώρησης</th>
        <th>Κατάσταση</th>
    </tr>
</table>

</body>
</html>
