#include <Adafruit_Sensor.h>
#include "DHT.h" // Library untuk sensor DHT

// Tentukan pin (SESUAI PIN WOKWI)
#define DHTPIN 4       
#define LED_HIJAU 5    
#define LED_KUNING 18  
#define LED_MERAH 12   
#define RELAY_POMPA 19 
#define BUZZER 21      

// Tentukan tipe DHT (DHT22 atau DHT11)
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

void setup() {
  Serial.begin(115200);
  Serial.println("Sistem Monitoring Hidroponik (Soal 1b)");

  // Atur semua pin output
  pinMode(LED_HIJAU, OUTPUT);
  pinMode(LED_KUNING, OUTPUT);
  pinMode(LED_MERAH, OUTPUT);
  pinMode(RELAY_POMPA, OUTPUT);
  pinMode(BUZZER, OUTPUT);

  // Pastikan semua output mati di awal
  digitalWrite(LED_HIJAU, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_MERAH, LOW);
  digitalWrite(RELAY_POMPA, LOW); 
  digitalWrite(BUZZER, LOW);
  
  dht.begin(); 
}

void loop() {
  delay(2000); 

  float t = dht.readTemperature();

  if (isnan(t)) {
    Serial.println("Gagal membaca dari sensor DHT!");
    return;
  }

  Serial.print("Suhu: ");
  Serial.print(t);
  Serial.println(" *C");
  
  if (t > 35.0) {
    // Kondisi Panas
    digitalWrite(LED_HIJAU, LOW);
    digitalWrite(LED_KUNING, LOW);
    digitalWrite(LED_MERAH, HIGH);  
    tone(BUZZER, 1000);
    Serial.println("KONDISI: PANAS! Buzzer ON.");
  } 
  else if (t >= 30.0 && t <= 35.0) {
    // Kondisi Hangat
    digitalWrite(LED_HIJAU, LOW);
    digitalWrite(LED_KUNING, HIGH); 
    digitalWrite(LED_MERAH, LOW);
    noTone(BUZZER); 
    Serial.println("KONDISI: HANGAT.");
  } 
  else { // t < 30.0
    // Kondisi Sejuk
    digitalWrite(LED_HIJAU, HIGH);  
    digitalWrite(LED_KUNING, LOW);
    digitalWrite(LED_MERAH, LOW);
    noTone(BUZZER); 
    Serial.println("KONDISI: SEJUK.");
  }
}