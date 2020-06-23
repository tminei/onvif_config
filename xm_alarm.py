import socket
import json
import os
import xmltodict
import datetime
import subprocess
import time
from dvrip import DVRIPCam
import cv2
import requests

cam = DVRIPCam("192.168.1.102", "admin", "")
TIMEOUT = 30

cam_door_link = ""
cam_room_link = ""
cam_cat_link = ''
photo_folder = "/photos/STORAGE/"

token = "611822792:AAFV2bYAdgqpGKACeheObAaz7jtzI1Y30qM"
chat_id = "274625481"

# stringa = '<event><title>motion_dect</title><time>2020-04-23T22:24:18</time><status>start</status></event>'
# text = xmltodict.parse(stringa)
# print(text['event'])

door_cam_ID = ''
room_cam_ID = ''
yard_cam_ID = ''

def apiMsg(msg, recipient):
    url = 'https://api.telegram.org/bot' + token + '/sendMessage?chat_id=' + recipient + '&parse_mode=Markdown&text=' + str(
        msg)
    response = requests.get(url)
    return response.json()
    pass

def check_events(event_type):
    if event_type == 'HumanDetect':
        return True
    elif event_type == 'MotionDetect':
        return True
    else:
        return False


def sendImage(img):
    url = "https://api.telegram.org/bot611822792:AAFV2bYAdgqpGKACeheObAaz7jtzI1Y30qM/sendPhoto"
    files = {'photo': open(img, 'rb')}
    data = {'chat_id': "274625481"}
    r = requests.post(url, files=files, data=data)
    print(r.status_code, r.reason, r.content)


def get_name(cam_name, type):
    time_now = datetime.datetime.now().strftime("%d-%m-%y_%H:%M:%S")
    file_name = "{2}{0}_{1}.{3}".format(cam_name, time_now, photo_folder, type)
    return file_name


sock = socket.socket()
sock.bind(('', 9999))
while True:
    sock.listen(10)
    conn, addr = sock.accept()
    print('Client {}'.format(addr))
    while True:
        data = conn.recv(1024)
        if not data:
            break
        else:
            message = json.loads(data[data.find(b'{'):])

            if message['SerialID'] is not None:


                print(message['SerialID'])


            if message['Event'] is not None:
                time1str = time.strftime("%Y%m%d-%H%M%S")
                capture = cv2.VideoCapture("rtsp://192.168.1.102:554/user=admin&password=&channel=1&stream=1?.sdp")
                # for i in range(0, 3):
                ret, frame = capture.read()
                if ret:
                    cv2.imwrite(time1str + ".png", frame)
                    apiMsg(message['Event'], chat_id)
                    sendImage(time1str + ".png")



            if message['Status'] is not None:
                print(message['Status'])
