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
    <table id="main_table">
        <table id="sub_table">
            <tr>
                <td>

                    <input value="SCAN" type="submit" name="scan">
                    <?php
                    //ini_set('error_reporting', E_ALL);
                    //ini_set('display_errors', 1);
                    //ini_set('display_startup_errors', 1);

                    if (isset($_POST['scan'])) {

                        echo "<br>";
                        echo "<br>";
                        echo "Finded ONVIF device:";
                        echo "<br>";
                        $temp = shell_exec('python3 /home/shyneko/PycharmProjects/onvif/get_cams_as_json.py');

                        $temp = json_decode($temp, true);
//                        var_dump($temp);
                        foreach ($temp as $t) {
                            echo($t["ip"] . ":" . $t["port"] . " -> " . $t["manufacturer"]);
                            echo "<br>";
                        }
                    }
                    ?>

                </td>
            </tr>
        </table>
    </table>
</form>

</body>

</html>