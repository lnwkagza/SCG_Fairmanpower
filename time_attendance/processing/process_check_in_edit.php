<?php
include("../database/connectdb.php");
session_start();

// Validate and sanitize user input
$transactioncheckin_id = filter_input(INPUT_POST, "transactioncheckin_id", FILTER_VALIDATE_INT);
$date = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING);
$edit_time_in = filter_input(INPUT_POST, "edit_time_in", FILTER_SANITIZE_STRING);
$edit_time_out = filter_input(INPUT_POST, "edit_time_out", FILTER_SANITIZE_STRING);
$reason = filter_input(INPUT_POST, "reason", FILTER_SANITIZE_STRING);
$supervisor = filter_input(INPUT_POST, "supervisor", FILTER_SANITIZE_STRING);
$inspector = filter_input(INPUT_POST, "inspector", FILTER_SANITIZE_STRING);
$status = "waiting";
$edit_time = date("Y-m-d H:i:s");

// File upload for imageAttachment and pdfAttachment
$uploadDirectoryImage = 'Upload/img/';
$uploadDirectoryPdf = 'Upload/pdf/';
$imageAttachmentFileName = '';
$pdfAttachmentFileName = '';

// Handle imageAttachment file upload
if (isset($_FILES["imageAttachment"]) && $_FILES["imageAttachment"]["error"] == UPLOAD_ERR_OK) {
    $imageAttachmentFileName = $uploadDirectoryImage . basename($_FILES["imageAttachment"]["name"]);

    if (move_uploaded_file($_FILES["imageAttachment"]["tmp_name"], $imageAttachmentFileName)) {
        echo "Image file uploaded successfully.";
    } else {
        die("Error uploading image file.");
    }
} else {
    // No image file uploaded.
}

// Handle pdfAttachment file upload
if (isset($_FILES["pdfAttachment"]) && $_FILES["pdfAttachment"]["error"] == UPLOAD_ERR_OK) {
    $pdfAttachmentFileName = $uploadDirectoryPdf . basename($_FILES["pdfAttachment"]["name"]);

    if (move_uploaded_file($_FILES["pdfAttachment"]["tmp_name"], $pdfAttachmentFileName)) {
        echo "PDF file uploaded successfully.";
    } else {
        die("Error uploading PDF file.");
    }
} else {
    // No PDF file uploaded.
}

// Check if the record already exists
$queryCheckExistence = "SELECT COUNT(*) as count FROM check_inout WHERE check_inout_id = ? AND card_id = ?";
$paramsCheckExistence = array($transactioncheckin_id, $_SESSION["card_id"]);
$stmtCheckExistence = sqlsrv_query($conn, $queryCheckExistence, $paramsCheckExistence);

if ($stmtCheckExistence === false) {
    die("Error executing SQL query: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtCheckExistence, SQLSRV_FETCH_ASSOC);
$recordExists = $row['count'] > 0;

// Prepare and execute the SQL query
if ($recordExists) {
    // Update existing record
    $query = "UPDATE check_inout SET edit_time_in = ?, edit_time_out = ?, edit_detail = ?, edit_attachment = ?, edit_image = ?, edit_time = ?, approve_status = ?, approver = ?, inspector = ? WHERE check_inout_id = ? AND card_id = ?";
    $params = array($edit_time_in, $edit_time_out, $reason, $pdfAttachmentFileName, $imageAttachmentFileName, $edit_time, $status, $supervisor, $inspector, $transactioncheckin_id, $_SESSION["card_id"]);
} else {
    // Insert new record
    $query = "INSERT INTO check_inout (card_id, shift_type_id, date, edit_time_in, edit_time_out, edit_detail, edit_attachment, edit_image, edit_time, approve_status, approver, inspector, check_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array($_SESSION["card_id"], 'DD01', $date, $edit_time_in, $edit_time_out, $reason, $pdfAttachmentFileName, $imageAttachmentFileName, $edit_time, $status, $supervisor, $inspector, 'ขาดงาน');
}

// Execute the SQL query
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    die("Error executing SQL query: " . print_r(sqlsrv_errors(), true));
}

// Notification for employee
$querynotify = "SELECT * FROM login RIGHT JOIN employee ON login.card_id = employee.card_id WHERE login.card_id = ?";
$params = array($_SESSION["card_id"]);
$stmtnotify = sqlsrv_query($conn, $querynotify, $params);

if ($stmtnotify === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

$firstname_emp = $row['firstname_thai'];
$lastname_emp = $row['lastname_thai'];

$url = 'https://notify-api.line.me/api/notify';
$headers = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $row['line_token']
);
$message = "เรียนคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' คุณได้ทำการแก้ไขรายการ check_in เมื่อเวลา ' . $edit_time . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

// Use the curl library to send the notification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($inspector) {
    // Notification for inspector
    $querynotify = "SELECT * FROM login RIGHT JOIN employee ON login.card_id = employee.card_id WHERE login.card_id = ?";
    $params = array($inspector);
    $stmtnotify = sqlsrv_query($conn, $querynotify, $params);

    if ($stmtnotify === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmtnotify, SQLSRV_FETCH_ASSOC);

    $url = 'https://notify-api.line.me/api/notify';
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer ' . $row['line_token']
    );
    $message = "เรียนคุณ " . $row['firstname_thai'] . ' ' . $row['lastname_thai'] . ' โปรดตรวจสอบการขออนุมัติ check_in ของคุณ ' . $firstname_emp . ' ' . $lastname_emp . ' เมื่อเวลา ' . $edit_time . " ดูเพิ่มเติม: https://liff.line.me/2003823996-Q0DXqyOZ";

    // Use the curl library to send the notification
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'message=' . urlencode($message));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}


echo '<script>
    window.location.href = "../check-in/check-in-attendance-schedule.php";
</script>';

exit();
?>
