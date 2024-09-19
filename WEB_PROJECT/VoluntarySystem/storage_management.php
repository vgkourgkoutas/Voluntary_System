<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php');

if (!isset($_SESSION['logged_in1'])) //Έλεγχος αν υπάρχει σύνδεση διαχειριστή
{
	header('Location: login_page.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name ="viewport" content = "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
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
                <div class="vertical-center">    <!-- Μενού όπου επιλέγοντας μία συγκεκριμένη λειτουργία ανακατευθύνεται στην συγκεκριμένη σελίδα -->
                        <h3> Βάση Εθελοντικού Συστήματος</h3>
                        <a href="add_category.php"><button>Προσθήκη Κατηγορίας</button></a>
                        <a href="add_item.php"><button>Προσθήκη Αντικειμένου</button></a>
                        <a href="get_json_url.php"><button>Λήψη Αποθετηρίου</button></a>
                        <a href="upload_json.php"><button>Μεταφόρτωση Ανανεωμένου Αποθετηρίου</button></a>
                        <h4><a href="admin_menu.php"><button id="exit">Επιστροφή στο αρχικό μενού</button></a></h4>
                </div>   
        </body>
</html>