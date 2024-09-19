<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in1'])) { // Έλεγχος της σύνδεσης αν είναι διαχειριστής
    header('Location: logout.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
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

        .error {
            color: #e74c3c;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let itemsData = []; // Θα περιέχει τα δεδομένα των αντικειμένων
    let vehicleStorageData = []; // Θα περιέχει τα δεδομένα αποθήκης οχημάτων
    let categories = []; // Θα περιέχει τις κατηγορίες αντικειμένων

    // Συνάρτηση για την ανάκτηση δεδομένων αντικειμένων από την βάση δεδομένων
    function fetchItems() {
        $.ajax({
            url: 'fetch_items.php', // URL του αρχείου PHP που επιστρέφει τα δεδομένα των αντικειμένων
            type: 'GET',
            dataType: 'json', // Τύπος δεδομένων που αναμένουμε από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data); // Εάν τα δεδομένα δεν είναι πίνακας, εμφάνιση σφάλματος
                    return;
                }
                
                itemsData = data; // Αποθήκευση των δεδομένων στην μεταβλητή για χρήση παρακάτω
                categories = [...new Set(data.map(item => item.item_category))]; // Δημιουργία πίνακα Set 
                // Η .map() δημιουργεί έναν νέο πίνακα categories 
                // που περιέχει μόνο τις τιμές της ιδιότητας item_category από κάθε αντικείμενο του πίνακα data.
                // Το [...] είναι η σύνταξη του spread operator, η οποία μετατρέπει το Set πίσω σε έναν κανονικό πίνακα (array).
                // Έτσι, το categories γίνεται ένας πίνακας που περιέχει μόνο τις μοναδικές τιμές κατηγοριών από τα δεδομένα.
                renderCategoryCheckboxes(categories);
                renderItemsTable(data);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφάνιση σφάλματος αν αποτύχει το AJAX αίτημα
                console.error('Response Text:', xhr.responseText); // Εμφάνιση του κειμένου της απάντησης
            } 
        });
    }
    // Συνάρτηση για την ανάκτηση δεδομένων αποθήκης οχημάτων
    function fetchVehicleStorageItems() {
        $.ajax({
            url: 'fetch_all_vehicle_storage.php', // URL του αρχείου PHP που επιστρέφει τα δεδομένα αποθήκης οχημάτων
            type: 'GET',
            dataType: 'json', // Τύπος δεδομένων που αναμένουμε από την απάντηση
            success: function(data) {
                if (!Array.isArray(data)) {
                    console.error('Unexpected data format:', data);
                    return;
                }
                
                vehicleStorageData = data; // Αποθήκευση των δεδομένων για μελλοντική χρήση
                renderVehicleStorageTable(data); // Απόδοση του πίνακα αποθήκης οχημάτων
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error); // Εμφάνιση σφάλματος αν αποτύχει το AJAX αίτημα
                console.error('Response Text:', xhr.responseText); // Εμφάνιση του κειμένου της απάντησης
            }
        });
    }
    // Συνάρτηση για την απόδοση των checkboxes για τις κατηγορίες
    function renderCategoryCheckboxes(categories) {
        const filterContainer = $('#categoryFilter');
        filterContainer.empty(); // Εκκαθάριση των φίλτρων

         // Προσθήκη checkboxes για επιλογή και αποεπιλογή όλων των κατηγοριών
        filterContainer.append('<input type="checkbox" id="selectAll" checked>Select All<br>');
        filterContainer.append('<input type="checkbox" id="deselectAll">Deselect All<br><br>');
        // Δημιουργία checkboxes για κάθε κατηγορία
        categories.forEach(category => {
            const checkbox = $('<input>', {
                type: 'checkbox',
                class: 'category-checkbox',
                value: category,
                checked: true // Προεπιλογή: εμφάνιση όλων των κατηγοριών
            });
            filterContainer.append(checkbox).append(category).append('<br>');
        });

        // Σύνδεση των συμβάντων αλλαγής στα checkboxes
        $('#selectAll').change(selectAllCategories);
        $('#deselectAll').change(deselectAllCategories);
        $('.category-checkbox').change(filterTables);
    }
    // Συνάρτηση για την απόδοση του πίνακα αντικειμένων
    function renderItemsTable(data) {
        let tableContent = '<tr><th>Κατηγορία</th><th>Αντικείμενο</th><th>Ποσότητα</th></tr>';
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.item_category || 'N/A') + '</td>';
            tableContent += '<td>' + (item.item_stuff || 'N/A') + '</td>';
            tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>';
            tableContent += '</tr>';
        });
        $('#itemsTable').html(tableContent); // Ενημέρωση του περιεχομένου του πίνακα
    }
    // Συνάρτηση για την απόδοση του πίνακα αποθήκης των οχημάτων
    function renderVehicleStorageTable(data) {
        let tableContent = '<tr><th>ID Οχήματος</th><th>Αντικείμενο</th><th>Ποσότητα</th></tr>';
        $.each(data, function(index, item) {
            tableContent += '<tr>';
            tableContent += '<td>' + (item.vehicle_rescuer_id || 'N/A') + '</td>';
            tableContent += '<td>' + (item.item_name || 'N/A') + '</td>';
            tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>';
            tableContent += '</tr>';
        });
        $('#itemsVehicleTable').html(tableContent);  // Ενημέρωση του περιεχομένου του πίνακα
    }
    // Συνάρτηση για την εφαρμογή φίλτρων στις λίστες
    function filterTables() {
        const selectedCategories = $('.category-checkbox:checked').map(function() {
            return this.value; // Λήψη των επιλεγμένων κατηγοριών
        }).get();

        // Φιλτράρισμα του πίνακα αντικειμένων με βάση τις επιλεγμένες κατηγορίες
        const filteredItemsData = itemsData.filter(item => selectedCategories.includes(item.item_category));
        renderItemsTable(filteredItemsData); // Κλήση της συνάρτησης για εμφάνιση των φιλτραρισμένων items 

        // Φιλτράρισμα του πίνακα αποθήκης οχημάτων με βάση τις επιλεγμένες κατηγορίες
        const filteredVehicleStorageData = vehicleStorageData.filter(item => {
            const itemCategory = itemsData.find(i => i.item_id == item.item_id).item_category;
            return selectedCategories.includes(itemCategory);
        });
        renderVehicleStorageTable(filteredVehicleStorageData); // Εμφάνιση μόνο των φιλτραρισμένων αντικειμένων με βάση τις κατηγορίες
    }
    // Συνάρτηση για την επιλογή όλων των κατηγοριών
    function selectAllCategories() {
        if ($('#selectAll').is(':checked')) {
            $('.category-checkbox').prop('checked', true); // Επιλογή όλων των checkboxes
            $('#deselectAll').prop('checked', false);
            renderItemsTable(itemsData); // Εμφάνιση όλων των αντικειμένων
            renderVehicleStorageTable(vehicleStorageData); // Εμφάνιση όλων των αντικειμένων αποθήκης οχημάτων
        }
    }
    // Συνάρτηση για την αποεπιλογή όλων των κατηγοριών
    function deselectAllCategories() {
        if ($('#deselectAll').is(':checked')) {
            $('.category-checkbox').prop('checked', false); // Αποεπιλογή όλων των checkboxes
            $('#selectAll').prop('checked', false);
            renderItemsTable([]); // Εμφάνιση κανενός αντικειμένου
            renderVehicleStorageTable([]); // Εμφάνιση κανενός αντικειμένου αποθήκης οχημάτων
        }
    }

    fetchItems(); // Κλήση συνάρτησης για την ανάκτηση αντικειμένων κατά την αρχική φόρτωση της σελίδας
    fetchVehicleStorageItems(); // Κλήση συνάρτησης για την ανάκτηση αποθήκης οχημάτων κατά την αρχική φόρτωση της σελίδας
});
</script>
</head>

<body>

<h2>Προβολή Κατάστασης Αποθήκης</h2>
<a href="admin_menu.php">Επιστροφή στο Μενού</a>

<h2>Φίλτρο Κατηγοριών</h2>
<div id="categoryFilter" class="filter-container">
    
</div>

<h2>Διαθέσιμα Αντικείμενα στη Βάση</h2>
<table id="itemsTable">
    <tr>
        <th>Κατηγορία</th>
        <th>Αντικείμενο</th>
        <th>Ποσότητα</th>
    </tr>
    
</table>

<h2>Αντικείμενα Φορτωμένα σε οχήματα</h2>
<table id="itemsVehicleTable">
    <tr>
        <th>ID Οχήματος</th>
        <th>Αντικείμενο</th>
        <th>Ποσότητα</th>
    </tr>
    
</table>

</body>
</html>
