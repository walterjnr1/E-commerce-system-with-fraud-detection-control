<?php 
session_start();
error_reporting(1);
include('../database/connect.php'); 
include('../database/connect2.php'); 
include('activity_log_function.php'); 
include('pagination_config.php'); 

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

//get total number of products in products table
$sql = "SELECT COUNT(*) AS total_products FROM products";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_products = $row['total_products'];

//get total number of transaction in payment table
$sql = "SELECT COUNT(*) AS total_payments FROM payments";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_payments = $row['total_payments'];

//get total number of order in order table
$sql = "SELECT COUNT(*) AS total_orders FROM orders";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_orders = $row['total_orders'];

//get total number offraud cases
$sql = "SELECT COUNT(*) AS total_fraud_cases FROM fraud_cases";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_fraud_cases = $row['total_fraud_cases'];


$app_name= 'Fraud detection System in E-commerce';
$app_email = 'support@TechParts.com';
?>