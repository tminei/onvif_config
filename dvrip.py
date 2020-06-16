#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os, sys, struct, json
from time import sleep
import hashlib
import threading
from socket import *
from datetime import *
import re


class DVRIPCam(object):
    DATE_FORMAT = "%Y-%m-%d %H:%M:%S"
    CODES = {
        100: "OK",
        101: "Unknown error",
        102: "Unsupported version",
        103: "Request not permitted",
        104: "User already logged in",
        105: "User is not logged in",
        106: "Username or password is incorrect",
        107: "User does not have necessary permissions",
        203: "Password is incorrect",
        511: "Start of upgrade",
        512: "Upgrade was not started",
        513: "Upgrade data errors",
        514: "Upgrade error",
        515: "Upgrade successful",
    }
    QCODES = {
        "AuthorityList": 1470,
        "Users": 1472,
        "Groups": 1474,
        "AddGroup": 1476,
        "ModifyGroup": 1478,
        "DelGroup": 1480,
        "AddUser": 1482,
        "ModifyUser": 1484,
        "DelUser": 1486,
        "ModifyPassword": 1488,
        "AlarmInfo": 1504,
        "AlarmSet": 1500,
        "KeepAlive": 1006,
        "ChannelTitle": 1046,
        "OPTimeQuery": 1452,
        "OPTimeSetting": 1450,
        "OPMailTest": 1636,
        # { "Name" : "OPMailTest", "OPMailTest" : { "Enable" : true, "MailServer" : { "Address" : "0x00000000", "Anonymity" : false, "Name" : "Your SMTP Server", "Password" : "", "Port" : 25, "UserName" : "" }, "Recievers" : [ "", "none", "none", "none", "none" ], "Schedule" : [ "0 00:00:00-24:00:00", "0 00:00:00-24:00:00" ], "SendAddr" : "", "Title" : "Alarm Message", "UseSSL" : false }, "SessionID" : "0x1" }
        "OPMachine": 1450,
        "OPMonitor": 1413,
        "OPTalk": 1434,
        "OPPTZControl": 1400,
        "OPNetKeyboard": 1550,
        "SystemFunction": 1360,
        "EncodeCapability": 1360,
        "OPSystemUpgrade": 0x5F5,
        "OPSendFile": 0x5F2,
    }
    KEY_CODES = {
        "M": "Menu",
        "I": "Info",
        "E": "Esc",
        "F": "Func",
        "S": "Shift",
        "L": "Left",
        "U": "Up",
        "R": "Right",
        "D": "Down",
    }
    OK_CODES = [100, 515]

    def __init__(self, ip, user="admin", password="", port=34567, hashPass=None):
        self.ip = ip
        self.user = user
        self.password = hashPass or self.sofia_hash(password)
        self.port = port
        self.socket = None
        self.packet_count = 0
        self.session = 0
        self.alive_time = 20
        self.alive = None
        self.alarm = None
        self.alarm_func = None
        self.busy = threading.Condition()

    def connect(self):
        self.socket = socket(AF_INET, SOCK_STREAM)
        self.socket.connect((self.ip, self.port))
        # it's important to extend timeout for upgrade procedure
        self.socket.settimeout(10)

    def close(self):
        self.alive.cancel()
        self.socket.close()
        self.socket = None

    def SetIP(self, ipadr):
        return "0x%08X" % struct.unpack("I", inet_aton(ipadr))

    def send(self, msg, data=None):
        if self.socket == None:
            return {"Ret": 101}
        # self.busy.wait()
        self.busy.acquire()
        if data:
            if hasattr(data, "__iter__"):
                data = bytes(json.dumps(data, ensure_ascii=False), "utf-8")
            payload = struct.pack(
                "BB2xII2xHI",
                255,
                0,
                self.session,
                self.packet_count,
                msg,
                len(data) + 2,
            ) + data + b"\x0a\x00"
        else:
            payload = struct.pack(
                "BB2xII2xHI",
                255,
                0,
                self.session,
                self.packet_count,
                msg,
                0,
            )
        self.socket.send(
            payload,
        )
        reply = {"Ret": 101}
        try:
            (
                head,
                version,
                self.session,
                sequence_number,
                msgid,
                len_data,
            ) = struct.unpack("BB2xII2xHI", self.socket.recv(20))
            sleep(0.1)  # Just for recive whole packet
            reply = self.socket.recv(len_data)
            self.packet_count += 1
            reply = json.loads(reply.replace(b"\x0a", b"").replace(b"\x00", b""), encoding="utf-8")
        except Exception as e:
            print(e)
        finally:
            self.busy.release()
        return reply

    def sofia_hash(self, password=""):
        md5 = hashlib.md5(bytes(password, "utf-8")).digest()
        chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        return "".join([chars[sum(x) % 62] for x in zip(md5[::2], md5[1::2])])

    def login(self, hashPass=None):
        if self.socket == None:
            self.connect()
        data = self.send(
            1000,
            {
                "EncryptType": "MD5",
                "LoginType": "DVRIP-Web",
                "PassWord": hashPass or self.password,
                "UserName": self.user,
            },
        )
        self.session = int(data["SessionID"], 16)
        self.alive_time = data["AliveInterval"]
        self.keep_alive()
        return data["Ret"] in self.OK_CODES

    def getAuthorityList(self):
        data = self.send(self.QCODES["AuthorityList"])
        if data["Ret"] in self.OK_CODES:
            return data["AuthorityList"]
        else:
            return []

    def getGroups(self):
        data = self.send(self.QCODES["Groups"])
        if data["Ret"] in self.OK_CODES:
            return data["Groups"]
        else:
            return []

    def addGroup(self, name, comment="", auth=None):
        data = self.set(
            self.QCODES["AddGroup"],
            "Group",
            {
                "AuthorityList": auth or self.getAuthorityList(),
                "Memo": comment,
                "Name": name,
            },
        )
        return data["Ret"] in self.OK_CODES

    def modifyGroup(self, name, newname=None, comment=None, auth=None):
        g = [x for x in self.getGroups() if x["Name"] == name]
        if g == []:
            print(f'Group " not found!')
            return False
        g = g[0]
        data = self.send(
            self.QCODES["ModifyGroup"],
            {
                "Group": {
                    "AuthorityList": auth or g["AuthorityList"],
                    "Memo": comment or g["Memo"],
                    "Name": newname or g["Name"],
                },
                "GroupName": name,
            },
        )
        return data["Ret"] in self.OK_CODES

    def delGroup(self, name):
        data = self.send(
            self.QCODES["DelGroup"],
            {
                "Name": name,
                "SessionID": "0x%08X" % self.session,
            },
        )
        return data["Ret"] in self.OK_CODES

    def getUsers(self):
        data = self.send(self.QCODES["Users"])
        if data["Ret"] in self.OK_CODES:
            return data["Users"]
        else:
            return []

    def addUser(self, name, password, comment="", group="user", auth=None, sharable=True):
        g = [x for x in self.getGroups() if x["Name"] == group]
        if g == []:
            print(f'Group "{group}" not found!')
            return False
        g = g[0]
        data = self.set(
            self.QCODES["AddUser"],
            "User",
            {
                "AuthorityList": auth or g["AuthorityList"],
                "Group": g["Name"],
                "Memo": comment,
                "Name": name,
                "Password": self.sofia_hash(password),
                "Reserved": False,
                "Sharable": sharable,
            },
        )
        return data["Ret"] in self.OK_CODES

    def modifyUser(self, name, newname=None, comment=None, group=None, auth=None, sharable=None):
        u = [x for x in self.getUsers() if x["Name"] == name]
        if u == []:
            print(f'User "{name}" not found!')
            return False
        u = u[0]
        if group:
            g = [x for x in self.getGroups() if x["Name"] == group]
            if g == []:
                print(f'Group "{group}" not found!')
                return False
            u["AuthorityList"] = g[0]["AuthorityList"]
        data = self.send(
            self.QCODES["ModifyUser"],
            {
                "User": {
                    "AuthorityList": auth or u["AuthorityList"],
                    "Group": group or u["Group"],
                    "Memo": comment or u["Memo"],
                    "Name": newname or u["Name"],
                    "Password": "",
                    "Reserved": u["Reserved"],
                    "Sharable": sharable or u["Sharable"],
                },
                "UserName": name,
            },
        )
        return data["Ret"] in self.OK_CODES

    def delUser(self, name):
        data = self.send(
            self.QCODES["DelUser"],
            {
                "Name": name,
                "SessionID": "0x%08X" % self.session,
            },
        )
        return data["Ret"] in self.OK_CODES

    def changePasswd(self, newpass="", oldpass=None, user=None):
        data = self.send(
            self.QCODES["ModifyPassword"],
            {
                "EncryptType": "MD5",
                "NewPassWord": self.sofia_hash(newpass),
                "PassWord": oldpass or self.password,
                "SessionID": "0x%08X" % self.session,
                "UserName": user or self.user
            },
        )
        return data["Ret"] in self.OK_CODES

    def channel_title(self, titles):
        if isinstance(titles, str):
            titles = [titles]
        self.send(
            self.QCODES["ChannelTitle"],
            {
                "ChannelTitle": titles,
                "Name": "ChannelTitle",
                "SessionID": "0x%08X" % self.session,
            },
        )

    def channel_bitmap(self, width, height, bitmap):
        header = struct.pack("HH12x", width, height)
        self.socket.send(
            struct.pack(
                "BB2xII2xHI",
                255,
                0,
                self.session,
                self.packet_count,
                0x041A,
                len(bitmap) + 16,
            )
            + header
            + bitmap
        )
        reply, rcvd = self.recv_json()
        if reply and reply["Ret"] != 100:
            return False
        return True

    def reboot(self):
        self.set(self.QCODES["OPMachine"], "OPMachine", {"Action": "Reboot"})
        self.close()

    def pretty_print(self, data):
        print(json.dumps(data, indent=4, sort_keys=True))

    def setAlarm(self, func):
        self.alarm_func = func

    def clearAlarm(self):
        self.alarm_func = None

    def alarmStart(self):
        self.alarm = threading.Thread(
            name="DVRAlarm%08X" % self.session,
            target=self.alarm_thread,
            args=[self.busy],
        )
        self.alarm.start()
        return self.get(self.QCODES["AlarmSet"], "")

    def alarm_thread(self, event):
        while True:
            event.acquire()
            try:
                (
                    head,
                    version,
                    session,
                    sequence_number,
                    msgid,
                    len_data,
                ) = struct.unpack("BB2xII2xHI", self.socket.recv(20))
                sleep(0.1)  # Just for recive whole packet
                reply = self.socket.recv(len_data)
                self.packet_count += 1
                reply = json.loads(reply[:-2], encoding="utf8")
                if msgid == self.QCODES["AlarmInfo"] and self.session == session:
                    if self.alarm_func != None:
                        self.alarm_func(reply[reply["Name"]], sequence_number)
            except:
                pass
            finally:
                event.release()
            if self.socket == None:
                break

    def keep_alive(self):
        self.send(
            self.QCODES["KeepAlive"],
            {"Name": "KeepAlive", "SessionID": "0x%08X" % self.session},
        )
        self.alive = threading.Timer(self.alive_time, self.keep_alive)
        self.alive.start()

    def keyDown(self, key):
        self.set(
            self.QCODES["OPNetKeyboard"],
            "OPNetKeyboard",
            {"Status": "KeyDown", "Value": key},
        )

    def keyUp(self, key):
        self.set(
            self.QCODES["OPNetKeyboard"],
            "OPNetKeyboard",
            {"Status": "KeyUp", "Value": key},
        )

    def keyPress(self, key):
        self.keyDown(key)
        sleep(0.3)
        self.keyUp(key)

    def keyScript(self, keys):
        for k in keys:
            if k != " " and k.upper() in self.KEY_CODES:
                self.keyPress(self.KEY_CODES[k.upper()])
            else:
                sleep(1)

    def ptz(self, cmd, step=5, preset=-1, ch=0):
        CMDS = [
            "DirectionUp",
            "DirectionDown",
            "DirectionLeft",
            "DirectionRight",
            "DirectionLeftUp",
            "DirectionLeftDown",
            "DirectionRightUp",
            "DirectionRightDown",
            "ZoomTile",
            "ZoomWide",
            "FocusNear",
            "FocusFar",
            "IrisSmall",
            "IrisLarge",
            "SetPreset",
            "GotoPreset",
            "ClearPreset",
            "StartTour",
            "StopTour",
        ]
        # ptz_param = { "AUX" : { "Number" : 0, "Status" : "On" }, "Channel" : ch, "MenuOpts" : "Enter", "POINT" : { "bottom" : 0, "left" : 0, "right" : 0, "top" : 0 }, "Pattern" : "SetBegin", "Preset" : -1, "Step" : 5, "Tour" : 0 }
        ptz_param = {
            "AUX": {"Number": 0, "Status": "On"},
            "Channel": ch,
            "MenuOpts": "Enter",
            "Pattern": "Start",
            "Preset": preset,
            "Step": step,
            "Tour": 1 if "Tour" in cmd else 0,
        }
        return self.set(
            self.QCODES["OPPTZControl"],
            "OPPTZControl",
            {"Command": cmd, "Parameter": ptz_param},
        )

    def set_info(self, command, data):
        return self.set(1040, command, data)

    def set(self, code, command, data):
        return self.send(
            code, {"Name": command, "SessionID": "0x%08X" % self.session, command: data}
        )

    def get_info(self, command):
        return self.get(1042, command)

    def get(self, code, command):
        data = self.send(code, {"Name": command, "SessionID": "0x%08X" % self.session})
        if data["Ret"] in self.OK_CODES and command in data:
            return data[command]
        else:
            return data

    def get_time(self):
        return datetime.strptime(
            self.get(self.QCODES["OPTimeQuery"], "OPTimeQuery"), self.DATE_FORMAT
        )

    def set_time(self, time=None):
        if time == None:
            time = datetime.now()
        return self.set(
            self.QCODES["OPTimeSetting"],
            "OPTimeSetting",
            time.strftime(self.DATE_FORMAT),
        )

    def get_system_info(self):
        data = self.get(1042, "General")
        self.pretty_print(data)

    def get_encode_capabilities(self):
        data = self.get(self.QCODES["EncodeCapability"], "EncodeCapability")
        self.pretty_print(data)

    def get_system_capabilities(self):
        data = self.get(self.QCODES["SystemFunction"], "SystemFunction")
        self.pretty_print(data)

    def get_camera_info(self, default=False):
        """Request data for 'Camera' from  the target DVRIP device."""
        if default:
            code = 1044
        else:
            code = 1042
        data = self.get_info(code, "Camera")
        self.pretty_print(data)

    def get_encode_info(self, default=False):
        """Request data for 'Simplify.Encode' from the target DVRIP device.

			Arguments:
			default -- returns the default values for the type if True
		"""

        if default:
            code = 1044
        else:
            code = 1042

        data = self.get_info(code, "Simplify.Encode")
        self.pretty_print(data)

    def recv_json(self, buf=bytearray()):
        p = re.compile(b".*({.*})")

        packet = self.socket.recv(0xFFFF)
        if not packet:
            return None, buf
        buf.extend(packet)
        m = p.search(buf)
        if m is None:
            print(buf)
            return None, buf
        buf = buf[m.span(1)[1]:]
        return json.loads(m.group(1), encoding="utf-8"), buf

    def get_upgrade_info(self):
        return self.get(self.QCODES["OPSystemUpgrade"], "OPSystemUpgrade")

    def upgrade(self, filename="", packetsize=0x8000, vprint=None):
        if not vprint:
            vprint = lambda x: print(x)

        data = self.set(0x5F0, "OPSystemUpgrade", {"Action": "Start", "Type": "System"})
        if data["Ret"] not in self.OK_CODES:
            return data

        vprint("Ready to upgrade")
        blocknum = 0
        sentbytes = 0
        fsize = os.stat(filename).st_size
        rcvd = bytearray()
        with open(filename, "rb") as f:
            while True:
                bytes = f.read(packetsize)
                if not bytes:
                    break
                header = struct.pack(
                    "BB2xII2xHI", 255, 0, self.session, blocknum, 0x5F2, len(bytes)
                )
                self.socket.send(header + bytes)
                blocknum += 1
                sentbytes += len(bytes)

                reply, rcvd = self.recv_json(rcvd)
                if reply and reply["Ret"] != 100:
                    vprint("Upgrade failed")
                    return reply

                progress = sentbytes / fsize * 100
                vprint(f"Uploaded {progress:.2f}%")
        vprint("End of file")

        pkt = struct.pack("BB2xIIxBHI", 255, 0, self.session, blocknum, 1, 0x05F2, 0)
        self.socket.send(pkt)
        vprint("Waiting for upgrade...")
        while True:
            reply, rcvd = self.recv_json(rcvd)
            if reply and reply["Name"] == "" and reply["Ret"] == 100:
                break

        while True:
            data, rcvd = self.recv_json(rcvd)
            if data["Ret"] in [512, 513]:
                vprint("Upgrade failed")
                return data
            if data["Ret"] == 515:
                vprint("Upgrade successful")
                self.socket.close()
                return data
            vprint(f"Upgraded {data['Ret']}%")
