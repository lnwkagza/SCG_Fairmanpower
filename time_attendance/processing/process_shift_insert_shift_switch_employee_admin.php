<?php
include("../database/connectdb.php");
session_start();

date_default_timezone_set('Asia/Bangkok');
$time_stamp = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the card_id session variable is set
    if (isset($_SESSION["card_id"])) {
        // Validate and sanitize input data
        $cardId = $_SESSION["card_id"];
        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $request_detail = isset($_POST['request_detail']) ? $_POST['request_detail'] : '';
        $inspector_id = isset($_POST['inspector_id']) ? $_POST['inspector_id'] : '';
        $approver_id = isset($_POST['approver_id']) ? $_POST['approver_id'] : '';
        $employeeCount = 3;

        try {
            // Start a database transaction
            sqlsrv_begin_transaction($conn);

            // Prepare the SQL query
            $query = "INSERT INTO shift_switch (
                input_timestamp, date, employee_1, old_shift_1, new_shift_1,
                employee_2, old_shift_2, new_shift_2, employee_3, old_shift_3,
                new_shift_3, request_card_id, request_detail, approve_status,
                approver, inspector
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            // Iterate through employees
            for ($i = 1; $i <= $employeeCount; $i++) {
                $employeeid = isset($_POST['employeeid' . $i]) ? $_POST['employeeid' . $i] : '';
                $shiftNew = isset($_POST['shiftNew' . $i]) ? $_POST['shiftNew' . $i] : '';
                $shiftold = "DD01";

                // Validate and sanitize inputs
                $employeeid = filter_var($employeeid, FILTER_SANITIZE_STRING);
                $shiftNew = filter_var($shiftNew, FILTER_SANITIZE_STRING);

                // Bind parameters
                $params = array(
                    $time_stamp, $date,
                    $employeeid[0], $shiftold, $shiftNew[0],
                    $employeeid[1], $shiftold, $shiftNew[1],
                    $employeeid[2], $shiftold, $shiftNew[2],
                    $cardId, $request_detail, 'waiting',
                    $approver_id, $inspector_id
                );

                // Prepare and execute the query
                $stmt = sqlsrv_prepare($conn, $query, $params);
                // Close the statement
                sqlsrv_free_stmt($stmt);
            }

            // Commit the transaction if everything is successful
            sqlsrv_commit($conn);
            header("Location: success.php");
            exit;
        } catch (Exception $e) {
            // Rollback the transaction on exception
            sqlsrv_rollback($conn);
            // Handle other exceptions
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Session variable 'card_id' is not set.";
    }
} else {
    echo "Form submission failed!";
}
?>
