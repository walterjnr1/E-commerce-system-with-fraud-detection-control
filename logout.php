<?php
include('config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

//Automatic logout
$t=time();
if (isset($_SESSION['logged']) && ($t - $_SESSION['logged'] > 3600)) {

    session_destroy();
   session_unset();
	echo ("<script LANGUAGE='JavaScript'>
    window.alert('Sorry , You have been Logout because of inactivity. Try Again');
    window.location.href= 'login.php';
    </script>");
	}else {
    $_SESSION['logged'] = time();
}


header("Location: login.php");

?>
