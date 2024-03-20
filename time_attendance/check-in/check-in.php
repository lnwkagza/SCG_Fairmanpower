<?php
include("../database/connectdb.php");
$time_stamp_YEAR = date("Y");
$time_stamp_MONTH = date("M");
$current_date = date("F");
$englishMonths = array(
    'January' => 'มกราคม',
    'February' => 'กุมภาพันธ์',
    'March' => 'มีนาคม',
    'April' => 'เมษายน',
    'May' => 'พฤษภาคม',
    'June' => 'มิถุนายน',
    'July' => 'กรกฎาคม',
    'August' => 'สิงหาคม',
    'September' => 'กันยายน',
    'October' => 'ตุลาคม',
    'November' => 'พฤศจิกายน',
    'December' => 'ธันวาคม',
);
$thaiYear = date("Y") + 543;
$thaiMonthYear = strtr($current_date, $englishMonths) . " " . $thaiYear;

$card_id = 1949999999903;

    $start_date = new DateTime('first day of this month');
    $end_date = new DateTime('first day of next month');
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $end_date);

    $select_check_inout_Query = "SELECT * FROM check_inout INNER JOIN shift_type on check_inout.shift_type_id = shift_type.shift_type_id WHERE card_id = ? AND date = ? ORDER BY date ASC";

    $select_absence_record = "SELECT * FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ?";

    $select_holiday = "SELECT * FROM holiday WHERE date = ?";

    $select_transaction_work_Query = "SELECT * FROM transaction_work WHERE card_id = ? AND date = ? ORDER BY date ASC";

    foreach ($date_range as $date) {
        $day_date = $date->format('d');
        $formatted_date = $date->format('Y-m-d');

        $check_inout_stmt = sqlsrv_prepare($conn, $select_check_inout_Query, array(&$card_id, &$formatted_date));
        $holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array(&$formatted_date));
        $absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array(&$card_id, &$formatted_date, &$formatted_date));
        $transaction_work_stmt = sqlsrv_prepare($conn, $select_transaction_work_Query, array(&$card_id, &$formatted_date));

        
        // Execute the SQL statements
        if (sqlsrv_execute($check_inout_stmt)&&
            sqlsrv_execute($absence_record_stmt) && 
            sqlsrv_execute($holiday_stmt)&& 
            sqlsrv_execute($transaction_work_stmt)) {

            $holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);
            $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
            $check_inout_result = sqlsrv_fetch_array($check_inout_stmt, SQLSRV_FETCH_ASSOC);
            $transaction_work_result = sqlsrv_fetch_array($transaction_work_stmt, SQLSRV_FETCH_ASSOC);

            $shift = array(
            'DD01' => 'ปกติ 1',
            'DD02' => 'ปกติ 2',
            'HOLIDAY' => 'วันหยุดนักขัต',
            'LEAVE' => 'ลา',
            'OFF' => 'วันหยุด',
            'SA01' => 'กะ 1',
            'SB01' => 'กะ 2',
            'SC01' => 'กะ 3',
            'TRAIN' => 'อบรม'
        );
        $shiftStatus = isset($shift[$transaction_work_result["shift_main"]]) ? $shift[$transaction_work_result["shift_main"]] : "";

            if ($transaction_work_result["shift_main"] == "DD01") {
                $time_in_set = "07:30:00";
                $time_out_set = "16:30:00";
            } elseif ($transaction_work_result["shift_main"] == "DD02") {
                $time_in_set = "08:00:00";
                $time_out_set = "17:00:00";
            } elseif ($transaction_work_result["shift_main"] == "SA01") {
                $time_in_set = "08:00:00";
                $time_out_set = "16:00:00";
            } elseif ($transaction_work_result["shift_main"] == "SB01") {
                $time_in_set = "16:00:00";
                $time_out_set = "00:00:00"; // Midnight, next day
            } elseif ($transaction_work_result["shift_main"] == "SC01") {
                $time_in_set = "00:00:00";
                $time_out_set = "08:00:00";
            } else {
                // Handle default case
                $time_in_set = "00:00:00";
                $time_out_set = "00:00:00";
            }
            
           if ($holiday_result) {
            $status = 'HOLIDAY';
            $symbol = $shiftStatus;
            $time_in = '-';
            $time_out = '-';
           }elseif($absence_record_result) {
            $status = 'LEAVE';
            $symbol = $shiftStatus;
            $time_in = '-';
            $time_in = '-';
            $time_out = '-';
            }elseif ($date > (new DateTime())->modify('-1 day')) {
            $status = "-";
            $symbol = $shiftStatus;
            $status = "-"; // วันที่ยังไม่ถึง
            $time_in = '-';
            $time_out = '-';
            }elseif ($check_inout_result) {
                $symbol = $shiftStatus;
                $time_in = $check_inout_result['time_in'] ? $check_inout_result['time_in']->format('H:i') : "";
                $time_out = $check_inout_result['time_out'] ? $check_inout_result['time_out']->format('H:i') : "";
                $approve_status = $check_inout_result['approve_status'] ? $check_inout_result['approve_status'] : "";
                //เวลา
                if ($time_in >= $time_in_set) {
                    $status = '<label style="color: #FF9922;">มาสาย</label>';
                }elseif ($time_in == "" && $time_out == "") {
                    $status = '<label style="color: red;">ขาดงาน</label>';
                    $time_in = "<label style='color: red;'>ขาดงาน</label>";
                    $time_out = "<label style='color: red;'>ขาดงาน</label>";
                } elseif ($time_in <= $time_in_set && $time_out >= $time_out_set) {
                    $status = '<label style="color: #5BAF33;">ตรงเวลา</label>';
                } elseif ($time_out <= $time_out_set) {
                    $status = '<label style="color: #9747FF; ">กลับก่อน</label>';
                }else {
                    $status = " <label style='color: red;'>ขาดงาน</label>";
                    $time_in = "-";
                    $time_out = "-";
                    $approve_status = "-";
                }
            }else {
                $symbol = $shiftStatus;
                $status = " <label style='color: red;'>ขาดงาน</label>";
                $Actionsmode = 1;
                $time_in = "-";
                $time_out = "-";
                $approve_status = "-";
                $OTmode = 0;
            }
            echo $day_date . ' ' . $thaiMonthYear;
            echo $symbol;
            echo $status;
            echo $time_in;
            echo $time_out;
            echo "<br>";
        }
    }
?>

