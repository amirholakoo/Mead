<?php
$servername = "localhost";
$username = "username"; // replace with your database username
$password = "password"; // replace with your database password
$dbname = "mead_monitoring";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extract data from POST request
$temperature = $_POST['temperature'];
$humidity = $_POST['humidity'];
$co2_level = $_POST['co2_level'];
$alcohol_level = $_POST['alcohol_level']; // assuming you're sending this data

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO fermentation_data (temperature, humidity, co2_level, alcohol_level) VALUES (?, ?, ?, ?)");
$stmt->bind_param("dddd", $temperature, $humidity, $co2_level, $alcohol_level);

// Execute the statement
if ($stmt->execute()) {
    echo "New records created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
