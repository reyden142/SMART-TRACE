import mysql.connector
import requests
import time
from requests.auth import HTTPBasicAuth
import urllib3

# Replace these with your database credentials
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "rfid_ips"
}

urllib3.disable_warnings()
apiURL = 'https://192.168.203.5/rest'  # Please enter your router's IP address
apiUsername = 'thesis2.0'  # Input your username here
apiPassword = 'admin'  # Input your password here
php_url = 'http://localhost/rfid_ips/map.php'  # Input the URL of your PHP script here

def retrieve_data_from_router():
    try:
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

            return rssi_data

        else:
            print("Failed to retrieve data from the API.")
            return None

    except Exception as e:
        print(f"Error retrieving data from the router: {e}")
        return None

# Function to insert data into the XAMPP database
def insert_data_into_database(data):
    if data is not None:
        try:
            # Create a connection to the MySQL database
            connection = mysql.connector.connect(**db_config)
            cursor = connection.cursor()

            # Define an SQL INSERT statement
            insert_query = "INSERT INTO macaddress_rssi (mac_address, rssi) VALUES (%s, %s)"

            for entry in data:
                # Execute the INSERT statement for each entry
                cursor.execute(insert_query, (entry["mac_address"], entry["rssi"]))

            # Commit the changes to the database
            connection.commit()

            print("Data inserted successfully.")

        except mysql.connector.Error as error:
            print(f"Error inserting data into the database: {error}")

        finally:
            # Close the cursor and the database connection
            cursor.close()
            connection.close()

while True:
    try:
        # Retrieve data from the router
        router_data = retrieve_data_from_router()

        if router_data is not None:
            # Insert the retrieved data into the XAMPP database
            insert_data_into_database(router_data)

    except Exception as e:
        print(f"Error during data retrieval and insertion: {e}")

    # Sleep for 2 seconds before collecting more data
    time.sleep(2)
