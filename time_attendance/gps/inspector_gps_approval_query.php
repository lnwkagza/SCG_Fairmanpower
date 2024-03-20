<?php
session_start();

$inspector_request_gps = "SELECT * FROM shift_work_location_temporary_request
                            LEFT JOIN employee ON shift_work_location_temporary_request.card_id = employee.card_id
                            LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                            LEFT JOIN approval_status ON shift_work_location_temporary_request.inspector_id = approval_status.approval_id
                            WHERE shift_work_location_temporary_request.inspector = ? 
                            AND shift_work_location_temporary_request.inspector_id = ?
";

$approver_request_gps = "SELECT * FROM shift_work_location_temporary_request
                            LEFT JOIN employee ON shift_work_location_temporary_request.card_id = employee.card_id
                            LEFT JOIN location_coords_default ON shift_work_location_temporary_request.coords_id = location_coords_default.coords_id
                            LEFT JOIN approval_status ON shift_work_location_temporary_request.approval_id = approval_status.approval_id
                            WHERE shift_work_location_temporary_request.approver = ?

                            AND shift_work_location_temporary_request.inspector_id = 6 
                            AND shift_work_location_temporary_request.approval_id = 1
";

$inspector_result = sqlsrv_query($conn, $inspector_request_gps, array($_SESSION["card_id"], 5));
$approver_result = sqlsrv_query($conn, $approver_request_gps, array($_SESSION["card_id"]));

if ($inspector_result === false || $approver_result === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>

<script>
    var cardID = <?= $_SESSION["card_id"]; ?>
</script>