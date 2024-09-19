<?php
session_start();// Εκίννηση του session ώστε να μπορούμε να χρησιμοποιούμε $_GET ή $_POST ή κάποια $_SESSION μεταβλητή που χρειαζόμαστε από άλλη συνεδρία σε php αρχείο

if (!isset($_SESSION['logged_in2'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
    exit(); 
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Χάρτης Διασώστη</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>

<body>
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
                    background-color: #811331;
                }

                .container {
                    display: flex;
                    align-items: flex-start;
                    gap: 20px;
                }

                .checkbox-container {
                    display: flex;
                    flex-direction: row;
                    gap: 10px;
                    background-color: rgba(255, 255, 255, 0.1);
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
                }

                .checkbox-container label {
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                }

                .checkbox-container input[type="checkbox"] {
                    margin-right: 10px;
                    width: 20px;
                    height: 20px;
                    accent-color: #2980b9;
                    border-radius: 4px;
                    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
                }

                #map {
                    width: 900px;
                    height: 500px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
                }

                .button-container {
                    display: flex;
                    justify-content: center;
                    gap: 10px;
                }

                #load-unload-container {
                    margin-top: 20px;
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
    </style>
     <div class="checkbox-container"> <!-- Ενότητες επιλογών φίλτρων -->
        <label><input type="checkbox" id="requestsToggle" checked> Εκκρεμή Αιτήματα</label>
        <label><input type="checkbox" id="offersToggle" checked> Εκκρεμείς Προσφορές</label>
        <label><input type="checkbox" id="tasksRequestsToggle" checked>Ληφθέντα Αιτήματα</label>
        <label><input type="checkbox" id="tasksOffersToggle" checked>Ληφθέντες Προσφορές</label>
        <label><input type="checkbox" id="activeVehiclesToggle" checked> Οχήματα με ενεργά tasks</label>
        <label><input type="checkbox" id="inactiveVehiclesToggle" checked> Οχήματα χωρίς ενεργά tasks</label>
        <label><input type="checkbox" id="connectingLinesToggle" checked> Ευθείες γραμμές</label>
        <img src="images/ypomnima.png" width="300" height="200">
    </div>
    <br>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
    // Δημιουργία λιστών για αποθήκευση των markers
    var requestMarkers = [];
    var offersMarkers = [];
    var tasksRequestMarkers = [];
    var tasksOfferMarkers = [];
    var vehicleMarker;
    var vehicleWithTasks = [];
    var vehicleNoTasks = [];
    var connectedLines = [];
    var BaseCoordinates = [];
    var vehicleCoordinates = null;
    var lines = [];

    // Ορισμός των αρχικών ρυθμίσεων του χάρτη.
    var mapOptions = {
        center: [38.24618, 21.73514],
        zoom: 17
    }

    let customIcon = {
        iconUrl: "images/building.png",
        iconSize: [40, 40]
    }

    let myIcon = L.icon(customIcon);

    let vehicleCustomIcon = L.icon({
        iconUrl: 'images/car.png',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    let requestIcon = L.icon({
        iconUrl: "images/request.png",
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    let offerIcon = L.icon({
        iconUrl: "images/offers.png",
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    let assignedRequestIcon = L.icon({
        iconUrl: "images/request_task.png", 
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    let assignedOfferIcon = L.icon({
        iconUrl: "images/offers_task.png", 
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    var map = new L.map('map', mapOptions); // Δημιουργία του χάρτη με τις ρυθμίσεις που καθορίσαμε παραπάνω.

    var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
    map.addLayer(layer);

// Συνάρτηση για τη λήψη των συντεταγμένων της βάσης.
function showBase() {
    return new Promise((resolve, reject) => {
        // Εκτέλεση ενός AJAX αιτήματος για τη λήψη δεδομένων από το fetch_base.php.
        $.ajax({
            url: 'fetch_base.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                 // Έλεγχος αν τα δεδομένα περιέχουν γεωγραφικές συντεταγμένες.
                if (data.latitude && data.longitude) {
                    var coordinates = data; 
                    var Latitude = coordinates.latitude;
                    var Longitude = coordinates.longitude;
                    // Δημιουργία ενός marker στον χάρτη με τις δεδομένες συντεταγμένες της βάσης.
                    var vash = new L.marker([Latitude, Longitude], { icon: myIcon, draggable: false });
                    vash.addTo(map); // Προσθήκη του marker της βάσης στον χάρτη.

                    BaseCoordinates.push(coordinates);
                    // Δημιουργία ενός κύκλου γύρω από την βάση με κόκκινο χρώμα ακτίνας 5 km
                    var circle = L.circle([Latitude, Longitude], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.2,
                        radius: 5000
                    }).addTo(map);

                    var circle = L.circle([Latitude, Longitude], {
                        color: 'green',
                        fillColor: '#f03',
                        fillOpacity: 0.2,
                        radius: 100
                    }).addTo(map);

                    map.setView([Latitude, Longitude], 13);

                    resolve();  // Επίλυση της υπόσχεσης όταν ολοκληρωθεί η λήψη και εμφάνιση των δεδομένων.
                } else {
                    console.error('Invalid coordinates received:', data);
                    reject('Invalid coordinates'); // Απόρριψη της υπόσχεσης σε περίπτωση σφάλματος.
                }
            },
            error: function (error) {
                console.error('Error fetching base data:', error);
                reject(error);
            }
        });
    });
}

 // Συνάρτηση για εμφάνιση του οχήματος
function showVehicle() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'fetch_rescuer_vehicle.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data && data.latitude && data.longitude) {
                    vehicleCoordinates = [data.latitude, data.longitude];  // Αποθήκευση συντεταγμένων

                    vehicleMarker = L.marker(vehicleCoordinates, {
                        icon: vehicleCustomIcon, draggable: true
                    }).addTo(map);

                    vehicleMarker.on('dragend', function (e) { // Επεξεργασία του event dragend του marker
                        var newLatLng = e.target.getLatLng();
                        vehicleCoordinates = [newLatLng.lat, newLatLng.lng]; // Ενημέρωση συντεταγμένων κατά το dragend
                        saveNewVehicleCoordinates(newLatLng.lat, newLatLng.lng);
                        updateCurrentTasks();
                        distanceFromBase(BaseCoordinates);
                        fetchVehicleStorageItems();
                        get_lines();
                    });
                    // Αποθήκευση του marker ανάλογα με την κατάσταση του οχήματος
                    if(data.tasks > 0){
                        vehicleWithTasks.push(vehicleMarker);
                    }
                    else{ vehicleNoTasks.push(vehicleMarker); }

                    resolve(vehicleCoordinates);  // Επίλυση της υπόσχεσης με συντεταγμένες
                } else {
                    reject('Invalid vehicle data received');
                }
            },
            error: function (error) {
                reject(error);   // Απόρριψη της υπόσχεσης σε περίπτωση σφάλματος
            }
        });
    });
}

// Καλούμε τη συνάρτηση showBase() για να φορτώσουμε και να εμφανίσουμε τη θέση της βάσης
showBase().then(() => { // Όταν η θέση της βάσης φορτωθεί επιτυχώς, καλούμε τη συνάρτηση showVehicle() για να φορτώσουμε και να εμφανίσουμε τη θέση του οχήματος
    return showVehicle();
}).then((coordinates) => {  // Όταν η θέση του οχήματος φορτωθεί επιτυχώς, εκτελούμε τις εξής ενέργειες:
    updateCurrentTasks(); // Ενημερώνουμε τα τρέχοντα tasks (αιτήματα και προσφορές) με την κλήση της συνάρτησης updateCurrentTasks()
    distanceFromBase(BaseCoordinates); // Υπολογίζουμε την απόσταση του οχήματος από τη βάση και την εμφανίζουμε με την κλήση της συνάρτησης distanceFromBase()
    fetchVehicleStorageItems(); // Ανακτούμε και εμφανίζουμε τα αντικείμενα αποθήκευσης του οχήματος με την κλήση της συνάρτησης fetchVehicleStorageItems()
}).catch((error) => { // Σε περίπτωση σφάλματος κατά τη φόρτωση της βάσης ή του οχήματος, καταγράφουμε το σφάλμα στο κονσόλα
    console.error('Error occurred while loading base or vehicle:', error);
});
// Συνάρτηση για την αποθήκευση των νέων συντεταγμένων του οχήματος (γεωγραφικό πλάτος και μήκος) στον διακομιστή
    function saveNewVehicleCoordinates(lat, lng) {
        $.ajax({ // Κάνουμε μια AJAX κλήση στον διακομιστή για να ενημερώσουμε τις συντεταγμένες του οχήματος
            url: 'update_vehicle_coordinates.php', // URL του διακομιστή όπου θα σταλούν οι νέες συντεταγμένες
            method: 'POST', // Μέθοδος HTTP που θα χρησιμοποιηθεί για την αποστολή των δεδομένων (POST)
            data: { // Δεδομένα που θα σταλούν στον διακομιστή 
                latitude: lat,
                longitude: lng
            },
            success: function (response) {  // Συνάρτηση που καλείται σε περίπτωση επιτυχούς αποστολής των δεδομένων
                console.log('Coordinates updated successfully:', response); // Καταγράφουμε την επιτυχία της ενημέρωσης των συντεταγμένων στην κονσόλα
            },
            error: function (error) {   // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την αποστολή των δεδομένων
                console.error('Error updating coordinates:', error); // Καταγράφουμε το σφάλμα στην κονσόλα
            }
        });
    }

function fetchRequests(){ // Συνάρτηση για την ανάκτηση αιτημάτων από τον διακομιστή
    $.ajax({ // Κάνουμε μια AJAX κλήση στον διακομιστή για να πάρουμε τα αιτήματα
        url: 'fetch_requests.php', // URL του διακομιστή για την ανάκτηση των αιτημάτων
        method: 'GET',
        dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση του διακομιστή (JSON)
        success: function (data) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς ανάκτησης των δεδομένων
            for (var i = 0; i < data.length; i++) { // Επεξεργαζόμαστε κάθε αίτημα από την απάντηση
                var request = data[i];
                // Μετατρέπουμε τις συντεταγμένες του αιτήματος σε αριθμητικές τιμές
                var Latitude = parseFloat(request.latitude);
                var Longitude = parseFloat(request.longitude);
                // Δημιουργούμε τυχαίες αποκλίσεις για τις συντεταγμένες για να τις τοποθετήσουμε ελαφρώς διαφορετικά
                var offsetLat = (Math.random() - 0.5) * 0.004;
                var offsetLng = (Math.random() - 0.5) * 0.004;
                // Δημιουργούμε ένα marker για το αίτημα στο χάρτη με τις τροποποιημένες συντεταγμένες
                var requestMarker = L.marker([Latitude + offsetLat, Longitude + offsetLng], {
                    icon: requestIcon,
                    data: {
                    citizen_id: request.citizen_id,
                    citizen_name: request.citizen_name,
                    citizen_phone: request.citizen_phone,
                    date_added: request.date_added,
                    item: request.item,
                    citizen_quantity: request.citizen_quantity,
                    state: request.state
                }
                }).addTo(map).bindPopup( // Προσθέτουμε το marker στο χάρτη και δημιουργούμε το περιεχόμενο του pop up
                    `<b>Citizen ID:</b> ${request.citizen_id}<br>` +
                    `<b>Name:</b> ${request.citizen_name}<br>` +
                    `<b>Phone:</b> ${request.citizen_phone}<br>` +
                    `<b>Date Added:</b> ${request.date_added}<br>` +
                    `<b>Item:</b> ${request.item}<br>` +
                    `<b>Quantity:</b> ${request.citizen_quantity}<br>` +
                    `<b>Vehicle Name:</b> NOT ASSIGNED <br>` +
                    `<b>Status:</b> ${request.state} <br>` +
                    `<button onclick="connectWithVehicle(${i}, 'request')">Ανάλαβε Task</button>`
                );
                requestMarker.addTo(map); // Προσθέτουμε το marker στο χάρτη
                requestMarkers.push(requestMarker); // Αποθηκεύουμε το marker στη λίστα των markers των αιτημάτων
            }
            console.log(data.length); // Καταγράφουμε το πλήθος των αιτημάτων στην κονσόλα
        },
        error: function (error) {  // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την ανάκτηση των δεδομένων
            console.error('Error fetching request data:', error); // Καταγράφουμε το σφάλμα στην κονσόλα
        }
    });
}

function fetchOffers(){ // Συνάρτηση για την ανάκτηση προσφορών από τον διακομιστή
    $.ajax({ // Κάνουμε μια AJAX κλήση στον διακομιστή για να πάρουμε τις προσφορές
        url: 'fetch_offers.php', // URL του διακομιστή για την ανάκτηση των προσφορών
        method: 'GET',
        dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση του διακομιστή (JSON)
        success: function (data) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς ανάκτησης των δεδομένων
            for (var i = 0; i < data.length; i++) { 
                var offers = data[i];  // Λαμβάνουμε την τρέχουσα προσφορά
                // Μετατρέπουμε τις συντεταγμένες της προσφοράς σε αριθμητικές τιμές
                var Latitude = parseFloat(offers.latitude);
                var Longitude = parseFloat(offers.longitude);
                // Δημιουργούμε τυχαίες αποκλίσεις για τις συντεταγμένες για να τις τοποθετήσουμε ελαφρώς διαφορετικά
                var offsetLat = (Math.random() - 0.5) * 0.004;
                var offsetLng = (Math.random() - 0.5) * 0.004;
                // Δημιουργούμε ένα marker για την προσφορά στο χάρτη με τις τροποποιημένες συντεταγμένες
                var offersMarker = L.marker([Latitude + offsetLat, Longitude + offsetLng], {
                    icon: offerIcon,
                    data: { // Αποθηκεύουμε επιπλέον δεδομένα με το marker
                    citizen_id: offers.citizen_id,
                    citizen_name: offers.citizen_name,
                    citizen_phone: offers.citizen_phone,
                    date_added: offers.date_added,
                    item: offers.item,
                    citizen_quantity: offers.citizen_quantity,
                    state: offers.state,
                }
                }).addTo(map).bindPopup( // Προσθέτουμε το marker στο χάρτη και δημιουργούμε το περιεχόμενο του pop up 
                    `<b>Citizen ID:</b> ${offers.citizen_id}<br>` +
                    `<b>Name:</b> ${offers.citizen_name}<br>` +
                    `<b>Phone:</b> ${offers.citizen_phone}<br>` +
                    `<b>Date Added:</b> ${offers.date_added}<br>` +
                    `<b>Item:</b> ${offers.item}<br>` +
                    `<b>Quantity:</b> ${offers.citizen_quantity}<br>` +
                    `<b>Vehicle Name:</b> NOT ASSIGNED <br>` +
                    `<b>State:</g> ${offers.state}<br>` +
                    `<button onclick="connectWithVehicle(${i}, 'offer')">Ανάλαβε Task</button>`
                );
                offersMarker.addTo(map); // Προσθέτουμε το marker στο χάρτη
                offersMarkers.push(offersMarker); // Αποθηκεύουμε το marker στη λίστα των markers των προσφορών
            }
            console.log(data.length); // Καταγράφουμε το πλήθος των προσφορών στην κονσόλα
        },
        error: function (error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την ανάκτηση των δεδομένων
            console.error('Error fetching offers data:', error); // Καταγράφουμε το σφάλμα στην κονσόλα
        }
    });
}

