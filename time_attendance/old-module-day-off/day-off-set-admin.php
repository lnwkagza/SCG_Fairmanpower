<?php
include('header.php');
?>
<link rel="stylesheet" href="../assets/css/navbar.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/day-off-set.css">
<link rel="stylesheet" href="node_modules\sweetalert2\dist\sweetalert2.css">
<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<title>Document</title>
<script type="text/javascript">
    function myFunction() {
        swal.fire({
            html: '<div style="font-weight: bold; font-size: 20px;">กำหนดวันหยุดทีมแล้ว</div><br>' +
                '<img src="../IMG/check1.png" style="width:80px; margin-top: 2px;  height:80px;"></img>',
            padding: '2em',
            showConfirmButton: false, // ไม่แสดงปุ่มตกลง
            showCancelButton: false // ไม่แสดงปุ่มยกเลิก
        }).then((result) => {
            if (result.isConfirmed) {} else {
                swal("รายการที่เลือกไม่มีการเปลี่ยนแปลง");
            }
        });

    }
</script>
<title>Document</title>
</head>

<body>
    <div class="navbar">
        <div class="div-span">
            <span>กำหนดวันหยุดทีม</span>
        </div>
    </div>
    <div class="container">
        <div class="select-employee">
            <span class="dayTopic">เลือกพนักงาน</span>
            <div class="boxSearch">
                <input type="text" placeholder="ค้นหารหัส/ชื่อพนักงาน">
                <div class="btn-search">
                    <button><img src="../IMG/Search.png" alt=""></button>
                    <button><img src="../IMG/cross.png" alt=""></button>
                </div>
            </div>
        </div>
        <div class="display-dayoff-old">
            <span class="dayTopic">วันหยุดประจำสัปดาห์เดิม</span>
            <div class="display-day-week">
                <span class="dayoff">อา.</span>
                <span>จ.</span>
                <span>อ.</span>
                <span>พ.</span>
                <span>พฤ.</span>
                <span>ศ.</span>
                <span class="dayoff">ส.</span>
            </div>
        </div>
        <div class="display-dayoff-new">
            <span class="dayTopic">วันหยุดประจำสัปดาห์ใหม่</span>
            <div class="display-day-week">
                <button>อา.</button>
                <button>จ.</button>
                <button>อ.</button>
                <button>พ.</button>
                <button>พฤ.</button>
                <button>ศ.</button>
                <button>ส.</button>
            </div>
        </div>
        <div class="display-date-dayoff">
            <div class="date-start">
                <span class="dayTopic">วันเริ่มต้น</span>
                <input type="date">
            </div>
            <div class="date-end">
                <span class="dayTopic">วันสิ้นสุด</span>
                <input type="date">
            </div>
        </div>
        <div class="select-inspector">
            <span class="dayTopic">ชื่อผู้ตรวจสอบ </span>
            <div class="show-inspector">
                <input type="radio" name="inspectorStatus" value="มี" onclick="showInspector()"> มี
                <input type="radio" name="inspectorStatus" value="ไม่มี" onclick="hideInspector()">ไม่มี
                <div id="inspectorshow">
                    <select name="inspector" id="inspector" class="js-example-basic-single">
                        <option value="">เลือกผู้ตรวจสอบ</option>
                        <?php
                        // while ($rs_emp = sqlsrv_fetch_array($stmtapprover, SQLSRV_FETCH_ASSOC)) {
                        //     echo '<option value="' . $rs_emp['card_id'] . '">' . $rs_emp['firstname_thai'] . ' ' . $rs_emp['lastname_thai'] . '</option>';
                        // }
                        ?>
                    </select>
                </div>
            </div>
            <div class="select-approve">
                <span>ชื่อผู้อนุมัติ</span>
                <div class="show-approve">
                    <select name="inspector" id="inspector" class="js-example-basic-single">
                        <option value="">เลือกผู้ตรวจสอบ</option>
                    </select>
                    <div class="detail-approve">
                        <span>ชื่อ- สกุล : เอกพงศ์ มีสุข</span>
                        <span>ตำแหน่ง : Manager</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <input type="submit" value="ยืนยัน" onclick="myFunction()" class="btnConfirm">
        </div>
    </div>
</body>
<?php include('../includes/footer.php') ?>

</html>