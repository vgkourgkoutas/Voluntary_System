<?php
session_start(); // Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
if (!isset($_SESSION['logged_in1'])) // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
{
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ανακοινώσεις</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: Arial, sans-serif;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #34495e; /* Σκούρο background για το φόρμα */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }

        h3 {
            color: #fff;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
        }

        textarea {
            width: 100%;
            height: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 20px;
            font-size: 16px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #2ecc71;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
        }

        #announcements-container {
            width: 100%;
            max-width: 600px;

        }

        .announcement {
            display: flex;
            align-items: center;
            background-color: #34495e;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #fff
        }

        .announcement button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .announcement button:hover {
            background-color: #c0392b;
        }

        .announcement p {
            margin: 0;
            flex: 1;
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <a href="admin_menu.php">Επιστροφή πίσω στο αρχικό μενού</a> <!-- Σύνδεσμος επιστροφής στο αρχικό μενού -->

    <form method="POST" action="announcement_redirect.php">     <!-- Φόρμα για ανάρτηση ανακοίνωσης -->
        <h3>Ανάρτησε μία Ανακοίνωση</h3>
        <?php if(isset($_GET['error'])) { ?> 
        <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
        <?php } ?> <!-- Εμφάνιση μηνύματος σφάλματος. -->

        <label for="announcement">Κείμενο:</label>  <!-- Ετικέτα και πεδίο κειμένου για την ανακοίνωση -->
        <textarea name="announcement" id="announcement"></textarea>
        <input type="submit" value="Ανάρτηση"> <!-- Κουμπί υποβολής της φόρμας -->
    </form>

    <!-- Ενότητα προβολής των ανακοινώσεων -->
    <div>
        <h3>Ανακοινώσεις</h3>
        <div id="announcements-container"></div>
    </div>

    <script>
        // Συνάρτηση για την ανάκτηση των ανακοινώσεων από την βάση
        function fetchAnnouncements() {
            $.ajax({
                url: 'fetch_anouncements.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Δημιουργούμε το HTML για κάθε ανακοίνωση και υπάρχει και ένα κουμπί για διαγραφή της ανακοίνωσης
                    let announcementsHtml = '';
                    data.forEach(function(ann) {
                        announcementsHtml += `<div class="announcement">
                            <button onclick="deleteAnnouncement(${ann.ann_id})">Διαγραφή</button>
                            <p>${ann.ann_announcement}</p>
                        </div>`;
                    });
                    // Εισάγουμε τις ανακοινώσεις σε πλαίσιο με id announcements-container
                    $('#announcements-container').html(announcementsHtml);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        }

        // Συνάρτηση για τη διαγραφή μίας ανακοίνωσης
        function deleteAnnouncement(announcementId) {
            $.ajax({
                url: 'delete_announcement_ajax.php',
                type: 'POST',
                data: { announcement_id: announcementId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        fetchAnnouncements(); // Ανακτούμε ξανά τις ανακοινώσεις μετά τη διαγραφή
                    } else {
                        console.error('Deletion failed:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        }

       // Ανακτούμε τις ανακοινώσεις όταν φορτώνει η σελίδα
        fetchAnnouncements();

        var errorMessage = $('#error-message');
        if (errorMessage.length > 0) {
            // Εμφανίζουμε το μήνυμα σφάλματος
            errorMessage.show();
            
            // Κρύβουμε το μήνυμα μετά από 3 δευτερόλεπτα (3000 ms)
            setTimeout(function() {
                errorMessage.fadeOut(); // Χρησιμοποιούμε fadeOut για ομαλή απόκρυψη
            }, 3000); // Χρόνος σε ms
        }
    </script>
</body>
</html>