function fetchTasks() { // Συνάρτηση για την ανάκτηση των tasks από τον διακομιστή
    $.ajax({ // Κάνουμε μια AJAX κλήση στον διακομιστή για να πάρουμε τα tasks
        url: 'fetch_tasks.php', // URL του διακομιστή για την ανάκτηση των tasks
        method: 'GET', 
        dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση του διακομιστή (JSON)
        success: function (data) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς ανάκτησης των δεδομένων
            if (data.length > 0) { // Ελέγχουμε αν έχουμε tasks στη λίστα
                for (var i = 0; i < data.length; i++) { // Επεξεργαζόμαστε κάθε task από την απάντηση
                    var task = data[i]; // Λαμβάνουμε το τρέχον task
                    // Αποθηκεύουμε τις συντεταγμένες της εργασίας
                    var Latitude = task.task_latitude;
                    var Longitude = task.task_longitude;
                    // Ελέγχουμε τον τύπο του task και δημιουργούμε τον κατάλληλο marker
                    if (task.task_type === 'request') {
                        var taskMarker = L.marker([Latitude, Longitude], {  // Δημιουργούμε marker για task τύπου 'request'
                            icon: assignedRequestIcon,
                            data: {
                                citizen_id: task.citizen_id,
                                citizen_fullname: task.citizen_fullname,
                                citizen_telephone: task.citizen_telephone,
                                offer_request_date_added: task.offer_request_date_added,
                                task_date_received: task.task_date_received,
                                item_stuff: task.item_stuff,
                                item_quantity: task.item_quantity,
                                latitude: task.task_latitude,
                                longitude: task.task_longitude,
                                task_type: task.task_type,
                                vehicle_username: task.vehicle_username
                            }
                        }).addTo(map).bindPopup( // Προσθέτουμε το marker στο χάρτη και δημιουργούμε το περιεχόμενο του pop up
                            `<b>Citizen ID:</b> ${task.citizen_id}<br>` +
                            `<b>Name:</b> ${task.citizen_fullname}<br>` +
                            `<b>Phone:</b> ${task.citizen_telephone}<br>` +
                            `<b>Date Added:</b> ${task.offer_request_date_added}<br>` +
                            `<b>Date Received:</b> ${task.task_date_received}<br>` +
                            `<b>Item:</b> ${task.item_stuff}<br>` +
                            `<b>Quantity:</b> ${task.item_quantity}<br>` +
                            `<b>Vehicle Name:</b> ${task.vehicle_username}<br>` +
                            `<b>Status:</b> Accepted <br>` +
                            `<b>Type:</b> ${task.task_type}<br>`
                        );
                        
                        // Προσθέτουμε το marker στη λίστα markers task τύπου 'request'
                        taskMarker.addTo(map);
                        tasksRequestMarkers.push(taskMarker);
                    } else if (task.task_type === 'offer'){
                        var taskMarker = L.marker([Latitude, Longitude], { // Δημιουργούμε marker για task τύπου 'offer'
                            icon: assignedOfferIcon,
                            data: {
                                citizen_id: task.citizen_id,
                                citizen_fullname: task.citizen_fullname,
                                citizen_telephone: task.citizen_telephone,
                                offer_request_date_added: task.offer_request_date_added,
                                task_date_received: task.task_date_received,
                                item_stuff: task.item_stuff,
                                item_quantity: task.item_quantity,
                                latitude: task.task_latitude,
                                longitude: task.task_longitude,
                                task_type: task.task_type
                            }
                        }).addTo(map).bindPopup( // Προσθέτουμε το marker στο χάρτη και δημιουργούμε το περιεχόμενο του pop up
                            `<b>Citizen ID:</b> ${task.citizen_id}<br>` +
                            `<b>Name:</b> ${task.citizen_fullname}<br>` +
                            `<b>Phone:</b> ${task.citizen_telephone}<br>` +
                            `<b>Date Added:</b> ${task.offer_request_date_added}<br>` +
                            `<b>Date Received:</b> ${task.task_date_received}<br>` +
                            `<b>Item:</b> ${task.item_stuff}<br>` +
                            `<b>Quantity:</b> ${task.item_quantity}<br>` +
                            `<b>Vehicle Name:</b> ${task.vehicle_username}<br>` +
                            `<b>Status:</b> Accepted <br>` +
                            `<b>Type:</b> ${task.task_type}<br>`
                        );
                        // Προσθέτουμε το marker στη λίστα markers tasks τύπου 'offer'
                        taskMarker.addTo(map);
                        tasksOfferMarkers.push(taskMarker);
                    }
                }
            } else {
                console.log('No Tasks found'); // Αν δεν υπάρχουν εργασίες, το καταγράφουμε στην κονσόλα
            }
        },
        error: function (error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την ανάκτηση των δεδομένων
            console.error('Error fetching tasks data:', error);  // Καταγράφουμε το σφάλμα στην κονσόλα
        }
    });
}

