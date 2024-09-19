<?php
session_start();// Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο
include ('database.php');// php αρχείο με το οποίο γίνεται σύνδεση στην βάση δεδομένων

if (!isset($_SESSION['logged_in1'])) {  // Έλεγχος εάν υπάρχει σύνδεση διαχειριστή 
    header('Location: login_page.php');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή Διασώστη</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(100deg, #2c3e50, #3498db);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .container {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: center;
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            margin: 20px;
            padding: 20px;
        }

        .form-container {
            flex: 1;
            max-width: 400px;
            background-color: linear-gradient(100deg, #2c3e50, #3498db);
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: #fff;
        }

        h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        label {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 15px;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
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

        input[type="submit"]:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            font-weight: bold;
        }

        h4 a {
            color: #D22B2B;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        h4 a:hover {
            color: #C41E3A;
        }

        #map {
            flex: 2;
            width: 100%;
            max-width: 800px;
            height: 600px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>

<body>

    

    <div class="container">
        
        <div class="form-container">
            <h2>Εγγραφή Διασώστη</h2>
            <?php if(isset($_GET['error'])) { ?> 
            <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
            <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->
            <form name="registration_rescuer_form" method="POST" action="registration_rescuer.php">
                <label>Όνομα Χρήστη:
                    <input type="text" name="username" required>
                </label>
                <label>Κωδικός Χρήστη:
                    <input type="password" name="password" id="myInput" required>
                </label>
                <label>Γεωγραφικό Πλάτος:
                    <input type="text" name="latitude" id="latitude" required>
                </label>
                <label>Γεωγραφικό Μήκος:
                    <input type="text" name="longitude" id="longitude" required>
                </label>
                <input type="submit" value="Εγγραφή" name="submit">
            </form>
            <h4><a href="admin_menu.php">Επιστροφή πίσω στο αρχικό μενού</a></h4>
            <h2>Παρακαλώ σύρετε τον κέρσορα του αυτοκινήτου ώστε να λάβουμε την θέση του διασώστη πάνω στον χάρτη.</h2>
        </div>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        
var mapOptions = {
    center: [38.24618, 21.73514], // Κεντρική τοποθεσία του χάρτη (γεωγραφικό πλάτος και μήκος)
    zoom: 17 // Αρχικό επίπεδο ζουμ του χάρτη
}

// Δημιουργία αντικειμένου χάρτη
var map = new L.map('map', mapOptions); // Δημιουργία του χάρτη με τις καθορισμένες επιλογές

// Προσθήκη του OpenStreetMap tile layer στον χάρτη
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map); // Προσθήκη του layer στον χάρτη

// Προσαρμοσμένο εικονίδιο για την βάση
let customIcon = {
    iconUrl: "images/building.png", // URL εικόνας για την βάση
    iconSize: [40, 40] // Διαστάσεις του εικονιδίου (πλάτος x ύψος)
}

// Δημιουργία εικονιδίου με χρήση του customIcon
let myIcon = L.icon(customIcon); 

// Προσαρμοσμένο εικονίδιο για το όχημα
let vehicleCustomIcon = L.icon({
    iconUrl: 'images/car.png', // URL εικόνας για το εικονίδιο του οχήματος
    iconSize: [40, 40], 
    iconAnchor: [20, 20], 
    popupAnchor: [0, -20], 
});

// Προσθήκη μετακινούμενου δείκτη (marker) στον χάρτη για το όχημα
var marker = L.marker([38.24618, 21.73514], { icon: vehicleCustomIcon, draggable: true }).addTo(map); // Τοποθέτηση του marker στην αρχική θέση με δυνατότητα μετακίνησης

// Event listener για το τέλος της μετακίνησης του marker
marker.on('dragend', function (event) {
    var markerPosition = marker.getLatLng(); // Λήψη της νέας θέσης του δείκτη
    var latitude = markerPosition.lat; // Απόκτηση του γεωγραφικού πλάτους
    var longitude = markerPosition.lng; // Απόκτηση του γεωγραφικού μήκους

    // Ενημέρωση των πεδίων εισαγωγής με τις νέες συντεταγμένες
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;
});

// AJAX αίτημα για λήψη δεδομένων βάσης από τον server
$.ajax({
    url: 'fetch_base.php', // URL του αρχείου που επιστρέφει τα δεδομένα
    method: 'GET', 
    dataType: 'json', 
    success: function (data) { 
        if (data.latitude && data.longitude) { // Έλεγχος αν οι συντεταγμένες είναι έγκυρες
            var coordinates = data; 
            var Latitude = coordinates.latitude; 
            var Longitude = coordinates.longitude; 

            // Δημιουργία και τοποθέτηση δείκτη για τη βάση στον χάρτη
            var vash = new L.marker([Latitude, Longitude], { icon: myIcon, draggable: false });
            vash.addTo(map); // Προσθήκη του δείκτη στον χάρτη

            // Δημιουργία και τοποθέτηση κύκλου γύρω από τη βάση
            var circle = L.circle([Latitude, Longitude], {
                color: 'red', 
                fillColor: '#f03', 
                fillOpacity: 0.2, 
                radius: 5000 
            }).addTo(map); 

            map.setView([Latitude, Longitude], 13); // Ρύθμιση της προβολής χάρτη στη θέση της βάσης

        } else {
            console.error('Invalid coordinates received:', data); // Εμφάνιση σφάλματος αν οι συντεταγμένες δεν είναι έγκυρες
        }
    },
    error: function (error) { // Συνάρτηση που εκτελείται όταν το αίτημα αποτυγχάνει
        console.error('Error fetching base data:', error); // Εμφάνιση σφάλματος στο console
    }
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


    </script>
</body>
</html>
