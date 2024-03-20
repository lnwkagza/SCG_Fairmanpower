
<?php
$serverName = "NB-DARKPEEZ";
$connectionOptions = array(
    "Database" => "fairman",
    "Uid" => "",
    "PWD" => "",
    "CharacterSet" => "UTF-8",
);

// Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    //  echo "Connection established.";
} else {
    echo "Connection could not be established.";
    die(print_r(sqlsrv_errors(), true));
}
?>