function connectWithVehicle(index, type) { // Συνάρτηση για να συνδέσει ένα task με ένα όχημα και να καταχωρήσει το task στην βάση δεδομένων
    if (vehicleCoordinates) { // Ελέγχουμε αν οι συντεταγμένες του οχήματος έχουν αρχικοποιηθεί
        var marker;

        if (type === 'request') { // Επιλέγουμε το κατάλληλο marker ανάλογα με τον τύπο της εργασίας
            marker = requestMarkers[index];
        } else if (type === 'offer') {
            marker = offersMarkers[index];
        }

        var markerLatLng = marker.getLatLng(); // Παίρνουμε τις συντεταγμένες του marker

        // Ετοιμάζουμε τα δεδομένα για την AJAX κλήση
        var data = marker.options.data;

        $.ajax({ // Κάνουμε AJAX κλήση για να αποθηκεύσουμε το task
            url: 'import_to_tasks.php',  
            method: 'POST',
            dataType: 'json', // Τύπος δεδομένων που αναμένεται από την απάντηση του διακομιστή (JSON)
            data: { // Δεδομένα που στέλνονται στον διακομιστή
                type: type,
                data: data,
                vehicle_latitude: vehicleCoordinates[0],
                vehicle_longitude: vehicleCoordinates[1],
                task_latitude: markerLatLng.lat,
                task_longitude: markerLatLng.lng
            },
            success: function (response) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς αποθήκευσης
                if (response.status === 'success') { // Ελέγχουμε αν η αποθήκευση ήταν επιτυχής
                    console.log('Task saved successfully'); // Καταγράφουμε το μήνυμα επιτυχίας στην κονσόλα
                    updateCurrentTasks(); // Ενημερώνουμε τα τρέχοντα tasks
                    fetchVehicleStorageItems();  // Ανακτούμε τα αντικείμενα αποθήκευσης του οχήματος

                    // Επαναφορτώνουμε τους markers
                    refreshMarkers();
                    get_lines();

                } else { // Καταγράφουμε το σφάλμα στην κονσόλα και ενημερώνουμε τον διασώστη ότι δεν μπορεί να αναλάβει περισσότερα από 4 tasks
                    console.error('Error saving task:', response.error);
                    alert('Έχεις αναλάβει ήδη 4 tasks! Δεν μπορείς να αναλάβεις παραπάνω!');
                }
            },
            error: function (error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την αποθήκευση
                console.error('Error saving task:', error);  // Καταγράφουμε το σφάλμα στην κονσόλα
            }
        });
    } else { // Αν οι συντεταγμένες του οχήματος δεν έχουν αρχικοποιηθεί, καταγράφουμε το σφάλμα στην κονσόλα
        console.error('Vehicle coordinates are not initialized yet.');
    }
}



