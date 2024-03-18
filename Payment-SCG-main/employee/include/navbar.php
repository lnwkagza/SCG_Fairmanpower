<style>
	@media (min-width: 1300px) {
		.burger {
			display: none;
		}
	}
</style>

<div class="header">
	<div class="header-left">
		<div class="menu-icon hideInMobile" style="font-size: 2vmax; justify-content: left; display: flex; width: 60px;">
			<img class="burger" src="include/menu.png" style="width:80% ; height:auto; padding-left: 10px;">
		</div>
	</div>
	<div class="header-right">
		<div class="dashboard-setting user-notification">
			<div class="dropdown">
				<button class="notification-btn">
					<i class="fa-regular fa-bell"></i>
				</button>
				<!-- <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
					<i class="fa-regular fa-bell"></i>
				</a> -->
			</div>
		</div>
		<div class="dashboard-setting user-notification">
			<div class="dropdown">
				<button class="notification-btn">
					<i class="fa-solid fa-comment-dots"></i>
				</button>
			</div>
		</div>
		<div class="user-info-dropdown">
			<div class="dropdown">
				<a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
					<span class="user-icon">
						<img src="<?php echo (!empty($row['location'])) ? '../uploads/' . $row['location'] : '../asset/img/admin.png'; ?>" alt="">
					</span>
					<!-- <span class="user-name">นายศิวกร แก้วมาลา</span> -->
					<!-- <span class="user-name"><?php echo  $prefix . $fname . ' ' . $lname . ' ' . $emp_split ?></span> -->

				</a>
				<div class="dropdown-menu dropdown-menu-right ">
					<a class="dropdown-item" href="../../employee/my_profile.php"><i class="fa-regular fa-user" style=" padding-right: 5px;"><br /> </i> ข้อมูลประวัติ</a>
					<a class="dropdown-item" href="../../SCG_Fairmanpower/index.php"><i class="fa-solid fa-arrow-right-from-bracket" style=" padding-right: 5px;"></i> ออกจากระบบ</a>
				</div>
			</div>
		</div>
	</div>
</div>