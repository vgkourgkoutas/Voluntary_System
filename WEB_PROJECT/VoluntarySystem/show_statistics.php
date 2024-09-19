<?php
session_start();
if (!isset($_SESSION['logged_in1'])) { // Έλεγχος αν υπάρχει σύνδεση διασώστη
    header('Location: logout.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Στατιστικά Εξυπηρέτησης</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
         body {
            background: linear-gradient(100deg, #2c3e50, #2980b9);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: #fff;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #fff;
        }

        div h3 a {
            text-decoration: none;
            color: #C41E3A;
            font-weight: bold;
            font-size: 18px;
            margin-left: 20px;
        }

        div h3 a:hover {
            color: #9A2A2A;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
            color: #000;
        }

        input[type="date"] {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        button {
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #2980b9;
        }

        canvas {
            display: block;
            margin: 30px auto;
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            button {
                width: 100%;
                margin-top: 10px;
            }

            label, input[type="date"] {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }

            input[type="date"] {
                margin-right: 0;
            }
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
<body>

<div><h3><a href="admin_menu.php"><button id="exit"> Επιστροφή στο Μενού </button></a></h3></div>

<h1>Στατιστικά Εξυπηρέτησης</h1>

<div class="container">
    <label for="start_date">Από:</label>
    <input type="date" id="start_date">
    <label for="end_date">Μέχρι:</label>
    <input type="date" id="end_date">
    <button onclick="updateChart()">Ενημέρωση Γραφήματος</button>
    
    <canvas id="serviceChart" width="800" height="400"></canvas>
</div>

    
    <script>  // Δημιουργία και αρχικοποίηση του γραφήματος
        var ctx = document.getElementById('serviceChart').getContext('2d');
        var serviceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Νέα Αιτήματα', 'Νέες Προσφορές', 'Ολοκληρωμένα Αιτήματα', 'Ολοκληρωμένες Προσφορές'],
                datasets: [{
                    label: 'Στατιστικά Εξυπηρέτησης',
                    data: [0, 0, 0, 0], // Αρχικά Δεδομένα
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function updateChart() {  // Συνάρτηση για την ενημέρωση του γραφήματος με βάση τις επιλεγμένες ημερομηνίες
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();

    // Έλεγχος αν έχουν επιλεγεί και οι δύο ημερομηνίες
    if (!startDate || !endDate) {
        alert('Please select both start and end dates.'); // Εμφάνιση μηνύματος αν λείπει κάποια ημερομηνία
        return;
    }

    console.log('AJAX request is being sent with dates:', startDate, endDate); // Debug: Εμφάνιση ημερομηνιών στο console
 
    $.ajax({  // Αίτημα AJAX για την απόκτηση των στατιστικών δεδομένων
        url: 'fetch_statistics.php',  // URL του αρχείου που θα επεξεργαστεί το αίτημα
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json', // Τύπος δεδομένων που αναμένουμε ως απάντηση
        success: function(response) {
            console.log('AJAX response:', response); // Debug: Εμφάνιση της απάντησης AJAX στο console
            // Ενημέρωση των δεδομένων του γραφήματος με τα νέα στατιστικά
            serviceChart.data.datasets[0].data = [
                response.new_requests,
                response.new_offers,
                response.completed_requests,
                response.completed_offers
            ];
            serviceChart.update(); // Ενημέρωση του γραφήματος
        },
        error: function(xhr, status, error) {
            console.error('Error:', status, error); // Εμφάνιση σφαλμάτων στο console
            console.error('Response Text:', xhr.responseText); // Εμφάνιση του κειμένου της απάντησης
        }
    });
}

    </script>
</body>
</html>
