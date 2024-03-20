<?php
include("../database/connectdb.php");
// include("dbconnect.php");
date_default_timezone_set('Asia/Bangkok');

//ข้ามการเช็ก gps
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $check_type = $_POST["check_type"];

    if ($check_type == 'gps_type') {

        $card_id = $_POST["gps-card-id"];

        $in_coords = $_POST["gps-in-coords"];
        $out_coords = $_POST["gps-out-coords"];

        $day_stamp = date("Y-m-d");
        $time_stamp = date("H:i:s");

        $day_input = $_POST["gps-date"];
        $time_input = $_POST["gps-time"];

        $SELECT = "SELECT * FROM check_inout WHERE card_id = ? AND date = ?";
        $SELECTParams = array(&$card_id, &$day_stamp);
        $rs_SELECT = sqlsrv_query($conn, $SELECT, $SELECTParams);

        if ($rs_SELECT === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($rs_SELECT)) {

            $rs_SELECT = sqlsrv_fetch_array($rs_SELECT, SQLSRV_FETCH_ASSOC);

            if ($in_coords !== null && $rs_SELECT['time_in'] !== null && $out_coords == null) {

                echo 'already-checked-in';
                exit();
            } 
            // else if ($rs_SELECT['time_in'] !== null && $rs_SELECT['time_out'] !== null) {

            //     echo 'already-checked-out';
            //     exit();
            // } 
            else if ($out_coords !== null && ($rs_SELECT['time_out'] == null || $rs_SELECT['time_out'] == !null)) {

                $updateQuery = "UPDATE check_inout SET time_out = ?, location_out = ? WHERE card_id = ?";
                $updateParams = array(&$time_stamp, &$out_coords, &$card_id);
                $updateStmt = sqlsrv_prepare($conn, $updateQuery, $updateParams);

                if (sqlsrv_execute($updateStmt) === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                echo 'check-out-complete';
                exit();
            };

            //-------------------------------------------------------------------------------------------------------------
        } else if (sqlsrv_has_rows($rs_SELECT) !== null && $out_coords && !$in_coords) {

            echo 'not-check-in';
            exit();
        } else {
            $insertQuery = "INSERT INTO check_inout (card_id, date, time_in, location_in) VALUES (?, ?, ?,?)";
            $insertParams = array($card_id, $day_stamp, $time_stamp, $in_coords);
            $insertStmt = sqlsrv_prepare($conn, $insertQuery, $insertParams);

            if (sqlsrv_execute($insertStmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo 'check-in-complete';
            exit();
        }
    } else if ($check_type == 'wifi_type') {

        function performSubnetValidation($ip, $subnetStart, $subnetEnd)
        {
            $ip = ip2long($ip); //แปลง IPv4 เป็น long int
            $subnetStart = ip2long($subnetStart);
            $subnetEnd = ip2long($subnetEnd);
            return ($ip >= $subnetStart && $ip <= $subnetEnd); //ไอพี Client มากกว่าหรือเท่ากับ subnet เริ่มต้น และไอพี Client น้อยกว่าหรือเท่ากับ subnet ท้ายสุด
        }

        $card_id = $_POST["wifi-card-id"];
        $coords = $_POST["client-coords"];
        $clientIP = $_POST["wifi-ip-subnet"];

        $day_stamp = date("Y-m-d");
        $time_stamp = date("H:i:s");

        $day_input = $_POST["wifi-date"];
        $time_input = $_POST["wifi-time"];

        $isSubnetValid = performSubnetValidation($clientIP, '172.16.0.0', '172.16.10.255');

        if ($isSubnetValid) {

            $SELECT = "SELECT * FROM check_inout WHERE card_id = ? AND date = ?";
            $SELECTParams = array($card_id, $day_stamp);
            $rs_SELECT = sqlsrv_query($conn, $SELECT, $SELECTParams);

            if ($rs_SELECT === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_has_rows($rs_SELECT)) {

                $rs_SELECT = sqlsrv_fetch_array($rs_SELECT, SQLSRV_FETCH_ASSOC);

                if ($rs_SELECT['time_in'] !== null) {

                    echo 'already-checked-in';
                    exit();
                } else if ($rs_SELECT['time_out'] !== null) {

                    echo 'already-checked-out';
                    exit();
                } else {
                    if ($coords) {

                        $updateQuery = "UPDATE check_inout SET time_out = ?, location_out = ? WHERE card_id = ?";
                        $updateParams = array(&$time_stamp, &$coords, &$card_id);
                        $updateStmt = sqlsrv_prepare($conn, $updateQuery, $updateParams);

                        if (sqlsrv_execute($updateStmt) === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        echo 'check-out-completes';
                        exit();
                    } else {

                        $updateQuery = "UPDATE check_inout SET time_out = ? WHERE card_id = ?";
                        $updateParams = array(&$time_stamp, &$card_id);
                        $updateStmt = sqlsrv_prepare($conn, $updateQuery, $updateParams);

                        if (sqlsrv_execute($updateStmt) === false) {
                            die(print_r(sqlsrv_errors(), true));
                        }

                        echo 'check-out-completes';
                        exit();
                    }
                };

                //-------------------------------------------------------------------------------------------------------------
            } else {

                if ($coords) {

                    $insertQuery = "INSERT INTO check_inout (card_id, date, time_in, location_in) VALUES (?, ?, ?, ?)";
                    $insertParams = array(&$card_id, &$day_stamp, &$time_stamp, &$coords);
                    $insertStmt = sqlsrv_prepare($conn, $insertQuery, $insertParams);

                    if (sqlsrv_execute($insertStmt) === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    echo 'check-in-completes';
                    exit();
                } else {


                    $insertQuery = "INSERT INTO check_inout (card_id, date, time_in) VALUES (?, ?, ?)";
                    $insertParams = array(&$card_id, &$day_stamp, &$time_stamp);
                    $insertStmt = sqlsrv_prepare($conn, $insertQuery, $insertParams);

                    if (sqlsrv_execute($insertStmt) === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    echo 'check-in-completes';
                    exit();
                }
            }
        } else {
            echo 'Invalid-subnet';
        }
    } else if ($check_type == 'qr_type') {

        $card_id = $_POST["qr-card-id"];

        $in_coords = $_POST["qr-coords"];

        $day_stamp = date("Y-m-d");
        $time_stamp = date("H:i:s");

        $SELECT = "SELECT * FROM check_inout WHERE card_id = ? AND date = ?";
        $SELECTParams = array(&$card_id, &$day_stamp);
        $rs_SELECT = sqlsrv_query($conn, $SELECT, $SELECTParams);

        if ($rs_SELECT === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($rs_SELECT)) {

            $rs_SELECT = sqlsrv_fetch_array($rs_SELECT, SQLSRV_FETCH_ASSOC);

            $updateQuery = "UPDATE check_inout SET time_out = ?, location_out = ? WHERE card_id = ?";
            $updateParams = array(&$time_stamp, &$out_coords, &$card_id);
            $updateStmt = sqlsrv_prepare($conn, $updateQuery, $updateParams);

            if (sqlsrv_execute($updateStmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo 'check-out-complete';
            exit();

            //-------------------------------------------------------------------------------------------------------------
        } else {

            $insertQuery = "INSERT INTO check_inout (card_id, date, time_in, location_in) VALUES (?, ?, ?, ?)";
            $insertParams = array(&$card_id, &$day_stamp, &$time_stamp, &$coords);
            $insertStmt = sqlsrv_prepare($conn, $insertQuery, $insertParams);

            if (sqlsrv_execute($insertStmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            echo 'check-in-complete';
            exit();
        }
    } else {
        echo "Invalid-request!";
    }
}
