import requests
from requests.auth import HTTPDigestAuth
import sys
session = requests.session()
session.auth = HTTPDigestAuth("admin", "admin1234")
try:
    old_ip = str(sys.argv[1])
    new_ip = str(sys.argv[2])
    do_this = "http://{0}/cgi-bin/configManager.cgi?action=setConfig&Network.eth0.IPAddress={1}".format(old_ip, new_ip)
    response = session.get(do_this)
    session.close()
    exit(response.status_code)
except:
    exit(1)


