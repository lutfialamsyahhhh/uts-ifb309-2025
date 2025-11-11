#include <WiFi.h>
#include <PubSubClient.h>

// --- Konfigurasi WiFi ---
const char* ssid = "Wokwi-GUEST";
const char* password = "";

// --- Konfigurasi MQTT ---
const char* mqtt_server = "broker.hivemq.com";
const int mqtt_port = 1883;
const char* topic_publish = "uts/ifb309/sensor";
const char* topic_subscribe = "uts/ifb309/relay";

// --- Konfigurasi Pin ---
const int relayPin = 2;

WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;

// --- FUNGSI INI DIPANGGIL SAAT ADA PESAN MASUK ---
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Pesan diterima [");
  Serial.print(topic);
  Serial.print("] ");
  
  String message;
  for (int i = 0; i < length; i++) {
    message += (char)payload[i];
  }
  Serial.println(message);

  // Kontrol relay/pompa (LED)
  if (message == "1") {
    digitalWrite(relayPin, HIGH);
    Serial.println("-> Pompa ON");
  } else if (message == "0") {
    digitalWrite(relayPin, LOW);
    Serial.println("-> Pompa OFF");
  }
}
// ---------------------------------------------------

void setup() {
  Serial.begin(115200);
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, LOW);

  setup_wifi();
  client.setServer(mqtt_server, mqtt_port);
  client.setCallback(callback);
}

void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void reconnect_mqtt() {
  // Loop sampai terhubung kembali
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Buat Client ID unik
    String clientId = "esp32-";
    clientId += String(random(0xffff), HEX);
    
    if (client.connect(clientId.c_str())) {
      Serial.println("connected");
      // Setelah terhubung, SUBSCRIBE ke topic relay
      client.subscribe(topic_subscribe);
      Serial.print("Subscribed to: ");
      Serial.println(topic_subscribe);
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

void loop() {
  // Cek koneksi, jika putus, sambungkan lagi
  if (!client.connected()) {
    reconnect_mqtt();
  }
  client.loop();

  // Kirim data sensor setiap 10 detik (Non-blocking)
  long now = millis();
  if (now - lastMsg > 10000) {
    lastMsg = now;

    // Simulasi data sensor
    float suhu = random(25, 35) + (random(0, 100) / 100.0);
    float humid = random(40, 70) + (random(0, 100) / 100.0);

    // Buat payload JSON
    char jsonPayload[100];
    snprintf(jsonPayload, 100, "{\"suhu\":%.2f, \"humid\":%.2f}", suhu, humid);

    // Publish ke MQTT
    client.publish(topic_publish, jsonPayload);
    
    Serial.print("Published to ");
    Serial.print(topic_publish);
    Serial.print(" -> ");
    Serial.println(jsonPayload);
  }
}