function deleteTask(taskId, citizenId, item, quantity, date_added, type, taskLatitude, taskLongitude) { // Συνάρτηση για τη διαγραφή μιας εργασίας με βάση το ID της
    $.ajax({ // Κάνουμε AJAX κλήση για να διαγράψουμε το task από τον διακομιστή
        url: 'delete_task.php',  // URL του διακομιστή για τη διαγραφή του task
        method: 'POST',
        data: { task_id: taskId,  // Δεδομένα που στέλνονται στον διακομιστή
                citizen_id: citizenId,
                item: item,
                quantity: quantity,
                date_added: date_added,
                type:type,
                task_latitude: taskLatitude,
                task_longitude: taskLongitude
         },
        success: function(response) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς διαγραφής
            $('#task-row-' + taskId).remove(); // Αφαιρούμε τη γραμμή του task από το DOM με βάση το ID της
            alert('Το task διαγράφηκε επιτυχώς.'); // Εμφανίζουμε μήνυμα επιτυχίας στον χρήστη
            refreshMarkers(); // Επαναφορτώνουμε τους markers για να ανανεώσουμε την εμφάνιση τους      
            get_lines(); // Σχεδιάζουμε τις γραμμές προς τα tasks
        },
        error: function(error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά τη διαγραφή
            console.error('Σφάλμα κατά τη διαγραφή του task:', error); // Καταγράφουμε το σφάλμα στην κονσόλα
            alert('Σφάλμα κατά τη διαγραφή του task.'); // Εμφανίζουμε μήνυμα σφάλματος στον χρήστη
        }
    });
}  

