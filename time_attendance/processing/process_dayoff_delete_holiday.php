<?php
include("../database/connectdb.php");

// Get the values from the AJAX request
$id = $_POST['id']; // Assuming 'id' is the parameter you want to use for deletion

$sql = "DELETE FROM holiday WHERE holiday_id = ?";
$params = array($id);

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Record deleted successfully";
}
?>
