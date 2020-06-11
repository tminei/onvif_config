<html>

<head>
    <style>
        #main_table {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        #sub_table {
            width: 100%;
            padding: 0;
            margin: 0;
        }
    </style>
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Project list</title>
</head>
<body>
<form action="index.php" method="post">


    <input value="SCAN" type="submit" name="scan">
    <?php
    //ini_set('error_reporting', E_ALL);
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);

    if (isset($_POST['scan'])) {

        echo "<br><br><table width='25%' border='1' style='border-collapse: collapse'>";
        $temp = shell_exec('python3 /home/shyneko/PycharmProjects/onvif/get_cams_as_json.py');

        $temp = json_decode($temp, true);
//                        var_dump($temp);
        echo "<tr><td  align='center' bgcolor='#f5deb3' style=' padding: 5px' colspan='3'>Found ONVIF device:</td></tr><tr><td  bgcolor='#dcdcdc' align='center' style='padding: 5px'>IP</td><td bgcolor='#dcdcdc' align='center' style='padding: 5px'>PORT</td><td  bgcolor='#dcdcdc' align='center' style='padding: 5px'>MANUFACTURER</td></tr>";
        foreach ($temp as $t) {
            echo "<tr><td align='center' style='padding: 5px'>";
            echo("<a href=\"http://" . $t["ip"] . "\">" . $t["ip"] . "</a></td ><td align='center' style='padding: 5px'>" . "" . $t["port"] . "</td><td align='center' style='padding: 5px'>" . $t["manufacturer"] . "</td>");
//            echo "<br>";
            echo "</td></tr>";
        }
        echo "</table>";
    }
    ?>


</form>

</body>

</html>