function updateCurrentTasks() {
    $.ajax({ // Κάνουμε AJAX κλήση για να πάρουμε τα τρέχοντα tasks από τον διακομιστή
        url: 'fetch_current_tasks.php', // URL του διακομιστή για την ανάκτηση των τρέχοντων tasks
        method: 'GET',
        dataType: 'json', // Μορφή των δεδομένων που αναμένονται από τον διακομιστή (σε μορφή JSON)
        success: function(data) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς ανάκτησης των δεδομένων
            var tasksTable = $('table tr:gt(0)'); // Επιλέγουμε όλες τις γραμμές του πίνακα εκτός από την κεφαλίδα
            tasksTable.remove(); // Αφαιρούμε όλα τα υπάρχοντα task από τον πίνακα

            // Προσθέτουμε τα νέα task στον πίνακα με id tasks
            data.forEach(function(task) {
                // Δημιουργούμε ένα αντικείμενο L.latLng για την τοποθεσία του task
                var taskLatLng = L.latLng(parseFloat(task.task_latitude), parseFloat(task.task_longitude));
                var vehicleLatLng = vehicleMarker.getLatLng(); // Παίρνουμε την τρέχουσα τοποθεσία του οχήματος
                var distance = vehicleLatLng.distanceTo(taskLatLng); // Υπολογίζουμε την απόσταση μεταξύ του οχήματος και του task
               // Δημιουργούμε ένα κουμπί για την ολοκλήρωση της εργασίας, αν η απόσταση είναι μικρότερη από 50 μέτρα
                var completeButton = distance <= 50 ? 
                    `<button style='background-color: blue; color:white;' onclick="completeTask(${task.task_id}, ${task.citizen_id},  '${task.item_stuff}', ${task.item_quantity}, '${task.task_date_received}', '${task.task_type}', ${task.task_latitude}, ${task.task_longitude})">Ολοκλήρωση</button>` : 
                    '<span>Too far to complete</span>';

                $('#tasks').append(  // Προσθέτουμε σε έναν πίνακα με id tasks, λεπτομέρειες του task που έχει αναλάβει ένας διασώστης
                    `<tr id="task-row-${task.task_id}">
                        <td style='text-align: center;'>${task.task_id}</td>
                        <td style='text-align: center;'>${task.citizen_fullname}</td>
                        <td style='text-align: center;'>${task.citizen_telephone}</td>
                        <td style='text-align: center;'>${task.task_date_received}</td>
                        <td style='text-align: center;'>${task.item_stuff}</td>
                        <td style='text-align: center;'>${task.item_quantity}</td>
                        <td style='text-align: center;'>${task.task_type}</td>
                        <td style='text-align: center;'>${completeButton}</td>
                        <td style='text-align: center;'>
                            <button onclick="deleteTask(${task.task_id}, ${task.citizen_id},  '${task.item_stuff}', ${task.item_quantity}, '${task.task_date_received}', '${task.task_type}', ${task.task_latitude}, ${task.task_longitude})">Ακύρωση</button>
                        </td>
                    </tr>`
                ); 
            }); 
            
        },
        error: function(error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την ανάκτηση των δεδομένων
            console.error('Error fetching current tasks:', error); // Καταγράφουμε το σφάλμα στην κονσόλα
        }
    });
}


