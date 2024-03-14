<div class="navbar">
            <!-- <div class="search-container">
                <div class="searcs">
                <button type="submit" class="searcsbar"><i class="bi bi-search"></i></button>
                <input type="text" placeholder="Searcs here...">
                </div>
            </div> -->

            <a href="#"><button type="button"class="Notifications"><i class='bx bxs-bell-ring'></i></button></a>

            <div class="profile">
                <div class="imageuser" ><?php echo "<img class='imageprofile' src='$image_path' alt='รูปภาพ' style='width: 4vmax;height: auto;'> ";?></div>
                <div class="data">
                    <div class="nameprofile"><?php echo  $row['firstname_th'].' '. $row['lastname_th']; ?></div>
                    <div class="role"><?php echo $row['role'] ?></div>
                </div>
            </div>
        </div>