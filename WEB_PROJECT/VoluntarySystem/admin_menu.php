<?php
session_start(); //Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
if (!isset($_SESSION['logged_in1'])) //Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
{
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Καλώς ήλθατε</title>
    <style>
       body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .vertical-center {
        margin: 0;
        position: absolute;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        }

        h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        a {
            display: block; 
            width: 100%; 
            margin: 10px 0; 
        }

        button {
            font-size: 18px;
            background-color: #3498db;
            border: none;
            border-radius: 10px;
            color: white;
            padding: 15px 30px;
            width: 100%; 
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        button:active {
            background-color: #1f77b4;
            transform: translateY(1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #exit {
            background-color: #e74c3c;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #exit:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        #exit:active {
            background-color: #a93226;
            transform: translateY(1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
        
        <div class="vertical-center">
        <!-- Μενού διαχειριστή όπου επιλέγοντας μία συγκεκριμένη λειτουργία ανακατευθύνεται στην συγκεκριμένη σελίδα -->
        <h3>Καλώς ήλθες, <?php echo htmlspecialchars($_SESSION['username']); ?> </h3>
        <a href="storage_management.php">
            <button>Διαχείρηση Αποθήκης Βάσης</button>
        </a>
        <a href="map_admin.php">
            <button>Προβολή Χάρτη</button>
        </a>
        <a href="show_storage_state.php">
            <button>Προβολή Κατάστασης Αποθήκης</button>
        </a>
        <a href="show_statistics.php">
            <button>Στατιστικά Εξυπηρέτησης</button>
        </a>
        <a href="admin_registers_rescuers.php">
            <button>Δημιουργία Λογ/σμών Διασωστών</button>
        </a>
        <a href="admin_announcement.php">
            <button>Δημιουργία Ανακοινώσεων</button>
        </a>
        <h4><a href="logout.php">
            <button id="exit">Έξοδος από το Σύστημα</button>
        </a></h4>
        </div>
        
</body>
</html>