function fetchVehicleStorageItems() { // Συνάρτηση για την ανάκτηση των στοιχείων αποθήκευσης του οχήματος
        $.ajax({  // Κάνουμε AJAX κλήση για να πάρουμε τα στοιχεία αποθήκευσης του οχήματος από τον διακομιστή ασύγχρονα
            url: 'fetch_vehicles_storage.php', // URL του διακομιστή για την ανάκτηση των στοιχείων αποθήκευσης
            type: 'GET',
            dataType: 'json',  // Μορφή των δεδομένων που αναμένονται από τον διακομιστή (σε μορφή JSON)
            success: function(data) { // Συνάρτηση που καλείται σε περίπτωση επιτυχούς ανάκτησης των δεδομένων
                if (!Array.isArray(data)) { // Ελέγχουμε αν τα δεδομένα είναι πίνακας
                    console.error('Unexpected data format:', data); // Καταγράφουμε σφάλμα αν τα δεδομένα δεν είναι πίνακας
                    return; // Σταματάμε την εκτέλεση της συνάρτησης
                }
                
                renderVehicleStorageTable(data); // Καλούμε τη συνάρτηση για να εμφανίσουμε τα δεδομένα στον πίνακα
                fetchVehicleStorageItems(); // Συνεχής ενημέρωση των δεδομένων μέσω AJAX.
            },
            error: function(xhr, status, error) { // Συνάρτηση που καλείται σε περίπτωση σφάλματος κατά την AJAX κλήση
                console.error('AJAX Error:', status, error);  // Καταγράφουμε σφάλμα AJAX στην κονσόλα
                console.error('Response Text:', xhr.responseText); // Καταγράφουμε το κείμενο του σφάλματος για περαιτέρω διάγνωση του σφάλματος
            }
        });
    }

function renderVehicleStorageTable(data) { // Συνάρτηση για την εμφάνιση των στοιχείων από το απόθεμα του οχήματος σε έναν πίνακα
    let tableContent = '<tr><th>ID Οχήματος</th><th>Αντικείμενο</th><th>Ποσότητα</th></tr>'; // Αρχικοποίηση της HTML περιεχομένου του πίνακα με επικεφαλίδες
    $.each(data, function(index, item) { // Χρήση της συνάρτησης $.each για επανάληψη σε κάθε στοιχείο του πίνακα δεδομένων
        tableContent += '<tr>';
        tableContent += '<td>' + (item.vehicle_rescuer_id || 'N/A') + '</td>';
        tableContent += '<td>' + (item.item_name || 'N/A') + '</td>';
        tableContent += '<td>' + (item.item_quantity || 'N/A') + '</td>';
        tableContent += '</tr>';
    });
    $('#itemsVehicleTable').html(tableContent); // Ενημέρωση του περιεχομένου του πίνακα με την HTML που δημιουργήθηκε
}

function completeTask(taskId, citizenId, item, quantity, date_added, type, taskLatitude, taskLongitude) { // Συνάρτηση για την ολοκλήρωση ενός task
    $.ajax({ // Αποστολή AJAX αιτήματος για την ολοκλήρωση του task
        url: 'complete_task.php', // Διεύθυνση του url που θα επεξεργαστεί την ολοκλήρωση του task
        method: 'POST',
        data: { task_id: taskId,  // Δεδομένα που αποστέλλονται στο server
                citizen_id: citizenId,
                item: item,
                quantity: quantity,
                date_added: date_added,
                type:type,
                task_latitude: taskLatitude,
                task_longitude: taskLongitude
            },
        success: function(response) { // Συνάρτηση που εκτελείται σε περίπτωση επιτυχίας
            updateCurrentTasks(); // Ενημέρωση της λίστας με τα τρέχοντα tasks
            refreshMarkers(); // Ανανέωση των markers στον χάρτη
            fetchVehicleStorageItems(); // Ενημέρωση των στοιχείων αποθήκευσης του οχήματος
            get_lines(); // Ανανεώνει τις γραμμές που ενώνουν τα tasks με τα οχήματα
        },
        error: function(error) {  // Συνάρτηση που εκτελείται σε περίπτωση σφάλματος
            console.error('Error completing task:', error); // Εμφάνιση σφάλματος στο κονσόλα του προγράμματος περιήγησης
            alert('Error completing task.'); // Εμφάνιση μηνύματος λάθους στον χρήστη
        }
    });
}
// Συνάρτηση για να υπολογίσει την απόσταση από τη βάση και να ενημερώσει το UI
function distanceFromBase(base_coordinates) {
    // Απόκτηση του γεωγραφικού πλάτους και μήκους της βάσης
    var base_latitude = base_coordinates[0].latitude;
    var base_longitude = base_coordinates[0].longitude;
    // Δημιουργία αντικειμένου L.latLng για την τοποθεσία της βάσης με τις συντεταγμένες
    var BaseLatLng = L.latLng(parseFloat(base_latitude), parseFloat(base_longitude));
    var vehicleLatLng = vehicleMarker.getLatLng(); // Απόκτηση των συντεταγμένων του οχήματος από τον marker του οχήματος
    var distance = vehicleLatLng.distanceTo(BaseLatLng); // Υπολογισμός της απόστασης μεταξύ του οχήματος και της βάσης

    $('#load-unload-container').empty();  // Καθαρισμός του περιεχομένου του container για φόρτωση/εκφόρτωση

    var loadButton = distance <= 100 ?  // Δημιουργία του κουμπιού, αναλόγως της απόστασης του οχήματος από την βάση
        `<button onclick="handleLoadUnload()">Φόρτωση/Εκφόρτωση</button>` : 
        `<span>Είσαι πολύ μακριά για Φόρτωση/Εκφόρτωση. Απόσταση: ${Math.round(distance)} m </span>`;

    $('#load-unload-container').append(loadButton); // Προσθήκη του κουμπιού στο container
}

