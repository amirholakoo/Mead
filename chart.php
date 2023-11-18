<?php
$servername = "localhost";
$username = "username"; // Your database username
$password = "password"; // Your database password
$dbname = "mead_monitoring";
$tableName = "Lavender01";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data
$sql = "SELECT timestamp, temperature, humidity, co2_level, alcohol_level FROM `$tableName` ORDER BY timestamp DESC LIMIT 1440";
$result = $conn->query($sql);

// Arrays to store data
$timestamps = array();
$temperatures = array();
$humidities = array();
$co2_levels = array();
$alcohol_levels = array();

// Fetch data
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        array_push($timestamps, $row["timestamp"]);
        array_push($temperatures, $row["temperature"]);
        array_push($humidities, $row["humidity"]);
        array_push($co2_levels, $row["co2_level"]);
        array_push($alcohol_levels, $row["alcohol_level"]);
    }
} else {
    echo "0 results";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mead Fermentation Monitoring</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div style="width:75%;">
    <h2>Temperature</h2>
    <canvas id="temperatureChart"></canvas>
    <h2>Humidity</h2>
    <canvas id="humidityChart"></canvas>
    <h2>CO2 Level</h2>
    <canvas id="co2Chart"></canvas>
    <h2>Alcohol Level</h2>
    <canvas id="alcoholChart"></canvas>
</div>

<script>
    var ctxTemp = document.getElementById('temperatureChart').getContext('2d');
    var temperatureChart = new Chart(ctxTemp, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($timestamps)); ?>,
            datasets: [{
                label: 'Temperature',
                data: <?php echo json_encode(array_reverse($temperatures)); ?>,
                borderColor: 'red',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    var ctxHumidity = document.getElementById('humidityChart').getContext('2d');
    var humidityChart = new Chart(ctxHumidity, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($timestamps)); ?>,
            datasets: [{
                label: 'Humidity',
                data: <?php echo json_encode(array_reverse($humidities)); ?>,
                borderColor: 'blue',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    var ctxCo2 = document.getElementById('co2Chart').getContext('2d');
    var co2Chart = new Chart(ctxCo2, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($timestamps)); ?>,
            datasets: [{
                label: 'CO2 Level',
                data: <?php echo json_encode(array_reverse($co2_levels)); ?>,
                borderColor: 'green',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    var ctxAlcohol = document.getElementById('alcoholChart').getContext('2d');
    var alcoholChart = new Chart(ctxAlcohol, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_reverse($timestamps)); ?>,
            datasets: [{
                label: 'Alcohol Level',
                data: <?php echo json_encode(array_reverse($alcohol_levels)); ?>,
                borderColor: 'purple',
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
</script>

</body>
</html>
