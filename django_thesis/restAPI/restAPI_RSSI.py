# This is a sample code for Mikrotik Router Automation
from requests.auth import HTTPBasicAuth
import requests
import json
from IPy import IP
import urllib3
import ipaddress
import time


urllib3.disable_warnings()
apiURL = 'https://192.168.203.5/rest'  # Please enter your IP address
apiUsername = 'thesis2.0'  # Input your username here
apiPassword = 'admin'  # Input your password here
php_url = 'http://localhost/rfid_ips/map.php'  # Input the URL of your PHP script here

def getRSSIValues():
    response = requests.get(apiURL + '/interface/wireless/registration-table',
                            auth=HTTPBasicAuth(apiUsername, apiPassword), verify=False)

    if response.status_code == 200:
        registration_data = response.json()

        rssi_data = []

        for entry in registration_data:
            rssi = entry.get("signal-strength", "N/A").split("@")[0]  # Extract only the RSSI value
            mac_address = entry.get("mac-address", "N/A")
            rssi_data.append({"mac_address": mac_address, "rssi": rssi})
            print(f"MAC Address: {mac_address}, RSSI: {rssi} dBm")
        # Send the data to the PHP script

        send_data_to_php(rssi_data)
    else:
        print("Failed to retrieve data from the API.")

def send_data_to_php(data):
    headers = {'Content-Type': 'application/json'}
    response = requests.post(php_url, data=json.dumps(data), headers=headers)

    if response.status_code == 200:
        print("Data sent to PHP script successfully.")
    else:
        print(f"Failed to send data to PHP script. Status code: {response.status_code}")

while True:
    getRSSIValues()
    time.sleep(2)  # Wait for 2 seconds before fetching data again