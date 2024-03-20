<?php
function update_dayoff($selectedYear,$selectedMonth,$card_id){
    // include("dbconnect.php");
    include("../database/connectdb.php");

    $start_date = new DateTime("$selectedYear-$selectedMonth-01");
    $endDate = new DateTime($start_date->format('Y-m-t'));
    $endDate->modify('+1 day'); // เพิ่มวันทีละหนึ่งวัน
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $endDate);

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

// Loop through the date range
foreach ($date_range as $date) {
    $formatted_date = $date->format('Y-m-d');
    $day_of_week = $date->format('D');
    $dayoff_syn_stmt = sqlsrv_prepare($conn, $select_dayoff_syn, array($card_id, $formatted_date, $formatted_date));

    // Execute the SQL statements
    if (sqlsrv_execute($dayoff_set_stmt) && sqlsrv_execute($dayoff_syn_stmt)) {
        $dayoff_set_result = sqlsrv_fetch_array($dayoff_set_stmt, SQLSRV_FETCH_ASSOC);
        $dayoff_syn_result = sqlsrv_fetch_array($dayoff_syn_stmt, SQLSRV_FETCH_ASSOC);

        // Initialize variables
        $day1 = '';
        $day2 = '';

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

        // Execute the SQL statement to insert or update day-off information
        $select_existing_query = "SELECT is_day_off FROM transaction_work WHERE card_id = ? AND date = ?";
        $select_existing_stmt = sqlsrv_prepare($conn, $select_existing_query, array(&$card_id, &$formatted_date));
        sqlsrv_execute($select_existing_stmt);
        $existing_result = sqlsrv_fetch_array($select_existing_stmt, SQLSRV_FETCH_ASSOC);

        if ($existing_result) {
            // Data for the current date already exists
            if ($existing_result['is_day_off'] == $is_holiday) {
                // echo "Data for $formatted_date is already up to date. Day: $day_of_week, Is Holiday: $is_holiday.<br>";
            } else {
                // Update the existing record
                $update_query = "UPDATE transaction_work SET is_day_off = ?, day = ? WHERE card_id = ? AND date = ?";
                $update_stmt = sqlsrv_prepare($conn, $update_query, array(&$is_holiday, &$day_of_week, &$card_id, &$formatted_date));
                sqlsrv_execute($update_stmt);
                // echo "Data for $formatted_date has been updated. Day: $day_of_week, Is Holiday: $is_holiday.<br>";
            }
        } else {
            // Insert new record
            $insert_query = "INSERT INTO transaction_work (card_id, date, is_day_off, day) VALUES (?, ?, ?, ?)";
            $insert_stmt = sqlsrv_prepare($conn, $insert_query, array(&$card_id, &$formatted_date, &$is_holiday, &$day_of_week));
            sqlsrv_execute($insert_stmt);
            // echo "Data for $formatted_date has been inserted. Day: $day_of_week, Is Holiday: $is_holiday.<br>";
        }
    }
}
}


function update_dayoff_team($selectedYear, $selectedMonth, $card_id) {
    // include("dbconnect.php");
    include("../database/connectdb.php");
    $sql = "SELECT card_id, firstname_thai, lastname_thai
            FROM employee
            WHERE cost_center_organization_id = (SELECT cost_center_organization_id
                                                FROM employee
                                                WHERE card_id = ?)";
    
    $params = array(&$card_id);
    $day_off_Team_section_result = sqlsrv_query($conn, $sql, $params);

    while ($row = sqlsrv_fetch_array($day_off_Team_section_result, SQLSRV_FETCH_ASSOC)) {
        $start_date = new DateTime("$selectedYear-$selectedMonth-01");
        $endDate = new DateTime($start_date->format('Y-m-t'));
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($start_date, $interval, $endDate);

        $card_id = $row['card_id'];

        foreach ($date_range as $date) {
            $formatted_date = $date->format('Y-m-d');
            $day_of_week = $date->format('D');

            $select_dayoff_set = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2
                                  FROM employee 
                                  INNER JOIN work_format ON employee.work_format_code = work_format.work_format_code
                                  WHERE employee.card_id = ?";

            $dayoff_set_stmt = sqlsrv_prepare($conn, $select_dayoff_set, array(&$card_id));

            if (sqlsrv_execute($dayoff_set_stmt)) {
                $dayoff_set_result = sqlsrv_fetch_array($dayoff_set_stmt, SQLSRV_FETCH_ASSOC);

                $select_dayoff_syn = "SELECT work_format.day_off1 AS day_off1, work_format.day_off2 AS day_off2,
                                      date_start, date_end
                                      FROM day_off_request 
                                      INNER JOIN work_format ON day_off_request.edit_work_format_code = work_format.work_format_code 
                                      WHERE card_id = ? AND date_start <= ? AND date_end >= ?";

                $dayoff_syn_stmt = sqlsrv_prepare($conn, $select_dayoff_syn, array(&$card_id, &$formatted_date, &$formatted_date));

                if (sqlsrv_execute($dayoff_syn_stmt)) {
                    $dayoff_syn_result = sqlsrv_fetch_array($dayoff_syn_stmt, SQLSRV_FETCH_ASSOC);

                    $day1 = '';
                    $day2 = '';

                    if ($dayoff_syn_result) {
                        $day1 = $dayoff_syn_result['day_off1'];
                        $day2 = $dayoff_syn_result['day_off2'];
                    } elseif ($dayoff_set_result) {
                        $day1 = $dayoff_set_result['day_off1'];
                        $day2 = $dayoff_set_result['day_off2'];
                    }

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

                    $is_holiday = ($day_off1 == $dayMap[$day_of_week] || $day_off2 == $dayMap[$day_of_week]) ? "Yes" : "No";

                    $select_existing_query = "SELECT is_day_off FROM transaction_work WHERE card_id = ? AND date = ?";
                    $select_existing_stmt = sqlsrv_prepare($conn, $select_existing_query, array(&$card_id, &$formatted_date));

                    if (sqlsrv_execute($select_existing_stmt)) {
                        $existing_result = sqlsrv_fetch_array($select_existing_stmt, SQLSRV_FETCH_ASSOC);

                        if ($existing_result) {
                            if ($existing_result['is_day_off'] == $is_holiday) {
                                // Data for the current date is already up to date.
                            } else {
                                $update_query = "UPDATE transaction_work SET is_day_off = ?, day = ? WHERE card_id = ? AND date = ?";
                                $update_stmt = sqlsrv_prepare($conn, $update_query, array(&$is_holiday, &$day_of_week, &$card_id, &$formatted_date));

                                if (sqlsrv_execute($update_stmt)) {
                                    // Data for the current date has been updated.
                                } else {
                                    // Handle update SQL error
                                    die(print_r(sqlsrv_errors(), true));
                                }
                            }
                        } else {
                            $insert_query = "INSERT INTO transaction_work (card_id, date, is_day_off, day) VALUES (?, ?, ?, ?)";
                            $insert_stmt = sqlsrv_prepare($conn, $insert_query, array(&$card_id, &$formatted_date, &$is_holiday, &$day_of_week));

                            if (sqlsrv_execute($insert_stmt)) {
                                // Data for the current date has been inserted.
                            } else {
                                // Handle insert SQL error
                                die(print_r(sqlsrv_errors(), true));
                            }
                        }
                    } else {
                        // Handle select existing SQL error
                        die(print_r(sqlsrv_errors(), true));
                    }
                } else {
                    // Handle dayoff_syn_stmt execution error
                    die(print_r(sqlsrv_errors(), true));
                }
            } else {
                // Handle dayoff_set_stmt execution error
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

?>