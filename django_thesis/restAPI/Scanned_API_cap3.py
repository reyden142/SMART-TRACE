import paramiko
import re
import csv
import time
import mysql.connector
from mysql.connector import Error
from datetime import datetime

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
specific_mac_addresses = ["FE:47:AD:D7:13:E2",
                          "06:7D:C7:A4:2A:E8",
                          "3E:8C:7C:66:6C:0C"]

def connect_to_database():
    try:
        connection = mysql.connector.connect(**db_config)
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error: {e}")
    return None

def transfer_to_database(data, connection, floorid):
    if "already running" not in data:
        if connection:
            cursor = connection.cursor()
            try:
                # Insert data into the database, including the timestamp
                insert_query = "INSERT INTO ap_data (mac_address, ssid, signal_strength, channel, source, floorid, timestamp) VALUES (%s, %s, %s, %s, %s, %s, %s)"
                current_timestamp = datetime.now()  # Capture the current timestamp
                data_with_timestamp = [(row[0], row[1], row[2], row[3], row[4], floorid, current_timestamp) for row in data]
                cursor.executemany(insert_query, data_with_timestamp)
                connection.commit()
                print(f"Transferred {len(data)} records to the database with floorid {floorid} and timestamp {current_timestamp}.")
            except Error as e:
                connection.rollback()
                print(f"Error transferring data to the database: {e}")
            finally:
                cursor.close()

def main():
    while True:
        try:
            # Define the floorid here or retrieve it as needed
            floorid = 4  # You can adjust the floorid as needed

            ssh = paramiko.SSHClient()
            ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            ssh.connect(router_ip, username=username, password=password)

            # Define the CAP interface names ('cap1' and 'cap2')
            cap_interfaces = ['cap3']

            # Create a list to store data from both CAP interfaces
            data = []

            pattern = r'(\S+)\s+(\S+)\s+(\d+/\d+/\w+).+?(-\d+)'

            for cap_interface in cap_interfaces:
                # Create the command
                command = f'/caps-man/interface/scan freeze-frame-interval=7 {cap_interface}'

                # Execute the command
                stdin, stdout, stderr = ssh.exec_command(command)

                output = stdout.read(1024).decode()
                lines = output.splitlines()

                print(f"unprocessed data {cap_interface}:", output)

                if "failure: already running" not in output:
                    # Data collection and processing for the current CAP interface
                    current_data = []

                    latest_results = {}

                    for line in lines:
                        match = re.search(pattern, line)
                        if match:
                            mac_address = match.group(1)
                            ssid = match.group(2)
                            channel = match.group(3)  # Capture the channel as a string
                            signal_strength = int(match.group(4))  # Capture the signal strength as an integer

                            if mac_address in specific_mac_addresses:
                                current_timestamp = datetime.now()  # Capture the current timestamp
                                latest_results[ssid] = (
                                    mac_address, ssid, channel, signal_strength, cap_interface, current_timestamp)

                    # Wait for the scan to complete, adjust the sleep time as needed
                    time.sleep(2)  # You can adjust the sleep duration

                    current_data.extend(latest_results.values())
                    data.extend(current_data)

                    print(f"data {cap_interface}:", current_data)

            if "failure: already running" not in output:  # Check the condition here as well
                # Create and open a CSV file for writing
                with open('scanned_aps_cap1.csv', 'w', newline='') as csv_file:
                    fieldnames = ['MAC', 'SSID', 'Signal_Strength', 'Channel', 'Source',
                                  'Timestamp']  # Add 'Timestamp' to fieldnames
                    csv_writer = csv.DictWriter(csv_file, fieldnames=fieldnames)
                    csv_writer.writeheader()

                    # Write the data to the CSV file
                    for row in data:
                        # Convert the timestamp to a string for CSV
                        timestamp_str = row[5].strftime('%Y-%m-%d %H:%M:%S')
                        csv_writer.writerow(
                            {'MAC': row[0], 'SSID': row[1], 'Signal_Strength': row[2], 'Channel': row[3], 'Source': row[4],
                             'Timestamp': timestamp_str})

                # Connect to the database
                connection = connect_to_database()

                # Transfer data to the database with the floorid and timestamp
                if connection:
                    transfer_to_database(data, connection, floorid)

            ssh.close()

        except Exception as e:
            print(f"Error: {e}")

if __name__ == "__main__":
    main()
