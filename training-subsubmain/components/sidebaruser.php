
 <div class="hamburgur">
        <div class="line1"></div>
        <div class="line1"></div>
        <div class="line2"></div>
    </div>

<div class="menu">
    <div class="imageusersidebar" ><?php echo "<img src='$image_path' class='imgside' alt='รูปภาพ' '> ";?></div>
    <div class="dataname"><?php echo $row['prefix_thai'] . $row['firstname_thai'].' '. $row['lastname_thai']; ?></div>
    <div class="dataposition"><?php echo "ตำเเหน่ง: " . $row['position_name_eng']; ?></div>
    <div class="sidemanu">
        <a href="../../../admin/dashboard.php"><button type="button" class="manusidebar"><i class='bx bxs-home-alt-2' ></i> หน้าหลัก</button></a>
        <a href="mainuserpage.php"><button type="button" class="manusidebar"><i class="bi bi-person-fill"></i></i> ข้อมูลส่วนตัว</button></a>
        <a href="usercoursetargetpage.php"><button type="button" class="manusidebar"><i class="bi bi-book-fill"></i> หลักสูตรที่ต้องเรียนรู้</button></a>
        <a href="usersubcourseotherpage.php"><button type="button" class="manusidebar"><i class="bi bi-journals"></i> หลักสูตรอื่นๆ</button></a>
        <a href="userlearnsummary.php"><button type="button" class="manusidebar"><i class="bi bi-reception-4"></i> สรุปผลการเรียนรู้</button></a>
        <a href="#"><button type="button" class="manusidebar"><i class='bx bxs-error'></i> แจ้งปัญหา</button></a>
    </div>
    <div class="logout"><a href="../../linelogin/index.html"><button type="button" class="logoutsidebar" ><i class="bi bi-box-arrow-right"></i>ออกจากระบบ</button></a></div>
</div>

    <script src="jquery.js"></script>
    <script src="sidebaruser.js"></script>
