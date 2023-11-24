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
specific_ssid = [
                    "C1",
                    "C2",
                    "C2",
                    "C3",
                    "C5",
                    "C6",
                    "C7",
                    "C8",
                    "C9",
                    "C10",
                    "C11"
                ]

print(f"specific_ssid: {specific_ssid}")
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
#    if "already running" not in data:
    if connection:
        cursor = connection.cursor()
        try:
            # Insert data into the database, including the timestamp
            insert_query = "INSERT INTO ap_data (mac_address, ssid, Channel, signal_strength, source, floorid, timestamp) VALUES (%s, %s, %s, %s, %s, %s, %s)"
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
    # Define the floorid here or retrieve it as needed
    floorid = 0  # You can adjust the floorid as needed

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(router_ip, username=username, password=password)


    # Create the command
    command = f'/caps-man/interface/scan cap1'

    # Execute the command
    stdin, stdout, stderr = ssh.exec_command(command)

    while True:
        try:

            # Create a list to store data from both CAP interfaces
            data = []

            cap_interfaces = ['cap1']

            pattern = r'(\S+)\s+(?:(\S+)\s+)?(\d+/\d+/\w+).+?(-\d+)'

            for cap_interface in cap_interfaces:

                output = stdout.read(2048).decode()
                lines = output.splitlines()

                print(f"unprocessed data {cap_interface}:", output)
                #print(f"lines {cap_interface}:", lines)

                if "failure: already running" not in output:
                    # Data collection and processing for the current CAP interface
                    current_data = []

                    latest_results = {}

                    for line in lines:
                        match = re.search(pattern, line)
                        #print(f"line: {line}")
                        if match:
                            mac_address = match.group(1)
                            ssid = match.group(2)
                            channel = match.group(3)  # Capture the channel as a string
                            signal_strength = int(match.group(4))  # Capture the signal strength as an integer
                            #print(f"ssid output: {ssid}")
                            if ssid in specific_ssid:
                                current_timestamp = datetime.now()  # Capture the current timestamp
                                latest_results[ssid] = (
                                    mac_address, ssid, channel, signal_strength, cap_interface, current_timestamp)
                    # Wait for the scan to complete, adjust the sleep time as needed
                    #time.sleep(1)  # You can adjust the sleep duration


                    current_data.extend(latest_results.values())
                    data.extend(current_data)

                    print(f"data {cap_interface}:", current_data)

            #if "failure: already running" not in output:  # Check the condition here as well

                # Connect to the database
                connection = connect_to_database()

                # Transfer data to the database with the floorid and timestamp
                if connection:
                    transfer_to_database(data, connection, floorid)

        except paramiko.AuthenticationException:
            print("Authentication failed. Please check your credentials.")
        except paramiko.SSHException as e:
            print(f"SSH connection failed: {str(e)}")
        except Exception as e:
            print(f"An unexpected error occurred: {str(e)}")

if __name__ == "__main__":
    main()
