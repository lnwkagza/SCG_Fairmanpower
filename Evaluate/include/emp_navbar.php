<div class="header">
	<div class="header-left">
		<div class="pl-2 menu-icon">
			<i class="fa-solid fa-bars fa-lg"></i>
		</div>

	</div>
	<div class="header-right">
		<div class="user-info-dropdown">
			<div class="dropdown ">
				<a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
					<span class="user-icon">
						<img src="<?php echo (!empty($e_row['employee_image'])) ? '../admin/uploads_img/' . $e_row['employee_image'] : '../asset/img/admin.png'; ?>" width="60" height="60">
					</span>
					<span class="user-name">
						<label>
							<?php echo  $prefix . $fname . ' ' . $lname ?><br/>
							บทบาท : <?php echo $e_row['permission'] ?>
						</label>

					</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="../employee/my_profile.php"><i class="fa-regular fa-user" style=" padding-right: 5px;"><br /> </i> ข้อมูลประวัติ</a>
					<a class="dropdown-item" href="../../SCG_Fairmanpower/index.php"><i class="fa-solid fa-arrow-right-from-bracket" style=" padding-right: 5px;"></i> ออกจากระบบ</a>
				</div>
			</div>
		</div>
	</div>
</div>