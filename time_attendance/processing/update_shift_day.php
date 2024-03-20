<?php

$selectedYear = date('Y');
$selectedMonth = date('n');// Fix: Assign to $selectedDay instead of $selectedMonth
// Make sure to handle the case when $_GET['id'] is not set
$card_id = isset($_GET['id']) ? $_GET['id'] : '';

update_shift($selectedYear, $selectedMonth, $card_id); // Pass $selectedDay as an argument

function update_shift($selectedYear, $selectedMonth, $card_id) {
    include("../database/connectdb.php");

    // Fix: Use $selectedDay instead of $selectedMonth in the date format
    $start_date = new DateTime("$selectedYear-$selectedMonth-01");
    $endDate = new DateTime($start_date->format('Y-m-t'));
    $endDate->modify('+1 day'); // Add one day
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $endDate);

    // $approve = "approve";

    // SQL query to retrieve day-off settings for the employee
    $select_dayoff_set = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2
                        FROM employee 
                        INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
                        WHERE employee.card_id = ?";
    $dayoff_set_stmt = sqlsrv_prepare($conn, $select_dayoff_set, array($card_id));

    // SQL query to retrieve day-off requests for the employee within the date range
    $select_dayoff_syn = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2,
                        date_start, date_end
                        FROM day_off_request 
                        INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code 
                        WHERE card_id = ? AND date_start <= ? AND date_end >= ?";

    $select_shift_format = "SELECT card_id, day, shift_format.shift_type_id
                        FROM employee
                        INNER JOIN shift_format ON employee.work_format_code = shift_format.work_format_code
                        INNER JOIN shift_type ON shift_format.shift_type_id = shift_type.shift_type_id
                        WHERE card_id = ? AND day = ?
                        ORDER BY card_id ASC";

    $select_absence_record = "SELECT * FROM absence_record WHERE card_id = ? AND date_start <= ? AND date_end >= ?";
    $select_shift_add = "SELECT * FROM shift_add WHERE card_id = ? AND date = ?";
    $select_shift_change = "SELECT * FROM shift_change WHERE card_id = ? AND date = ? ";
    $select_shift_lock = "SELECT * FROM shift_lock WHERE card_id = ? AND date = ? ";
    
    $select_shift_switch = "SELECT date, new_shift_1 AS new_shift FROM shift_switch WHERE employee_1 = ? AND date = ?
                        UNION
                        SELECT date, new_shift_2 AS new_shift FROM shift_switch WHERE employee_2 = ? AND date = ?
                        UNION
                        SELECT date, new_shift_3 AS new_shift FROM shift_switch WHERE employee_3 = ? AND date = ? ";

    $select_holiday = "SELECT * FROM holiday WHERE date = ?";


    // Loop through the date range
    foreach ($date_range as $date) {

        $formatted_date = $date->format('Y-m-d');

        $day_of_week = $date->format('D');

        $absence_record_stmt = sqlsrv_prepare($conn, $select_absence_record, array($card_id, $formatted_date, $formatted_date));

        $dayoff_syn_stmt = sqlsrv_prepare($conn, $select_dayoff_syn, array($card_id, $formatted_date, $formatted_date));
        $holiday_stmt = sqlsrv_prepare($conn, $select_holiday, array($formatted_date));

        $shift_format_stmt = sqlsrv_prepare($conn, $select_shift_format, array($card_id,$day_of_week));

        $shift_add_stmt = sqlsrv_prepare($conn, $select_shift_add, array($card_id, $formatted_date));
        $shift_change_stmt = sqlsrv_prepare($conn, $select_shift_change, array($card_id, $formatted_date));
        $shift_lock_stmt = sqlsrv_prepare($conn, $select_shift_lock, array($card_id, $formatted_date));
        $shift_switch_stmt = sqlsrv_prepare($conn, $select_shift_switch, array(&$card_id, &$formatted_date, &$card_id, &$formatted_date, &$card_id, &$formatted_date));
        
        // Execute the SQL statements
        if (sqlsrv_execute($dayoff_set_stmt) && 
            sqlsrv_execute($dayoff_syn_stmt) && 
            sqlsrv_execute($shift_format_stmt) && 
            sqlsrv_execute($absence_record_stmt) && 
            sqlsrv_execute($shift_add_stmt) &&
            sqlsrv_execute($shift_change_stmt) &&
            sqlsrv_execute($shift_lock_stmt) && 
            sqlsrv_execute($shift_switch_stmt) && 
            sqlsrv_execute($holiday_stmt)) {

            $dayoff_set_result = sqlsrv_fetch_array($dayoff_set_stmt, SQLSRV_FETCH_ASSOC);
            $dayoff_syn_result = sqlsrv_fetch_array($dayoff_syn_stmt, SQLSRV_FETCH_ASSOC);

            $holiday_result = sqlsrv_fetch_array($holiday_stmt, SQLSRV_FETCH_ASSOC);

            $absence_record_result = sqlsrv_fetch_array($absence_record_stmt, SQLSRV_FETCH_ASSOC);
            $shift_add_result = sqlsrv_fetch_array($shift_add_stmt, SQLSRV_FETCH_ASSOC);
            $shift_change_result = sqlsrv_fetch_array($shift_change_stmt, SQLSRV_FETCH_ASSOC);
            $shift_lock_result = sqlsrv_fetch_array($shift_lock_stmt, SQLSRV_FETCH_ASSOC);
            $shift_switch_result = sqlsrv_fetch_array($shift_switch_stmt, SQLSRV_FETCH_ASSOC);

            $shift_format_result = sqlsrv_fetch_array($shift_format_stmt, SQLSRV_FETCH_ASSOC);

            // Initialize variables
            $day1 = NULL;
            $day2 = NULL;

            $shift_main = $shift_format_result['shift_type_id'];

            $shift_add = NULL;
            $shift_lock = NULL;

            // Check if there is a day-off request for the current date
            if ($dayoff_syn_result) {
                $day1 = $dayoff_syn_result['day_off1'];
                $day2 = $dayoff_syn_result['day_off2'];
            } elseif ($dayoff_set_result) {
                $day1 = $dayoff_set_result['day_off1'];
                $day2 = $dayoff_set_result['day_off2'];
            }

            // Map day names to numbers
            $dayMap = [
                'Sun' => 7,
                'Mon' => 1,
                'Tue' => 2,
                'Wed' => 3,
                'Thu' => 4,
                'Fri' => 5,
                'Sat' => 6,
            ];
            $day_off1 = $dayMap[$day1];
            $day_off2 = $dayMap[$day2];

            // Check if the current date is a holiday
            if ($day_off1 == $dayMap[$day_of_week] || $day_off2 == $dayMap[$day_of_week]) {
                $is_holiday = "Yes";
            } else {
                $is_holiday = "No";
            }

            if ($absence_record_result) {
                $shift_main = 'LEAVE';
            } elseif ($holiday_result) {
                $shift_main = 'HOLIDAY';
                $is_holiday = "Yes";
            } elseif ($shift_add_result) {
                $shift_add = $shift_add_result['add_shift_type_id'];
            } elseif ($shift_change_result) {
                $shift_main = $shift_change_result['new_shift_id'];
            } elseif ($shift_lock_result) {
                // $shift_main = $shift_lock_result['shift_type_id'];
                $shift_lock = $shift_lock_result['shift_type_id'];
            }
            elseif ($shift_switch_result) {
                $shift_main = $shift_switch_result['new_shift'];
                // $shift_switch = $shift_switch_result['new_shift'];
            }


            // Execute the SQL statement to insert or update day-off information
            $select_existing_query = "SELECT is_day_off, shift_main, shift_add, shift_lock FROM transaction_work WHERE card_id = ? AND date = ?";
            $select_existing_stmt = sqlsrv_prepare($conn, $select_existing_query, array(&$card_id, &$formatted_date));
            sqlsrv_execute($select_existing_stmt);
            $existing_result = sqlsrv_fetch_array($select_existing_stmt, SQLSRV_FETCH_ASSOC);

            if ($existing_result) {
                // Data for the current date already exists
                $update_fields = array();

                if (isset($existing_result['is_day_off']) && $existing_result['is_day_off'] != $is_holiday) {
                    $update_fields[] = 'is_day_off = ?';
                }

                if (isset($existing_result['shift_main']) && $existing_result['shift_main'] != $shift_main) {
                    $update_fields[] = 'shift_main = ?';
                }

                if (isset($existing_result['shift_add']) && $existing_result['shift_add'] != $shift_add) {
                    $update_fields[] = 'shift_add = ?';
                }

                if (isset($existing_result['shift_lock']) && $existing_result['shift_lock'] != $shift_lock) {
                    $update_fields[] = 'shift_lock = ?';
                }

                if (!empty($update_fields)) {
                    // Update the existing record
                    $update_query = "UPDATE transaction_work SET day = ?, " . implode(', ', $update_fields) . " WHERE card_id = ? AND date = ?";
                    $update_params = array_merge(array(&$day_of_week, &$is_holiday, &$shift_main, &$shift_add, &$shift_lock), array(&$card_id, &$formatted_date));
                    $update_stmt = sqlsrv_prepare($conn, $update_query, $update_params);
                    sqlsrv_execute($update_stmt);
                    // echo "$formatted_date | Day: $day_of_week | Update ";
                } else {
                    // echo "$formatted_date | Day: $day_of_week | latest ";
                }
            } else {
                // Insert new record
                $insert_query = "INSERT INTO transaction_work (card_id, date, day, is_day_off, shift_main, shift_add, shift_lock) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = sqlsrv_prepare($conn, $insert_query, array(&$card_id, &$formatted_date, &$day_of_week, &$is_holiday, &$shift_main, &$shift_add, &$shift_lock));
                sqlsrv_execute($insert_stmt);
                // echo "$formatted_date | Day: $day_of_week | New ";
            }

            // if ($shift_main == "OFF") {
            //     echo "วันหยุด";
            // }elseif ($shift_main == "LEAVE"){
            //     echo "วันลา";
            // }elseif ($shift_main == "TRAIN"){
            //     echo "วันอบรม";
            // }elseif ($shift_main == "HOLIDAY"){
            //     echo "วันหยุดนักขัตฤกษ์";
            // }else {
            //     echo "รูปแบบการทำงานหลักคือ  $shift_main";
            // }

            // if ($shift_add) {
            //     echo " | เสริม $shift_add";
            // }
            // if ($shift_lock) {
            //     echo " | ล๊อคเหลี่ยม $shift_lock";
            // }
            // if ($shift_switch_result) {
            //     echo " | สลับ $shift_switch";
            // }
            // echo '<br>';

        }
    }
}
?>
