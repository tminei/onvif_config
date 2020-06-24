from onvif import ONVIFCamera
import zeep
import re
import sys
import json

login = str(sys.argv[1])
password = str(sys.argv[2])
port = str(sys.argv[3])
ip = str(sys.argv[4])


def zeep_pythonvalue(self, xmlvalue):
    return xmlvalue


zeep.xsd.simple.AnySimpleType.pythonvalue = zeep_pythonvalue
mycam = ONVIFCamera(ip, port, login, password, 'wsdl')

media_service = mycam.create_media_service()
media_service.GetProfiles()
media_profile = media_service.GetProfiles()[0]
token = media_profile["token"]
# media_service.GetStreamUri({'ProfileToken':token})
camType = "NaN"
link = str(
    media_service.GetStreamUri({'StreamSetup': {'Stream': 'RTP-Unicast', 'Transport': 'UDP'}, 'ProfileToken': token})[
        "Uri"])
lowLink = "NaN"
pattern = 'rtsp:\/\/192\.168\.1\.[0-9]{1,3}:[0-9]{1,3}\/live\/main'
result = re.match(pattern, link)

if result:
    camType = "IPC"
else:
    pattern = 'rtsp:\/\/192\.168\.1\.[0-9]{1,3}:[0-9]{1,3}\/cam'
    result = re.match(pattern, link)
    if result:
        camType = "DAHUA"
if camType == "IPC":
    lowLink = link[:-4] + "sub"
elif camType == "DAHUA":
    lowLink = link[:link.find("subtype=") + 8] + "1" + link[link.find("subtype=") + 9:]
# print(link + "\n" + lowLink)
links = {}
links["low"] = lowLink
links["high"] = link
print(json.dumps(links))