function handleLoadUnload() { // Συνάρτηση για τη διαχείριση της διαδικασίας φόρτωσης/εκφόρτωσης
    window.location.href = 'load_unload_to_base.php'; // Ανακατεύθυνση του χρήστη στη σελίδα 'load_unload_to_base.php'
}

function refreshMarkers() { // Συνάρτηση για την ανανέωση όλων των markers στον χάρτη
    requestMarkers.forEach(function(marker) { // Αφαιρεί όλα τα markers που σχετίζονται με requests από τον χάρτη
        map.removeLayer(marker);
    });
    requestMarkers = []; // Καθαρίζει τη λίστα των markers για requests

    offersMarkers.forEach(function(marker) { // Αφαιρεί όλα τα markers που σχετίζονται με offers από τον χάρτη
        map.removeLayer(marker);
    });
    offersMarkers = []; // Καθαρίζει τη λίστα των markers για offers

    tasksRequestMarkers.forEach(function(marker) { // Αφαιρεί όλα τα markers που σχετίζονται με tasks τύπου request από τον χάρτη
        map.removeLayer(marker);
    });
    tasksRequestMarkers = [];  // Καθαρίζει τη λίστα των markers για tasks τύπου request

    tasksOfferMarkers.forEach(function(marker) {  // Αφαιρεί όλα τα markers που σχετίζονται με tasks τύπου offer από τον χάρτη
        map.removeLayer(marker);
    }); 
    tasksOfferMarkers = []; // Καθαρίζει τη λίστα των markers για tasks τύπου offer

    vehicleWithTasks.forEach(function(marker) { // Αφαιρεί όλα τα markers που σχετίζονται με vehicles που έχουν ανατεθεί tasks από τον χάρτη
        map.removeLayer(marker);
    });
    vehicleWithTasks = []; // Καθαρίζει τη λίστα των markers για vehicles με ανατεθειμένα tasks

    vehicleNoTasks.forEach(function(marker) { // Αφαιρεί όλα τα markers που σχετίζονται με vehicles χωρίς ανατεθειμένα tasks από τον χάρτη
        map.removeLayer(marker);
    });
    vehicleNoTasks = []; // Καθαρίζει τη λίστα των markers για vehicles χωρίς ανατεθειμένα tasks

    fetchRequests(); // Επαναφέρει τα markers για requests
    fetchOffers(); // Επαναφέρει τα markers για offers
    fetchTasks();  // Επαναφέρει τα markers για tasks
    showVehicle(); // Επαναφέρει τα markers για το vehicle
}



