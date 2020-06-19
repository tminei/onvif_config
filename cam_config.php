<?php
include_once __DIR__ . '/vendor/autoload.php';

use xobotyi\beansclient\BeansClient;
use xobotyi\beansclient\Connection;

$PASSWORD = rtrim(shell_exec('bash /scr/scripts/system/get_dvr_password.sh'));
session_start();
if (password_verify($PASSWORD, $_SESSION['auth'])) {
//    ini_set('error_reporting', E_ALL);
//    ini_set('display_errors', 1);
//    ini_set('display_startup_errors', 1);
    $settingsFileNetwork = 'settings/network_settings.cfg';
    $camAssignTemp = 'settings/cameras_temp.cfg';
    $settingsArrayNetwork = json_decode(file_get_contents($settingsFileNetwork, true), true);
    $settingsFile = 'settings/cam_settings.cfg';
    $settingsArray = [];
    $settingsArray = json_decode(file_get_contents($settingsFile, true), true);

    $settings_saved = 'settings/settings_saved';
    $settings_saved_array = [];
    $settings_saved_array = json_decode(file_get_contents($settings_saved, true), true);

    $select_array['CAM1_MAN_XM'] = '';
    $select_array['CAM2_MAN_XM'] = '';
    $select_array['CAM3_MAN_XM'] = '';
    $select_array['CAM4_MAN_XM'] = '';

    $select_array['CAM1_MAN_DAHUA'] = '';
    $select_array['CAM2_MAN_DAHUA'] = '';
    $select_array['CAM3_MAN_DAHUA'] = '';
    $select_array['CAM4_MAN_DAHUA'] = '';

    $select_array['CAM1_MAN_OTHER'] = '';
    $select_array['CAM2_MAN_OTHER'] = '';
    $select_array['CAM3_MAN_OTHER'] = '';
    $select_array['CAM4_MAN_OTHER'] = '';

    switch ($settingsArray['CAM1_MAN']) {

        case 'XM':
            $select_array['CAM1_MAN_XM'] = 'selected';
            break;
        case 'DAHUA':
            $select_array['CAM1_MAN_DAHUA'] = 'selected';
            break;
        case 'OTHER':
            $select_array['CAM1_MAN_OTHER'] = 'selected';
            break;
    }

    switch ($settingsArray['CAM2_MAN']) {

        case 'XM':
            $select_array['CAM2_MAN_XM'] = 'selected';
            break;
        case 'DAHUA':
            $select_array['CAM2_MAN_DAHUA'] = 'selected';
            break;
        case 'OTHER':
            $select_array['CAM2_MAN_OTHER'] = 'selected';
            break;
    }

    switch ($settingsArray['CAM3_MAN']) {

        case 'XM':
            $select_array['CAM3_MAN_XM'] = 'selected';
            break;
        case 'DAHUA':
            $select_array['CAM3_MAN_DAHUA'] = 'selected';
            break;
        case 'OTHER':
            $select_array['CAM3_MAN_OTHER'] = 'selected';
            break;
    }

    switch ($settingsArray['CAM4_MAN']) {

        case 'XM':
            $select_array['CAM4_MAN_XM'] = 'selected';
            break;
        case 'DAHUA':
            $select_array['CAM4_MAN_DAHUA'] = 'selected';
            break;
        case 'OTHER':
            $select_array['CAM4_MAN_OTHER'] = 'selected';
            break;
    }

    if ($settingsArray['CAM1_STATE'] == 'ON') {
        $checked['CAM1_STATE_ON'] = "checked";
        $checked['CAM1_STATE_OFF'] = "";
    } else {
        $checked['CAM1_STATE_OFF'] = "checked";
        $checked['CAM1_STATE_ON'] = "";
    }
    if ($settingsArray['CAM2_STATE'] == 'ON') {
        $checked['CAM2_STATE_ON'] = "checked";
        $checked['CAM2_STATE_OFF'] = "";
    } else {
        $checked['CAM2_STATE_OFF'] = "checked";
        $checked['CAM2_STATE_ON'] = "";
    }
    if ($settingsArray['CAM3_STATE'] == 'ON') {
        $checked['CAM3_STATE_ON'] = "checked";
        $checked['CAM3_STATE_OFF'] = "";
    } else {
        $checked['CAM3_STATE_OFF'] = "checked";
        $checked['CAM3_STATE_ON'] = "";
    }
    if ($settingsArray['CAM4_STATE'] == 'ON') {
        $checked['CAM4_STATE_ON'] = "checked";
        $checked['CAM4_STATE_OFF'] = "";
    } else {
        $checked['CAM4_STATE_OFF'] = "checked";
        $checked['CAM4_STATE_ON'] = "";
    }

    function sendToTube($pipe, $data)
    {
        $connection = new Connection('127.0.0.1', 11300, 2, false);
        $BS_client = new BeansClient($connection);
        $BS_client->useTube($pipe)
            ->put($data);
    }

    if (isset($_POST['assign_save'])) {
        $settingsArrayNew = [];
        $tempFile = json_decode(file_get_contents($camAssignTemp, true), true);
        $toAssign = [];
        $settingsArrayNew["CAM1_STATE"] == "OFF";
        $settingsArrayNew["CAM2_STATE"] == "OFF";
        $settingsArrayNew["CAM3_STATE"] == "OFF";
        $settingsArrayNew["CAM4_STATE"] == "OFF";
        $settingsArrayNew["CAM1_AUDIO_STATE"] == "OFF";
        $settingsArrayNew["CAM2_AUDIO_STATE"] == "OFF";
        $settingsArrayNew["CAM3_AUDIO_STATE"] == "OFF";
        $settingsArrayNew["CAM4_AUDIO_STATE"] == "OFF";
        if (isset($_POST["CAM1_ASSIGN"]) and $_POST["CAM1_ASSIGN"] != "0") {
            $toAssign["CAM1"] = [$tempFile[0]["manufacturer"], $tempFile[0]["ip"], $_POST["CAM1_ASSIGN"]];
            if ($toAssign["CAM1"][2] == "192.168.1.10") {
                $settingsArrayNew["CAM1_STATE"] = "ON";
                $settingsArrayNew["CAM1_AUDIO_STATE"] = $_POST["CAM1_AUDIO_STATE"];
            }
            if ($toAssign["CAM1"][2] == "192.168.1.11") {
                $settingsArrayNew["CAM2_STATE"] = "ON";
                $settingsArrayNew["CAM1_AUDIO_STATE"] = $_POST["CAM2_AUDIO_STATE"];
            }
            if ($toAssign["CAM1"][2] == "192.168.1.12") {
                $settingsArrayNew["CAM3_STATE"] = "ON";
                $settingsArrayNew["CAM1_AUDIO_STATE"] = $_POST["CAM3_AUDIO_STATE"];
            }
            if ($toAssign["CAM1"][2] == "192.168.1.13") {
                $settingsArrayNew["CAM4_STATE"] = "ON";
                $settingsArrayNew["CAM1_AUDIO_STATE"] = $_POST["CAM4_AUDIO_STATE"];
            }
        }
        if (isset($_POST["CAM2_ASSIGN"]) and $_POST["CAM2_ASSIGN"] != "0") {
            $toAssign["CAM2"] = [$tempFile[1]["manufacturer"], $tempFile[1]["ip"], $_POST["CAM2_ASSIGN"]];
            if ($toAssign["CAM2"][2] == "192.168.1.10") {
                $settingsArrayNew["CAM1_STATE"] = "ON";
                $settingsArrayNew["CAM2_AUDIO_STATE"] = $_POST["CAM1_AUDIO_STATE"];
            }
            if ($toAssign["CAM2"][2] == "192.168.1.11") {
                $settingsArrayNew["CAM2_STATE"] = "ON";
                $settingsArrayNew["CAM2_AUDIO_STATE"] = $_POST["CAM2_AUDIO_STATE"];
            }
            if ($toAssign["CAM2"][2] == "192.168.1.12") {
                $settingsArrayNew["CAM3_STATE"] = "ON";
                $settingsArrayNew["CAM2_AUDIO_STATE"] = $_POST["CAM3_AUDIO_STATE"];
            }
            if ($toAssign["CAM2"][2] == "192.168.1.13") {
                $settingsArrayNew["CAM4_STATE"] = "ON";
                $settingsArrayNew["CAM2_AUDIO_STATE"] = $_POST["CAM4_AUDIO_STATE"];
            }
        }
        if (isset($_POST["CAM3_ASSIGN"]) and $_POST["CAM3_ASSIGN"] != "0") {
            $toAssign["CAM3"] = [$tempFile[2]["manufacturer"], $tempFile[2]["ip"], $_POST["CAM3_ASSIGN"]];
            if ($toAssign["CAM3"][2] == "192.168.1.10") {
                $settingsArrayNew["CAM1_STATE"] = "ON";
                $settingsArrayNew["CAM3_AUDIO_STATE"] = $_POST["CAM1_AUDIO_STATE"];
            }
            if ($toAssign["CAM3"][2] == "192.168.1.11") {
                $settingsArrayNew["CAM2_STATE"] = "ON";
                $settingsArrayNew["CAM3_AUDIO_STATE"] = $_POST["CAM2_AUDIO_STATE"];
            }
            if ($toAssign["CAM3"][2] == "192.168.1.12") {
                $settingsArrayNew["CAM3_STATE"] = "ON";
                $settingsArrayNew["CAM3_AUDIO_STATE"] = $_POST["CAM3_AUDIO_STATE"];
            }
            if ($toAssign["CAM3"][2] == "192.168.1.13") {
                $settingsArrayNew["CAM4_STATE"] = "ON";
                $settingsArrayNew["CAM3_AUDIO_STATE"] = $_POST["CAM4_AUDIO_STATE"];
            }
        }
        if (isset($_POST["CAM4_ASSIGN"]) and $_POST["CAM4_ASSIGN"] != "0") {
            $toAssign["CAM4"] = [$tempFile[3]["manufacturer"], $tempFile[3]["ip"], $_POST["CAM4_ASSIGN"]];
            if ($toAssign["CAM4"][2] == "192.168.1.10") {
                $settingsArrayNew["CAM1_STATE"] = "ON";
                $settingsArrayNew["CAM4_AUDIO_STATE"] = $_POST["CAM1_AUDIO_STATE"];
            }
            if ($toAssign["CAM4"][2] == "192.168.1.11") {
                $settingsArrayNew["CAM2_STATE"] = "ON";
                $settingsArrayNew["CAM4_AUDIO_STATE"] = $_POST["CAM2_AUDIO_STATE"];
            }
            if ($toAssign["CAM4"][2] == "192.168.1.12") {
                $settingsArrayNew["CAM3_STATE"] = "ON";
                $settingsArrayNew["CAM4_AUDIO_STATE"] = $_POST["CAM3_AUDIO_STATE"];
            }
            if ($toAssign["CAM4"][2] == "192.168.1.13") {
                $settingsArrayNew["CAM4_STATE"] = "ON";
                $settingsArrayNew["CAM4_AUDIO_STATE"] = $_POST["CAM4_AUDIO_STATE"];
            }
        }

        if ($toAssign["CAM1"][0] == "Dahua") {
            if ($toAssign["CAM1"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'DAHUA';
            }
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_dahua.py ' . $toAssign["CAM1"][1] . ' ' . $toAssign["CAM1"][2]);
        } elseif ($toAssign["CAM1"][0] == "H264") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_xm.py ' . $toAssign["CAM1"][1] . ' ' . $toAssign["CAM1"][2]);
            if ($toAssign["CAM1"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'XM';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'XM';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'XM';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'XM';
            }
        } else {
            if ($toAssign["CAM1"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM1"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
        }
        if ($toAssign["CAM2"][0] == "Dahua") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_dahua.py ' . $toAssign["CAM2"][1] . ' ' . $toAssign["CAM2"][2]);
            if ($toAssign["CAM2"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'DAHUA';
            }
        } elseif ($toAssign["CAM2"][0] == "H264") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_xm.py ' . $toAssign["CAM1"][1] . ' ' . $toAssign["CAM1"][2]);
            if ($toAssign["CAM2"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'XM';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'XM';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'XM';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'XM';
            }
        } else {
            if ($toAssign["CAM2"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM2"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
        }
        if ($toAssign["CAM3"][0] == "Dahua") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_dahua.py ' . $toAssign["CAM3"][1] . ' ' . $toAssign["CAM3"][2]);
            if ($toAssign["CAM3"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'DAHUA';
            }
        } elseif ($toAssign["CAM3"][0] == "H264") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_xm.py ' . $toAssign["CAM1"][1] . ' ' . $toAssign["CAM1"][2]);
            if ($toAssign["CAM3"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'XM';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'XM';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'XM';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'XM';
            }
        } else {
            if ($toAssign["CAM3"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM3"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
        }
        if ($toAssign["CAM4"][0] == "Dahua") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_dahua.py ' . $toAssign["CAM4"][1] . ' ' . $toAssign["CAM4"][2]);
            if ($toAssign["CAM4"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'DAHUA';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'DAHUA';
            }
        } elseif ($toAssign["CAM4"][0] == "H264") {
            $temp = shell_exec('python3 /scr/scripts/cameras/set_ip_xm.py ' . $toAssign["CAM1"][1] . ' ' . $toAssign["CAM1"][2]);
            if ($toAssign["CAM4"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM1_MAN'] = 'XM';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM2_MAN'] = 'XM';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM3_MAN'] = 'XM';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'XM';
            }
        } else {
            if ($toAssign["CAM4"][2] == "192.168.1.10") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.11") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.12") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
            if ($toAssign["CAM4"][2] == "192.168.1.13") {
                $settingsArrayNew['CAM4_MAN'] = 'OTHER';
            }
        }
        if ($settingsArrayNew['CAM1_STATE'] == "OFF") {
            $settingsArrayNew['CAM1_IP'] = $settingsArray['CAM1_IP'];
            $settingsArrayNew['CAM1_LINK'] = $settingsArray['CAM1_LINK'];
            $settingsArrayNew['CAM1_LOW_LINK'] = $settingsArray['CAM1_LOW_LINK'];
        } else {
            $settingsArrayNew['CAM1_IP'] = $_POST['CAM1_IP'];
            $settingsArrayNew['CAM1_LINK'] = $_POST['CAM1_LINK'];
            $settingsArrayNew['CAM1_LOW_LINK'] = $_POST['CAM1_LOW_LINK'];
        }

        if ($settingsArrayNew['CAM2_STATE'] == "OFF") {
            $settingsArrayNew['CAM2_IP'] = $settingsArray['CAM2_IP'];
            $settingsArrayNew['CAM2_LINK'] = $settingsArray['CAM2_LINK'];
            $settingsArrayNew['CAM2_LOW_LINK'] = $settingsArray['CAM2_LOW_LINK'];
        } else {
            $settingsArrayNew['CAM2_IP'] = $_POST['CAM2_IP'];
            $settingsArrayNew['CAM2_LINK'] = $_POST['CAM2_LINK'];
            $settingsArrayNew['CAM2_LOW_LINK'] = $_POST['CAM2_LOW_LINK'];
        }

        if ($settingsArrayNew['CAM3_STATE'] == "OFF") {
            $settingsArrayNew['CAM3_IP'] = $settingsArray['CAM3_IP'];
            $settingsArrayNew['CAM3_LINK'] = $settingsArray['CAM3_LINK'];
            $settingsArrayNew['CAM3_LOW_LINK'] = $settingsArray['CAM3_LOW_LINK'];
        } else {
            $settingsArrayNew['CAM3_IP'] = $_POST['CAM3_IP'];
            $settingsArrayNew['CAM3_LINK'] = $_POST['CAM3_LINK'];
            $settingsArrayNew['CAM3_LOW_LINK'] = $_POST['CAM3_LOW_LINK'];
        }
        if ($settingsArrayNew['CAM4_STATE'] == "OFF") {
            $settingsArrayNew['CAM4_IP'] = $settingsArray['CAM4_IP'];
            $settingsArrayNew['CAM4_LINK'] = $settingsArray['CAM4_LINK'];
            $settingsArrayNew['CAM4_LOW_LINK'] = $settingsArray['CAM4_LOW_LINK'];
        } else {
            $settingsArrayNew['CAM4_IP'] = $_POST['CAM4_IP'];
            $settingsArrayNew['CAM4_LINK'] = $_POST['CAM4_LINK'];
            $settingsArrayNew['CAM4_LOW_LINK'] = $_POST['CAM4_LOW_LINK'];
        }

        if ($settingsArrayNew['CAM1_MAN'] == "XM") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://192.168.1.10:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://192.168.1.10:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "XM") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://192.168.1.11:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://192.168.1.11:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "XM") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://192.168.1.12:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://192.168.1.12:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "XM") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://192.168.1.13:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://192.168.1.13:554/user=admin&password=&channel=1&stream=1?.sdp";
        }

        if ($settingsArrayNew['CAM1_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://admin:admin1234@192.168.1.10:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.10:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://admin:admin1234@192.168.1.11:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.11:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://admin:admin1234@192.168.1.12:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.12:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://admin:admin1234@192.168.1.13:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.13:554/cam/realmonitor?channel=1&subtype=1";
        }

        if ($settingsArrayNew['CAM1_MAN'] == "OTHER") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://admin:12345@192.168.1.10:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://admin:12345@192.168.1.10:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "OTHER") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://admin:12345@192.168.1.11:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://admin:12345@192.168.1.11:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "OTHER") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://admin:12345@192.168.1.12:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://admin:12345@192.168.1.12:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "OTHER") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://admin:12345@192.168.1.13:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://admin:12345@192.168.1.13:554/ISAPI/Streaming/Channels/102";
        }



        file_put_contents($settingsFile, json_encode($settingsArrayNew));
        $settingsArrayNew['SETTINGS_TYPE'] = 'CAM';
        sendToTube("settings", json_encode($settingsArrayNew));

        $settings_saved_array = json_decode(file_get_contents($settings_saved, true), true);
        $settings_saved_array['CAM'] = date("d.m.y H:i:s P");
        file_put_contents($settings_saved, json_encode($settings_saved_array));

        header('Location: cam_config.php');

    }
    if (isset($_POST['set_defaults'])) {
        $settingsArrayNew['CAM1_MAN'] = $_POST['CAM1_MAN'];
        $settingsArrayNew['CAM2_MAN'] = $_POST['CAM2_MAN'];
        $settingsArrayNew['CAM3_MAN'] = $_POST['CAM3_MAN'];
        $settingsArrayNew['CAM4_MAN'] = $_POST['CAM4_MAN'];
        $settingsArrayNew['CAM1_STATE'] = "ON";
        $settingsArrayNew['CAM2_STATE'] = "OFF";
        $settingsArrayNew['CAM3_STATE'] = "OFF";
        $settingsArrayNew['CAM4_STATE'] = "OFF";
        $settingsArrayNew['CAM1_IP'] = "192.168.1.10";
        $settingsArrayNew['CAM2_IP'] = "192.168.1.11";
        $settingsArrayNew['CAM3_IP'] = "192.168.1.12";
        $settingsArrayNew['CAM4_IP'] = "192.168.1.13";
        $settingsArrayNew['CAM1_AUDIO_STATE'] = "OFF";
        $settingsArrayNew['CAM2_AUDIO_STATE'] = "OFF";
        $settingsArrayNew['CAM3_AUDIO_STATE'] = "OFF";
        $settingsArrayNew['CAM4_AUDIO_STATE'] = "OFF";

        if ($settingsArrayNew['CAM1_MAN'] == "XM") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://192.168.1.10:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://192.168.1.10:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "XM") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://192.168.1.11:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://192.168.1.11:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "XM") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://192.168.1.12:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://192.168.1.12:554/user=admin&password=&channel=1&stream=1?.sdp";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "XM") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://192.168.1.13:554/user=admin&password=&channel=1&stream=0?.sdp";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://192.168.1.13:554/user=admin&password=&channel=1&stream=1?.sdp";
        }

        if ($settingsArrayNew['CAM1_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://admin:admin1234@192.168.1.10:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.10:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://admin:admin1234@192.168.1.11:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.11:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://admin:admin1234@192.168.1.12:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.12:554/cam/realmonitor?channel=1&subtype=1";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "DAHUA") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://admin:admin1234@192.168.1.13:554/cam/realmonitor?channel=1&subtype=0";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://admin:admin1234@192.168.1.13:554/cam/realmonitor?channel=1&subtype=1";
        }

        if ($settingsArrayNew['CAM1_MAN'] == "OTHER") {
            $settingsArrayNew['CAM1_LINK'] = "rtsp://admin:12345@192.168.1.10:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM1_LOW_LINK'] = "rtsp://admin:12345@192.168.1.10:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM2_MAN'] == "OTHER") {
            $settingsArrayNew['CAM2_LINK'] = "rtsp://admin:12345@192.168.1.11:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM2_LOW_LINK'] = "rtsp://admin:12345@192.168.1.11:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM3_MAN'] == "OTHER") {
            $settingsArrayNew['CAM3_LINK'] = "rtsp://admin:12345@192.168.1.12:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM3_LOW_LINK'] = "rtsp://admin:12345@192.168.1.12:554/ISAPI/Streaming/Channels/102";
        }
        if ($settingsArrayNew['CAM4_MAN'] == "OTHER") {
            $settingsArrayNew['CAM4_LINK'] = "rtsp://admin:12345@192.168.1.13:554/ISAPI/Streaming/Channels/101";
            $settingsArrayNew['CAM4_LOW_LINK'] = "rtsp://admin:12345@192.168.1.13:554/ISAPI/Streaming/Channels/102";
        }
        file_put_contents($settingsFile, json_encode($settingsArrayNew));
        $settingsArrayNew['SETTINGS_TYPE'] = 'CAM';
        sendToTube("settings", json_encode($settingsArrayNew));
        $settings_saved_array = json_decode(file_get_contents($settings_saved, true), true);
        $settings_saved_array['CAM'] = date("d.m.y H:i:s P");
        file_put_contents($settings_saved, json_encode($settings_saved_array));
        header('Location: cam_config.php');
    }

    if (isset($_POST['dvr_save'])) {
        $settingsArrayNew = [];
        if ($_POST['CAM1_STATE'] == '') {
            $_POST['CAM1_STATE'] = 'OFF';
        } else {
            $_POST['CAM1_STATE'] = 'ON';
        }
        if ($_POST['CAM2_STATE'] == '') {
            $_POST['CAM2_STATE'] = 'OFF';
        } else {
            $_POST['CAM2_STATE'] = 'ON';
        }
        if ($_POST['CAM3_STATE'] == '') {
            $_POST['CAM3_STATE'] = 'OFF';
        } else {
            $_POST['CAM3_STATE'] = 'ON';
        }
        if ($_POST['CAM4_STATE'] == '') {
            $_POST['CAM4_STATE'] = 'OFF';
        } else {
            $_POST['CAM4_STATE'] = 'ON';
        }

        if ($_POST['CAM1_AUDIO_STATE'] == '') {
            $_POST['CAM1_AUDIO_STATE'] = 'OFF';
        } else {
            $_POST['CAM1_AUDIO_STATE'] = 'ON';
        }
        if ($_POST['CAM2_AUDIO_STATE'] == '') {
            $_POST['CAM2_AUDIO_STATE'] = 'OFF';
        } else {
            $_POST['CAM2_AUDIO_STATE'] = 'ON';
        }
        if ($_POST['CAM3_AUDIO_STATE'] == '') {
            $_POST['CAM3_AUDIO_STATE'] = 'OFF';
        } else {
            $_POST['CAM3_AUDIO_STATE'] = 'ON';
        }
        if ($_POST['CAM4_AUDIO_STATE'] == '') {
            $_POST['CAM4_AUDIO_STATE'] = 'OFF';
        } else {
            $_POST['CAM4_AUDIO_STATE'] = 'ON';
        }

        if ($_POST['CAM1_STATE'] == "OFF") {
            $settingsArrayNew['CAM1_STATE'] = 'OFF';
            $settingsArrayNew['CAM1_AUDIO_STATE'] = $settingsArray['CAM1_AUDIO_STATE'];
            $settingsArrayNew['CAM1_IP'] = $settingsArray['CAM1_IP'];
            $settingsArrayNew['CAM1_LINK'] = $settingsArray['CAM1_LINK'];
            $settingsArrayNew['CAM1_LOW_LINK'] = $settingsArray['CAM1_LOW_LINK'];
            $settingsArrayNew['CAM1_MAN'] = $settingsArray['CAM1_MAN'];
        } else {
            $settingsArrayNew['CAM1_STATE'] = 'ON';
            $settingsArrayNew['CAM1_AUDIO_STATE'] = $_POST['CAM1_AUDIO_STATE'];
            $settingsArrayNew['CAM1_IP'] = $_POST['CAM1_IP'];
            $settingsArrayNew['CAM1_LINK'] = $_POST['CAM1_LINK'];
            $settingsArrayNew['CAM1_LOW_LINK'] = $_POST['CAM1_LOW_LINK'];
            $settingsArrayNew['CAM1_MAN'] = $_POST['CAM1_MAN'];
        }

        if ($_POST['CAM2_STATE'] == "OFF") {
            $settingsArrayNew['CAM2_STATE'] = 'OFF';
            $settingsArrayNew['CAM2_AUDIO_STATE'] = $settingsArray['CAM2_AUDIO_STATE'];
            $settingsArrayNew['CAM2_IP'] = $settingsArray['CAM2_IP'];
            $settingsArrayNew['CAM2_LINK'] = $settingsArray['CAM2_LINK'];
            $settingsArrayNew['CAM2_LOW_LINK'] = $settingsArray['CAM2_LOW_LINK'];
            $settingsArrayNew['CAM2_MAN'] = $settingsArray['CAM2_MAN'];
        } else {
            $settingsArrayNew['CAM2_STATE'] = 'ON';
            $settingsArrayNew['CAM2_AUDIO_STATE'] = $_POST['CAM2_AUDIO_STATE'];
            $settingsArrayNew['CAM2_IP'] = $_POST['CAM2_IP'];
            $settingsArrayNew['CAM2_LINK'] = $_POST['CAM2_LINK'];
            $settingsArrayNew['CAM2_LOW_LINK'] = $_POST['CAM2_LOW_LINK'];
            $settingsArrayNew['CAM2_MAN'] = $_POST['CAM2_MAN'];
        }

        if ($_POST['CAM3_STATE'] == "OFF") {
            $settingsArrayNew['CAM3_STATE'] = 'OFF';
            $settingsArrayNew['CAM3_AUDIO_STATE'] = $settingsArray['CAM3_AUDIO_STATE'];
            $settingsArrayNew['CAM3_IP'] = $settingsArray['CAM3_IP'];
            $settingsArrayNew['CAM3_LINK'] = $settingsArray['CAM3_LINK'];
            $settingsArrayNew['CAM3_LOW_LINK'] = $settingsArray['CAM3_LOW_LINK'];
            $settingsArrayNew['CAM3_MAN'] = $settingsArray['CAM3_MAN'];
        } else {
            $settingsArrayNew['CAM3_STATE'] = 'ON';
            $settingsArrayNew['CAM3_AUDIO_STATE'] = $_POST['CAM3_AUDIO_STATE'];
            $settingsArrayNew['CAM3_IP'] = $_POST['CAM3_IP'];
            $settingsArrayNew['CAM3_LINK'] = $_POST['CAM3_LINK'];
            $settingsArrayNew['CAM3_LOW_LINK'] = $_POST['CAM3_LOW_LINK'];
            $settingsArrayNew['CAM3_MAN'] = $_POST['CAM3_MAN'];
        }
        if ($_POST['CAM4_STATE'] == "OFF") {
            $settingsArrayNew['CAM4_STATE'] = 'OFF';
            $settingsArrayNew['CAM4_AUDIO_STATE'] = $settingsArray['CAM4_AUDIO_STATE'];
            $settingsArrayNew['CAM4_IP'] = $settingsArray['CAM4_IP'];
            $settingsArrayNew['CAM4_LINK'] = $settingsArray['CAM4_LINK'];
            $settingsArrayNew['CAM4_LOW_LINK'] = $settingsArray['CAM4_LOW_LINK'];
            $settingsArrayNew['CAM4_MAN'] = $settingsArray['CAM4_MAN'];
        } else {
            $settingsArrayNew['CAM4_STATE'] = 'ON';
            $settingsArrayNew['CAM4_AUDIO_STATE'] = $_POST['CAM4_AUDIO_STATE'];
            $settingsArrayNew['CAM4_IP'] = $_POST['CAM4_IP'];
            $settingsArrayNew['CAM4_LINK'] = $_POST['CAM4_LINK'];
            $settingsArrayNew['CAM4_LOW_LINK'] = $_POST['CAM4_LOW_LINK'];
            $settingsArrayNew['CAM4_MAN'] = $_POST['CAM4_MAN'];
        }

        file_put_contents($settingsFile, json_encode($settingsArrayNew));
        $settingsArrayNew['SETTINGS_TYPE'] = 'CAM';
        sendToTube("settings", json_encode($settingsArrayNew));

        $settings_saved_array = json_decode(file_get_contents($settings_saved, true), true);
        $settings_saved_array['CAM'] = date("d.m.y H:i:s P");
        file_put_contents($settings_saved, json_encode($settings_saved_array));

        header('Location: cam_config.php');
    }


    ?>
    <html>
    <head>
        <link href="style.css" rel="stylesheet" type="text/css">
        <title> -IP cams config- | Bitrek DVR</title>
    </head>
    <body>
    <table align=center border="0" cellpadding="0" cellspacing="0" class="tbl1" width="795">
        <tr>
            <td colspan="4"></td>
        </tr>
        <tr valign="top">
            <td width="60%" height="91"><img src="single_pixel.gif" width="0" height="93"></td>
            <td width="40%" height="91" colspan="3" valign="top" align="center">

            </td>
        </tr>
        <tr>
            <td colspan="3">
        <tr>
            <td valign="top" colspan="4">
                <table width="100%" border="0" cellspacing="15">
                    <tr>
                        <td width="20%" valign="top"><img src="single_pixel.gif" width="60" height="1"><br>
                            <div id="navcontainer">
                                <ul id="navlist">
                                    <li><a href="./main.php" title="DVR main information">Main information</a></li>
                                    <li><a href="./system.php" title="System configuration">System settings</a></li>
                                    <li><a href="./tracker.php" title="Tracker configuration">Tracker settings</a></li>
                                    <li><a href="./enet.php" title="FTP server configuration">FTP settings</a></li>
                                    <li><a href="./openvpn.php" title="OpenVPN client configuration">VPN client</a></li>
                                    <li><a href="./gsm.php" title="GSM APN and dialing number configuration">GSM
                                            settings</a></li>
                                    <li><a href="./wifi.php" title="WiFi configuration">WiFi settings
                                        </a></li>
                                    <li><a href="./connect.php" title="CONNECT read and write">CONNECT
                                            settings</a></li>
                                    <li style="background: #003d66"><a href="./cam_config.php"
                                                                       title="IP cameras link,port and quality configuration">IP
                                            cameras
                                            settings</a></li>
                                    <li><a href="./cam_stream.php" title="Live stream configuration">Live stream
                                            settings</a></li>
                                    <li><a href="./cam_cv.php"
                                           title="DVR reaction for different types of content events">Event capture
                                            settings</a></li>
                                    <li><a href="./cycle.php"
                                           title="DVR loop recording">Loop recording</a></li>
                                    <li><a href="./cam_test.php" title="DVR test page">mDVR
                                            test</a></li>
                                    <li><a href="./fm/" target="_blank" title="mDVR internal file storage">Files
                                            storage</a></li>
                                    <li><a href="./index.php" title="Exit to authorization page">Exit</a></li>
                                </ul>
                            </div>
                        </td>
                        <td width="100%" valign="top">
                            <h2 class="customfont">IP cameras settings</h2>

                            <style>
                                .input {
                                    padding: .5em .6em;
                                    display: inline-block;
                                    border: 1px solid #ccc;
                                    box-shadow: inset 0 1px 3px #ddd;
                                    border-radius: 4px;
                                    vertical-align: middle;
                                    box-sizing: border-box;
                                    height: 35px;
                                }

                                #table_cams {
                                    border-radius: 5px;
                                    box-shadow: 0 0 3px black;
                                }

                                #checkbox {
                                    color: red;
                                }

                                #cell_free {
                                    border-width: 0;
                                }
                            </style>
                            <style type="text/css">
                                @font-face {
                                    font-family: "My Custom Font";
                                    src: url("ethnocentric rg.ttf") format("truetype");
                                    text-decoration: none;
                                }

                                h2.customfont {
                                    font-family: "My Custom Font", Verdana, Tahoma;
                                    margin-top: 1px;
                                }

                                h3.customfont {
                                    font-family: "My Custom Font", Verdana, Tahoma;

                                }
                            </style>
                            <p><a href="manuals/camera_config_manual_RU.pdf" target="_blank">Description manual(RU)</a>
                            </p>
                            <h5>Settings last saved at <?= $settings_saved_array['CAM'] ?></h5>
                            <form action="cam_config.php" method="post">
                                <table id="table_cams" width="600" border="1">
                                    <?php
                                    function stest($ip, $portt)
                                    {
                                        $fp = @fsockopen($ip, $portt, $errno, $errstr, 0.1);
                                        if (!$fp) {
                                            return 'OFFLINE';
                                        } else {
                                            fclose($fp);
                                            return 'ONLINE';
                                        }
                                    }

                                    $params["CAM1_STATE"] = stest($settingsArray['CAM1_IP'], 554);
                                    $params["CAM2_STATE"] = stest($settingsArray['CAM2_IP'], 554);
                                    $params["CAM3_STATE"] = stest($settingsArray['CAM3_IP'], 554);
                                    $params["CAM4_STATE"] = stest($settingsArray['CAM4_IP'], 554);
                                    ?>
                                    <tr>
                                        <td bgcolor="#696969"></td>
                                        <td align="center" bgcolor="#696969"><b style="color: white">State</b></td>
                                        <td align="center" bgcolor="#696969"><b style="color: white">Connection
                                                state</b>
                                        </td>
                                        <td align="center" bgcolor="#696969"><b style="color: white">Audio</b>
                                        </td>
                                        <td align="center" bgcolor="#696969"><b style="color: white">Type</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">IP camera 1
                                            </b></td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM1_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM1_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td align="center">
                                            <b><?= $params["CAM1_STATE"] ?></b>
                                        </td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM1_AUDIO_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM1_AUDIO_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <select name="CAM1_MAN">
                                                <option <?= $select_array['CAM1_MAN_XM'] ?> value="XM">Bitrek(XM)
                                                </option>
                                                <option <?= $select_array['CAM1_MAN_DAHUA'] ?> value="DAHUA">
                                                    Bitrek(Dahua)
                                                </option>
                                                <option <?= $select_array['CAM1_MAN_OTHER'] ?> value="OTHER">Other
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">IP camera 2
                                            </b></td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM2_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM2_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <b><?= $params["CAM2_STATE"] ?></b>
                                        </td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM2_AUDIO_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM2_AUDIO_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <select name="CAM2_MAN">
                                                <option <?= $select_array['CAM2_MAN_XM'] ?> value="XM">Bitrek(XM)
                                                </option>
                                                <option <?= $select_array['CAM2_MAN_DAHUA'] ?> value="DAHUA">
                                                    Bitrek(Dahua)
                                                </option>
                                                <option <?= $select_array['CAM2_MAN_OTHER'] ?> value="OTHER">Other
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">IP camera 3
                                            </b></td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM3_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM3_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <b><?= $params["CAM3_STATE"] ?></b>
                                        </td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM3_AUDIO_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM3_AUDIO_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <select name="CAM3_MAN">
                                                <option <?= $select_array['CAM3_MAN_XM'] ?> value="XM">Bitrek(XM)
                                                </option>
                                                <option <?= $select_array['CAM3_MAN_DAHUA'] ?> value="DAHUA">
                                                    Bitrek(Dahua)
                                                </option>
                                                <option <?= $select_array['CAM3_MAN_OTHER'] ?> value="OTHER">Other
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">IP camera 4
                                            </b></td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM4_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM4_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <b><?= $params["CAM4_STATE"] ?></b>
                                        </td>
                                        <td align="center"><label class="switch">
                                                <input <?php if ($settingsArray["CAM4_AUDIO_STATE"] == "ON") {
                                                    echo "checked";
                                                } ?> type="checkbox"
                                                     name="CAM4_AUDIO_STATE">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <select name="CAM4_MAN">
                                                <option <?= $select_array['CAM4_MAN_XM'] ?> value="XM">Bitrek(XM)
                                                </option>
                                                <option <?= $select_array['CAM4_MAN_DAHUA'] ?> value="DAHUA">
                                                    Bitrek(Dahua)
                                                </option>
                                                <option <?= $select_array['CAM4_MAN_OTHER'] ?> value="OTHER">Other
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td colspan="3" align="center">
                                            <button style="width:120px;height:30px" type="submit" name="dvr_save"
                                            >Set state!
                                            </button>
                                        </td>
                                        <td align="center">
                                            <button style="width:140px;height:30px" type="submit" name="cam_scan">Scan
                                                network
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                                if (isset($_POST['cam_scan'])) {
                                    echo "<br><br><table width='25%' border='1' style='border-collapse: collapse'>";
                                    $temp = shell_exec('python3 /scr/scripts/cameras/get_cams_as_json.py');
                                    file_put_contents($camAssignTemp, $temp);
                                    $temp = json_decode($temp, true);
                                    echo "<table id=\"table_cams\" width=\"600\" border=\"1\">";
                                    if (sizeof($temp) == 0) {
                                        echo "<tr><td colspan=\"5\" bgcolor=\"#E77654\" align=\"center\"><b style=\"color: white\">No cameras found!</b></td></tr>";
                                    } else {


                                        echo "<tr><td colspan=\"5\" bgcolor=\"#696969\" align=\"center\"><b style=\"color: white\">Found cameras:</b></td></tr>";
                                        echo "<tr><td align=\"center\" width='20%' bgcolor=\"#00558b\"><b style=\"color: snow\">IP address</b></td>";
                                        echo "<td align=\"center\" width='20%' bgcolor=\"#00558b\"><b style=\"color: snow\">Manufacturer</b></td>";
                                        echo "<td align=\"center\" width='20%' bgcolor=\"#00558b\"><b style=\"color: snow\">Serial</b></td>";
                                        echo "<td align=\"center\" width='20%' bgcolor=\"#00558b\"><b style=\"color: snow\">Assign</b></td></tr>";
                                        $cur_cam = 0;
                                        foreach ($temp as $t) {
                                            $cur_cam++;
                                            if ($t["manufacturer"] != "H264") {
                                                echo "<tr><td align='center' style='padding: 5px'><a href=\"http://" . $t["ip"] . "\"><b>" . $t["ip"] . "</b></a></td ><td align='center' style='padding: 5px'><b>" . $t["manufacturer"] . "</b></td><td align='center' style='padding: 5px'>" . $t["serial"] . "</td><td align=\"center\"><select name=\"CAM" . $cur_cam . "_ASSIGN\"><option value=\"0\">――――</option><option value=\"192.168.1.10\">Camera 1</option><option value=\"192.168.1.11\">Camera 2</option><option value=\"192.168.1.12\">Camera 3</option><option value=\"192.168.1.13\">Camera 4</option></select></td></tr>";
                                            } else {
                                                $XM = "XM";
                                                echo "<tr><td align='center' style='padding: 5px'><a href=\"http://" . $t["ip"] . "\"><b>" . $t["ip"] . "</b></a></td ><td align='center' style='padding: 5px'><b>" . $XM . "</b></td><td align='center' style='padding: 5px'>" . $t["serial"] . "</td><td align=\"center\"><select name=\"CAM" . $cur_cam . "_ASSIGN\"><option value=\"0\">――――</option><option value=\"192.168.1.10\">Camera 1</option><option value=\"192.168.1.11\">Camera 2</option><option value=\"192.168.1.12\">Camera 3</option><option value=\"192.168.1.13\">Camera 4</option></select></td></tr>";

                                            }
//                                            echo "<tr><td align='center' style='padding: 5px'><a href=\"http://" . $t["ip"] . "\"><b>" . $t["ip"] . "</b></a></td ><td align='center' style='padding: 5px'><input type='text' size='5' value=\"" . $t["manufacturer"] . "\"></td><td align='center' style='padding: 5px'>" . $t["serial"] . "</td><td align=\"center\"><select name=\"CAM" . $cur_cam . "_ASSIGN\"><option value=\"0\">――――</option><option value=\"1\">Camera 1</option><option value=\"2\">Camera 2</option><option value=\"3\">Camera 3</option><option value=\"4\">Camera 4</option></select></td></tr>";

                                        }
                                        echo "</tr><tr><td align='center' colspan='10'><button style=\"width:120px;height:30px\" type=\"submit\" name=\"assign_save\">Set state!</button></td></tr>";

//                                            echo "<tr><td align=\"center\" width='20%' bgcolor=\"#dddddd\"><a href=http://192.168.1.10><b style=\"color: black\">192.168.1.10</b></a></td><td align=\"center\" width='20%' bgcolor=\"#dddddd\"><b style=\"color: black\">Dahua</b></td><td align=\"center\" width='34%' bgcolor=\"#dddddd\"><p style=\"color: black\">5D02576PAG24219</p></td> <td align=\"center\"> <select name=\"CAM1_AS\"><option " . $select_array['CAM1_AS_1'] . " value=\"1\">1</option><option " . $select_array['CAM1_AS_2'] . "value=\"2\"> 2</option><option " . $select_array['CAM1_AS_3'] . " value=\"3\">3</option><option" . $select_array['CAM1_AS_4'] . " value=\"4\">4</option> </select></td></tr>";

                                    }

                                    echo "</table>";

                                }
                                ?>
                                <br>
                                <?php if ($settingsArray['CAM1_STATE'] == 'ON') {
                                    echo "<details open=\"open\">";
                                } else {
                                    echo "<details>";
                                }
                                ?>
                                <summary>IP camera 1 settings</summary>
                                <table id="table_cams" width="600" border="1">
                                    <tr>
                                        <td colspan="2" bgcolor="#696969" align="center"><b style="color: white">IP
                                                camera 1 settings</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2">
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td align="center" bgcolor="#00558b"><b style="color: snow">IP
                                                            address</b></td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM1_IP"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArray['CAM1_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArray['CAM1_IP'] ?>">
                                                    </td>
                                                    <td align="center" bgcolor="#00558b"><b
                                                                style="color: snow">Gateway</b>
                                                    </td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM1_GW"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArrayNetwork['LAN_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArrayNetwork['LAN_IP'] ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">High
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td width="50%" align="left"><input size="39"
                                                                            type="url"
                                                                            class="input"
                                                                            name="CAM1_LINK"
                                                                            required
                                                                            value="<?= $settingsArray['CAM1_LINK'] ?>"
                                                                            placeholder="Value <?= $settingsArray['CAM1_LINK'] ?>">
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">Low
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td align="left"><input size="39" type="url"

                                                                class="input"
                                                                name="CAM1_LOW_LINK"
                                                                required
                                                                value="<?= $settingsArray['CAM1_LOW_LINK'] ?>"
                                                                placeholder="Value <?= $settingsArray['CAM1_LOW_LINK'] ?>">
                                        </td>

                                    </tr>
                                </table>
                                </details>
                                <br>
                                <?php if ($settingsArray['CAM2_STATE'] == 'ON') {
                                    echo "<details open=\"open\">";
                                } else {
                                    echo "<details>";
                                }
                                ?>
                                <summary>IP camera 2 settings</summary>
                                <table id="table_cams" width="600" border="1">
                                    <tr>
                                        <td colspan="2" bgcolor="#696969" align="center"><b style="color: white">IP
                                                camera 2 settings</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2">
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td align="center" bgcolor="#00558b"><b style="color: snow">IP
                                                            address</b></td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM2_IP"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArray['CAM2_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArray['CAM2_IP'] ?>">
                                                    </td>
                                                    <td align="center" bgcolor="#00558b"><b
                                                                style="color: snow">Gateway</b>
                                                    </td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM2_GW"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArrayNetwork['LAN_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArrayNetwork['LAN_IP'] ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">High
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td width="50%" align="left"><input size="39"
                                                                            type="url"
                                                                            class="input"
                                                                            name="CAM2_LINK"
                                                                            required
                                                                            value="<?= $settingsArray['CAM2_LINK'] ?>"
                                                                            placeholder="Value <?= $settingsArray['CAM2_LINK'] ?>">
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">Low
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td align="left"><input size="39" type="url"

                                                                class="input"
                                                                name="CAM2_LOW_LINK"
                                                                required
                                                                value="<?= $settingsArray['CAM2_LOW_LINK'] ?>"
                                                                placeholder="Value <?= $settingsArray['CAM2_LOW_LINK'] ?>">
                                        </td>

                                    </tr>
                                </table>
                                </details>
                                <br>
                                <?php if ($settingsArray['CAM3_STATE'] == 'ON') {
                                    echo "<details open=\"open\">";
                                } else {
                                    echo "<details>";
                                }
                                ?>
                                <summary>IP camera 3 settings</summary>
                                <table id="table_cams" width="600" border="1">
                                    <tr>
                                        <td colspan="2" bgcolor="#696969" align="center"><b style="color: white">IP
                                                camera 3 settings</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2">
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td align="center" bgcolor="#00558b"><b style="color: snow">IP
                                                            address</b></td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM3_IP"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArray['CAM3_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArray['CAM3_IP'] ?>">
                                                    </td>
                                                    <td align="center" bgcolor="#00558b"><b
                                                                style="color: snow">Gateway</b>
                                                    </td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM3_GW"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArrayNetwork['LAN_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArrayNetwork['LAN_IP'] ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">High
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td width="50%" align="left"><input size="39"
                                                                            type="url"
                                                                            class="input"
                                                                            name="CAM3_LINK"
                                                                            required
                                                                            value="<?= $settingsArray['CAM3_LINK'] ?>"
                                                                            placeholder="Value <?= $settingsArray['CAM3_LINK'] ?>">
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">Low
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td align="left"><input size="39" type="url"

                                                                class="input"
                                                                name="CAM3_LOW_LINK"
                                                                required
                                                                value="<?= $settingsArray['CAM3_LOW_LINK'] ?>"
                                                                placeholder="Value <?= $settingsArray['CAM3_LOW_LINK'] ?>">
                                        </td>

                                    </tr>
                                </table>
                                </details>
                                <br>
                                <?php if ($settingsArray['CAM4_STATE'] == 'ON') {
                                    echo "<details open=\"open\">";
                                } else {
                                    echo "<details>";
                                }
                                ?>
                                <summary>IP camera 4 settings</summary>
                                <table id="table_cams" width="600" border="1">
                                    <tr>
                                        <td colspan="2" bgcolor="#696969" align="center"><b style="color: white">IP
                                                camera 4 settings</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center" colspan="2">
                                            <table width="100%" border="1">
                                                <tr>
                                                    <td align="center" bgcolor="#00558b"><b style="color: snow">IP
                                                            address</b></td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM4_IP"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArray['CAM4_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArray['CAM4_IP'] ?>">
                                                    </td>
                                                    <td align="center" bgcolor="#00558b"><b
                                                                style="color: snow">Gateway</b>
                                                    </td>
                                                    <td align="center"><input type="text" size="10"
                                                                              class="input"
                                                                              name="CAM4_GW"
                                                                              minlength="7"
                                                                              maxlength="15"
                                                                              required
                                                                              pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}"
                                                                              value="<?= $settingsArrayNetwork['LAN_IP'] ?>"
                                                                              placeholder="Value <?= $settingsArrayNetwork['LAN_IP'] ?>">
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">High
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td width="50%" align="left"><input size="39"
                                                                            type="url"
                                                                            class="input"
                                                                            name="CAM4_LINK"
                                                                            required
                                                                            value="<?= $settingsArray['CAM4_LINK'] ?>"
                                                                            placeholder="Value <?= $settingsArray['CAM4_LINK'] ?>">
                                        </td>

                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#00558b"><b style="color: snow">Low
                                                resolution
                                                RTSP
                                                link</b></td>
                                        <td align="left"><input size="39" type="url"

                                                                class="input"
                                                                name="CAM4_LOW_LINK"
                                                                required
                                                                value="<?= $settingsArray['CAM4_LOW_LINK'] ?>"
                                                                placeholder="Value <?= $settingsArray['CAM4_LOW_LINK'] ?>">
                                        </td>

                                    </tr>
                                </table>
                                </details>
                                <br>
                                <table width="600">
                                    <tr>
                                        <td align="left">
                                            <button class="button" type="submit" name="set_defaults">Set default
                                                settings
                                            </button>
                                        </td>
                                        <td align="right">
                                            <button class="button" type="submit" name="dvr_save">Save to DVR
                                            </button>
                                        </td>
                                    </tr>

                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </body>
    </html>
    <?php
} else {
    header('Location: index.php');
}
?>