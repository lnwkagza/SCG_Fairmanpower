<?php 
    
    session_start();
    require_once 'connect\connect.php';

    if (isset($_POST['login'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email)) {
                $_SESSION['error'] = 'กรุณากรอกอีเมล';
                header("location: login.php");
        }
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'รูปเเบบอีเมลไม่ถูกต้อง';
                header("location: login.php");
        }
            else if (strlen($_POST['password']) <8  ) {
                $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน 8 ตัวขึ้นไป';
                header("location: login.php");
        }

        else {
            try {
                $sql = "SELECT * FROM userid WHERE email = ?";
                $params = array($email);
                $stmt = sqlsrv_query($conn, $sql, $params);
            
                if ($stmt === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
            
                $hasRows = sqlsrv_has_rows($stmt);
            
                if ($hasRows === true) {
                    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        if ($email == $row['email']) {
                            if ($password === $row['password']) {
                                if ($row['role'] == 'admin') {
                                    $_SESSION['admin_login'] = $row['person_id'];
                                    header("location: uploadadminmain.php");
                                } else {
                                    $_SESSION['user_login'] = $row['person_id'];
                                    header("location: mainuserpage.php");
                                }
                            } else {
                                $_SESSION['error'] = 'รหัสผ่านผิด';
                                header("location: login.php");
                            }
                        } else {
                            $_SESSION['error'] = 'อีเมลผิด';
                            header("location: login.php");
                        }
                    }
                } else {
                    $_SESSION['error'] = 'ไม่มีข้อมูลในระบบ';
                    header("location: login.php");
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }


?>