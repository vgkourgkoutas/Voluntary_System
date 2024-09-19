<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name ="viewport" content = "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title> Είσοδος </title>
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
            text-align: center;
            color: #fff;
            font-size: 24px;
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

        select {
          width: 100%;
          padding: 10px;
          margin: 8px 0;
          box-sizing: border-box;
          border: 1px solid #ccc;
          border-radius: 4px;
          background-color: #2c3e50;
          color: white;
          font-size: 16px;
          appearance: none; 
          -webkit-appearance: none; 
          -moz-appearance: none;
          background-image: url('data:image/svg+xml;utf8,<svg fill="%23fff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
          background-repeat: no-repeat;
          background-position: right 10px center;
          background-size: 18px;
      }

      select:focus {
          border-color: #3498db; /* Μπλε περίγραμμα όταν επιλέγεται το select */
          outline: none;
      }

      div select {
          max-width: 100%;
      }

      .center {
      display: block;
      margin-left: auto;
      margin-right: auto;
      width: 50%;
    }


    </style>
</head>
<body>  
<img src="images/loginimg2.png" style="width:200px;height:200px;" class="center">
<div>
<h2>Καλώς ήλθατε</h2>

<?php if(isset($_GET['error'])) { ?> 
    <p id="error-message" class="error"><?php echo $_GET['error']; ?></p> 
    <?php } ?> <!-- Εμφάνιση μηνύματος λάθους. -->
<form name="login_form" method="POST" action="login.php">
        Όνομα Χρήστη:<input type="text" name="username"><br>
        Κωδικός Χρήστη:<input type="password" id="myInput" value="" name="password"><br>
        <input type="checkbox" onclick="password_visibility()">Εμφάνιση κωδικού
        
        <div style="width:200px;">
          <br>
  <select name="category">
    <option value="0">Επιλέξτε Κατηγορία:</option>
    <option value="admin">Διαχειριστής</option>
    <option value="rescuer">Διασώστης</option>
    <option value="citizen">Πολίτης</option>
  </select>
    </div>

      
          <br>
          
          <input type="submit" value="Είσοδος" name="submit">
          <h4><a href="registration.php">Εγγραφή για Πολίτες</a></h4>      
    </form>
    </div>
    <script>
//Συνάρτηση που κρύπτει/αποκρύπτει τον κωδικό που πληκτρολογεί ο χρήστης
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
    </script> 
</body>
</html>




