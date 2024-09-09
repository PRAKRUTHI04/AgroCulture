<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = dataFilter($_POST['name']);
    $username = dataFilter($_POST['uname']);
    $mobile = dataFilter($_POST['mobile']);
    $email = dataFilter($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $address = dataFilter($_POST['addr']);
    $aadhaar = dataFilter($_POST['aadhaar']);
    $category = dataFilter($_POST['category']);
    $hash = md5(rand(0, 1000));

    $_SESSION['Email'] = $email;
    $_SESSION['Name'] = $name;
    $_SESSION['Password'] = $password;
    $_SESSION['Username'] = $username;
    $_SESSION['Mobile'] = $mobile;
    $_SESSION['Category'] = $category;
    $_SESSION['Hash'] = $hash;
    $_SESSION['Addr'] = $address;
    $_SESSION['Aadhaar'] = $aadhaar;

    require '../db.php';

    $length = strlen($mobile);

    if ($length != 10) {
        $_SESSION['message'] = "Invalid Mobile Number !!!";
        header("location: error.php");
        die();
    }

    if ($category == 1) {
        $sql = "SELECT * FROM farmer WHERE femail='$email'";
        $result = mysqli_query($conn, $sql) or die($mysqli->error());

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "User with this email already exists!";
            header("location: error.php");
        } else {
            $sql = "INSERT INTO farmer (fname, fusername, fpassword, fhash, fmobile, femail, faddress, aadhar)
                    VALUES ('$name','$username','$password','$hash','$mobile','$email','$address', '$aadhaar')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['Active'] = 0;
                $_SESSION['logged_in'] = true;
                $_SESSION['picStatus'] = 0;
                $_SESSION['picExt'] = 'png';

                $sql = "SELECT * FROM farmer WHERE fusername='$username'";
                $result = mysqli_query($conn, $sql);
                $User = $result->fetch_assoc();
                $_SESSION['id'] = $User['fid'];

                if ($_SESSION['picStatus'] == 0) {
                    $_SESSION['picId'] = 0;
                    $_SESSION['picName'] = "profile0.png";
                } else {
                    $_SESSION['picId'] = $_SESSION['id'];
                    $_SESSION['picName'] = "profile" . $_SESSION['picId'] . "." . $_SESSION['picExt'];
                }

                $_SESSION['message'] = "Confirmation link has been sent to $email, please verify your account by clicking on the link in the message!";
                $to = $email;
                $subject = "Account Verification (AgroCulture.com)";
                $message_body = "
                Hello $username,
                
                Thank you for signing up!
                
                Please click this link to activate your account:
                
                http://localhost/AgroCulture/Login/verify.php?email=$email&hash=$hash";

                mail($to, $subject, $message_body);
                header("location: profile.php");
            } else {
                $_SESSION['message'] = "Registration failed!";
                header("location: error.php");
            }
        }
    } else {
        $sql = "SELECT * FROM buyer WHERE bemail='$email'";
        $result = mysqli_query($conn, $sql) or die($mysqli->error());

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "User with this email already exists!";
            header("location: error.php");
        } else {
            $sql = "INSERT INTO buyer (bname, busername, bpassword, bhash, bmobile, bemail, baddress, aadhar)
                    VALUES ('$name','$username','$password','$hash','$mobile','$email','$address', '$aadhaar')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['Active'] = 0;
                $_SESSION['logged_in'] = true;

                $sql = "SELECT * FROM buyer WHERE busername='$username'";
                $result = mysqli_query($conn, $sql);
                $User = $result->fetch_assoc();
                $_SESSION['id'] = $User['bid'];

                $_SESSION['message'] = "Confirmation link has been sent to $email, please verify your account by clicking on the link in the message!";
                $to = $email;
                $subject = "Account Verification (AgroCulture.com)";
                $message_body = "
                Hello $username,
                
                Thank you for signing up!
                
                Please click this link to activate your account:
                
                http://localhost/AgroCulture/Login/verify.php?email=$email&hash=$hash";

                mail($to, $subject, $message_body);
                header("location: profile.php");
            } else {
                $_SESSION['message'] = "Registration not successful!";
                header("location: error.php");
            }
        }
    }
}

function dataFilter($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>