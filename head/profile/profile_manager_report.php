                                        <!-- manager_report Tab start -->
                                        <!-- <div class="tab-pane fade" id="manager"> -->
                                        <div class="row" style="justify-content: space-between;">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-30 mb-30 pl-30 pb-30" style="align-items: center;">
                                                <div class="col-md-12">
                                                    <h4 class="text-blue mb-20"><i class="fa-solid fa-user-tie"></i> ผู้จัดการ</h4>
                                                </div>
                                                <div class="pd-30 card-box height-50-p">
                                                    <?php
                                                    if (!empty($manger)) {
                                                        // ตรวจสอบว่า $manger ไม่ว่าง
                                                    ?>
                                                        <div class="profile-photo">
                                                            <img src="<?php echo (!empty($manger['em_img'])) ? '../admin/uploads_img/' . $manger['em_img'] : '../asset/img/admin.png'; ?>" alt="" class="avatar-photo">
                                                        </div>
                                                        <h5 class="text-center text-blue pb-2">
                                                            <?php echo $manger["em_scg_id"]; ?>
                                                            <b><br />
                                                                <a class="text-blue"><?php echo $manger["em_pre"], ' ', $manger["em_fname"], ' ', $manger["em_lname"]; ?>
                                                                </a>
                                                            </b>
                                                        </h5>
                                                        <div class="profile-info">
                                                            <ul>
                                                                <?php
                                                                if (!empty($manger["pm_id"])) {
                                                                    // ตรวจสอบว่า $manger["pm_id"] ไม่ว่าง
                                                                ?>
                                                                    <li class="pb-3">
                                                                        <b class="text-blue" style="padding-right: 5px;">
                                                                            <i class="fa-solid fa-circle-user fa-lg" style="color: #1FBAC0;"></i>
                                                                            บทบาท :
                                                                        </b>
                                                                        <b style="padding: 5px" class='permission-<?php echo $manger["pm_id"]; ?>'>
                                                                            <?php echo  $manger["pm_name"]; ?>
                                                                        </b>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($manger["em_email"])) {
                                                                    // ตรวจสอบว่า $manger["em_email"] ไม่ว่าง
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-solid fa-envelope fa-lg" style="color: #1FBAC0;"></i> Email : </b><a class='text-primary'><?php echo  ' ' . $manger["em_email"]; ?></a>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($manger["em_phone"]) && strlen($manger["em_phone"]) == 10) {
                                                                    // ตรวจสอบว่า $manger["em_phone"] ไม่ว่าง และมีขนาด 10 ตัว
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-solid fa-phone fa-lg" style="color: #1FBAC0;"></i> เบอร์โทร : </b>
                                                                        <a class='text-primary'>
                                                                            <?php
                                                                            $phone_number = $manger["em_phone"];
                                                                            $formatted_phone = substr($phone_number, 0, 3) . '-' . substr($phone_number, 3);
                                                                            echo ' ' . $formatted_phone;
                                                                            ?>
                                                                        </a>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($manger["department"])) {
                                                                    // ตรวจสอบว่า $manger["department"] ไม่ว่าง
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-regular fa-building fa-xl" style="color: #1FBAC0;"></i> แผนก : </b><a class='text-primary'><?php echo  ' ' . $manger["department"]; ?></a><br />
                                                                        <?php
                                                                        if (!empty($manger["section"])) {
                                                                            // ตรวจสอบว่า $manger["section"] ไม่ว่าง
                                                                        ?>
                                                                            <b class="text-blue" style="padding-left: 20px;">Section : </b><a class='text-primary'><?php echo  ' ' . $manger["section"]; ?></a><br />
                                                                        <?php
                                                                        }
                                                                        if (!empty($manger["em_cost"])) {
                                                                            // ตรวจสอบว่า $manger["em_cost"] ไม่ว่าง
                                                                        ?>
                                                                            <b class="text-blue" style="padding-left: 20px;"> Cost-Center :</b><a class='text-primary'><?php echo  '  ' . $manger["em_cost"]; ?></a><br />
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    <?php
                                                    } else {
                                                        // กรณี $r_port ว่าง
                                                        echo "ท่านยังไม่ถูกระบุ ผู้จัดการ";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mt-30 mb-30 pr-30 pb-30" style="align-items: center;">
                                                <div class="col-md-12">
                                                    <h4 class="text-blue mb-20"><i class="fa-solid fa-user-tie"></i> Report-to</h4>
                                                </div>
                                                <div class="pd-30 card-box height-50-p">
                                                    <?php
                                                    if (!empty($r_port)) {
                                                        // ตรวจสอบว่า $r_port ไม่ว่าง
                                                    ?>
                                                        <div class="profile-photo">
                                                            <img src="<?php echo (!empty($r_port['em_img'])) ? '../admin/uploads_img/' . $r_port['em_img'] : '../asset/img/admin.png'; ?>" alt="" class="avatar-photo">
                                                        </div>
                                                        <h5 class="text-center text-blue pb-2">
                                                            <?php echo $r_port["em_scg_id"]; ?>
                                                            <b><br />
                                                                <a class="text-blue"><?php echo $r_port["em_pre"], ' ', $r_port["em_fname"], ' ', $r_port["em_lname"]; ?>
                                                                </a>
                                                            </b>
                                                        </h5>
                                                        <div class="profile-info">
                                                            <ul>
                                                                <?php
                                                                if (!empty($r_port["pm_id"])) {
                                                                    // ตรวจสอบว่า $r_port["pm_id"] ไม่ว่าง
                                                                ?>
                                                                    <li class="pb-3">
                                                                        <b class="text-blue" style="padding-right: 5px;">
                                                                            <i class="fa-solid fa-circle-user fa-lg" style="color: #1FBAC0;"></i>
                                                                            บทบาท :
                                                                        </b>
                                                                        <b style="padding: 5px" class='permission-<?php echo $r_port["pm_id"]; ?>'>
                                                                            <?php echo  $r_port["pm_name"]; ?>
                                                                        </b>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($r_port["em_email"])) {
                                                                    // ตรวจสอบว่า $r_port["em_email"] ไม่ว่าง
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-solid fa-envelope fa-lg" style="color: #1FBAC0;"></i> Email : </b><a class='text-primary'><?php echo  ' ' . $r_port["em_email"]; ?></a>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($r_port["em_phone"]) && strlen($r_port["em_phone"]) == 10) {
                                                                    // ตรวจสอบว่า $r_port["em_phone"] ไม่ว่าง และมีขนาด 10 ตัว
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-solid fa-phone fa-lg" style="color: #1FBAC0;"></i> เบอร์โทร : </b>
                                                                        <a class='text-primary'>
                                                                            <?php
                                                                            $phone_number = $r_port["em_phone"];
                                                                            $formatted_phone = substr($phone_number, 0, 3) . '-' . substr($phone_number, 3);
                                                                            echo ' ' . $formatted_phone;
                                                                            ?>
                                                                        </a>
                                                                    </li>
                                                                <?php
                                                                }
                                                                if (!empty($r_port["department"])) {
                                                                    // ตรวจสอบว่า $r_port["department"] ไม่ว่าง
                                                                ?>
                                                                    <li>
                                                                        <b class="text-blue"><i class="fa-regular fa-building fa-xl" style="color: #1FBAC0;"></i> แผนก : </b><a class='text-primary'><?php echo  ' ' . $r_port["department"]; ?></a><br />
                                                                        <?php
                                                                        if (!empty($r_port["section"])) {
                                                                            // ตรวจสอบว่า $r_port["section"] ไม่ว่าง
                                                                        ?>
                                                                            <b class="text-blue" style="padding-left: 20px;">Section : </b><a class='text-primary'><?php echo  ' ' . $r_port["section"]; ?></a><br />
                                                                        <?php
                                                                        }
                                                                        if (!empty($r_port["em_cost"])) {
                                                                            // ตรวจสอบว่า $r_port["em_cost"] ไม่ว่าง
                                                                        ?>
                                                                            <b class="text-blue" style="padding-left: 20px;"> Cost-Center :</b><a class='text-primary'><?php echo  '  ' . $r_port["em_cost"]; ?></a><br />
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </li>
                                                                <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    <?php
                                                    } else {
                                                        // กรณี $r_port ว่าง
                                                        echo "ท่านยังไม่ถูกระบุ Report-to";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                        <!-- manager_report Tab End -->