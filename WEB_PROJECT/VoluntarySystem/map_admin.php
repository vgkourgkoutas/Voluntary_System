<?php
session_start();

if (!isset($_SESSION['logged_in']))
{
	header('Location: logout.php');
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Χάρτης Διαχειριστή</title>
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
                    flex-direction: column;
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
                    height: 600px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
                }

   
    </style>
    <div class="container">
        <div class="checkbox-container">
            <label><input type="checkbox" id="requestsToggle" checked> Εκκρεμή Αιτήματα</label>
            <label><input type="checkbox" id="offersToggle" checked> Εκκρεμείς Προσφορές</label>
            <label><input type="checkbox" id="tasksRequestsToggle" checked> Ληφθέντα Αιτήματα</label>
            <label><input type="checkbox" id="tasksOffersToggle" checked> Ληφθέντες Προσφορές</label>
            <label><input type="checkbox" id="activeVehiclesToggle" checked> Οχήματα με ενεργά tasks</label>
            <label><input type="checkbox" id="inactiveVehiclesToggle" checked> Οχήματα χωρίς ενεργά tasks</label>
            <label><input type="checkbox" id="connectingLinesToggle" checked> Ευθείες γραμμές</label>

            <img src="images/ypomnima.png" width="300" height="400">

        </div>
        <div id="map"></div>
    </div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        var vehicleMarkers = []; // Αποθήκευση των markers οχημάτων
        var requestMarkers = []; // Αποθήκευση των markers αιτημάτων
        var offersMarkers = []; // Αποθήκευση των markers προσφορών
        var tasksRequestMarkers = []; // Αποθήκευση των markers ληφθέντων αιτημάτων
        var tasksOffersMarkers = []; // Αποθήκευση των markers ληφθέντων προσφορών
        var vehicleWithTasks = []; // Αποθήκευση των markers αυτοκινήτων που έχουν αναλάβει tasks
        var vehicleNoTasks = []; // Αποθήκευση των markers αυτοκινήτων που δεν έχουν αναλάβει tasks
        var lines = []; // Αποθήκευση των γραμμών για αξιοποίηση τους στα φίλτρα

        var baseMarkerCoords = { lat: 38.24618, lng: 21.73514 }; // Αρχικές συντεταγμένες για την βάση πριν την αποθήκευση καινούριων

        var mapOptions = {
            // Κεντράρισμα του χάρτη στον marker   
            center: [38.24618, 21.73514],
            zoom: 17
        }

        // Εικόνα για τον marker της βάσης
        let customIcon = {
            iconUrl:"images/building.png",
            iconSize:[40,40]
        }

        // Δημιουργία της εικόνας με το custom icon
        let myIcon = L.icon(customIcon);

        // Εικόνα για τους markers των αυτοκινήτων
        let vehicleCustomIcon = L.icon({
            iconUrl: 'images/car.png',
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20],
        });

        // Εικόνα για τους markers των requests
        let requestIcon = L.icon({
        iconUrl: "images/request.png",
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
        });

        // Εικόνα για τους markers των offers
        let offerIcon = L.icon({
            iconUrl: "images/offers.png",
            iconSize: [60, 60],
            iconAnchor: [20, 20],
            popupAnchor: [0, -20],
        });

        // Εικόνα για τους markers των requests που έχουν αναληφθεί
        let assignedRequestIcon = L.icon({
        iconUrl: "images/request_task.png", // νέα εικόνα για αναληφθέντα αιτήματα
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

    // Εικόνα για τους markers των offers που έχουν αναληφθεί
    let assignedOfferIcon = L.icon({
        iconUrl: "images/offers_task.png", // νέα εικόνα για αναληφθείσες προσφορές
        iconSize: [60, 60],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });

        var map = new L.map('map', mapOptions);// Δημιουργία χάρτη

        var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'); // Προσθήκη layer στον χάρτη
        map.addLayer(layer);

        // Επαναφορά των τελευταίων γνωστών συντεταγμένων από το localStorage ή χρήση των προκαθορισμένων
        var savedCoords = JSON.parse(localStorage.getItem('baseMarkerCoords')) || baseMarkerCoords;

        // Κεντράρισμα του χάρτη στις συντεταγμένες της βάσης
        map.setView(savedCoords, 13);

        // Δημιουργία marker για τη βάση (όνομα μεταβλητής vash)
        var vash = new L.marker(savedCoords,{icon:myIcon,draggable: true}); 
        
        // Προσθήκη του marker της βάσης στον χάρτη
        vash.addTo(map); 

        // Δημιουργία και προσθήκη κύκλου γύρω από τη βάση με ακτίνα 5km 
        var circle = L.circle(savedCoords, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: 5000
        }).addTo(map);

        // Εμφάνιση των θέσεων των οχημάτων στον χάρτη
        showVehiclePositions();

        
// Ενημέρωση των θέσων των οχημάτων σε τυχαία θέση στον χάρτη εντός 5 km αν  μετατοπισθεί η βάση
function updateVehiclePositions(coords) {
    $.ajax({
        url: 'fetch_vehicles.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            for (var i = 0; i < data.length; i++) {
                var vehicle = data[i];

                var radius = 5000;
                var randomAngle = Math.random() * 2 * Math.PI;
                var randomRadius = Math.sqrt(Math.random()) * radius;
                var randomLatitudeOffset = (randomRadius * Math.cos(randomAngle)) / 111300;
                var randomLongitudeOffset = (randomRadius * Math.sin(randomAngle)) / (111300 * Math.cos(38.24618));

                var randomLatitude = coords.lat + randomLatitudeOffset;
                var randomLongitude = coords.lng + randomLongitudeOffset;

                var vehicleMarker = L.marker([randomLatitude, randomLongitude], {
                    icon: vehicleCustomIcon,
                }).addTo(map).bindPopup(
                    `<b>Name:</b> ${vehicle.name}<br>` +
                    `<b>Tasks:</b> ${vehicle.tasks}<br>` +
                    `<b>Storage:</b> ${vehicle.storage_items || 'No items'}`
                );

                vehicleMarkers.push(vehicleMarker);

                // Ανάλογα με το αν το όχημα έχει tasks, προσθήκη στο σωστό array
                if (vehicle.tasks > 0) {
                    vehicleWithTasks.push(vehicleMarker);
                } else {
                    vehicleNoTasks.push(vehicleMarker);
                }

                saveVehicleCoordinates(vehicle.name, randomLatitude, randomLongitude);
                
            }
            get_lines();
        },
        error: function (error) {
            console.error('Error fetching vehicle data:', error);
        }
    });
}


        // Συνάρτηση για την εμφάνιση των θέσεων των οχημάτων στον χάρτη
        function showVehiclePositions(){

            $.ajax({
                url: 'fetch_vehicles.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Με μία for εμφανίζονται οι markers των αυτοκινήτων που γίνονται fetch με ajax
                    for (var i = 0; i < data.length; i++) {
                        var vehicle = data[i];

                
                        var Latitude = vehicle.vehicle_latitude;
                        var Longitude = vehicle.vehicle_longitude;

                        // Δημιουργία marker και αναδυόμενου παραθύρου με πληροφορίες για το όχημα
                        var vehicleMarker = L.marker([Latitude, Longitude], {
                            icon: vehicleCustomIcon,
                        }).addTo(map).bindPopup(
                            `<b>Name:</b> ${vehicle.name}<br>` +
                            `<b>Tasks:</b> ${vehicle.tasks}<br>` +
                            `<b>Storage:</b> ${vehicle.storage_items || 'No items'}`
                        );
                        // Προσθήκη του marker του οχήματος στον χάρτη
                        vehicleMarker.addTo(map);
                        //Προσθήκη του marker στον πίνακα vehicleMarkers για την αξιοποίηση στην συνάρτηση clearVehicleMarkers για καθαρισμό των markers από τον χάρτη
                        vehicleMarkers.push(vehicleMarker);

                        // Έλεγχος αν κάποιο αυτοκίνητο έχει αναλάβει κάποιο task
                        if(vehicle.tasks > 0){
                            // Αν ναί τότε γίνεται προσθήκη στον πίνακα vehicleWithTasks ώστε να εμφανίζονται ή όχι στα φίλτρα τα Οχήματα με ενεργά tasks
                            vehicleWithTasks.push(vehicleMarker);
                        } // Αν όχι τότε γίνεται προσθήκη στον πίνακα vehicleNoTasks ώστε να εμφανίζονται ή όχι στα φίλτρα τα Οχήματα χωρίς ενεργά tasks
                        else{ vehicleNoTasks.push(vehicleMarker); }


                    }
                },
                error: function (error) {
                    console.error('Error fetching vehicle data:', error);
                }
            });




        }

        // Αποθήκευση των νέων συντεταγμένων των marker των αυτοκινήτων στην βάση ώστε να 
        // γίνεται εμφάνιση τους στον χάρτη των διαχειριστών αλλά και στου διαχειριστή
        function saveVehicleCoordinates(vehicle_name, latitude, longitude) {
            $.ajax({
                url: 'insert_vehicles.php',
                method: 'POST',
                data: { // Τα δεδομένα που θα αποθηκευτούν στο αρχείο insert_vehicles.php
                    vehicle_name: vehicle_name,
                    latitude: latitude,
                    longitude: longitude
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (error) {
                    console.error('Error saving vehicle coordinates:', error);
                }
            });
        }

        // Αποθήκευση των συντεταγμένων της βάσης για ανάκτηση στους χάρτες των διασωστών αλλά και του διαχειριστή
        function savebaseCoordinates(base_id, latitude, longitude) {
            $.ajax({
                url: 'insert_base.php',
                method: 'POST',
                data: { // Τα δεδομένα που θα αποθηκευτούν στο αρχείο insert_base.php
                    base_id: base_id,
                    latitude: latitude,
                    longitude: longitude
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (error) {
                    console.error('Error saving base coordinates:', error);
                }
            });
        }

        // Καθαρισμός των marker των αυτοκινήτων από τον χάρτη 
        function clearVehicleMarkers() {
            for (var i = 0; i < vehicleMarkers.length; i++) {
                map.removeLayer(vehicleMarkers[i]);
            }
            vehicleMarkers = [];
            vehicleMarkers.splice(); // Η μέθοδος splice() χρησιμοποιείται για την προσθήκη ή την αφαίρεση στοιχείων από τον πίνακα.
        }

        // Εμφάνιση των marker requests με ανάκτηση απο την βάση δεδομένων με το αρχείο fetch_requests.php με ajax 
        $.ajax({
                url: 'fetch_requests.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Με μία for εμφανίζονται οι markers των requests που γίνονται fetch με ajax
                    for (var i = 0; i < data.length; i++) {
                        var request = data[i];

                        var Latitude = request.latitude; // Το latitude του marker
                        var Longitude = request.longitude; // Το longitude του marker

                        var Latitude = parseFloat(request.latitude); // Μετατροπή σε float
                        var Longitude = parseFloat(request.longitude);

                        // Υπολογισμός μικρού offset για να μην συμπέφτουν οι markers ο ένας πάνω στον άλλο
                        var offsetLat = (Math.random() - 0.5) * 0.004; 
                        var offsetLng = (Math.random() - 0.5) * 0.004;

                        var requestMarker = L.marker([Latitude + offsetLat, Longitude + offsetLng], {
                            icon: requestIcon
                        }).addTo(map).bindPopup(
                            `<b>Name:</b> ${request.citizen_name}<br>` +
                            `<b>Phone:</b> ${request.citizen_phone}<br>` +
                            `<b>Date Added:</b> ${request.date_added}<br>` +
                            `<b>Item:</b> ${request.item}<br>` +
                            `<b>Quantity:</b> ${request.citizen_quantity}<br>` +
                            `<b>Vehicle Name:</b> NOT ASSIGNED <br>` +
                            `<b>State:</b> ${request.state}`
                        );
                        requestMarker.addTo(map);
                        // Αποθήκευση των markers requests στον πίνακα requestMarkers για εμφάνιση στα φίλτρα
                        requestMarkers.push(requestMarker);
                    }
                    console.log(data.length);
                },
                error: function (error) {
                    console.error('Error fetching request data:', error);
                }
            });


            // Εμφάνιση των marker offers με ανάκτηση απο την βάση δεδομένων με το αρχείο fetch_offers.php με ajax 
            $.ajax({
                url: 'fetch_offers.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    for (var i = 0; i < data.length; i++) {
                        var offers = data[i];

                        var Latitude = offers.latitude; // Το latitude του marker
                        var Longitude = offers.longitude; // Το longitude του marker

                        var Latitude = parseFloat(offers.latitude);
                        var Longitude = parseFloat(offers.longitude);

                        // Υπολογισμός μικρού offset για να μην συμπέφτουν οι markers ο ένας πάνω στον άλλο
                        var offsetLat = (Math.random() - 0.5) * 0.004;
                        var offsetLng = (Math.random() - 0.5) * 0.004;

                        var offersMarker = L.marker([Latitude + offsetLat, Longitude + offsetLng], {
                            icon: offerIcon
                        }).addTo(map).bindPopup(
                            `<b>Name:</b> ${offers.citizen_name}<br>` +
                            `<b>Phone:</b> ${offers.citizen_phone}<br>` +
                            `<b>Date Added:</b> ${offers.date_added}<br>` +
                            `<b>Item:</b> ${offers.item}<br>` +
                            `<b>Quantity:</b> ${offers.citizen_quantity}<br>` +
                            `<b>Vehicle Name:</b> NOT ASSIGNED<br>` +
                            `<b>State:</b> ${offers.state}`
                        );
                        offersMarker.addTo(map);
                        // Αποθήκευση των markers offers στον πίνακα offersMarkers για εμφάνιση στα φίλτρα
                        offersMarkers.push(offersMarker);
                    }
                    console.log(data.length);
                },
                error: function (error) {
                    console.error('Error fetching offers data:', error);
                }
            });
        
        
