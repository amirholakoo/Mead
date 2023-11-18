Mead Fermentation Monitoring System
===================================

Overview
--------

This project encompasses a complete setup for monitoring and controlling the fermentation process of mead. It utilizes a NodeMCU ESP8266 to gather various environmental data pertinent to the fermentation process, such as temperature, humidity, CO2, and alcohol levels. The system also includes a Raspberry Pi running a LAMP server to log this data and display it through a web interface.

Features
--------

-   Sensor Data Collection: Temperature, humidity, CO2, and alcohol levels are continuously monitored.
-   Web Interface: Real-time data visualization using Chart.js on a PHP-based web server.
-   Local Control and Monitoring: Built-in web server on the ESP8266 for quick data checks.
-   Active Fermentation Control: Relay-controlled environment adjustments based on temperature readings.

Hardware Components
-------------------

-   NodeMCU ESP8266
-   DHT21 Temperature and Humidity Sensor
-   MQ-135 Gas Sensor for CO2 and Alcohol
-   2-Channel Relay Module
-   Raspberry Pi 4 (Running a LAMP Server)

Wiring Instructions
-------------------

1.  DHT21 Sensor:

    -   VCC to 3.3V on NodeMCU
    -   GND to GND
    -   DATA to D2 on NodeMCU
2.  MQ-135 Sensor:

    -   VCC to VIN on NodeMCU
    -   GND to GND
    -   AOUT to A0 on NodeMCU
3.  Relay Module:

    -   VCC to VIN on NodeMCU
    -   GND to GND
    -   IN1 to D5 on NodeMCU
    -   IN2 to D6 on NodeMCU

Software Setup
--------------

-   The NodeMCU is programmed using the Arduino IDE. The code is responsible for reading sensor data, controlling the relay based on temperature, and sending data to the Raspberry Pi server.
-   The Raspberry Pi runs a LAMP server to store the data in a MySQL database and hosts a PHP-based web interface for data visualization.

Code Structure
--------------

-   `NodeMCU_Code.ino`: The main Arduino sketch for the NodeMCU ESP8266.
-   `chart.php`: A PHP script that retrieves data from the MySQL database and displays it using Chart.js.
-   `post-data.php`: The PHP endpoint on the Raspberry Pi that receives and logs data from the NodeMCU.

Modification
--------------

-   'Modify chart.php and post-chart.php:'

$servername = "localhost";

$username = "username"; // Your database username

$password = "password"; // Your database password

$dbname = "mead_monitoring";

$tableName = "Lavender01";

-   Also modify your .ino file:

//################################# CHANGE HERE:

// Raspberry Pi server details

const char* serverName = "http://192.168.1.88/Monitoring/XXX/post-data.php";

IPAddress staticIP(192, 168, 1, 146); //<<<<<<<<<<<<<<

IPAddress gateway(192, 168, 1, 1);    // Gateway 

IPAddress subnet(255, 255, 255, 0);   // Subnet mask

float TEMP_THRESHOLD = 18;

float TEMP_HYSTERESIS = 0.5;

//################################################

// Replace with your network credentials

const char* ssid = "XXX";

const char* password = "XXX";


Web Interface
-------------

Access the web interface by navigating to `http://[Raspberry_Pi_IP]/Monitoring/XXX/chart.php` to view the real-time and historical data of your mead fermentation process.

Acknowledgements
----------------

A special thank you to ChatGPT from OpenAI for providing extensive guidance and support throughout the development of this project. From the initial concept to the final implementation, ChatGPT has been instrumental in offering coding assistance, troubleshooting advice, and overall project development.
