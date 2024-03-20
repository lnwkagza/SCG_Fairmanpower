<?php
require_once("../database/connectdb.php");

$person_id = $_SESSION["card_id"];

// $SELECTapprover = "SELECT card_id, scg_employee_id, prefix_thai, firstname_thai, lastname_thai FROM employee";

$SELECTapprover = "SELECT 
                    employee.card_id, 
                    cost_center.cost_center_code, 
                    employee.firstname_thai, 
                    employee.lastname_thai 
                    FROM 
                    employee
                    LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
                    WHERE cost_center_organization_id IN (
                            SELECT cost_center_id 
                            FROM cost_center_head 
                            WHERE card_id = ?
                    )
                    ";

$SELECTapprover_Params = array($person_id);
$stmtapprover = sqlsrv_query($conn, $SELECTapprover, $SELECTapprover_Params);

if ($stmtapprover === false) {
    die(print_r(sqlsrv_errors(), true));
}

//approver query
$SELECT_approver_query = "SELECT * FROM manager
                            LEFT JOIN employee ON manager.manager_card_id = employee.card_id 
                            WHERE manager.card_id = ? ";

$SELECT_approver_Params = array($person_id);
$rs_approver = sqlsrv_query($conn, $SELECT_approver_query, $SELECT_approver_Params);

if ($rs_approver === false) {
    die(print_r(sqlsrv_errors(), true));
}

$rs_approverQ = sqlsrv_fetch_array($rs_approver, SQLSRV_FETCH_ASSOC);
$approver_id = $rs_approverQ["card_id"];
$approver_name = $rs_approverQ["prefix_thai"] . $rs_approverQ["firstname_thai"] . " " . $rs_approverQ["lastname_thai"];

//inspector query
$SELECT_inspector_query = "SELECT DISTINCT manager_card_id,
                                            employee.card_id,
                                            employee.firstname_thai,
                                            employee.lastname_thai 
                            FROM manager
                            INNER JOIN employee ON manager.manager_card_id = employee.card_id
                            WHERE manager_card_id != ?
                            ";

$SELECT_inspector_Params = array($rs_approverQ['manager_card_id']);
$rs_inspector = sqlsrv_query($conn, $SELECT_inspector_query, $SELECT_inspector_Params);

if ($rs_inspector  === false) {
    die(print_r(sqlsrv_errors(), true));
}







?>



<script>
    var personID = <?php echo $person_id; ?>
</script>