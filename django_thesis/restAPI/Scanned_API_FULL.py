import paramiko
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
            ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())  # Add the missing closing parenthesis
            try:
                ssh.connect(router_ip, username=username, password=password)

                # Define the CAP interface names ('cap1' and 'cap2')
                cap_interfaces = ['cap1', 'cap2']

                # Create a list to store data from both CAP interfaces
                data = []

                for cap_interface in cap_interfaces:
                    # Create the command to retrieve CAPsMAN registration table data
                    command = f'/caps-man/registration-table/print where interface="{cap_interface}"'

                    # Execute the command
                    stdin, stdout, stderr = ssh.exec_command(command)

                    output = stdout.read().decode()
                    print(f"Output for {cap_interface}:\n{output}")

                    # Process the output to extract data
                    lines = output.split('\n')
                    for line in lines:
                        if line.strip():
                            columns = line.split()
                            mac_address = columns[0]
                            ssid = columns[1]
                            signal_strength = columns[2]
                            data.append((mac_address, ssid, signal_strength, cap_interface))

                # Connect to the database
                connection = connect_to_database()

                # Transfer data to the database
                if connection:
                    transfer_to_database(data, connection)

                ssh.close()

            except Exception as e:
                print(f"Error: {e}")

            # Add a delay before the next iteration
            time.sleep(5)  # Adjust the delay as needed (e.g., 60 seconds)

        except KeyboardInterrupt:
            # Handle Ctrl+C to exit the loop gracefully
            break


if __name__ == "__main__":
    main()
