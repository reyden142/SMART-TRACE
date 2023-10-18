from librouteros import connect

# Define your MikroTik router's IP address, username, and password
router_ip = '192.168.204.1'
username = 'admin'
password = 'admin'

try:
    # Connect to the router
    connection = connect(username=username, password=password, host=router_ip)

    # Perform an API query (for example, get system identity)
    response = connection('/system/identity/print')

    # Iterate through the generator and print each item
    for item in response:
        print(item)
except Exception as e:
    print(f"An error occurred: {e}")