// Ανάκτηση των tasks για τον χάρτη του διαχειριστή από το αρχείο fetch_tasks_admin.php με ajax
function fetchTasks() {
    $.ajax({
        url: 'fetch_tasks_admin.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            // Με μία for εμφανίζονται οι markers των tasks που γίνονται fetch με ajax
            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    var task = data[i];

                    var Latitude = task.task_latitude;
                    var Longitude = task.task_longitude;

                    // Αν το task είναι request τότε αλλάζει το εικονίδιο του marker request σε αναληφθέν task request
                    if (task.task_type === 'request') {
                        var taskMarker = L.marker([Latitude, Longitude], {
                            icon: assignedRequestIcon,
                            data: {
                                task_rescuer_id: task.task_rescuer_id,
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
                        }).addTo(map).bindPopup(
                            `<b>Vehicle ID:</b> ${task.task_rescuer_id}<br>` +
                            `<b>Rescuer Username:</b> ${task.rescuer_username}<br>` +
                            `<b>Citizen ID:</b> ${task.citizen_id}<br>` +
                            `<b>Name:</b> ${task.citizen_fullname}<br>` +
                            `<b>Phone:</b> ${task.citizen_telephone}<br>` +
                            `<b>Date Added:</b> ${task.offer_request_date_added}<br>` +
                            `<b>Date Received:</b> ${task.task_date_received}<br>` +
                            `<b>Item:</b> ${task.item_stuff}<br>` +
                            `<b>Quantity:</b> ${task.item_quantity}<br>` +
                            `<b>Status:</b> Accepted <br>` +
                            `<b>Type:</b> ${task.task_type}<br>`
                        );
                        taskMarker.addTo(map);
                        // Αποθήκευση των markers tasks requests στον πίνακα tasksRequestMarkers για εμφάνιση στα φίλτρα
                        tasksRequestMarkers.push(taskMarker);
                    }else if (task.task_type === 'offer'){ // Αν το task είναι offer τότε αλλάζει το εικονίδιο του marker offer σε αναληφθέν task offer
                        var taskMarker = L.marker([Latitude, Longitude], {
                            icon: assignedOfferIcon,
                            data: {
                                task_rescuer_id: task.task_rescuer_id,
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
                        }).addTo(map).bindPopup(
                            `<b>Vehicle ID:</b> ${task.task_rescuer_id}<br>` +
                            `<b>Rescuer Username:</b> ${task.rescuer_username}<br>` +
                            `<b>Citizen ID:</b> ${task.citizen_id}<br>` +
                            `<b>Name:</b> ${task.citizen_fullname}<br>` +
                            `<b>Phone:</b> ${task.citizen_telephone}<br>` +
                            `<b>Date Added:</b> ${task.offer_request_date_added}<br>` +
                            `<b>Date Received:</b> ${task.task_date_received}<br>` +
                            `<b>Item:</b> ${task.item_stuff}<br>` +
                            `<b>Quantity:</b> ${task.item_quantity}<br>` +
                            `<b>Status:</b> Accepted <br>` +
                            `<b>Type:</b> ${task.task_type}<br>`
                        );
                        taskMarker.addTo(map);
                        // Αποθήκευση των markers tasks offers στον πίνακα tasksOffersMarkers για εμφάνιση στα φίλτρα
                        tasksOffersMarkers.push(taskMarker);
                    }
                }
                console.log(data.length);
            } else {
                console.log('No Tasks found');
            }
        },
        error: function (error) {
            console.error('Error fetching tasks data:', error);
        }
    });
}

