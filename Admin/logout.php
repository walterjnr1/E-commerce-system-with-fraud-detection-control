<?php
include('../inc/config.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
}

//Automatic logout
$t=time();
if (isset($_SESSION['logged']) && ($t - $_SESSION['logged'] > 3600)) {

	//session_destroy();
   // session_unset();
	echo ("<script LANGUAGE='JavaScript'>
    window.alert('Sorry , You have been Logout because of inactivity. Try Again');
    window.location.href= '../login.php';
    </script>");
	}else {
    $_SESSION['logged'] = time();
}


  // Initialize variables from session or set default values
  $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
  //$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

  //activity log
  //$operation = "logged out on $current_date";
 // log_activity($conn, $user_id, $role, $operation, $ip_address);
  //session_destroy(); //destroy the session

header("Location: ../login.php");

?>
