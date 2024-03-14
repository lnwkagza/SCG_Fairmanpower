<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/Navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <nav>
        <div class="logo">
            <img src="img/siam-cement-group-scg-logo-vector.png" class="imglogo">
            <!-- <span class="title">ระบบการประเมิน</span> -->
        </div>

        <ul class="menu">
            <li><img src="img/profile.png" alt="profile" class="profile"></li>
            <li><span><?php echo  $prefix . ' ' . $fname . ' ' . $lname ?></span></li>
        </ul>
    </nav>
</body>
</html>