<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name ="viewport" content = "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<meta charset="UTF-8">
<title>Εγγραφή Χρήστη</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
  
    .form-container
    {
      text-align: center;
    }

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
            background-color: #e74c3c;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #c0392b;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #34495e; /* Σκούρο background για το φόρμα */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="password"] {
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
</style>

</head>

<body>
<div class="container">
  
  <div class="form-container">
    <form name="registration_form" method="POST" action="registration_redirect.php">
        <label>
          <h2 style="text-align:center">Εγγραφή Πολίτη</h2>
           
          <?php if(isset($_GET['error'])) { ?> 
          <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
          <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->
          
          Όνομα Χρήστη:<input type="text" name="username"><br>
          Κωδικός Χρήστη:<input type="password" value="" id="myInput" name="password"><br>
          <input type="checkbox" onclick="password_visibility()">Εμφάνιση κωδικού <br>
          <br>
          Ονοματεπώνυμο:<input type="text" name="fullname"><br>
          Τηλέφωνο:<input type="text" name="telephone"><br>  
          Γεωγραφικό Πλάτος:<input type="text" name="latitude" id="latitude" value=""><br>
          Γεωγραφικό Μήκος:<input type="text" name="longitude" id="longitude" value=""><br>
        </label> 
        
          <br>
          
          <input type="submit" value="Εγγραφή" name="submit">
          <!-- Σύνδεσμος επιστροφής στην αρχική σελίδα -->
          <h4><a href="login_page.php">Επιστροφή στην αρχική σελίδα</a></h4>
    </form>
  </div>  <!-- Δημιουργεί ένα στοιχείο div για την εμφάνιση του χάρτη -->
         <div id="map" style="width: 800px; height: 600px"></div>

          </div>

         

          <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
          <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
          <script>

            $(document).ready(function (){
                    
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

            function password_visibility()
            {
              var kodikos = document.getElementById("myInput");  //Παίρνουμε τον κωδικό που εισήγαγε ο χρήστης και ελέγχεται
              if (kodikos.type === "password")                   //αν ο κωδικός είναι τύπου password, δηλαδή φαίνεται ως τελείες
              {
                kodikos.type = "text";  //Ο κωδικός γίνεται ορατός στον χρήστη
              } 
              else
              {
                kodikos.type = "password"; //Ο κωδικός αποκρύπτεται ξανά για λόγους ασφαλείας
              }
            }
          </script>
          
          <script>
             
        var mapOptions = {
            center: [38.24618, 21.73514],
            zoom: 17
        }

        
        var map = new L.map('map', mapOptions);

let customIcon = {
iconUrl: "images/building.png",
iconSize: [40, 40]
}

let myIcon = L.icon(customIcon);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

// Προσθήκη ένός draggable marker με δυνατότητα μετακίνησης στο χάρτη
var marker = L.marker([38.24618, 21.73514], { draggable: true }).addTo(map);

 // Εvent listener drag and drop για την κίνηση του marker
marker.on('dragend', function (event) {
    var markerPosition = marker.getLatLng();
    var latitude = markerPosition.lat;
    var longitude = markerPosition.lng;

    // Ενημέρωση των πεδίων εισαγωγής με latitude και longitude με τις νέες συντεταγμένες
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;
});

$.ajax({
            url: 'fetch_base.php', // URL του αρχείου PHP που θα επιστρέψει τα δεδομένα
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.latitude && data.longitude) { // Αν υπάρχουν συντεταγμένες, προσθήκη marker και κύκλου στο χάρτη
                  
                    var coordinates = data;
                    var Latitude = coordinates.latitude;
                    var Longitude = coordinates.longitude;
                    // Δημιουργία ενός μη draggable marker για τις συντεταγμένες της βάσης
                    var vash = new L.marker([Latitude, Longitude], { icon: myIcon, draggable: false }); //marker==vash
                    vash.addTo(map);  // Προσθήκη του marker στο χάρτη
                    // Δημιουργεί και προσθέτει έναν κύκλο γύρω από τις συντεταγμένες της βάσης 5km
                    var circle = L.circle([Latitude, Longitude], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.2,
                        radius: 5000
                    }).addTo(map);

                    
                    map.setView([Latitude, Longitude], 13);

                } else {  // Εμφανίζει μήνυμα σφάλματος αν οι συντεταγμένες είναι μη έγκυρες
                    console.error('Invalid coordinates received:', data);
                }
            },
            error: function (error) {   // Εμφανίζει μήνυμα σφάλματος αν υπάρχει πρόβλημα με το AJAX αίτημα
                console.error('Error fetching base data:', error);
            }
        });
        
          </script>
  </div>
 </body>
 