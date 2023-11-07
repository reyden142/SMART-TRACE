import requests
import urllib3

# MikroTik RouterOS REST API settings
urllib3.disable_warnings()
router_ip = '192.168.203.2'  # Replace with the IP address of your RouterOS device
api_username = 'thesis2.0'  # Replace with your RouterOS username
api_password = 'admin'  # Replace with your RouterOS password

# Create a function to retrieve and display selected fields from the CAPsMAN registration table
def get_and_display_selected_data(router_ip, api_username, api_password):
    selected_data = []

    # Retrieve CAPsMAN registration table data
    capsman_data = requests.get(f'https://{router_ip}/rest/caps-man/registration-table',
                                auth=(api_username, api_password), verify=False).json()

    for entry in capsman_data:
        # Extract the desired fields
        interface = entry.get('interface', 'N/A')
        mac_address = entry.get('mac-address', 'N/A')
        rx_signal = entry.get('rx-signal', 'N/A')

        selected_data.append(f"Interface: {interface}, MAC Address: {mac_address}, Rx Signal: {rx_signal} dBm")

    return selected_data

if __name__ == '__main__':
    try:
        selected_data = get_and_display_selected_data(router_ip, api_username, api_password)

        if selected_data:
            print("Selected Data from CAPsMAN Registration Table:\n")
            for entry in selected_data:
                print(entry)
        else:
            print("No registration data found in CAPsMAN Registration Table.")

    except Exception as e:
        print(f"An error occurred: {e}")
