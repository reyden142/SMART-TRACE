import requests
import paramiko
import time
import urllib3

# MikroTik RouterOS REST API settings
urllib3.disable_warnings()
capsman_ip = '192.168.203.2'  # Replace with the IP address of your RouterOS device
username = 'thesis2.0'  # Replace with your RouterOS username
password = 'admin'  # Replace with your RouterOS password


api_url = f'http://{capsman_ip}/caps-man/scan'
response = requests.get(api_url, auth=(username, password))

if response.status_code == 200:
    # The response contains information about the wireless scan results
    print(response.json())
else:
    print(f"Error: {response.status_code}")

# Close the session if needed
print(response.content)

response.close()