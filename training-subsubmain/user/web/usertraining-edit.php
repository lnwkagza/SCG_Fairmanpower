<?php
session_start();
require_once '../../connect/connect.php';
if(!isset($_SESSION['user_login'])){
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

// ถ้าผู้ใช้กด Logout
if (isset($_GET['logout']) && isset($_SESSION['user_login'])) {
    // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
    unset($_SESSION['user_login']);
    $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
    header('location:  ../../../../linelogin/index.html');
    exit();
}

?>

<?php include('../../components/head.php') ?>
<link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
<link rel="stylesheet" href="../css/usertraining-add.css">
<title>AdminPage</title>

<body>
<?php 
        if(isset($_SESSION['user_login'])) {
            $user_id = $_SESSION['user_login'];
            echo $user_id;
        } elseif(isset($_SESSION['admin_login'])) {
            $user_id = $_SESSION['admin_login'];
            echo $user_id;
        } else {
            // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
            $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
            echo $user_id;
        }
        $role = $user_id;
        $sql = "SELECT 
        employee.employee_image,
        employee.card_id,
        employee.person_id,
        employee.scg_employee_id,
        employee.prefix_thai,
        employee.firstname_thai,
        employee.lastname_thai,
        employee.service_year,
        employee.service_month,
        employee.skill,
        position.name_eng AS position_name_eng,
        section.name_eng AS section_name_eng,
        department.name_eng AS department_name_eng,
        permission.name AS permission
        FROM employee 
        LEFT JOIN position_info ON employee.card_id = position_info.card_id
        LEFT JOIN position ON position_info.position_id = position.position_id
        LEFT JOIN cost_center ON employee.cost_center_organization_id = cost_center.cost_center_id
        LEFT JOIN section ON cost_center.section_id = section.section_id
        LEFT JOIN department ON section.department_id = department.department_id
        LEFT JOIN permission ON employee .permission_id = permission.permission_id  
         
                WHERE employee.person_id = ?";
        $params = array($user_id);
        $stmt = sqlsrv_query($conn, $sql, $params);
        if( $stmt === false ) {
            die( print_r( sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $image_path = '../../data/imageprofile/'.$row['employee_image'];
        $years = $row['service_year'];
        $months = $row['service_month'];
    ?>
    <?php include('../../components/navbaruserall.php') ?>
    <?php include('../../components/sidebaruserall.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pl-10 pt-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <span id="head">การฝึกอบรม</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div id="add">แก้ไขข้อมูลการฝึกอบรม</div>
                                    <div class="data">
                                        <div class="row1">
                                            <div class="name">
                                                <label for="">ชื่อหลักสูตร<label style="color: red;">*</label></label>
                                                <input type="text" autocomplete="off" required>
                                            </div>
                                            <div class="start">
                                                <label for="">วันที่เริ่มอบรม<label style="color: red;">*</label></label>
                                                <input type="date" required>
                                            </div>
                                            <div class="end">
                                                <label for="">วันที่สิ้นสุดอบรม<label style="color: red;">*</label></label>
                                                <input type="date" required>
                                            </div>
                                            <div class="hour">
                                                <label for="">ชั่วโมงการฝึกอบรม<label style="color: red;">*</label></label>
                                                <input type="text" autocomplete="off" placeholder="กรุณากรอกจำนวนชั่วโมงที่ฝึกอบรม" required>
                                            </div>
                                        </div>
                                        <div class="row2">
                                            <div class="location">
                                                <label for="">สถานที่<label style="color: red;">*</label></label>
                                                <input type="text" autocomplete="off" required>
                                            </div>
                                            <div class="type">
                                                <label for="">ประเภทการฝึกอบรม<label style="color: red;">*</label></label>
                                                <select name="" id="" required>
                                                    <option value="" selected disabled>กรุณาเลือก</option>
                                                    <option value="">ภายใน</option>
                                                    <option value="">ภายนอก</option>
                                                </select>
                                            </div>
                                            <div class="notify">
                                                <label for="">แจ้งกรมพัฒนาฝีมือแรงงาน<label style="color: red;">*</label></label>
                                                <select name="" id="" required>
                                                    <option value="" selected disabled>กรุณาเลือก</option>
                                                    <option value="">ไม่แจ้งกรมพัฒนาฝีมือแรงงาน</option>
                                                    <option value="">แจ้งกรมพัฒนาฝีมือแรงงาน</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row3">
                                            <div class="objective">
                                                <label for="">วัตถุประสงค์<label style="color: red;">*</label></label>
                                                <textarea class="" required></textarea>
                                            </div>
                                            <div class="project-topic">
                                                <label for="">หัวข้อสัมมนา หรือ โครงการ<label style="color: red;">*</label></label>
                                                <textarea class="" required></textarea>
                                            </div>
                                        </div>
                                        <div class="row4">
                                            <label for="">วิทยากร<label style="color: red;">*</label></label>
                                            <div class="row4-1">
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 1" required>
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 2">
                                            </div>
                                            <div class="row4-2">
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 3">
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 4">
                                            </div>
                                            <div class="row4-3">
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 5">
                                                <input type="text" autocomplete="off" placeholder="ท่านที่ 6">
                                            </div>
                                        </div>

                                        <div class="row5">
                                            <label for="" id="code">รหัสค่าใช้จ่าย</label>
                                            <div class="data-t">
                                                <div class="cost-center">
                                                    <label for="">Cost Center<label style="color: red;">*</label></label>
                                                    <input type="text" autocomplete="off" required>
                                                </div>
                                                <div class="cost-element">
                                                    <label for="">Cost Element<label style="color: red;">*</label></label>
                                                    <input type="text" autocomplete="off" required>
                                                </div>
                                                <div class="internal-order">
                                                    <label for="">Internal Order</label>
                                                    <input type="text" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row6">
                                            <div id="code">ประมาณการค่าอบรมต่อหลักสูตร</div>
                                            <div class="data-b">
                                                <div class="c-row1">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าหลักสูตร</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                                    <span class="to">X</span>
                                                    <input type="number" id="people" autocomplete="off">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row2">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าตั๋วเดินทาง</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                                    <span class="to">X</span>
                                                    <input type="number" id="people" autocomplete="off">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row3">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าเช่ารถ</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                                    <span class="to">X</span>
                                                    <input type="number" id="people" autocomplete="off">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row4">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าโรงแรม</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                                    <span class="to">X</span>
                                                    <input type="number" id="people" autocomplete="off">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row5">
                                                    <input type="checkbox">
                                                    <span  id="name">ค่าใช้จ่ายอื่นๆ</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" autocomplete="off">
                                                    <span class="to">X</span>
                                                    <input type="number" id="people" autocomplete="off">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="result">
                                                    <span id="cost-1">รวมค่าอบรมต่อหลักสูตรทั้งหมด</span>
                                                    <input class="channel2-2" type="number" placeholder="สรุปค่าอบรมทั้งหมด" autocomplete="off" required>
                                                    <span>บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row7">
                                            <div id="code">สรุปค่าใช้จ่ายในการอบรมต่อท่าน</div>
                                            <div class="result">
                                                <span id="cost">ราคาต่อท่าน</span>
                                                <input class="channel1-1" type="number" placeholder="จำนวนเงินรวม" autocomplete="off" disabled>
                                                <span class="to">/</span>
                                                <input type="number" id="people" autocomplete="off" required>
                                                <span>คน</span>
                                                <input class="channel2-1" type="number" placeholder="ราคารวมต่อท่าน" autocomplete="off" disabled>
                                                <span>บาท</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="datatable">
                                        <table>
                                            <thead>
                                                <th><input type="checkbox"></th>
                                                <th>พนักงาน</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="checkbox"></td>
                                                    <td>Data Driven</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a class="save" href="usertraining-preview.php">บันทึก</a>


                                </div>
                            </div>
                        </div>

                    </div>


                </div>

            </div>
        </div>
    </div>

    <?php include('../../components/script.php') ?>
</body>

</html>