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
        echo "Card ID: " . $cardId . "<br>";

        $date = isset($_POST['date']) ? $_POST['date'] : '';
        echo "Date: " . $date . "<br>";

        $request_detail = isset($_POST['request_detail']) ? $_POST['request_detail'] : '';
        echo "Request Detail: " . $request_detail . "<br>";

        $inspector_id = isset($_POST['inspector_id']) ? $_POST['inspector_id'] : '';
        echo "Inspector ID: " . $inspector_id . "<br>";

        $approver_id = isset($_POST['approver_id']) ? $_POST['approver_id'] : '';
        echo "Approver ID: " . $approver_id . "<br>";

        $employeeCount = 3;

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
            echo "Employee ID " . $i . ": " . $employeeid . "<br>";
            $shiftNew = isset($_POST['shiftNew' . $i]) ? $_POST['shiftNew' . $i] : '';
            echo "Shift New " . $i . ": " . $shiftNew . "<br>";
            $shiftold = "DD01";
        }
        
        $params = array(
            $time_stamp, $date,
            $_POST['employeeid' . 1], $shiftold, $_POST['shiftNew' . 1],
            $_POST['employeeid' . 2], $shiftold, $_POST['shiftNew' . 2],
            $_POST['employeeid' . 3], $shiftold, $_POST['shiftNew' . 3],
            $cardId, $request_detail, 'waiting',
            $approver_id, $inspector_id
        );

        $stmt = sqlsrv_prepare($conn, $query, $params);
        sqlsrv_execute($stmt);

    } else {
        echo "Session variable 'card_id' is not set.";
    }
} else {
    echo "Form submission failed!";
}
?>