fetchTasks(); // Κλήση της συνάρτησης για εμφάνιση των tasks στον χάρτη

// Συνάρτηση για τον καθαρισμό των γραμμών από τον χάρτη
function clearLines() {
    // Πρώτα διαγράφουμε όλες τις παλιές γραμμές
    lines.forEach(function(line) {
        map.removeLayer(line);
    });
    // Καθαρίζουμε τον πίνακα από τις παλιές γραμμές
    lines = [];
}

// Εμφανίζουμε τις γραμμές που ενώνουν τα tasks με τα markers των οχημάτων
function get_lines(){
    clearLines(); // Καθαρισμός προηγούμενων γραμμών
    // Ανάκτηση συντεταγμένων με AJAX
    $.ajax({
        url: 'get_all_lines_for_admin.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
        
            data.forEach(function(coord) {

                var vehicleLatitude = coord.vehicle_latitude;
                var vehicleLongitude = coord.vehicle_longitude;

                var taskLatitude = coord.task_latitude;
                var taskLongitude = coord.task_longitude;

                // Αποθήκευση των συντεταγμένων για δημιουργία (polyline) για σύνδεση του οχήματος με το task
                var latlngs = [
                    [vehicleLatitude, vehicleLongitude],
                    [taskLatitude, taskLongitude]
                ];

                // Σχεδιάζουμε την γραμμή και την αποθηκεύουμε στον πίνακα
                var line = L.polyline(latlngs, {
                        color: 'blue',
                        weight: 2,
                        opacity: 0.6,
                        dashArray: '5, 5'
                    }).addTo(map);

                // Αποθήκευση της γραμμής στον πίνακα lines για επεξεργασία στα φίλτρα
                lines.push(line);

                console.log(lines);
            });

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Σφάλμα κατά την ανάκτηση των συντεταγμένων: " + textStatus + " " + errorThrown);
        }
    });
}


