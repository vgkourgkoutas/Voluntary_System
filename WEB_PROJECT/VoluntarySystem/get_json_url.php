<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο


if (!isset($_SESSION['logged_in1'])) // Έλεγχος αν υπάρχει σύνδεση διαχειριστή
{
     header('Location: logout.php');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Λήψη Αποθετηρίου</title>
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

        input[type="url"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
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
    <!-- Φόρμα για την εισαγωγή του συνδέσμου JSON -->
    <form method="POST" action="upload_json_url.php" id="urlForm">

    <?php if(isset($_GET['error'])) { ?> 
        <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
        <!-- Εμφανίζει μήνυμα λάθους εάν υπάρχει -->
    <?php } ?>

        <h1><label for="urlInput">Εισάγετε τον σύνδεσμο του JSON αρχείου:</label></h1>
        <br>
        <input type="url" name="urlInput">
        <br>
        <button type="submit">Ανάκτηση συνδέσμου</button>   <!-- Κουμπί υποβολής της φόρμας και σύνδεσμος επιστροφής στο μενού -->
    </form>
    <br>
    <a href="storage_management.php"><button id="exit">Επιστροφή στο Μενού</button></a>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Εισάγουμε τη βιβλιοθήκη jQuery για τη χρήση της AJAX και άλλων λειτουργιών. -->
<script>

$(document).ready(function () {
    const myForm = document.getElementById('urlForm');
    // Προσθήκη event listener στη φόρμα για την εκτέλεση κώδικα κατά την υποβολή
    myForm.addEventListener('submit', function (e){

    });

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
    
    
</body> 
</html>
