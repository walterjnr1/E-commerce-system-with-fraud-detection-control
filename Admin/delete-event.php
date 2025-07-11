<?php
include('../inc/config.php');
if (empty($_SESSION['user_id'])) {
  header("Location: login.php");
}

$id= $_GET['id'];
$sql = "DELETE FROM events WHERE id=?";
$stmt= $dbh->prepare($sql);
$stmt->execute([$id]);

      //activity log
     $operation = "deleted an event on $current_date";
     log_activity($conn, $manager_id,$role, $operation, $ip_address);

      header("Location: event-record");
 ?>
