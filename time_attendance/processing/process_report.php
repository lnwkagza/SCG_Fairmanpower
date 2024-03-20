<?php
include("../database/connectdb.php");
session_start();

// Function to safely handle user input
function cleanInput($input) {
    $cleanedInput = trim($input);
    $cleanedInput = htmlspecialchars($cleanedInput);
    return $cleanedInput;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clean and validate input data
    $details = cleanInput($_POST["details"]);
    $type = cleanInput($_POST["type"]);

    // Check if session variable is set
    if (isset($_SESSION["card_id"])) {
        $card_id = $_SESSION["card_id"];
    } else {
        die("Session variable 'card_id' not set.");
    }

    // Get current timestamp
    $timestamp = date("Y-m-d H:i:s");

    // Directory for image uploads
    $uploadDirectoryImage = '../Upload/report/';

    // Initialize image attachment file name
    $imageAttachmentFileName = '';

    // Check if image file is uploaded
    if (isset($_FILES["imageAttachment"]) && $_FILES["imageAttachment"]["error"] == UPLOAD_ERR_OK) {
        // Validate file type and size
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        $fileExtension = strtolower(pathinfo($_FILES['imageAttachment']['name'], PATHINFO_EXTENSION));
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (in_array($fileExtension, $allowedTypes) && $_FILES["imageAttachment"]["size"] <= $maxFileSize) {
            // Generate unique file name
            $imageAttachmentFileName = $uploadDirectoryImage . uniqid('', true) . '.' . $fileExtension;

            // Move uploaded file to destination directory
            if (move_uploaded_file($_FILES["imageAttachment"]["tmp_name"], $imageAttachmentFileName)) {
                // File uploaded successfully
            } else {
                die("Error uploading image file.");
            }
        } else {
            die("Invalid file type or size.");
        }
    }

    // Prepare SQL query
    $query = "INSERT INTO report_log (card_id,input_timestamp,detail, attachment, type) VALUES (?, ?, ?, ?, ?)";

    // Bind parameters
    $params = array($card_id, $timestamp, $details, $imageAttachmentFileName, $type);

    // Execute SQL query
    $stmt = sqlsrv_prepare($conn, $query, $params);
    if ($stmt === false) {
        die("Error preparing statement: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_execute($stmt)) {
        echo '<script>
            alert("คุณได้ทำรายการแจ้งปัญหาสำเร็จ");
            window.location.href = "check-in-attendance-schedule.php";
        </script>';
    } else {
        die("Error executing query: " . print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($conn);
    exit();
} else {
    die("Invalid request.");
}
?>
