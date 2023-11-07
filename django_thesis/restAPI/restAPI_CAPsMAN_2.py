import requests
import urllib3

# MikroTik RouterOS REST API settings
urllib3.disable_warnings()

# RouterOS API settings
routeros_ip = "192.168.203.2"  # Replace with the IP address of your RouterOS device
routeros_api_port = "8728"  # The default API port for RouterOS
api_username = "thesis2.0"  # Replace with your RouterOS username
api_password = "admin"  # Replace with your RouterOS password

# CAPsMAN API endpoint URL for scanning
capsman_url = f"http://{routeros_ip}:{routeros_api_port}/capsman"


# Function to list CAPs (Access Points) and associated devices
def list_caps_and_devices():
    response = requests.get(f"{capsman_url}/cap/list", auth=(api_username, api_password))

    # After each API request, print status code and response content
    print("Status Code:", response.status_code)
    print("Response Content:", response.text)

    if response.status_code == 200:
        caps_list = response.json()
        for cap in caps_list:
            print("Access Point:", cap['name'])
            print("SSID:", cap['ssid'])
            print("Channel:", cap['channel'])
            print("MAC Address:", cap['mac-address'])

            response = requests.get(f"{capsman_url}/cap/{cap['id']}/registration-table",
                                    auth=(api_username, api_password))
            if response.status_code == 200:
                registration_data = response.json()
                for device in registration_data:
                    print("Device MAC:", device['mac-address'])
                    print("Signal Strength (dBm):", device['signal-strength'])
                    print("Tx/Rx Rates (Mbps):", device['tx-rate'], "/", device['rx-rate'])
                    print("---")
            print("==================================")
    else:
        print("Error listing CAPs:", response.status_code, response.text)


# Call the function to list CAPs and associated devices
list_caps_and_devices()


