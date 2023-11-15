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
specific_mac_addresses = ["FE:47:AD:D7:13:E2", #C1
                          "F2:1C:E8:EB:2E:E2", #C2
                          "56:3A:A2:F8:0C:63", #C3
                          "B6:6A:AD:C1:CF:19", #C4
                          "F6:CE:87:F2:06:21", #C5
                          "02:9D:2F:8D:49:90", #C6
                          "A2:89:5E:B6:E7:58", #C7
                          "7A:6B:C2:5A:7B:88", #C8
                          "56:DE:9D:83:4D:C6", #C9
                          "52:39:94:90:76:D2", #C10
                          "FA:C0:A6:71:FA:ED" #AP4
                          ]

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
    if "already running" not in data:
        if connection:
            cursor = connection.cursor()
            try:
                # Insert data into the database, including the timestamp
                insert_query = "INSERT INTO ap_data_position (mac_address, ssid, channel, signal_strength, source, timestamp) VALUES (%s, %s, %s, %s, %s, %s)"
                current_timestamp = datetime.now()  # Capture the current timestamp
                data_with_timestamp = [(row[0], row[1], row[2], row[3], row[4], current_timestamp) for row in data]
                cursor.executemany(insert_query, data_with_timestamp)
                connection.commit()
                print(f"Transferred {len(data)} records to the database with timestamp {current_timestamp}.")
            except Error as e:
                connection.rollback()
                print(f"Error transferring data to the database: {e}")
            finally:
                cursor.close()

def extract_numeric_channel(channel):
    # Use regular expression to extract numeric part from the channel
    match = re.match(r'(\d+)', str(channel))  # Ensure the result is always a string
    if match:
        return match.group(1)
    else:
        return None
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

            pattern = r'(\S+)\s+(\S+)\s+(\d+/\d+/\w+).+?(-\d+)'

            for cap_interface in cap_interfaces:
                # Create the command
                command = f'/caps-man/interface/scan freeze-frame-interval=8 {cap_interface}'

                # Execute the command
                stdin, stdout, stderr = ssh.exec_command(command)

                output = stdout.read(1024).decode()
                lines = output.splitlines()

                # Wait for the scan to complete, adjust the sleep time as needed
                time.sleep(3)  # You can adjust the sleep duration

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



                    current_data.extend(latest_results.values())
                    data.extend(current_data)

                    print(f"data {cap_interface}:", current_data)

            if "failure: already running" not in output:  # Check the condition here as well
                # Create and open a CSV file for writing
                with open('scanned_aps_cap2.csv', 'w', newline='') as csv_file:
                    fieldnames = ['mac_address', 'ssid', 'channel', 'signal_strength', 'source',
                                  'timestamp']  # mac_address, ssid, signal_strength, channel, source, timestamp
                    csv_writer = csv.DictWriter(csv_file, fieldnames=fieldnames)
                    csv_writer.writeheader()

                    # Write the data to the CSV file
                    for row in data:
                        numeric_channel = extract_numeric_channel(row[2])
                        # Convert the timestamp to a string for CSV
                        timestamp_str = row[5].strftime('%Y-%m-%d %H:%M:%S')
                        csv_writer.writerow(
                            {'mac_address': row[0], 'ssid': row[1], 'channel': numeric_channel, 'signal_strength': row[3],  'source': row[4],
                             'timestamp': timestamp_str})

                # Connect to the database
                connection = connect_to_database()

                # Transfer data to the database with timestamp
                if connection:
                    transfer_to_database(data, connection)

            ssh.close()

        except Exception as e:
            print(f"Error: {e}")

if __name__ == "__main__":
    main()
