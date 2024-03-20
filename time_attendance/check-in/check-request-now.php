<?php
include("../database/connectdb.php");
date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");
$multiEmployee_token = random_bytes(16);
$multiEmployee_tokenSTR = str_replace(['+', '/', '='], '', base64_encode($multiEmployee_token));

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //วัน
    $shift_start_date = isset($_POST['shift_start_date']) ? $_POST['shift_start_date'] : null;
    $shift_end_date = isset($_POST['shift_end_date']) ? $_POST['shift_end_date'] : null;

    //พิกัด ชื่อ
    $shift_coords_nameWork = isset($_POST['coords_nameWork']) ? $_POST['coords_nameWork'] : null;
    $shift_coords_nameIN = isset($_POST['coords_nameIN']) ? $_POST['coords_nameIN'] : null;
    $shift_coords_nameOUT = isset($_POST['coords_nameOUT']) ? $_POST['coords_nameOUT'] : null;

    //พิกัด lat lng
    $check_in_coords_str = isset($_POST['check_in_coords_str']) ? $_POST['check_in_coords_str'] : null;
    $check_out_coords_str = isset($_POST['check_out_coords_str']) ? $_POST['check_out_coords_str'] : null;
    $check_both_coordinates_str = isset($_POST['check_both_coordinates_str']) ? $_POST['check_both_coordinates_str'] : null;

    $check_coords_range = isset($_POST['coords_range']) ? $_POST['coords_range'] : null;

    //ผู้ตรวจสอบและอนุมัติ
    $approverID = isset($_POST['approverID']) ? $_POST['approverID'] : null;
    $inspectorID = isset($_POST['inspectorID']) ? $_POST['inspectorID'] : null;



    if (isset($_POST['employeesList']) && isset($_POST['employee'])) { //แบบหลายคน


        $employeeList = json_decode($_POST['employeesList'], true);

        if (is_array($employeeList) && !empty($employeeList)) {

            $insert_shift_work_tempo = "INSERT INTO shift_work_location_temporary_request (card_id, request_card_id, input_timestamp, multi_employee_status, multi_employee_token) 
                                            VALUES (?, ?, ?, ?, ?)";

            foreach ($employeeList as $employee) {
                // $stringData = "Data: " . print_r($employee['employee_id'], true);
                // echo $stringData;
                $params = array($employee['employee_id'], $_POST['employee'],  $time_stamp, 1, $multiEmployee_tokenSTR);
                $stmt = sqlsrv_query($conn, $insert_shift_work_tempo, $params);
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                sqlsrv_free_stmt($stmt);
            }
            
            $INSERT_coords = "INSERT INTO location_coords_default (coords_name, coords_in_name, coords_out_name, coords_in_lat_lng, coords_out_lat_lng, coords_range) 
                                VALUES (?, ?, ?, ?, ?, ?)
                                ";

            $SELECT_coords = "SELECT coords_id FROM location_coords_default 
                                WHERE coords_name LIKE '%' + ? + '%' 
                                AND coords_in_lat_lng = ? 
                                AND coords_out_lat_lng = ?
                                ";

            if (($shift_coords_nameWork && $shift_coords_nameIN && $shift_coords_nameOUT) &&
                ($check_in_coords_str && $check_out_coords_str && !$check_both_coordinates_str)
            ) {

                $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameOUT, $check_in_coords_str, $check_out_coords_str, $check_coords_range);
                $sql_search_coords_para = array($shift_coords_nameWork, $check_in_coords_str, $check_out_coords_str);
            } else if (($shift_coords_nameWork && $shift_coords_nameIN) &&
                (!$check_in_coords_str && !$check_out_coords_str && $check_both_coordinates_str)
            ) {

                $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameIN, $check_both_coordinates_str, $check_both_coordinates_str, $check_coords_range);
                $sql_search_coords_para = array($shift_coords_nameWork, $check_both_coordinates_str, $check_both_coordinates_str);
            }

            $insert_coords_Stmt = sqlsrv_prepare($conn, $INSERT_coords,  $sql_coords_para);

            if (sqlsrv_execute($insert_coords_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $select_coords_Stmt = sqlsrv_prepare($conn, $SELECT_coords,  $sql_search_coords_para);

            if (sqlsrv_execute($select_coords_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $rs_SELECT = sqlsrv_fetch_array($select_coords_Stmt, SQLSRV_FETCH_ASSOC);
            $start_data = (new DateTime($shift_start_date))->format('Y-m-d');
            $end_date = (new DateTime($shift_end_date))->format('Y-m-d');

            // ใช้ array_map เพื่อดึง employee_id ออกมา
            $employeeIds = array_map(function ($item) {
                return $item['employee_id'];
            }, $employeeList);

            // นำรายชื่อพนักงานมาเตรียมสำหรับ SQL query
            $employeeIdsString = implode(',', array_map(function ($id) {
                return "'" . $id . "'";
            }, $employeeIds));

            
            if (!is_bool($employeeIdsString)) {

                $UPDATE_coords = "UPDATE shift_work_location_temporary_request SET coords_id = ? 
                                    WHERE request_card_id = ? 

                                    AND card_id IN ($employeeIdsString)
                             ";

                $sql_coords_para = array($rs_SELECT['coords_id'], $_POST['employee'], $start_data, $end_date);
                $update_coords_Stmt = sqlsrv_prepare($conn,  $UPDATE_coords, $sql_coords_para);

                if (sqlsrv_execute($update_coords_Stmt) === false) {
                    die(print_r(sqlsrv_errors(), true));
                    echo "ERORR 4";
                } else {
                    echo "work-shift-tempo-success";
                    // sqlsrv_free_stmt($update_coords_Stmt);
                    sqlsrv_close($conn);
                    exit();
                }
            } else {
                echo "ERROR: Invalid employee data";
            }
        } else {
            echo "ERROR: Empty or invalid employee data";
        }
    } else if (isset($_POST['employee'])) { //แบบคนเดียว

        $employee = $_POST['employee'];

        $insert_shift_work_tempo = "INSERT INTO shift_work_location_temporary_request (card_id, request_card_id, shift_start_date, shift_end_date, input_timestamp, approver) 
                                    VALUES (?, ?, ?, ?, ?, ?)
                                    ";
        //set แบบคนละจุด
        if (
            ($shift_coords_nameWork && $shift_coords_nameIN && $shift_coords_nameOUT)
            &&
            ($check_in_coords_str && $check_out_coords_str && !$check_both_coordinates_str)
        ) {

            $insert_SWT_Params = array($employee, $employee, $shift_start_date, $shift_end_date, $time_stamp, $approverID);
            $insert_SWT_Stmt = sqlsrv_prepare($conn, $insert_shift_work_tempo, $insert_SWT_Params);

            if (sqlsrv_execute($insert_SWT_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
                // echo "ERORR 1";
            }

            if ($inspectorID) {
                $update_inspectorSQL = "UPDATE shift_work_location_temporary_request SET inspector = ?";
                $update_inspectorSQL_Para = array($inspectorID);
                $update_inspectorSQL_Stmt = sqlsrv_prepare($conn, $update_inspectorSQL, $update_inspectorSQL_Para);
                if (sqlsrv_execute($update_inspectorSQL_Stmt) === false) {
                    die(print_r(sqlsrv_errors(), true));
                    // echo "ERROR 1";
                }
            }

            $sql_coords = "INSERT INTO location_coords_default (coords_name, coords_in_name, coords_out_name, coords_in_lat_lng, coords_out_lat_lng, coords_range) 
                                                                VALUES (?, ?, ?, ?, ?, ?)";

            $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameOUT, $check_in_coords_str, $check_out_coords_str, $check_coords_range);
            $insert_coords_Stmt = sqlsrv_prepare($conn, $sql_coords,  $sql_coords_para);

            if (sqlsrv_execute($insert_coords_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
                // echo "ERROR 2";
            }

            // $search_coords = "SELECT coords_id FROM location_coords_default 
            //                 WHERE coords_name LIKE '%' + ? + '%' 
            //                 AND coords_in_name LIKE '%' + ? + '%' 
            //                 AND coords_out_name LIKE '%' + ? + '%'
            //                 AND (coords_in_lat_lng = ? OR  coords_out_lat_lng = ?)
            //                 ";

            // $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameOUT, $check_in_coords_str, $check_out_coords_str);
            // $insert_coords_Stmt = sqlsrv_prepare($conn, $search_coords,  $sql_coords_para);

            // if (sqlsrv_execute($insert_coords_Stmt) === false) {
            //     die(print_r(sqlsrv_errors(), true));
                // echo "ERROR 3";
            // }
            // $rs_SELECT = sqlsrv_fetch_array($insert_coords_Stmt, SQLSRV_FETCH_ASSOC);


            $sqlGetLastID = "SELECT IDENT_CURRENT('location_coords_default') AS coords_id";
            $resultLastID = sqlsrv_query($conn, $sqlGetLastID);
        
            if ($resultLastID === false) {
                die(print_r(sqlsrv_errors(), true));
            }
    
            $rs_SELECT = sqlsrv_fetch_array($resultLastID, SQLSRV_FETCH_ASSOC);



            $start_data = (new DateTime($shift_start_date))->format('Y-m-d');
            $end_date = (new DateTime($shift_end_date))->format('Y-m-d');

            $sql_update_coords = "UPDATE shift_work_location_temporary_request SET coords_id = ? 
                                    WHERE card_id = ? 
                                    AND request_card_id = ?
                                    AND shift_start_date =  ?
                                    AND shift_end_date =  ?
                            
                            ";

            $sql_coords_para = array($rs_SELECT['coords_id'], $employee, $employee, $start_data, $end_date);
            $update_coords_Stmt = sqlsrv_prepare($conn, $sql_update_coords, $sql_coords_para);

            if (sqlsrv_execute($update_coords_Stmt) === false) {
                die(print_r(sqlsrv_errors(), true));
                echo "ERORR 4";
            } else {
                echo "work-shift-tempo-success";
                // sqlsrv_free_stmt($update_coords_Stmt);
                sqlsrv_close($conn);
                exit();
            }



            //set แบบจุดเดียวกัน    
        } else if (($shift_coords_nameWork && $shift_coords_nameIN) && (!$check_in_coords_str && !$check_out_coords_str && $check_both_coordinates_str)) {

            $insert_SWT_Params = array($employee, $employee, $shift_start_date, $shift_end_date, $time_stamp, $approverID);
            $insert_SWT_Stmt = sqlsrv_prepare($conn, $insert_shift_work_tempo, $insert_SWT_Params);

            if (sqlsrv_execute($insert_SWT_Stmt) === false) {
                print_r(sqlsrv_errors(), true);
                echo "ERORR 1";
            } else {

                if ($inspectorID) {
                    $update_inspectorSQL = "UPDATE shift_work_location_temporary_request SET inspector = ?, inspector_id = ?";
                    $update_inspectorSQL_Para = array($inspectorID, 5);
                    $update_inspectorSQL_Stmt = sqlsrv_prepare($conn, $update_inspectorSQL, $update_inspectorSQL_Para);

                    if (sqlsrv_execute($update_inspectorSQL_Stmt) === false) {
                        die(print_r(sqlsrv_errors(), true));
                        // echo "ERORR 1";
                    }
                }

                $sql_coords = "INSERT INTO location_coords_default (coords_name, coords_in_lat_lng, coords_out_lat_lng, coords_range, coords_in_name, coords_out_name) VALUES (?, ?, ?, ?, ?, ?)";
                $sql_coords_para = array($shift_coords_nameWork, $check_both_coordinates_str, $check_both_coordinates_str, $check_coords_range, $shift_coords_nameIN, $shift_coords_nameIN);
                $insert_coords_Stmt = sqlsrv_prepare($conn, $sql_coords,  $sql_coords_para);

                if (sqlsrv_execute($insert_coords_Stmt) === false) {
                    die(print_r(sqlsrv_errors(), true));
                    // print_r(sqlsrv_errors(), true);
                    echo "ERORR 2";
                } else {

                    $search_coords = "SELECT coords_id FROM location_coords_default 
                            WHERE coords_name LIKE '%' + ? + '%' 
                            AND coords_in_name LIKE '%' + ? + '%' 
                            AND coords_out_name LIKE '%' + ? + '%'
                            AND (coords_in_lat_lng = ? OR  coords_out_lat_lng = ?)
                            ";

                    $sql_coords_para = array($shift_coords_nameWork, $shift_coords_nameIN, $shift_coords_nameIN, $check_both_coordinates_str, $check_both_coordinates_str);
                    $insert_coords_Stmt = sqlsrv_prepare($conn, $search_coords,  $sql_coords_para);

                    if (sqlsrv_execute($insert_coords_Stmt) === false) {
                        // die(print_r(sqlsrv_errors(), true));
                        // print_r(sqlsrv_errors(), true);
                        echo "ERORR 3";
                    } else {

                        $rs_SELECT = sqlsrv_fetch_array($insert_coords_Stmt, SQLSRV_FETCH_ASSOC);
                        $start_data = (new DateTime($shift_start_date))->format('Y-m-d');
                        $end_date = (new DateTime($shift_end_date))->format('Y-m-d');

                        $sql_update_coords = "UPDATE shift_work_location_temporary_request SET coords_id = ? 
                                                    WHERE card_id = ? 
                                                    AND request_card_id = ?
                                                    AND shift_start_date =  ?
                                                    AND shift_end_date =  ?
                                                    
                                                    ";

                        $sql_coords_para = array($rs_SELECT['coords_id'], $employee, $employee, $start_data, $end_date);
                        $update_coords_Stmt = sqlsrv_prepare($conn, $sql_update_coords,   $sql_coords_para);

                        if (sqlsrv_execute($update_coords_Stmt) === false) {
                            die(print_r(sqlsrv_errors(), true));
                            echo "ERORR 4";
                        } else {
                            echo "work-shift-tempo-success";
                            exit();
                        }
                    }
                }
            }
        }
    } else {
        echo "Invalid or empty data received.";
    }
} else {
    echo 'ไม่มีข้อมูลที่ถูกส่งมา';
}


function sql_query_temporary()
{
}

function sql_query_permanent()
{
}

