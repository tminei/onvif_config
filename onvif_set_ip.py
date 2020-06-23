from onvif import ONVIFCamera
import sys
login = str(sys.argv[1])
password = str(sys.argv[2])
port = str(sys.argv[3])
old_ip = str(sys.argv[4])
new_ip = str(sys.argv[5])
print(old_ip, new_ip)


mycam = ONVIFCamera(old_ip, port, login, password, 'wsdl')
mycam.devicemgmt.SetNetworkInterfaces(dict(InterfaceToken='eth0', NetworkInterface=dict(
    IPv4=dict(Enabled=True, Manual=[dict(Address=new_ip, PrefixLength=24)], DHCP=False))))

