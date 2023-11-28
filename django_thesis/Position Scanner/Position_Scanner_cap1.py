import paramiko
import re
import csv
import mysql.connector
from mysql.connector import Error
from datetime import datetime

router_ip = '192.168.203.2'
username = 'thesis2.0'
password = 'admin'

db_config = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "rfid_ips"
}

specific_ssid = [
    "C1",
    "C2",
    "AP4",
    "CpE_Wifi"
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
    if connection:
        cursor = connection.cursor()
        try:
            insert_query = "INSERT INTO ap_data_position (mac_address, ssid, channel, signal_strength, source, timestamp) VALUES (%s, %s, %s, %s, %s, %s)"
            current_timestamp = datetime.now()
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
    match = re.match(r'(\d+)', str(channel))
    if match:
        return match.group(1)
    else:
        return None

def main():
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(router_ip, username=username, password=password)

    cap_interfaces = ['cap1']

    all_data = {}  # Dictionary to store data for each SSID

    signal_strength_counter = {}

    # Move the command execution outside the loop
    command = f'/caps-man/interface/scan {cap_interfaces[0]}'
    stdin, stdout, stderr = ssh.exec_command(command)

    while True:
        try:
            data = []

            pattern = r'(\S+)\s+(?:(\S+)\s+)?(\d+/\d+/\w+).+?(-\d+)'

            output = stdout.read(2048).decode()
            lines = output.splitlines()

            print(f"unprocessed data {cap_interfaces[0]}:", output)

            if "failure: already running" not in output:
                current_data = []
                latest_results = {}

                for line in lines:
                    match = re.search(pattern, line)
                    if match:
                        mac_address = match.group(1)
                        ssid = match.group(2)
                        channel = match.group(3)
                        signal_strength = int(match.group(4))

                        if ssid in specific_ssid:
                            current_timestamp = datetime.now()
                            latest_results[ssid] = (
                                mac_address, ssid, channel, signal_strength, cap_interfaces[0], current_timestamp)

                            if ssid not in signal_strength_counter:
                                signal_strength_counter[ssid] = {signal_strength: 1}
                            else:
                                if signal_strength in signal_strength_counter[ssid]:
                                    signal_strength_counter[ssid][signal_strength] += 1

                                    print('SSID: ', ssid)
                                    print('count: ', signal_strength_counter[ssid][signal_strength])
                                else:
                                    signal_strength_counter[ssid][signal_strength] = 1

                            if signal_strength_counter[ssid].get(signal_strength, 0) == 3:
                                current_data.extend(latest_results.values())
                                if ssid not in all_data:
                                    all_data[ssid] = []
                                all_data[ssid].extend(current_data)
                                signal_strength_counter[ssid] = {}

                print(f"data {cap_interfaces[0]}:", current_data)

            if "failure: already running" not in output:
                connection = connect_to_database()

                if connection:
                    for ssid, ssid_data in all_data.items():
                        # Write data to CSV file
                        csv_filename = f'scanned_aps_{cap_interfaces[0]}.csv'
                        with open(csv_filename, 'w', newline='') as csv_file:
                            fieldnames = ['mac_address', 'ssid', 'channel', 'signal_strength', 'source',
                                          'timestamp']
                            csv_writer = csv.DictWriter(csv_file, fieldnames=fieldnames)
                            csv_writer.writeheader()  # Write the header in each iteration
                            for row in ssid_data:
                                numeric_channel = extract_numeric_channel(row[2])
                                timestamp_str = row[5].strftime('%Y-%m-%d %H:%M:%S')
                                csv_writer.writerow(
                                    {'mac_address': row[0], 'ssid': row[1], 'channel': numeric_channel,
                                     'signal_strength': row[3], 'source': row[4],
                                     'timestamp': timestamp_str})

                        # Transfer data to the database with timestamp
                        transfer_to_database(ssid_data, connection)

                    # Reset the all_data dictionary after transferring data
                    all_data = {}

        except paramiko.AuthenticationException:
            print("Authentication failed. Please check your credentials.")

        except paramiko.SSHException as e:
            print(f"SSH connection failed: {str(e)}")

        except Exception as e:
            print(f"An unexpected error occurred: {str(e)}")

if __name__ == "__main__":
    main()
