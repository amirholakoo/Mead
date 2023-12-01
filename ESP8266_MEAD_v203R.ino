#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>

#include <DHT.h>
#include <MQ135.h>

// Replace with your network credentials
const char* ssid = "XXX";
const char* password = "XXX";

IPAddress staticIP(192, 168, 1, 145); // Example IP
IPAddress gateway(192, 168, 1, 1);    // Gateway (usually your router IP)
IPAddress subnet(255, 255, 255, 0);   // Subnet mask

// DHT Sensor setup
#define DHTPIN D2     // Data pin for DHT21
#define DHTTYPE DHT21
DHT dht(DHTPIN, DHTTYPE);

float TEMP_THRESHOLD = 20;
float TEMP_HYSTERESIS = 0.5;

// MQ-135 and Relay setup
const int mqPin = A0;  // MQ-135 on analog pin A0
MQ135 mq135_sensor(mqPin);

const int relayPin1 = D5; // Relay on D1
const int relayPin2 = D6; // Relay on D5

// Raspberry Pi server details
const char* serverName = "http://192.168.1.88/Monitoring/MEAD/post-data.php";

// Initialize Web Server
ESP8266WebServer server(80);

void handleRoot() {
  String message = "Temperature: " + String(dht.readTemperature()) + "<br>";
  message += "Humidity: " + String(dht.readHumidity()) + "<br>";
  message += "CO2 Level: " + String(mq135_sensor.getCorrectedPPM(dht.readTemperature(), dht.readHumidity())) + "<br>";
  message += "Alcohol Level: " + String(analogRead(mqPin)) + "<br>";
  server.send(200, "text/html", message);
}

void fadeLED(int pin) {
  for (int i = 0; i <= 255; i++) {
    analogWrite(pin, i);
    delay(10);
  }
  for (int i = 255; i >= 0; i--) {
    analogWrite(pin, i);
    delay(10);
  }
}

void fastBlink(int pin, int times, int delayTime) {
  for (int i = 0; i < times; i++) {
    digitalWrite(pin, LOW); // Turn on LED (active LOW)
    delay(delayTime);
    digitalWrite(pin, HIGH); // Turn off LED
    delay(delayTime);
  }
}



void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  
  // Set static IP
  WiFi.config(staticIP, gateway, subnet);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi. IP address: ");
  Serial.println(WiFi.localIP());
  delay(100); 

  // set up the web server
  server.on("/", handleRoot); // Define root route
  server.begin();
  Serial.println("HTTP server started");

  dht.begin();
  delay(100); 
  
  pinMode(relayPin1, OUTPUT);
  digitalWrite(relayPin1, HIGH);
  pinMode(relayPin2, OUTPUT);
  digitalWrite(relayPin2, HIGH);
  
  pinMode(mqPin, INPUT);
  
}

void loop() {
  // Read sensor data
  float h = dht.readHumidity();
  Serial.print("humidity: ");
  Serial.println(h);
  delay(100); 
  
  float temp = dht.readTemperature();
  Serial.print("temperature: ");
  Serial.println(temp);
  delay(100); 

  if (isnan(h) || isnan(temp)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  int co2_level = mq135_sensor.getCorrectedPPM(temp, h);
  Serial.print("co2_level: ");
  Serial.println(co2_level);
  delay(100);
  
  int alcohol_level = analogRead(mqPin);
  Serial.print("alcohol_level: ");
  Serial.println(alcohol_level);
  delay(100);

  if (isnan(co2_level) || isnan(alcohol_level)) {
    Serial.println("Failed to read from MQ-135 sensor!");
    return;
  }

  // Temperature-based relay control
  if (temp > (TEMP_THRESHOLD + TEMP_HYSTERESIS)) {
    digitalWrite(relayPin1, LOW); // Turn on cooling/heating device
    Serial.println("Relay 1 is ON");
  } else if (temp < (TEMP_THRESHOLD - TEMP_HYSTERESIS)) {
    digitalWrite(relayPin1, HIGH); // Turn off cooling/heating device
    Serial.println("Relay 1 is OFF");
  }

  
  // handle incoming client request
  server.handleClient();

  // Send data to Raspberry Pi
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;

    http.begin(client, serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String httpRequestData = "temperature=" + String(temp) + "&humidity=" + String(h) + "&co2_level=" + String(co2_level) + "&alcohol_level=" + String(alcohol_level);
    int httpResponseCode = http.POST(httpRequestData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println(httpResponseCode);
      Serial.println(response);
      
      fastBlink(LED_BUILTIN, 5, 500);
      fadeLED(LED_BUILTIN);
      
      
        
      

    }
    else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  
  //fadeLED(LED_BUILTIN);
  delay(1*60000);  // Send data every X MIN
  //fadeLED(LED_BUILTIN);
}
