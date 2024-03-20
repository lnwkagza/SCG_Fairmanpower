<?php
include("../database/connectdb.php");
$date = $_POST['date']; 
$nameday = $_POST['nameday'];

$dayOfWeek = date('w', strtotime($date));
if ($dayOfWeek == 0 || $dayOfWeek == 6) {
    $date = date('Y-m-d', strtotime($date . ' +1 weekday'));
    $nameday = $nameday . " (วันหยุดชดเชย)";
}

$sql = "INSERT INTO holiday (date, name) VALUES (?, ?)";
$params = array($date, $nameday);

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Record inserted successfully";
}
?>
