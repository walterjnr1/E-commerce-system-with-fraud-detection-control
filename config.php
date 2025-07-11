<?php 
session_start();
error_reporting(1);
include('database/connect.php'); 
include('database/connect2.php'); 
include('activity_log_function.php'); 

//set time
date_default_timezone_set('Africa/Accra');
$current_date = date('Y-m-d H:i:s');

// Define the current month and year
$current_month = date('m');
$current_year = date('Y');

//fetch user data
$user_id = $_SESSION["user_id"];
$stmt = $dbh->query("SELECT * FROM users where id='$user_id'");
$row_user = $stmt->fetch();
$role = $row_user['role'];

$senderID = 'GradePulse';
$paystack_public_key = 'pk_test_9180e4cbc4f6da45138bf24d6e5a4fce84439c58';
$paystack_secret_key = 'sk_test_47baa9aaab29e730ccc5d25c1f00761454fc58e4';

$app_name= 'Fraud detection System in E-commerce';
$app_email = 'support@TechParts.com';
?>