<?php
include("../database/connectdb.php");
session_start();

if (isset($_SESSION["card_id"])) {
    // Validate and sanitize input
    $teamValue = isset($_POST['team']) ? $_POST['team'] : '';
    $inspectorIds = isset($_POST['inspectorIds']) ? $_POST['inspectorIds'] : '';
    $employeeIds = isset($_POST['employeeIds']) ? $_POST['employeeIds'] : '';

    if (empty($teamValue) || empty($employeeIds)) {
        echo "Invalid input data.";
        exit;
    }

    // Fetch the manager_card_id
    $query = "SELECT manager_card_id FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ?";
    $params = array($_SESSION["card_id"]);
    $stmt = sqlsrv_query($conn, $query, $params);

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $approver_card_id = $row['manager_card_id'];

    // Update employee table with multiple card_id values
    $employeeIdsArray = explode(',', $employeeIds);
    $placeholders = rtrim(str_repeat('?,', count($employeeIdsArray)), ',');

    $query = "UPDATE employee SET sub_team_id = ? WHERE card_id IN ($placeholders)";
    $addvalues = array_merge([$teamValue], $employeeIdsArray);
    
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);
    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Update sub_team table
    $query = "UPDATE sub_team SET approve_status = ?, approver = ?, inspector = ? WHERE sub_team_id = ?";
    $addvalues = array('waiting', $approver_card_id, $inspectorIds, $teamValue);
    $stmt = sqlsrv_prepare($conn, $query, $addvalues);
    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Updates successful.";

} else {
    echo "No card_id in session.";
}
?>
