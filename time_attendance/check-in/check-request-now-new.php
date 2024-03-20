<?php
include("../database/connectdb.php");
date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");
$multiEmployee_token = random_bytes(16);
$multiEmployee_tokenSTR = str_replace(['+', '/', '='], '', base64_encode($multiEmployee_token));

$shiftIds = array(); // ประกาศใน global scope
$lastInsertedID = null; // ประกาศใน global scope

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //วัน
    $shift_start_date = isset($_POST['shift_start_date']) ? $_POST['shift_start_date'] : null;
    $shift_end_date = isset($_POST['shift_end_date']) ? $_POST['shift_end_date'] : null;

    $end_date = null;

    $start_date = (new DateTime($shift_start_date))->format('Y-m-d');

    //พิกัด ชื่อ
    $shift_coords_nameWork = isset($_POST['coords_nameWork']) ? $_POST['coords_nameWork'] : null;
    $shift_coords_nameIN = isset($_POST['coords_nameIN']) ? $_POST['coords_nameIN'] : null;
    $shift_coords_nameOUT = isset($_POST['coords_nameOUT']) ? $_POST['coords_nameOUT'] : null;

    //พิกัด lat lng
    $check_in_coords_str = isset($_POST['check_in_coords_str']) ? $_POST['check_in_coords_str'] : null;
    $check_out_coords_str = isset($_POST['check_out_coords_str']) ? $_POST['check_out_coords_str'] : null;
    $check_both_coordinates_str = isset($_POST['check_both_coordinates_str']) ? $_POST['check_both_coordinates_str'] : null;
    $check_coords_range = isset($_POST['coords_range']) ? $_POST['coords_range'] : null;

    $gps_type = isset($_POST['gps_type']) ? $_POST['gps_type'] : null;

    $requested_employee_id = isset($_POST['employee']) ? $_POST['employee'] : null;

    //ผู้ตรวจสอบและอนุมัติ
    $approverID = isset($_POST['approverID']) ? $_POST['approverID'] : null;
    $inspectorID = isset($_POST['inspectorID']) ? $_POST['inspectorID'] : null;

    $headman = isset($_POST['headman']) ? $_POST['headman'] : null;

    //-------------------------------------------------------------------------------//

    if (isset($_POST['employeesList']) && isset($_POST['employee'])) {

        $employeeList = json_decode($_POST['employeesList'], true);

        if (is_array($employeeList) && !empty($employeeList)) {

            $insert_shift_work_tempo = "INSERT INTO shift_work_location_temporary_request (card_id, request_card_id, shift_start_date, input_timestamp, multi_employee_status, multi_employee_token, approver) 
                                        OUTPUT INSERTED.shift_id
                                        VALUES (?, ?, ?, ?, ?, ?, ?)
                                        ";

            foreach ($employeeList as $employee) {
                $params = array($employee['card_id'], $requested_employee_id, $start_date, $time_stamp, "1", $multiEmployee_tokenSTR, $approverID);

                $stmt = sqlsrv_query($conn, $insert_shift_work_tempo, $params);

                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                    echo "ERROR";
                }

                $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                $arrayInsertedID = $row['shift_id'];

                $shiftIds[] = $arrayInsertedID;

                if ($inspectorID) {
                    $update_inspectorSQL = "UPDATE shift_work_location_temporary_request SET inspector = ?
                                            WHERE multi_employee_token = ?";
                    $update_inspectorSQL_Para = array($inspectorID, $multiEmployee_tokenSTR);
                    $update_inspectorSQL_Stmt = sqlsrv_prepare($conn, $update_inspectorSQL, $update_inspectorSQL_Para);

                    if (sqlsrv_execute($update_inspectorSQL_Stmt) === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                }

                if ($headman){
                    $update_headmanBypassSQL = "UPDATE shift_work_location_temporary_request SET inspector_id = ?, approval_id = ?
                                                     WHERE multi_employee_token = ? ";
                    $update_headmanBypassSQL_Para = array(6, 3, $multiEmployee_tokenSTR);
                    $update_headmanBypassSQL_Stmt = sqlsrv_prepare($conn, $update_headmanBypassSQL, $update_headmanBypassSQL_Para);

                    if (sqlsrv_execute($update_headmanBypassSQL_Stmt) === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    //หัวหน้าเซ็ตให้
                }

                sqlsrv_free_stmt($stmt);
            }
        }
    } else if (isset($_POST['employee'])) {

        $insert_shift_work_tempo = "INSERT INTO shift_work_location_temporary_request (card_id, request_card_id, shift_start_date, input_timestamp, approver) 
                                    OUTPUT INSERTED.shift_id
                                    VALUES (?, ?, ?, ?, ?)
                                    ";

        $params = array($_POST['employee'], $_POST['employee'], $start_date, $time_stamp, $approverID);
        $stmt = sqlsrv_query($conn, $insert_shift_work_tempo, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
            echo "ERROR";
        }

        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $lastInsertedID = $row['shift_id'];

        sqlsrv_free_stmt($stmt);

        if ($inspectorID) {
            $update_inspectorSQL = "UPDATE shift_work_location_temporary_request SET inspector = ?";
            $update_inspectorSQL_Para = array($inspectorID);
            $update_inspectorSQL_Stmt = sqlsrv_prepare($conn, $update_inspectorSQL, $update_inspectorSQL_Para);

            if (sqlsrv_execute($update_inspectorSQL_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            //ร้องข้อถ้ามีผุ้ตรวจสอบ
        }
    }


    // if (!empty($shiftIds)) {
    //     print_r($shiftIds);
    // }

    // if (!empty($lastInsertedID)) {
    //     echo "Single: " . $lastInsertedID;
    // }

    if ($shift_end_date != null) {

        if ($shift_end_date !== null) {
            $end_date = (new DateTime($shift_end_date))->format('Y-m-d');
        } else {
            $end_date = null;
        }

        if (!empty($lastInsertedID)) {
            $UPDATE_time = "UPDATE shift_work_location_temporary_request SET shift_end_date = ? 
                                WHERE shift_id = ? 
                            ";
            $UPDATE_time_para = array($end_date, $lastInsertedID);
            $stmt = sqlsrv_query($conn, $UPDATE_time, $UPDATE_time_para);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }

        if (!empty($shiftIds)) {

            // นำ arrayมาแปลงเตรียมสำหรับ SQL query
            $shiftIdsString = implode(',', array_map(function ($id) {
                // return "'" . $id . "'";
                return $id;
            },  $shiftIds));

            $UPDATE_time = "UPDATE shift_work_location_temporary_request SET shift_end_date = ? 
                                WHERE shift_id IN ($shiftIdsString)
                        
                             ";
            $UPDATE_time_para = array($end_date, $multiEmployee_tokenSTR);
            $stmt = sqlsrv_query($conn, $UPDATE_time, $UPDATE_time_para);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }


    $INSERT_coords = "INSERT INTO location_coords_default (coords_name, coords_in_name, coords_out_name, coords_in_lat_lng, coords_out_lat_lng, coords_range, coords_type) 
                        OUTPUT INSERTED.coords_id
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                            ";

    $insert_coords_Stmt = null;
    $lastInsertedID_coords = null;

    if ($gps_type == '1') {
        // echo "จุดเดียว";
        $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameIN, $check_both_coordinates_str, $check_both_coordinates_str, $check_coords_range, "จุดเดียวกัน");
        $insert_coords_Stmt = sqlsrv_query($conn, $INSERT_coords,  $sql_coords_para);

        if ($insert_coords_Stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    if ($gps_type == '2') {
        // echo "คนละจุด";
        $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameOUT, $check_in_coords_str, $check_out_coords_str, $check_coords_range , "คนละจุด");
        $insert_coords_Stmt = sqlsrv_query($conn, $INSERT_coords,  $sql_coords_para);

        if ($insert_coords_Stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }

    if ($insert_coords_Stmt != null) {

        $row = sqlsrv_fetch_array($insert_coords_Stmt, SQLSRV_FETCH_ASSOC);
        $lastInsertedID_coords = $row['coords_id'];

    }


    if ($lastInsertedID_coords != null) {

        if (!empty($lastInsertedID)) {
            $UPDATE_time = "UPDATE shift_work_location_temporary_request SET coords_id = ? 
                                WHERE shift_id = ? ";
            $UPDATE_time_para = array($lastInsertedID_coords, $lastInsertedID);
            $stmt = sqlsrv_query($conn, $UPDATE_time, $UPDATE_time_para);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }

        if (!empty($shiftIds)) {

            // นำ arrayมาแปลงเตรียมสำหรับ SQL query
            $shiftIdsString = implode(',', array_map(function ($id) {
                // return "'" . $id . "'";
                return $id;
            },  $shiftIds));

            $UPDATE_time = "UPDATE shift_work_location_temporary_request SET coords_id = ? 
                                WHERE shift_id IN ($shiftIdsString)
                        
                             ";
            $UPDATE_time_para = array($lastInsertedID_coords, $multiEmployee_tokenSTR);
            $stmt = sqlsrv_query($conn, $UPDATE_time, $UPDATE_time_para);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
    if ($inspectorID !="" && $approverID !="" ){
        notify_inspector($inspectorID,$time_stamp);
    }elseif($inspectorID =="" && $approverID !="" ){
        notify_approver($approverID,$time_stamp);
    }
    echo "work-shift-tempo-success";

} else {
    echo 'ไม่มีข้อมูลที่ถูกส่งมา';
}


function notify_inspector($id,$time_stamp){

    global $conn;
    
    $querynotify = "SELECT * FROM login 
                    RIGHT JOIN employee ON login.card_id = employee.card_id
                    WHERE login.card_id = ?";
    $params = array($id);
    $stmtnotify = sqlsrv_query($conn, $querynotify, $params);
    $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);
    
        $url = 'https://notify-api.line.me/api/notify';
        
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $row['line_token']
        );
        $message = 'โปรดตรวจสอบ' . "\n" . 'คำร้องทำงานนอกสถานที่' . "\n" . 'ของคุณ ' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "\n" . 'เมื่อเวลา ' . $time_stamp . "\n" . 'ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ';
    
        // Use cURL to send the notification
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    
    }
    function notify_approver($id,$time_stamp){
        global $conn;

        $querynotify = "SELECT * FROM login 
        RIGHT JOIN employee ON login.card_id = employee.card_id
        WHERE login.card_id = ?";
        $params = array($id);
        $stmtnotify = sqlsrv_query($conn, $querynotify, $params);
        $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

        $url = 'https://notify-api.line.me/api/notify';

        $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
        );
        $message = 'โปรดอนุมัติ' . "\n" . 'คำร้องทำงานนอกสถานที่' . "\n" . 'ของคุณ ' . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . "\n" . 'เมื่อเวลา ' . $time_stamp . "\n" . 'ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ';

        // Use cURL to send the notification
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        
    }