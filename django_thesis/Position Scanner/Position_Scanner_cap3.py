import paramiko
import re
import pandas as pd
import csv
import time
import mysql.connector
from mysql.connector import Error
from datetime import datetime

# Define the MikroTik router's IP address, username, and password
router_ip = '[fe80::6e3b:6bff:fe5a:e788%5]'
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
                    #"AP4",
                    #"CpE_Wifi"

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
                insert_query = "INSERT INTO position_scanner_cap3 (mac_address, ssid, channel, signal_strength, source, timestamp) VALUES (%s, %s, %s, %s, %s, %s)"
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

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(router_ip, username=username, password=password)

    # Create the command
    command = f'/caps-man/interface/scan cap3'

    # Execute the command
    stdin, stdout, stderr = ssh.exec_command(command)

    while True:
        try:

            # Define the CAP interface names ('cap3' and 'cap3')
            cap_interfaces = ['cap3']

            # Create a list to store data from both CAP interfaces
            data = []

            pattern = r'(\S+)\s+(?:(\S+)\s+)?(\d+/\d+/\w+).+?(-\d+)'

            for cap_interface in cap_interfaces:

                output = stdout.read(3000).decode()
                lines = output.splitlines()

                #print(f"unprocessed data {cap_interface}:", output)
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

                    # If specific_ssid is not in the data, add a default entry
                    for ssid in specific_ssid:
                        if ssid not in latest_results:
                            default_entry = ('0', ssid, '0', 100, cap_interface, datetime.now())
                            latest_results[ssid] = default_entry

                    current_data.extend(latest_results.values())
                    data.extend(current_data)

                    print(f"data {cap_interface}:", current_data)



            if "failure: already running" not in output:  # Check the condition here as well

                # Connect to the database
                connection = connect_to_database()

                # Transfer data to the database with the floorid and timestamp
                if connection:
                    transfer_to_database(data, connection)

                    # Extract data to the database
                    # Replace 'your_query' with the SQL query to select the data you want
                    your_query = "SELECT * FROM `position_scanner_cap3`"

                    # Use pandas to read the query result into a DataFrame
                    df = pd.read_sql_query(your_query, connection)

                    # Replace 'output_file.csv' with the desired file name
                    position_scanner_cap3 = 'position_scanner_cap3.csv'

                    # Save the DataFrame to a CSV file
                    df.to_csv(position_scanner_cap3, index=False)

                    print(f"Data extracted and saved to {position_scanner_cap3}")

                    # Read the CSV file into a new DataFrame
                    position_scanner_cap3 = pd.read_csv(position_scanner_cap3)

                    print('position_scanner_cap3', position_scanner_cap3)

                    # Check for three similar rows based on specific columns
                    columns_to_check = ['mac_address', 'ssid', 'channel', 'signal_strength']

                    duplicates = position_scanner_cap3[
                        position_scanner_cap3.duplicated(subset=columns_to_check, keep=False)]

                    if not duplicates.empty:
                        # Group by 'ssid', 'channel', and 'signal_strength' columns and count the number of duplicate rows for each group
                        duplicate_counts = position_scanner_cap3[
                            position_scanner_cap3.duplicated(subset=columns_to_check, keep=False)].groupby(
                            ['ssid', 'channel', 'signal_strength']).size()

                        # Print the results and transfer data for groups with duplicate_count >= 4
                        for (ssid, channel, signal_strength), count in duplicate_counts.items():

                            # Print the total count for each SSID before processing the data
                            total_count = position_scanner_cap3[position_scanner_cap3['ssid'] == ssid].shape[0]
                            print(f"SSID: {ssid}, Total Count: {total_count}, Number of Duplicate Rows: {count}")

                            print(
                                f"SSID: {ssid}, Channel: {channel}, Signal Strength: {signal_strength}, Number of Duplicate Rows: {count}")

                            # Check if duplicate_count is 4 or more
                            if count >= 3:
                                # Extract the data for the current group
                                group_data = position_scanner_cap3[
                                    (position_scanner_cap3['ssid'] == ssid) &
                                    (position_scanner_cap3['channel'] == channel) &
                                    (position_scanner_cap3['signal_strength'] == signal_strength)
                                    ]

                                # Select the row with the maximum signal strength
                                #representative_row = group_data.loc[group_data['signal_strength'].idxmax()]

                                # Save the representative row to "scanned_aps_cap3.csv"
                                #representative_row.to_csv("scanned_aps_cap3.csv", index=False, mode='a', header=False)

                                # Create and open a CSV file for writing
                                with open('scanned_aps_cap3.csv', 'w', newline='') as csv_file:
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
                                            {'mac_address': row[0], 'ssid': row[1], 'channel': numeric_channel,
                                             'signal_strength': row[3], 'source': row[4],
                                             'timestamp': timestamp_str})

                                print(
                                    f"Data transferred to scanned_aps_cap3.csv for SSID: {ssid}, Channel: {channel}, Signal Strength: {signal_strength}")

                                # Connect to the database
                                with connect_to_database() as connection:
                                    delete_data_from_database(ssid, connection)

                            if total_count >= 10:
                                print(f"Total count for SSID {ssid} is 20 or more. Performing actions...")

                                # Connect to the database
                                with connect_to_database() as connection:
                                    delete_data_from_database(ssid, connection)

                    else:
                        print("No rows with similar values found.")

        except paramiko.AuthenticationException:
            print("Authentication failed. Please check your credentials.")
        except paramiko.SSHException as e:
            print(f"SSH connection failed: {str(e)}")
        except Exception as e:
            print(f"An unexpected error occurred: {str(e)}")

def delete_data_from_database(ssid, connection):
    if connection:
        try:
            with connection.cursor() as cursor:
                delete_query = "DELETE FROM position_scanner_cap3 WHERE ssid = %s"
                cursor.execute(delete_query, (ssid,))
            connection.commit()
            print(f"All data with SSID {ssid} deleted from the database.")
        except Error as e:
            connection.rollback()
            print(f"Error deleting data from the database: {e}")

if __name__ == "__main__":
    main()
