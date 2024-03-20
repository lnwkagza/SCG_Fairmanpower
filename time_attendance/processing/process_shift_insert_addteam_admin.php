<?php
include("../database/connectdb.php");
session_start();

// Check if the user is authenticated
if (isset($_SESSION["card_id"])) {

    // Query to retrieve manager_card_id
    $query = "SELECT manager_card_id FROM manager INNER JOIN employee ON manager.manager_card_id = employee.card_id WHERE manager.card_id = ?";
    $params = array($_SESSION["card_id"]);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the manager_card_id
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $approver_card_id = $row['manager_card_id'];

    // Query to retrieve cost_center_organization_id
    $card_id = $_SESSION["card_id"];
    $section = "SELECT cost_center_organization_id FROM employee WHERE card_id = ?";
    $params2 = array($card_id);
    $shiftsection = sqlsrv_query($conn, $section, $params2);

    if ($shiftsection === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the cost_center_organization_id
    $row2 = sqlsrv_fetch_array($shiftsection, SQLSRV_FETCH_ASSOC);

    $nameteamValue = isset($_POST['nameteam']) ? $_POST['nameteam'] : ''; // Validate and sanitize user input

    // Validate input data
    if (!empty($nameteamValue)) {

        // Insert into sub_team table
        $sql = "INSERT INTO sub_team (name, head_card_id, cost_center_id,request_card_id) VALUES (?, ?, ?,?)";
        $params = array($nameteamValue, $approver_card_id, $row2["cost_center_organization_id"],$_SESSION["card_id"]);
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            echo "Record inserted successfully";
        }
    } else {
        echo "Invalid input for 'nameteam'";
    }

} else {
    echo "No card_id in session.";
}
?>
