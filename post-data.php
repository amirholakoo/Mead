<?php
$servername = "localhost";
$username = "username"; // replace with your database username
$password = "password"; // replace with your database password

$dbname = "mead_monitoring"; // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

///////////////////////////////////////////////////////////////////
$tableName = "Lavender01"; // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<       Change with new database every time
///////////////////////////////////////////////////////////////////

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

// Check if the table exists, and if not, create it
$table_check = "SHOW TABLES LIKE '$tableName'";
$result = $conn->query($table_check);
if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows == 0) {
    $create_table = "CREATE TABLE `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        temperature FLOAT,
        humidity FLOAT,
        co2_level FLOAT,
        alcohol_level FLOAT
    )";

    if ($conn->query($create_table) === TRUE) {
        echo "Table $tableName created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO `$tableName` (temperature, humidity, co2_level, alcohol_level) VALUES (?, ?, ?, ?)");
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
