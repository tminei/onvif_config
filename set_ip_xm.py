import sys
from dvrip import DVRIPCam

try:
    old_ip = str(sys.argv[1])
    new_ip_dec = str(sys.argv[2])
    if new_ip_dec == "192.168.1.10":
        new_ip_hex = "0x0A01A8C0"
    elif new_ip_dec == "192.168.1.11":
        new_ip_hex = "0x0B01A8C0"
    elif new_ip_dec == "192.168.1.12":
        new_ip_hex = "0x0C01A8C0"
    elif new_ip_dec == "192.168.1.13":
        new_ip_hex = "0x0D01A8C0"
    cam = DVRIPCam(old_ip, "admin", "")
    if cam.login():
        print(cam.get_info('NetWork.NetCommon.HostIP'))
        cam.set_info('NetWork.NetCommon.HostIP', new_ip_hex)
        cam.close()
        exit(0)
    else:
        exit(2)


    # do_this = "http://{0}/cgi-bin/configManager.cgi?action=setConfig&Network.eth0.IPAddress={1}".format(old_ip, new_ip)
    # # print(do_this)
    # response = session.get(do_this)
    # session.close()
    # exit(response.status_code)
except:
    exit(1)

# http://10.10.0.85:8080/cgi-bin/configManager.cgi?action=setConfig&Network.eth0.IPAddress=192.168.1.11


