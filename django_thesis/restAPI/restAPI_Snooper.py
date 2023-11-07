from pyroute2 import IPRoute
from pyroute2.iproute import RouterID
from pyroute2.common import AF_MPLS
import binascii


def get_wireless_snooper_data(ap_ip, ap_username, ap_password, interface_name):
    # Connect to the RouterOS API on the AP
    ip = IPRoute()
    ip.create(kind='raw', family=AF_MPLS)
    ip.bind()
    ip.connect(('API', ap_ip))

    # Authenticate with the AP
    ip.login(ap_username, ap_password)

    # Send the "/interface/wireless/registration-table/print" command to the AP's RouterOS API
    with RouterID(ip, binascii.crc32(b'pyroute2')) as api:
        snooper_data = api.get('/interface/wireless/registration-table',
                               **{'.proplist': 'mac-address,rssi',
                                  'interface': interface_name})

    # Close the connection to the AP's RouterOS API
    ip.close()

    return snooper_data


if __name__ == '__main__':
    # Configure the AP's IP address and credentials
    ap_ip = 'https://192.168.203.5/rest'
    ap_username = 'thesis2.0'  # Input your username here
    ap_password = 'admin'  # Input your password here
    interface_name = 'wlan1'

    snooper_data = get_wireless_snooper_data(ap_ip, ap_username, ap_password, interface_name)

    # Print the retrieved data (MAC addresses and RSSI values)
    for entry in snooper_data:
        print(f"MAC Address: {entry.get('mac-address')}, RSSI: {entry.get('rssi')} dBm")