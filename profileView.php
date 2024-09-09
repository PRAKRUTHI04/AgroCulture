<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != 1) {
    $_SESSION['message'] = "You have to log in to view this page!";
    header("Location: Login/error.php");
    exit();
}

// Include database connection file
require 'db.php';

// Fetch Rating from the database
if (!isset($_SESSION['Rating'])) {
    $username = $_SESSION['Username'];
    $stmt = $conn->prepare("SELECT frating FROM farmer WHERE fusername = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($rating);
    if ($stmt->fetch()) {
        $_SESSION['Rating'] = $rating;
    } else {
        $_SESSION['Rating'] = 0; // Default value if not found
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Profile: <?php echo htmlspecialchars($_SESSION['Username']); ?></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="login.css"/>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <link rel="stylesheet" href="css/skel.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/style-xlarge.css" />
</head>

<body>

    <?php
        require 'menu.php';
    ?>

    <section id="one" class="wrapper style1 align">
        <div class="inner">
            <div class="box">
                <header>
                    <center>
                        <span><img src="<?php echo 'images/profileImages/' . htmlspecialchars($_SESSION['picName']) . '?' . mt_rand(); ?>" class="image-circle img-responsive" height="200%"></span>
                        <br>
                        <h2><?php echo htmlspecialchars($_SESSION['Name']); ?></h2>
                        <h4 style="color: black;"><?php echo htmlspecialchars($_SESSION['Username']); ?></h4>
                        <br>
                    </center>
                </header>
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <b><font size="+1" color="black">RATINGS: </font></b>
                        <font size="+1"><?php echo htmlspecialchars($_SESSION['Rating']); ?></font>
                    </div>
                    <div class="col-sm-3">
                        <b><font size="+1" color="black">Email ID: </font></b>
                        <font size="+1"><?php echo htmlspecialchars($_SESSION['Email']); ?></font>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <br />
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <b><font size="+1" color="black">Mobile No: </font></b>
                        <font size="+1"><?php echo htmlspecialchars($_SESSION['Mobile']); ?></font>
                    </div>
                    <div class="col-sm-3">
                        <b><font size="+1" color="black">ADDRESS: </font></b>
                        <font size="+1"><?php echo htmlspecialchars($_SESSION['Addr']); ?></font>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="12u$">
                    <center>
                        <div class="row uniform">
                            <div class="3u 12u$(large)">
                                <a href="changePassPage.php" class="btn btn-danger" style="text-decoration: none;">Change Password</a>
                            </div>
                            <div class="3u 12u$(large)">
                                <a href="profileEdit.php" class="btn btn-danger" style="text-decoration: none;">Edit Profile</a>
                            </div>
                            <div class="3u 12u$(xsmall)">
                                <a href="uploadProduct.php" class="btn btn-danger" style="text-decoration: none;">Upload Product</a>
                            </div>
                            <div class="3u 12u$(large)">
                                <a href="Login/logout.php" class="btn btn-danger" style="text-decoration: none;">LOG OUT</a>
                            </div>
                        </div>
                    </center>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/skel.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>