function toggleMarkers() { // Συνάρτηση για την εναλλαγή εμφάνισης των markers στον χάρτη με βάση τις επιλογές του διασώστη
            // Διαβάζει την κατάσταση του checkbox για visibility των requests
            var requestsVisible = document.getElementById('requestsToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των tasks requests
            var tasksRequestsVisible = document.getElementById('tasksRequestsToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των tasks offers
            var tasksOffersVisible = document.getElementById('tasksOffersToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των offers
            var offersVisible = document.getElementById('offersToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των ενεργών οχημάτων
            var activeVehiclesVisible = document.getElementById('activeVehiclesToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των ανενεργών οχημάτων
            var inactiveVehiclesVisible = document.getElementById('inactiveVehiclesToggle').checked;
            // Διαβάζει την κατάσταση του checkbox για visibility των γραμμών
            var connectedLines = document.getElementById('connectingLinesToggle').checked;

            for (var i = 0; i < requestMarkers.length; i++) {  // Ελέγχει αν τα markers για requests πρέπει να εμφανίζονται ή όχι
                if (requestsVisible) {
                    requestMarkers[i].addTo(map); // Αν είναι ενεργοποιημένο, προσθέτει τα markers για requests στον χάρτη
                } else {
                    map.removeLayer(requestMarkers[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για requests από τον χάρτη
                }
            }

            for (var i = 0; i < offersMarkers.length; i++) { // Ελέγχει αν τα markers για offers πρέπει να εμφανίζονται ή όχι
                if (offersVisible) {
                    offersMarkers[i].addTo(map);  // Αν είναι ενεργοποιημένο, προσθέτει τα markers για offers στον χάρτη
                } else {
                    map.removeLayer(offersMarkers[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για offers από τον χάρτη
                }
            }

            for (var i = 0; i < tasksRequestMarkers.length; i++) { // Ελέγχει αν τα markers για tasks τύπου request πρέπει να εμφανίζονται ή όχι
                if (tasksRequestsVisible) {
                    tasksRequestMarkers[i].addTo(map); // Αν είναι ενεργοποιημένο, προσθέτει τα markers για tasks requests στον χάρτη
                } else {
                    map.removeLayer(tasksRequestMarkers[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για tasks requests από τον χάρτη
                }
            }

            for (var i = 0; i < tasksOfferMarkers.length; i++) { // Ελέγχει αν τα markers για tasks τύπου offer πρέπει να εμφανίζονται ή όχι
                if (tasksOffersVisible) {
                    tasksOfferMarkers[i].addTo(map); // Αν είναι ενεργοποιημένο, προσθέτει τα markers για tasks offers στον χάρτη
                } else {
                    map.removeLayer(tasksOfferMarkers[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για tasks offers από τον χάρτη
                }
            }

            for (var i = 0; i < vehicleWithTasks.length; i++) { // Ελέγχει αν τα markers για οχήματα με αναθετειμένα tasks πρέπει να εμφανίζονται ή όχι
                if (activeVehiclesVisible) {
                    vehicleWithTasks[i].addTo(map); // Αν είναι ενεργοποιημένο, προσθέτει τα markers για ενεργά οχήματα στον χάρτη
                } else {
                    map.removeLayer(vehicleWithTasks[i]);  // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για ενεργά οχήματα από τον χάρτη
                }
            }

            for (var i = 0; i < vehicleNoTasks.length; i++) { // Ελέγχει αν τα markers για οχήματα χωρίς αναθετειμένα tasks πρέπει να εμφανίζονται ή όχι
                if (inactiveVehiclesVisible) {
                    vehicleNoTasks[i].addTo(map);  // Αν είναι ενεργοποιημένο, προσθέτει τα markers για ανενεργά οχήματα στον χάρτη
                } else {
                    map.removeLayer(vehicleNoTasks[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τα markers για ανενεργά οχήματα από τον χάρτη
                }
            }

            for (var i = 0; i < lines.length; i++) { // Ελέγχει αν οι γραμμές πρέπει να εμφανίζονται ή όχι
                if (connectedLines) {
                    lines[i].addTo(map);  // Αν είναι ενεργοποιημένο, προσθέτει τις γραμμές
                } else {
                    map.removeLayer(lines[i]); // Αν δεν είναι ενεργοποιημένο, αφαιρεί τις γραμμές
                }
            }

            
        }

function get_lines(){
    // Πρώτα διαγράφουμε όλες τις παλιές γραμμές
    lines.forEach(function(line) {
        map.removeLayer(line);
    });
    // Καθαρίζουμε τον πίνακα από τις παλιές γραμμές
    lines = [];

    // Ανάκτηση συντεταγμένων με AJAX
    $.ajax({
        url: 'get_task_coordinates.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
        
            data.forEach(function(coord) {

                var vehicleLatitude = coord.vehicle_latitude;
                var vehicleLongitude = coord.vehicle_longitude;

                var taskLatitude = coord.task_latitude;
                var taskLongitude = coord.task_longitude;

                // Αποθήκευση των συντεταγμένων για την δημιουργία polyline γραμμών για την σύνδεση του οχήματος με το task
                var latlngs = [
                    [vehicleLatitude, vehicleLongitude],
                    [taskLatitude, taskLongitude]
                ];

                // Σχεδιάζουμε την γραμμή και την αποθηκεύουμε στον πίνακα lines
                var line = L.polyline(latlngs, {
                        color: 'blue',
                        weight: 2,
                        opacity: 0.6,
                        dashArray: '5, 5'
                    }).addTo(map);
                
                // Αποθήκευση της γραμμής στον πίνακα lines για επεξεργασία στα φίλτρα
                lines.push(line);
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Σφάλμα κατά την ανάκτηση των συντεταγμένων: " + textStatus + " " + errorThrown);
        }
    });
}

   




// Καλεί τις συναρτήσεις για να φορτώσει δεδομένα και να ενημερώσει την προβολή του χάρτη κατά την πρώτη φόρτωση
fetchTasks(); // Φορτώνει τα tasks από τον server και προσθέτει markers για αυτά στον χάρτη
fetchRequests(); // Φορτώνει τα requests από τον server και προσθέτει markers για αυτά στον χάρτη
fetchOffers(); // Φορτώνει τις προσφορές από τον server και προσθέτει markers για αυτές στον χάρτη
get_lines();  // Σχεδιάζει τις γραμμές από το όχημα προς τα tasks

        // Εγκαθιστά event listeners για τους ελέγχους ορατότητας (toggles) του χάρτη
        // Όταν αλλάζει η κατάσταση του toggle για requests, καλείται η συνάρτηση toggleMarkers για να ενημερωθούν οι markers
        document.getElementById('requestsToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για tasks τύπου request, καλείται η συνάρτηση toggleMarkers
        document.getElementById('tasksRequestsToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για tasks τύπου offer, καλείται η συνάρτηση toggleMarkers
        document.getElementById('tasksOffersToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για offers, καλείται η συνάρτηση toggleMarkers
        document.getElementById('offersToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για ενεργά οχήματα, καλείται η συνάρτηση toggleMarkers
        document.getElementById('activeVehiclesToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για ανενεργά οχήματα, καλείται η συνάρτηση toggleMarkers
        document.getElementById('inactiveVehiclesToggle').addEventListener('change', toggleMarkers);
        // Όταν αλλάζει η κατάσταση του toggle για γραμμές σύνδεσης, καλείται η συνάρτηση toggleMarkers
        document.getElementById('connectingLinesToggle').addEventListener('change', toggleMarkers);

</script>
<br>
    <div> <!-- Σύνδεσμος επιστροφής στο μενού του διασώστη -->
        <h3><a href="rescuer_menu.php">Επιστροφή στο Μενού</a></h3>
    </div>
    <!-- Κοντέινερ για το κουμπί φόρτωσης/εκφόρτωσης -->
    <div id="load-unload-container">
        
    </div>
    <h2>Τρέχοντα Tasks</h2>
    <!-- Πίνακας για την εμφάνιση των τρεχόντων tasks -->
    <table id="tasks" border="1"> 
        <tr>
            <th>Task ID</th>
            <th>Όνομα</th>
            <th>Τηλέφωνο</th>
            <th>Ημερομηνία</th>
            <th>Είδος</th>
            <th>Ποσότητα</th>
            <th>Τύπος Task</th>
            <th></th>
            <th></th>
        </tr>
    </table>
    
    <!-- Πίνακας για την εμφάνιση των αντικειμένων που είναι φορτωμένα σε οχήματα -->
    <h2>Αντικείμενα Φορτωμένα στο όχημα μου</h2>
    <table id="itemsVehicleTable">
        <tr>
            <th>ID Οχήματος</th>
            <th>Αντικείμενο</th>
            <th>Ποσότητα</th>
        </tr>
</table>
</body>

</html>
