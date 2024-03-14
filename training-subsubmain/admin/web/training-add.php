<?php
session_start();
require_once '../../connect/connect.php';
if(!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])){
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
<link rel="stylesheet" href="../css/training-add.css">
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
        $image_path = 'imageprofile/'.$row['employee_image'];
        $years = $row['service_year'];
        $months = $row['service_month'];
    ?>
    <?php include('../../components/navbar.php') ?>
    <?php include('../../components/sidebar.php') ?>

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
                                    <div id="add">เพิ่มข้อมูลการฝึกอบรม</div>

                                    <form action="db_training_add.php" method="POST">
                                    <div class="data">
                                        <div class="row1">
                                            <div class="name">
                                                <label for="">ชื่อหลักสูตร<label style="color: red;">*</label></label>
                                                <input type="text" name="training_name" autocomplete="off" required>
                                            </div>
                                            <div class="start">
                                                <label for="">วันที่เริ่มอบรม<label style="color: red;">*</label></label>
                                                <input type="date" name="training_start" required>
                                            </div>
                                            <div class="end">
                                                <label for="">วันที่สิ้นสุดอบรม<label style="color: red;">*</label></label>
                                                <input type="date" name="training_end" required>
                                            </div>
                                            <div class="hour">
                                                <label for="">ชั่วโมงการฝึกอบรม<label style="color: red;">*</label></label>
                                                <input type="text" name="training_totaltime" autocomplete="off" placeholder="กรุณากรอกจำนวนชั่วโมงที่ฝึกอบรม" required>
                                            </div>
                                        </div>
                                        <div class="row2">
                                            <div class="location">
                                                <label for="">สถานที่<label style="color: red;">*</label></label>
                                                <input type="text" name="training_from" autocomplete="off" required>
                                            </div>
                                            <div class="type">
                                                <label for="">ประเภทการฝึกอบรม<label style="color: red;">*</label></label>
                                                <select name="training_type" id="training_type" required>
                                                    <option value="" selected disabled>กรุณาเลือก</option>
                                                    <option value="ภายใน">ภายใน</option>
                                                    <option value="ภายนอก">ภายนอก</option>
                                                </select>
                                            </div>
                                            <div class="notify">
                                                <label for="">แจ้งกรมพัฒนาฝีมือแรงงาน<label style="color: red;">*</label></label>
                                                <select name="training_notifydevelopment" id="training_notifydevelopment" required>
                                                    <option value=" " selected disabled>กรุณาเลือก</option>
                                                    <option value="ไม่แจ้งกรมพัฒนาฝีมือแรงงาน">ไม่แจ้งกรมพัฒนาฝีมือแรงงาน</option>
                                                    <option value="แจ้งกรมพัฒนาฝีมือแรงงาน">แจ้งกรมพัฒนาฝีมือแรงงาน</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row3">
                                            <div class="objective">
                                                <label for="">วัตถุประสงค์<label style="color: red;">*</label></label>
                                                <textarea class="training_objective"  name="training_objective" required></textarea>
                                            </div>
                                            <div class="project-topic">
                                                <label for="">หัวข้อสัมมนา หรือ โครงการ<label style="color: red;">*</label></label>
                                                <textarea class="training_topic" name="training_topic" required></textarea>
                                            </div>
                                        </div>
                                        <div class="row4">
                                            <label for="">วิทยากร<label style="color: red;">*</label></label>
                                            <div class="row4-1">
                                                <input type="text" name="training_lecturer1" autocomplete="off" placeholder="ท่านที่ 1" required>
                                                <input type="text" name="training_lecturer2" autocomplete="off" placeholder="ท่านที่ 2">
                                            </div>
                                            <div class="row4-2">
                                                <input type="text" name="training_lecturer3" autocomplete="off" placeholder="ท่านที่ 3">
                                                <input type="text" name="training_lecturer4" autocomplete="off" placeholder="ท่านที่ 4">
                                            </div>
                                            <div class="row4-3">
                                                <input type="text" name="training_lecturer5" autocomplete="off" placeholder="ท่านที่ 5">
                                                <input type="text" name="training_lecturer6" autocomplete="off" placeholder="ท่านที่ 6">
                                            </div>
                                        </div>

                                        <div class="row5">
                                            <label for="" id="code">รหัสค่าใช้จ่าย</label>
                                            <div class="data-t">
                                                <div class="cost-center">
                                                    <label for="">Cost Center<label style="color: red;">*</label></label>
                                                    <input type="text" name="training_costcenter" autocomplete="off" required>
                                                </div>
                                                <div class="cost-element">
                                                    <label for="">Cost Element<label style="color: red;">*</label></label>
                                                    <input type="text" name="training_costelement" autocomplete="off" required>
                                                </div>
                                                <div class="internal-order">
                                                    <label for="">Internal Order</label>
                                                    <input type="text" name="training_internalorder" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row6">
                                            <div id="code">ประมาณการค่าอบรมต่อหลักสูตร</div>
                                            <div class="data-b">
                                                <div class="c-row1">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าหลักสูตร</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" name="training_courseperperson" autocomplete="off" onchange="calculateTotal()">
                                                    <span class="to">X</span>
                                                    <input type="number" class="people" id="people" name="training_personcourse" autocomplete="off" onchange="calculateTotal()">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" name="training_totalcourse" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row2">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าตั๋วเดินทาง</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" name="training_ticketperperson" autocomplete="off" onchange="calculateTotal()">
                                                    <span class="to">X</span>
                                                    <input type="number" class="people" id="people" name="training_personticket" autocomplete="off" onchange="calculateTotal()">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" name="training_totalticket" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row3">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าเช่ารถ</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" name="training_carperperson" autocomplete="off" onchange="calculateTotal()">
                                                    <span class="to">X</span>
                                                    <input type="number" class="people" id="people" name="training_personcar" autocomplete="off" onchange="calculateTotal()">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" name="training_totalcar" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row4">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าโรงแรม</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" name="training_hotalperperson" autocomplete="off" onchange="calculateTotal()">
                                                    <span class="to">X</span>
                                                    <input type="number" class="people" id="people" name="training_personhotal" autocomplete="off" onchange="calculateTotal()">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" name="training_totalhotal" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="c-row5">
                                                    <input type="checkbox">
                                                    <span id="name">ค่าใช้จ่ายอื่นๆ</span>
                                                    <input class="channel1" type="number" placeholder="กรุณาระบุจำนวนเงิน" name="training_otherperperson" autocomplete="off" onchange="calculateTotal()">
                                                    <span class="to">X</span>
                                                    <input type="number" class="people" id="people" name="training_personother" autocomplete="off" onchange="calculateTotal()">
                                                    <span>คน</span>
                                                    <input class="channel2" type="number" placeholder="กรุณาระบุจำนวนเงินรวม" name="training_totalother" autocomplete="off">
                                                    <span>บาท</span>
                                                </div>
                                                <div class="result">
                                                    <span id="cost-1">รวมค่าอบรมต่อหลักสูตรทั้งหมด</span>
                                                    <input class="channel2-2" type="number" placeholder="สรุปค่าอบรมทั้งหมด" name="training_costtotal" autocomplete="off" required>
                                                    <span>บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row7">
                                            <div id="code">สรุปค่าใช้จ่ายในการอบรมต่อท่าน</div>
                                            <div class="result">
                                                <span id="cost">ราคาต่อท่าน</span>
                                                <input class="channel1-1" type="number" placeholder="จำนวนเงินรวม" name="training_costtotal1" autocomplete="off" disabled>
                                                <span class="to">/</span>
                                                <input type="number" class="people" id="people" name="training_persontotal" autocomplete="off" onchange="calculateTotal()" required>
                                                <span>คน</span>
                                                <input class="channel2-1" type="number" placeholder="ราคารวมต่อท่าน" name="training_costperperson" autocomplete="off" >
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
                                    <button class="save" name="save">บันทึก</button>
                                    
                                    </form>

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
<script>
function calculateTotal() {
    var totalTrainingCost = 0;
    
    var totalPeople = parseFloat(document.getElementsByName("training_persontotal")[0].value) || 0;
    // คำนวณค่าอบรม
    var coursePerPerson = parseFloat(document.getElementsByName("training_courseperperson")[0].value) || 0;
    var coursePeople = parseFloat(document.getElementsByName("training_personcourse")[0].value) || 0;
    var courseTotal = coursePerPerson * coursePeople;
    totalTrainingCost += courseTotal;
    document.getElementsByName("training_totalcourse")[0].value = courseTotal;

    // คำนวณค่าตั๋ว
    var ticketPerPerson = parseFloat(document.getElementsByName("training_ticketperperson")[0].value) || 0;
    var ticketPeople = parseFloat(document.getElementsByName("training_personticket")[0].value) || 0;
    var ticketTotal = ticketPerPerson * ticketPeople;
    totalTrainingCost += ticketTotal;
    document.getElementsByName("training_totalticket")[0].value = ticketTotal;

    // คำนวณค่าเช่ารถ
    var carPerPerson = parseFloat(document.getElementsByName("training_carperperson")[0].value) || 0;
    var carPeople = parseFloat(document.getElementsByName("training_personcar")[0].value) || 0;
    var carTotal = carPerPerson * carPeople;
    totalTrainingCost += carTotal;
    document.getElementsByName("training_totalcar")[0].value = carTotal;

    // คำนวณค่าโรงแรม
    var hotelPerPerson = parseFloat(document.getElementsByName("training_hotalperperson")[0].value) || 0;
    var hotelPeople = parseFloat(document.getElementsByName("training_personhotal")[0].value) || 0;
    var hotelTotal = hotelPerPerson * hotelPeople;
    totalTrainingCost += hotelTotal;
    document.getElementsByName("training_totalhotal")[0].value = hotelTotal;

    // คำนวณค่าใช้จ่ายอื่นๆ
    var otherPerPerson = parseFloat(document.getElementsByName("training_otherperperson")[0].value) || 0;
    var otherPeople = parseFloat(document.getElementsByName("training_personother")[0].value) || 0;
    var otherTotal = otherPerPerson * otherPeople;
    totalTrainingCost += otherTotal;
    document.getElementsByName("training_totalother")[0].value = otherTotal;
    
    // แสดงผลรวมค่าอบรม
    var averagePeople = totalPeople > 0 ? totalTrainingCost / totalPeople : 0;
    document.getElementsByName("training_costtotal")[0].value = totalTrainingCost;
    document.getElementsByName("training_costtotal1")[0].value = totalTrainingCost;
    document.getElementsByName("training_persontotal")[0].value = totalPeople;
    document.getElementsByName("training_costperperson")[0].value = averagePeople.toFixed(2);
}
</script>