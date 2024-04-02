

//***************************** RFID DATABASING *************************
//********************************* ESP8266 *****************************

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SoftwareSerial.h>
#include <ESP8266WebServer.h>
#include <SPI.h>
#include <string.h>

WiFiClient client;
//************************************************************************
const int LEDgreen = D3;
const int LEDred = D4;
//************************************************************************
unsigned char incomingbyte;
String CardID = "";
 
SoftwareSerial RFID1 (D5,D6); //232_TX,232_RX Serial connection between RS232 shield and microcontroller

//************************************************************************

/* Set these to your desired credentials. */
const char *ssid = "AP4";
const char *password = "engineering02";
const char* device_token  = "e33ecebe831f152a";

//************************************************************************

String URL = "http://192.168.137.115/rfid_ips/getdata.php"; //computer IP or the server domain
String getData, Link;
String OldCardID = "";
unsigned long previousMillis = 0;
int n = 1; // new ID
String RFIDcode[100], code = "";

//MOTOR*******************************************************************

  int ENA = D8;
  int IN1 = D2;
  int IN2 = D1;
  const int PushButton = D0;

  int up = 0; 
  int down = 0;

//MANUAL OR AUTOMATIC*****************************************************

  const int manual = D7; 
  const int automatic = 3; //RX

//************************************************************************

void setup() {
  delay(1000);
  Serial.begin(9600);

  pinMode(LEDgreen, OUTPUT);
  pinMode(LEDred, OUTPUT);
  pinMode(manual, INPUT);
  pinMode(automatic, OUTPUT);

  //MOTOR **************
  pinMode(ENA, OUTPUT);
  pinMode(IN1, OUTPUT);
  pinMode(IN2, OUTPUT); 
  pinMode(PushButton, INPUT);  
  digitalWrite(IN1, LOW);
  digitalWrite(IN2, LOW);


  //************************

  while (!Serial) {
        ; // wait for serial port to connect. 
    }
  
  RFID1.begin(9600);
  //---------------------------------------------
  
  connectToWiFi();
}
//************************************************************************
void loop() {

  digitalWrite(LEDgreen, HIGH);

  //check if there's a connection to Wi-Fi or not
  if(!WiFi.isConnected()){
    connectToWiFi();    //Retry to connect to Wi-Fi
  }
  //---------------------------------------------

  if (millis() - previousMillis >= 15000) {
    previousMillis = millis();
  }

/*
  if(digitalRead(manual) == HIGH) {
    digitalWrite(automatic, HIGH);
 
    motor();
    return;
  }
*/
  digitalWrite(automatic, LOW);
  //Serial.println("AUTOMATIC");
  delay(50);

  A:
  
  while(RFID1.available ()> 0)
  {
    incomingbyte = RFID1.read();
    //Serial.print(incomingbyte,HEX);
    code = code + String(incomingbyte, HEX);
    
    if(code.length() == 30) // HEX = 22 , DEC = 32
    {
      
      String checker = String(code.charAt(0)) + String(code.charAt(1)) + String(code.charAt(2)) + String(code.charAt(3)) + String(code.charAt(4)) + String(code.charAt(5));
      //Serial.println(checker);
     
      if(checker != "303132")
      {
        Serial.println(code);
        Serial.println("error RFID card ID reloading");
        Serial.println();
          while(RFID1.available ()> 0) {
            code = code + String(incomingbyte, HEX);
            code = "";
            break;
          }
        code = "";
        n = 2;
        goto A;
        break;
      }

      RFIDcode[n] = code;
      code = "";

      if(n == 2)
        {
        if(RFIDcode[1] == RFIDcode[2]) //look for new card
        {
            break;
        }  
        else
          {
            Serial.println();
            
            RFIDcode[2] == RFIDcode[1];
            RFIDcode[2] = "";
            n = 1;
            
            break;
          }   
        }
        
      else if(n == 1) // New Card Present
      {
        //Serial.print(RFIDcode[1]);
        Serial.println();

        Serial.print("CARDID FIRST PART: ");
        Serial.println(RFIDcode[1]);
        SendCardID(RFIDcode[1]);
        
        //delay(2000);
        n++;
      }
  
    }
  }
  code = ""; 

}

//************send the Card UID to the website*************
void SendCardID(String Card_uid){

  Serial.println("Sending the Card ID");
  if(WiFi.isConnected())
  {
    HTTPClient http;    //Declare object of class HTTPClient
    //GET Data          
      getData = "?card_uid=" + String(Card_uid) + "&device_token=" + String(device_token); // Add the Card ID to the GET array in order to send it
      Serial.println(getData);
      //GET methode
      Link = URL + getData;
      http.begin(client,Link); //initiate HTTP request   //Specify content-type header
            
      int httpCode = http.GET();   //Send the request
      String payload = http.getString();    //Get the response payload

      if (httpCode == 200) 
      {
          
        
        if (payload.substring(0, 5) == "login") 
        {
          String user_name = payload.substring(5);
          digitalWrite(LEDgreen, HIGH);
          digitalWrite(LEDred, LOW);
          Serial.println(user_name);

          digitalWrite(IN1, LOW);
          digitalWrite(IN2, HIGH); 

          //delay();
          digitalWrite(LEDgreen, LOW);
          digitalWrite(LEDgreen, LOW);
        }
   else if (payload.substring(0, 6) == "logout") 
        {
          String user_name = payload.substring(6);
          digitalWrite(LEDgreen, HIGH);
          digitalWrite(LEDred, LOW);
          Serial.println(user_name);

          digitalWrite(IN1, LOW);
          digitalWrite(IN2, HIGH); 

          //delay(5000);
          digitalWrite(LEDgreen, LOW);
          digitalWrite(LEDgreen, LOW);
        }

        else
        {
          String user_name = payload.substring(5);
          digitalWrite(LEDgreen, LOW);
          digitalWrite(LEDred, HIGH);
          Serial.println(user_name);
          Serial.println("Not registered"); 
          Serial.println(user_name);
          digitalWrite(IN1, HIGH);
          digitalWrite(IN2, LOW);
          down++;
          up = 0 ; 

          delay(5000);
          digitalWrite(LEDgreen, LOW);
          digitalWrite(LEDgreen, LOW);
        }
        
        http.end();  //Close connection
      
      }
  }
}      
/*
void motor() {

  Serial.println("MANUAL");
  
  Serial.println("up: ");
  Serial.println(up);
  Serial.println("down: ");
  Serial.println(down);
  
  analogWrite(ENA, 255);
 
  if ( digitalRead(PushButton) == HIGH && up == 0){ 
    
     digitalWrite(IN1, LOW);
     digitalWrite(IN2, HIGH); 
     up++;
     down = 0;

     }
 
  if( digitalRead(PushButton) == LOW && down == 0) {
     digitalWrite(IN1, HIGH);
     digitalWrite(IN2, LOW);
     down++;
     up = 0 ; 
    }

}*/


//********************connect to the WiFi******************
void connectToWiFi(){
    WiFi.mode(WIFI_OFF);        //Prevents reconnection issue (taking too long to connect)
    delay(1000);
    WiFi.mode(WIFI_STA);
    Serial.print("Connecting to ");
    Serial.println(ssid);
    WiFi.begin(ssid, password);
    
    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print(".");
    }
    Serial.println("");
    Serial.println("Connected");
  
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());  //IP address assigned to your ESP
    
    delay(1000);
}
//=======================================================================
 
