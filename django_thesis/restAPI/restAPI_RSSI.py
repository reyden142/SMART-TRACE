# This is a sample code for Mikrotik Router Automation
from requests.auth import HTTPBasicAuth
import requests
import json
from IPy import IP
import urllib3
import ipaddress

urllib3.disable_warnings()
apiURL = 'https://192.168.203.5/rest'  # Please enter your IP address
apiUsername = 'thesis2.0'  # Input your username here
apiPassword = 'admin'  # Input your password here

def addRSSIMacAddress():
    response = requests.get(apiURL + '/interface/wireless/registration-table',
                            auth=HTTPBasicAuth(apiUsername, apiPassword), verify=False)

    if response.status_code == 200:
        registration_data = response.json()

        for entry in registration_data:
            rssi = entry.get("signal-strength", "N/A").split("@")[0]  # Extract only the RSSI value
            mac_address = entry.get("mac-address", "N/A")
            print(f"MAC Address: {mac_address}, RSSI: {rssi} dBm")

    else:
        print("Failed to retrieve data from the API.")


addRSSIMacAddress()