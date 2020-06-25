import sys
from dvrip import DVRIPCam

try:
    old_ip = str(sys.argv[1])
    new_ip_dec = str(sys.argv[2])
    if new_ip_dec == old_ip:
        exit(0)
    if new_ip_dec == "192.168.1.10":
        new_ip_hex = "0x0A01A8C0"
    elif new_ip_dec == "192.168.1.11":
        new_ip_hex = "0x0B01A8C0"
    elif new_ip_dec == "192.168.1.12":
        new_ip_hex = "0x0C01A8C0"
    elif new_ip_dec == "192.168.1.13":
        new_ip_hex = "0x0D01A8C0"
    elif new_ip_dec == "192.168.1.14":
        new_ip_hex = "0x0E01A8C0"
    elif new_ip_dec == "192.168.1.15":
        new_ip_hex = "0x0F01A8C0"
    cam = DVRIPCam(old_ip, "admin")
    if cam.login():
        # cam.set_info('NetWork.NetDHCP[0].Enable', "False")
        # cam.set_info('IPAdaptive.IPAdaptive', "False")
        # print(cam.get_info('IPAdaptive.IPAdaptive'))
        # print(cam.get_info('NetWork'))
        # print(cam.get_info('NetWork.NetDHCP[0].Enable'))
        cam.set_info('NetWork.NetCommon.HostIP', new_ip_hex)
        cam.close()
    # cam = DVRIPCam(new_ip_dec, "admin", "")
    # if cam.login():
    #     print(cam.get_info('NetWork.NetDHCP[0].Enable'))
    #     cam.close()



except:
    exit(1)
