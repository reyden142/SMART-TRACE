import paramiko
import re
import csv
import time
import mysql.connector
from mysql.connector import Error

# Define the MikroTik router's IP address, username, and password
router_ip = '192.168.203.2'
username = 'thesis2.0'
password = 'admin'

# Database configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "rfid_ips"
}

# List of specific MAC addresses to filter
specific_mac_addresses = ["C2:D2:0C:49:2D:89",
                          "A6:3C:4C:E2:1B:5D",
                          "66:49:7D:0A:CF:76",
                          "FA:C0:A6:71:FA:ED"]


def connect_to_database():
    try:
        connection = mysql.connector.connect(**db_config)
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error: {e}")
    return None

def transfer_to_database(data, connection):
    if connection:
        cursor = connection.cursor()
        try:
            # Insert data into the database
            insert_query = "INSERT INTO ap_data (mac_address, ssid, signal_strength, source) VALUES (%s, %s, %s, %s)"
            cursor.executemany(insert_query, data)
            connection.commit()
            print(f"Transferred {len(data)} records to the database.")
        except Error as e:
            connection.rollback()
            print(f"Error transferring data to the database: {e}")
        finally:
            cursor.close()


def main():
    while True:
        try:
            ssh = paramiko.SSHClient()
            ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            ssh.connect(router_ip, username=username, password=password)

            # Define the CAP interface names ('cap1' and 'cap2')
            cap_interfaces = ['cap2']

            # Create a list to store data from both CAP interfaces
            data = []

            pattern = r'(\S+)\s+(\S+)\s+\S+\s+(-\d+)\s+'

            for cap_interface in cap_interfaces:
                # Create the command
                command = f'/caps-man/interface/scan freeze-frame-interval=5 {cap_interface}'

                # Execute the command
                stdin, stdout, stderr = ssh.exec_command(command)

                # Wait for the scan to complete, adjust the sleep time as needed
                # time.sleep(1)  # You can adjust the sleep duration

                output = stdout.read(1024).decode()
                lines = output.splitlines()

                # Data collection and processing for the current CAP interface
                current_data = []
                latest_results = {}

                for line in lines:
                    match = re.search(pattern, line)
                    if match:
                        mac_address = match.group(1)
                        ssid = match.group(2)
                        signal_strength = int(match.group(3))

                        # Check if the MAC address is in the specific MAC addresses list
                        if mac_address in specific_mac_addresses:
                            # Check if an entry for this SSID already exists
                            latest_results[ssid] = (mac_address, ssid, signal_strength, cap_interface)
                            if ssid in latest_results:
                                # Update the entry if the new result has a stronger signal
                                #if signal_strength > latest_results[ssid][2]:
                                #    latest_results[ssid] = (mac_address, ssid, signal_strength, cap_interface)
                            #else:
                                latest_results[ssid] = (mac_address, ssid, signal_strength, cap_interface)

                current_data.extend(latest_results.values())
                data.extend(current_data)

                print(f"data {cap_interface}:", current_data)

            # Create and open a CSV file for writing
            with open('scanned_aps.csv', 'w', newline='') as csv_file:
                csv_writer = csv.DictWriter(csv_file, fieldnames=['MAC', 'SSID', 'Signal_Strength', 'Source'])
                csv_writer.writeheader()

                # Write the data to the CSV file
                for row in data:
                    csv_writer.writerow({'MAC': row[0], 'SSID': row[1], 'Signal_Strength': row[2], 'Source': row[3]})

            # Connect to the database
            connection = connect_to_database()

            # Transfer data to the database
            if connection:
                transfer_to_database(data, connection)

            ssh.close()

        except Exception as e:
            print(f"Error: {e}")

if __name__ == "__main__":
    main()

