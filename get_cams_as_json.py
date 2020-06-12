import subprocess
import pyonvif
# do this to install pyonvif to www-data
# sudo mkdir /var/www/.local
# sudo mkdir /var/www/.cache
# sudo chown www-data.www-data /var/www/.local
# sudo chown www-data.www-data /var/www/.cache
# sudo -H -u www-data pip3 install pyonvif[discovery]


import json

goodPorts = ["80", "8899"]


#
def net_scan():
    output = subprocess.check_output("nmap 192.168.1.*", shell=True).decode().splitlines()
    iList = []
    adrList = []
    goodList = []
    for i in range(0, len(output) - 1):
        if "Nmap scan report for" in output[i]:
            if ")" not in output[i]:
                adrList.append(output[i][output[i].find("192"):])
            else:
                adrList.append(output[i][output[i].find("192"):-1])
            iList.append(i)
    iList.append(len(output))
    for j in range(0, len(iList) - 1):
        for k in range(iList[j], iList[j + 1]):
            if "554" in output[k]:
                if ")" not in output[iList[j]]:
                    goodList.append(output[iList[j]][output[iList[j]].find("192"):])
                else:
                    goodList.append(output[iList[j]][output[iList[j]].find("192"):-1])
    return goodList


def check_info(ipList, portList):
    infoList = []
    for i in ipList:
        for j in portList:
            try:
                mycam = pyonvif.OnvifCam(addr=i, port=80, usr='admin', pwd='admin1234')
                raw = mycam.execute("GET_DEVICE_INFO").decode()
                infoList.append([i, j, str(raw[raw.find("Manufacturer") + 13:raw.find("</tds:Manufacturer>")]),
                                 str(raw[raw.find("SerialNumber") + 13:raw.find("</tds:SerialNumber>")])])
            except:
                pass
            break
    return infoList


def get_manufactured(info):
    out_list = []
    for i in info:
        tempDict = {}
        tempDict["ip"] = i[0]
        tempDict["port"] = i[1]
        tempDict["manufacturer"] = i[2]
        try:
            tempDict["serial"] = i[3]
        except:
            pass
        out_list.append(tempDict)
    return out_list


print(json.dumps(get_manufactured(check_info(net_scan(), goodPorts))))
# get_manufactured(check_info(net_scan(), goodPorts))
