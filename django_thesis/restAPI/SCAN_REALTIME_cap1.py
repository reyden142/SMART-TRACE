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

def main():
    # Define the floorid here or retrieve it as needed
    floorid = 36  # You can adjust the floorid as needed

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(router_ip, username=username, password=password)
    # Create the command
    command = f'/caps-man interface scan cap1'

    # Execute the command
    stdin, stdout, stderr = ssh.exec_command(command)

    while True:
        try:


            # Define the CAP interface names ('cap1' and 'cap2')
            cap_interfaces = ['cap1']

            # Create a list to store data from both CAP interfaces
            data = []

            pattern = r'(\S+)\s+(?:(\S+)\s+)?(\d+/\d+/\w+).+?(-\d+)'

            for cap_interface in cap_interfaces:

                #time.sleep(3)

                #command = f':put [/file get console-dump.txt contents]'

                # Execute the command
                #stdin, stdout, stderr = ssh.exec_command(command)

                output = stdout.read(2048).decode()
                lines = output.splitlines()

                print(f"unprocessed data {cap_interface}:", output)
                #print(f"lines {cap_interface}:", lines)

                #if "failure: already running" not in output:
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
                #time.sleep(5)  # You can adjust the sleep duration


                current_data.extend(latest_results.values())
                data.extend(current_data)

                print(f"data {cap_interface}:", current_data)

        except paramiko.AuthenticationException:
            print("Authentication failed. Please check your credentials.")
        except paramiko.SSHException as e:
            print(f"SSH connection failed: {str(e)}")
        except Exception as e:
            print(f"An unexpected error occurred: {str(e)}")


if __name__ == "__main__":
    main()
