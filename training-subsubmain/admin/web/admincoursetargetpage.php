<?php 
        session_start();
        require_once '../../connect/connect.php';
        if(!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])){
            $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
            header('location: ../../../../linelogin/index.html');
            exit();
        }

        // ถ้าผู้ใช้กด Logout
        if(isset($_GET['logout']) && isset($_SESSION['admin_login'])) {
            // ลบ session และส่งผู้ใช้กลับไปยังหน้าล็อกอิน
            unset($_SESSION['admin_login']);
            $_SESSION['error'] = 'คุณได้ออกจากระบบแล้ว';
            header('location: ../../../../linelogin/index.html');
            exit();
        }
        unset($_SESSION['chapterId']);
    ?>

        <?php include('../../components/head.php') ?>
        <link rel="stylesheet" href="../../plugin/bootstrap-icons-1.11.2/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../../plugin/boxicons-2.1.4/css/boxicons.min.css">
        <link rel="stylesheet" href="../css/admincoursepage.css">
        <title>Admin Page</title>
    <body>

    <?php

    if(isset($_SESSION['user_login'])) {
        $user_id = $_SESSION['user_login'];
    } elseif(isset($_SESSION['admin_login'])) {
        $user_id = $_SESSION['admin_login'];
    } else {
        // ทำการกำหนดค่าเริ่มต้นสำหรับ $user_id ในกรณีที่ไม่มีค่าใน session ที่ต้องการ
        $user_id = null; // หรือค่าอื่น ๆ ตามที่ต้องการ
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
    $image_path = '../../../admin/uploads_img/'.$row['employee_image'];
    $years = $row['service_year'];
    $months = $row['service_month'];


        // $sql1 = "SELECT *FROM Tablepositiontarget 
        // JOIN Tablechapter ON Tablechapter.chapter_id = Tablepositiontarget.chapter_id
        // JOIN Tablecourse ON Tablecourse.course_id = Tablechapter.course_id WHERE Tablepositiontarget.person_id = ? ORDER BY course_date DESC";
        
        // $sql1 = "SELECT *, Tablecourse.course_id AS course_code, Tablechapter.VDO AS clip, Tablechapter.chapter_id AS chapterid  FROM Tablecourse 
        // LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
        // LEFT JOIN Tabletrainningdata ON Tabletrainningdata.chapter_id = Tablechapter.chapter_id 
        // WHERE Tabletrainningdata.person_id = ? AND Tablechapter.chapter_type = 'ตามตำแหน่ง' 
        //     AND (Tablecourse.course_name LIKE '%$search_query%' OR Tablechapter.chapter_name LIKE '%$search_query%')
        // ORDER BY CASE WHEN status_total = 2 THEN 1 ELSE 0 END, course_date DESC";
        $search_query = isset($_GET['search']) ? $_GET['search'] : '';
        $sql1 = "SELECT *, Tablecourse.course_id AS course_code, Tablechapter.VDO AS clip, Tablechapter.chapter_id AS chapterid  FROM Tablecourse 
        LEFT JOIN Tablechapter ON Tablechapter.course_id = Tablecourse.course_id
        LEFT JOIN Tablepositiontarget ON Tablepositiontarget.chapter_id = Tablechapter.chapter_id 
        LEFT JOIN Tabletrainningdata ON Tabletrainningdata.chapter_id = Tablechapter.chapter_id 
        AND Tabletrainningdata.person_id = Tablepositiontarget.person_id
        WHERE Tablepositiontarget.person_id = ? AND Tablechapter.chapter_type = N'ตามตำแหน่ง' AND (Tabletrainningdata.person_id = ? OR Tabletrainningdata.person_id IS NULL)
            AND (Tablecourse.course_name LIKE N'%$search_query%' OR Tablechapter.chapter_name LIKE N'%$search_query%')
        ORDER BY CASE WHEN status_total = 2 THEN 1 ELSE 0 END, course_date DESC";
        $params1 = array($user_id, $user_id);
        $stmt1 = sqlsrv_query($conn, $sql1, $params1);
        if ($stmt1 === false) {
            die(print_r(sqlsrv_errors(), true));
        }

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
                                <span id="head">หลักสูตรที่ต้องเรียนรู้</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                        <div class="card-box pd-10 pt-10 height-100-p">
                            <div class="bar">
                                <div class="mainclass">
                                    <div class="contianer">
                                        <div class="searchcon">
                                            <form action="" method="GET">
                                                <div class="searchbar">
                                                    <div class="inputsearch">
                                                        <i class='bx bx-search'></i>
                                                        <input type="text" name="search" placeholder="Search..." autocomplete="off">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="manucon">
                                            <div class="manuselect">
                                                <div class="manuon"><a href="#"><button>ตามตำเเหน่ง</button></a></div>
                                                <div class="manuoff"><a href="admincourselawpage.php"><button>กฎหมายบังคับ</button></a></div>
                                                <div class="manuoff"><a href="admincoursebasicpage.php"><button>พื้นฐาน</button></a></div>
                                                <div class="manuoff"><a href="admincourseotherpage.php"><button>อื่นๆ</button></a></div>
                                            </div>

                                            <div class="manucourse">
                                                <ul class="accordion-menu">
                                                    <?php
                                                    $grouped_data = []; // สร้าง array เพื่อจัดกลุ่มข้อมูล

                                                    while ($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                                                        $course_code = $row1['course_code'];
                                                        $video_filename = $row1['clip'];
                                                        $video_directory = '../../data/VDO/';
                                                        $video_url = $video_directory . $video_filename;

                                                        // ถ้ายังไม่มีการจัดกลุ่มสำหรับ course code นี้ สร้าง array ใหม่
                                                        if (!isset($grouped_data[$course_code])) {
                                                            $grouped_data[$course_code] = [
                                                                'course_name' => $row1['course_name'],
                                                                'chapter_id' => [],
                                                                'chapters' => [], // เตรียม array เพื่อเก็บ chapters
                                                                'videos' => [] // เตรียม array เพื่อเก็บ videos
                                                            ];
                                                        }

                                                        // เพิ่ม chapter เข้าไปใน array ของ course code นี้
                                                        $grouped_data[$course_code]['chapter_id'][] = $row1['chapterid'];

                                                        // เพิ่ม chapter เข้าไปใน array ของ course code นี้
                                                        $grouped_data[$course_code]['chapters'][] = $row1['chapter_name'];

                                                        // เพิ่มข้อมูล video เข้าไปใน array ของ course code นี้
                                                        $grouped_data[$course_code]['videos'][] = $video_url;
                                                    }

                                                    // วนลูปแสดงผล
                                                    if (!empty($grouped_data)) {
                                                        foreach ($grouped_data as $course_code => $course_data) {
                                                    ?>
                                                            <li>
                                                                <div class="dropdownlink">
                                                                    <?php echo $course_code ?> : <?php echo $course_data['course_name'] ?>
                                                                    <i class="bi bi-caret-down-fill" aria-hidden="true"></i>
                                                                </div>
                                                                <ul class="submenuItems">
                                                                    <?php
                                                                    foreach ($course_data['chapters'] as $index => $chapter_name) {
                                                                        $video_url = $course_data['videos'][$index]; // แก้ไขตรงนี้เพื่อดึง URL ของวิดีโอในแต่ละตอน
                                                                        $chapter_id = $course_data['chapter_id'][$index];
                                                                    ?>
                                                                        <a href="#" onclick="openVideo('<?php echo $video_url; ?>', '<?php echo $course_code; ?>', '<?php echo $course_data['course_name']; ?>', '<?php echo $chapter_name; ?>', '<?php echo $chapter_id; ?>')">
                                                                            <i class="bi bi-play-circle"></i><?php echo $chapter_name; ?>
                                                                        </a>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <table>
                                                            <tr>
                                                                <td>ไม่มีหลักสูตร</td>
                                                            </tr>
                                                        </table>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
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
    <script src="submanu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
            function openVideo(videoURL, courseCode, courseName, chapterName, chapterId) {
            console.log("Video URL:", videoURL);
            console.log("Course Code:", courseCode);
            console.log("Course Name:", courseName);
            console.log("Chapter Name:", chapterName);
            console.log("Chapter ID:", chapterId);

            var videoContainer = document.createElement('div');
            var videoElement;
            videoContainer.innerHTML = `
                <div id="videoPopup" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); display: flex; justify-content: center; align-items: center; z-index: 9999;">
                    <video controls autoplay  style="max-width: 80%; max-height: 80%;" onended="redirectToNextPage('${chapterId}', '${courseName}')">
                        <source src="${videoURL}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <button onclick="closeVideoPopup()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #fff; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
            `;
            document.body.appendChild(videoContainer);

            videoElement = videoContainer.querySelector('video');
            videoElement.addEventListener('loadedmetadata', function () {
                // เมื่อเกิดเหตุการณ์ loadedmetadata, เปิดใช้โหมดเต็มหน้าจอโดยอัตโนมัติ
                openFullscreen(videoElement);
            });

            videoElement.addEventListener('ended', function () {
                redirectToNextPage(chapterId, courseName);
            });
        }

        function openFullscreen(elem) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.mozRequestFullScreen) { /* Firefox */
                elem.mozRequestFullScreen();
            } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) { /* IE/Edge */
                elem.msRequestFullscreen();
            }
        }

        function redirectToNextPage(chapterId, courseName) {
            // แสดง SweetAlert2 โดยกำหนด z-index สูงกว่า
            Swal.fire({
                title: 'คลิปจบแล้ว',
                html: `<span style="font-size: 5vmin;">ต้องการทำแบบทดสอบต่อหรือไม่?</span> `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ใช่',
                cancelButtonText: 'เรียนบทอื่นก่อน',
                customClass: {
                    container: 'sweet-container',
                    popup: 'sweet-popup',
                    header: 'sweet-header',
                    title: 'sweet-title',
                    closeButton: 'sweet-close-button',
                    icon: 'sweet-icon',
                    content: 'sweet-content',
                    input: 'sweet-input',
                    actions: 'sweet-actions',
                    confirmButton: 'sweet-confirm-button',
                    cancelButton: 'sweet-cancel-button',
                    footer: 'sweet-footer'
                }
            }).then((result) => {
                // ไม่ว่าผู้ใช้จะกด "ใช่" หรือ "เรียนบทอื่นก่อน" ก็ส่งข้อมูลไปยัง PHP script
                $.ajax({
                    type: 'POST',
                    url: '../../backend/db_autoinsertstatusclip.php',
                    data: {
                        chapterId: chapterId,
                        courseName: courseName
                    },
                    success: function (response) {
                        console.log(response);
                        // ทำตามที่คุณต้องการทำหลังจากส่งข้อมูลไปยัง PHP script

                        // ตรวจสอบผลลัพธ์จาก SweetAlert2 และดำเนินการต่อไป
                        if (result.isConfirmed) {
                            // Redirect ไปยังหน้าถัดไป
                            window.location.href = 'admintakequizpage.php';
                        } else {
                            // ปิดคลิปพร้อมกับ SweetAlert2
                            closeVideoPopup();
                        }
                    },
                    error: function (error) {
                        console.error('Error sending data:', error);
                        // กรณีเกิดข้อผิดพลาดในการส่งข้อมูลไปยัง PHP script
                        // ตัวอย่างการเปลี่ยนหน้าไปยังหน้าที่ต้องการ
                        window.location.href = 'admintakequizpage.php';
                    }
                });
            });
        }

        // เพิ่ม style ด้านล่างเพื่อกำหนด z-index สูงกว่า
        const style = document.createElement('style');
        style.innerHTML = `
        .sweet-container {
            z-index: 99999 !important;
        }
        `;
        document.head.appendChild(style);

        function closeVideoPopup() {
            var videoPopup = document.getElementById('videoPopup');
            if (videoPopup) {
                videoPopup.remove();
            }
        }

    </script>
