<?php
include("../database/connectdb.php");
include('../includes/header.php');
define('CLIENT_ID', '7PMPgTculCaIGVLyoo8U4x');
define('CLIENT_SECRET', '4vbCR8irEAwoXXvRSqMtMfuCFqlQmCOL03EAoCRFSOE');
define('LINE_API_URI', 'https://notify-bot.line.me/oauth/token');
define('CALLBACK_URI', 'https://localhost/Timefair/line/callback.php');
parse_str($_SERVER['QUERY_STRING'], $queries);

$fields = [
    'grant_type' => 'authorization_code',
    'code' => $queries['code'],
    'redirect_uri' => CALLBACK_URI,
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET
];

try {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, LINE_API_URI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $res = curl_exec($ch);
    curl_close($ch);

    if ($res === false) {
        throw new Exception(curl_error($ch), curl_errno($ch));
    }

    $json = json_decode($res);
    $token = $json->access_token;
} catch (Exception $e) {
    var_dump($e);
}

$line_token = $_POST['line_token'];
$card_id = $_POST['card_id'];

if (isset($_POST["submit"])) {
    $query = "SELECT * FROM login WHERE card_id = ?";
    $params = array($card_id);
    $stmt = sqlsrv_prepare($conn, $query, $params);

    sqlsrv_execute($stmt);

    if (sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_object($stmt);

        if (is_null($row->line_id)) {
            $updateQuery = "UPDATE login SET line_token = ? WHERE card_id = ?";
            $updateParams = array($line_token, $card_id);
            $updateStmt = sqlsrv_prepare($conn, $updateQuery, $updateParams);

            if (!$updateStmt || !sqlsrv_execute($updateStmt)) {
                die(print_r(sqlsrv_errors(), true));
            }

            header("Location: ../index.html");
            exit;
        } else {
            header("Location: ../index.html");
            exit;
        }
    } else {
        $insertQuery = "INSERT INTO login (line_token, card_id) VALUES (?, ?)";
        $insertParams = array($line_token, $card_id);
        $insertStmt = sqlsrv_prepare($conn, $insertQuery, $insertParams);

        if (!$insertStmt || !sqlsrv_execute($insertStmt)) {
            $errors = sqlsrv_errors();

            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                echo "Code: " . $error['code'] . "<br />";
                echo "Message: " . $error['message'] . "<br />";
            }

            die("An error occurred while inserting a new record.");
        }

        header("Location: ../index.html");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../assets/css/register.css">
    <title>ลงทะเบียน</title>
</head>

<body>
    <div class="container">
        <img src="../IMG/scg.png" alt="" style="width: 25%; margin-left: -70%;">
        <hr style="width: 100%; border: 1px solid #B0B2B3;">
        <div class="imgLogo">
            <img src="../IMG/LOGOTIME1.png" alt="">
        </div>

        <div class="box-confirm">
            <span>ลงทะเบียน</span>
            <form method="POST" class="register">
                <input name="line_token" type="hidden" value="<?php echo $token; ?>" />
                <input type="text" name="card_id" placeholder="รหัสพนักงาน 0150-xxxxxxx" />
                <button type="submit" name="submit">บันทึกข้อมูล</button>
            </form>
        </div>

    </div>
</body>

</html>