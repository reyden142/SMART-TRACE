import paramiko

# Replace with your router's IP, username, and password
router_ip = "192.168.203.2"
username = "thesis2.0"
password = "admin"

try:
    # Establish an SSH connection
    ssh_client = paramiko.SSHClient()
    ssh_client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh_client.connect(router_ip, username=username, password=password)

    # Execute RouterOS commands
    ssh_shell = ssh_client.invoke_shell()
    ssh_shell.send("/caps-man registration-table print")

    # Read the response
    response = ssh_shell.recv(4096).decode("utf-8")

    # Process the response
    print(response)

except Exception as e:
    print(f"Error: {e}")

finally:
    if ssh_client:
        ssh_client.close()