// Συνάρτηση για την εμφάνιση ή εξαφάνιση ορισμένων marker από τον χάρτη (φίλτρα)
function toggleMarkers() {

            // Μεταβλητές που αρχικά είναι επιλεγμένες και ανάλογα το checkbox στο οποίο κάνουμε κλίκ αποεπιλέγονται.
            // Η κάθε μία έχει μοναδικό χαρακτηρηστικό για το checkbox που του έχει ανατεθεί
            var requestsVisible = document.getElementById('requestsToggle').checked;
            var tasksRequestsVisible = document.getElementById('tasksRequestsToggle').checked;
            var tasksOffersVisible = document.getElementById('tasksOffersToggle').checked;
            var offersVisible = document.getElementById('offersToggle').checked;
            var activeVehiclesVisible = document.getElementById('activeVehiclesToggle').checked;
            var inactiveVehiclesVisible = document.getElementById('inactiveVehiclesToggle').checked;
            var connectedLines = document.getElementById('connectingLinesToggle').checked;

            // Ανάλογα το checkbox στο οποίο έχει γίνει κλίκ εκτελείται και η αντίστοιχη for

            for (var i = 0; i < requestMarkers.length; i++) {
                if (requestsVisible) { // Αν το checkbox είναι checked τότε εμφανίζονται τα φίλτρα
                    requestMarkers[i].addTo(map);
                } else {
                    map.removeLayer(requestMarkers[i]); // Αν το checkbox δεν είναι checked τότε δεν εμφανίζονται τα φίλτρα
                }
            }

            for (var i = 0; i < offersMarkers.length; i++) {
                if (offersVisible) {
                    offersMarkers[i].addTo(map);
                } else {
                    map.removeLayer(offersMarkers[i]);
                }
            }

            for (var i = 0; i < tasksRequestMarkers.length; i++) {
                if (tasksRequestsVisible) {
                    tasksRequestMarkers[i].addTo(map);
                } else {
                    map.removeLayer(tasksRequestMarkers[i]);
                }
            }

            for (var i = 0; i < tasksOffersMarkers.length; i++) {
                if (tasksOffersVisible) {
                    tasksOffersMarkers[i].addTo(map);
                } else {
                    map.removeLayer(tasksOffersMarkers[i]);
                }
            }

            for (var i = 0; i < vehicleWithTasks.length; i++) {
                if (activeVehiclesVisible) {
                    vehicleWithTasks[i].addTo(map);
                } else {
                    map.removeLayer(vehicleWithTasks[i]);
                }
            }

            for (var i = 0; i < vehicleNoTasks.length; i++) {
                if (inactiveVehiclesVisible) {
                    vehicleNoTasks[i].addTo(map);
                } else {
                    map.removeLayer(vehicleNoTasks[i]);
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


       get_lines(); // Κλήση της συνάρτησης για εμφάνιση γραμμών μεταξύ των tasks και των αυτοκινήτων


        // Προσθήκη event listeners για τα checkboxes ώστε να ενεργοποιείται η συνάρτηση toggleMarkers
        document.getElementById('requestsToggle').addEventListener('change', toggleMarkers);
        document.getElementById('tasksRequestsToggle').addEventListener('change', toggleMarkers);
        document.getElementById('tasksOffersToggle').addEventListener('change', toggleMarkers);
        document.getElementById('offersToggle').addEventListener('change', toggleMarkers);
        document.getElementById('activeVehiclesToggle').addEventListener('change', toggleMarkers);
        document.getElementById('inactiveVehiclesToggle').addEventListener('change', toggleMarkers);
        document.getElementById('connectingLinesToggle').addEventListener('change', toggleMarkers);


        // Προσθήκη event handler όταν τελειώσει η μεταφορά (drag) του marker της βάσης
        vash.on('dragend', function (event) {
    // Μήνυμα επιβεβαίωσης που επιστρέφει true αν ο χρήστης πατήσει οκ και false αν πατήσει cancel
    var confirmation = confirm('Θέλετε να μεταφέρετε την Βάση σας σε αυτήν την τοποθεσία;');

    if (confirmation == true) {
        var newCoords = event.target.getLatLng(); // Νέες συντεταγμένες μετά το drag and drop

        circle.setLatLng(newCoords); // Μεταφορά του κύκλου στις νέες συντεταγμένες

        // Καθαρισμός των προηγούμενων markers και arrays
        clearVehicleMarkers(); 
        clearLines();
        vehicleWithTasks = []; 
        vehicleNoTasks = [];

        // Τοποθέτηση του marker της βάσης στις νέες συντεταγμένες
        vash.setLatLng(newCoords);
        // Ενημέρωση των marker των αυτοκινήτων στις νέες random θέσεις εντός 5 km της βάσης
        updateVehiclePositions(newCoords);

        // Αποθήκευση των συντεταγμένων σε localStorage
        localStorage.setItem('baseMarkerCoords', JSON.stringify(newCoords));
        savebaseCoordinates(1, newCoords.lat, newCoords.lng); // Αποθήκευση στη βάση

    } else {
        vash.setLatLng(savedCoords); // Επαναφορά στις αποθηκευμένες συντεταγμένες
        showVehiclePositions(); // Εμφάνιση των προηγούμενων θέσεων των οχημάτων
        
    }
});
    </script>
    </script>

    <div><h3><a href="admin_menu.php">Επιστροφή στο Μενού</a></h3></div>
</body>

</html>
