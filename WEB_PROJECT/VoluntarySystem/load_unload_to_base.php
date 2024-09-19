<?php
session_start();  // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include('database.php'); //Εισαγωγή του αρχείου για τη σύνδεση με τη βάση δεδομένων.


if (!isset($_SESSION['logged_in2'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit();
}
// Σύνδεση με τη βάση δεδομένων.
$con = mysqli_connect('localhost', 'root', '');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

// Επιλογή της βάσης δεδομένων.
mysqli_select_db($con, "voluntary_system");

// Εκτέλεση του ερωτήματος SQL για την ανάκτηση των αντικειμένων από τον πίνακα `items`.
$sql = "SELECT * FROM items";
$results = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>Φόρτωση/Εκφόρτωση</title>
    <style>
        body {
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: #fff;
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

        button {
            font-size: 16px;
            background-color: #3498db;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 30%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        #unload {
            font-size: 12px;
            background-color: #F28C28;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 30%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #unload:hover {
            background-color: #D27D2D;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        #load {
            font-size: 12px;
            background-color: #097969;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 30%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #load:hover {
            background-color: #088F8F;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>

<h2>Αποθήκη Βάσης</h2>

<a href="map_rescuer.php"><button>Επιστροφή στον χάρτη</button></a>

<!-- Φόρμα για τη φόρτωση αντικειμένων στο όχημα -->
<form id="itemsForm" method="post" action="process_items.php">
<br>
    <table>
        <tr>
            <th>ID</th>
            <th>Κατηγορία</th>
            <th>Αντικείμενο</th>
            <th>Ποσότητα</th>
            <th>Προσθήκη</th>
            <th>Επιλογή</th>
        </tr>
        <?php while($data = $results->fetch_assoc()): ?>
            <tr>  <!-- Εμφάνιση των δεδομένων από τη βάση στον πίνακα -->
                <td><?php echo $data['item_id']; ?></td>
                <td><?php echo $data['item_category']; ?></td>
                <td><?php echo $data['item_stuff']; ?></td>
                <td><?php echo $data['item_quantity']; ?></td>
                <td>  <!-- Πεδίο αριθμού για να εισάγει ο χρήστης την ποσότητα που θα φορτωθεί -->
                    <input type="number" name="quantity[<?php echo $data['item_id']; ?>]" value="0" min="0" max="<?php echo $data['item_quantity']; ?>">
                </td>
                <td>   <!-- Κουμπί επιλογής για την επιλογή του αντικειμένου -->
                    <input type="checkbox" name="select[<?php echo $data['item_id']; ?>]">
                </td>
            </tr>
        <?php endwhile; ?>
    </table>  <!-- Κουμπί υποβολής για τη φόρτωση των αντικειμένων στο όχημα -->
    <br>
    <button id="load" type="submit"><h2>Φόρτωσε στο όχημα τα αντικείμενα</h2></button>
</form>

<!-- Φόρμα για την ξεφόρτωση όλων των αντικειμένων από το όχημα -->
<form id="unloadForm" method="post" action="unload_items.php">
    <br>
    <button id="unload" type="submit" name="unload_all"><h2>Ξεφόρτωσε όλα τα αντικείμενα από το όχημα</h2></button>
</form>


</body>
</html>

<?php
mysqli_close($con);  // Κλείσιμο της σύνδεσης με τη βάση δεδομένων.
?>
