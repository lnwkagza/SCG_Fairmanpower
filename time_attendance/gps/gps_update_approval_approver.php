<?php
include('../database/connectdb.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $time_stamp = date("Y-m-d H:i:s");
    $shiftId = $_POST['shift_id'];
    $cardId = $_POST['card_id'];
    $approvalStatus = $_POST['approval_id'];

    // echo $shiftId;
    // echo $cardId;
    // echo $approvalStatus;

    $approvarID = isset($_POST['approvarID']) ? $_POST['approvarID'] : null;
    $start_date = (new DateTime($_POST['start_date']))->format('Y-m-d');
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

    if ($end_date) {
        (new DateTime($end_date))->format('Y-m-d');
    }

    // $end_date = (new DateTime($_POST['end_date']))->format('Y-m-d');
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;

    $sql = "UPDATE shift_work_location_temporary_request SET approval_id = ? ";

    if ($reason) {
        $sql .= ", note = '" . $reason . "'";
    }

    $sql .= "WHERE shift_id = ?";

    // echo $approvalStatus;

    // $sql .= " WHERE shift_id = ?
    //             AND shift_start_date = ?
    //             AND shift_end_date = ? 
    //         ";

    $params = array($approvalStatus, $shiftId);
    $update_stmt = sqlsrv_prepare($conn, $sql, $params);

    if (sqlsrv_execute($update_stmt) === false) {
        die(print_r(sqlsrv_errors(), true));
        // echo "ERROR 1";
    }

    if ($approvalStatus == 3 || $approvalStatus == 2) {

        $querynotify = "SELECT * FROM shift_work_location_temporary_request
                            INNER JOIN login ON shift_work_location_temporary_request.card_id = login.card_id
                            RIGHT JOIN employee ON login.card_id = employee.card_id
                            WHERE shift_id = ?";

        $params = array($shiftId);
        $stmt_notify = sqlsrv_prepare($conn, $querynotify, $params);

        if (sqlsrv_execute($stmt_notify) === false) {
            die(print_r(sqlsrv_errors(), true));
            // echo "ERROR 2";
        }

        $row = sqlsrv_fetch_array($stmt_notify, SQLSRV_FETCH_ASSOC);

        // $url = 'https://notify-api.line.me/api/notify';
        // $headers = array(
        //     'Content-Type: application/x-www-form-urlencoded',
        //     'Authorization: Bearer ' . $row['line_token']
        // );
        // $message = "รายการขอเปลี่ยนพิกัดการเข้า-ออกงานของคุณ " . $row['firstname_thai'] .
        //     ' ' . $row['lastname_thai'] . ' ได้รับการอนุมัติ เมื่อเวลา ' . $time_stamp;

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        // curl_close($ch);


    //     // สร้าง URL พร้อมพารามิเตอร์
    //     $url = "https://127.0.0.1/Timefair/gps/inspector_gps_approval.php" . $shiftId;

    //     $line_notify_token = $row['line_token'];

    //     // ส่วนของโค้ดที่ใช้ส่ง Notify
    //     $message = "คลิกที่ลิ้งค์เพื่อดูข้อมูล GPS";
    //     $message .= "\n" . $url;

    //     $line_notify_url = "https://notify-api.line.me/api/notify";
    //     $data = array('message' => $message);
    //     $options = array(
    //         'http' => array(
    //             'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer " . $line_notify_token,
    //             'method' => 'POST',
    //             'content' => http_build_query($data),
    //         ),
    //     );
    //     $context = stream_context_create($options);
    //     $result = file_get_contents($line_notify_url, false, $context);

    //     if ($result === FALSE) {
    //         // Handle error
    //         echo "Error sending Line Notify";
    //     } else {
    //         echo 'Data updated successfully.';
    //     }

    
    }

} else {
    // Return an error message if the request method is not POST
    echo 'Invalid request method.';
}
