<?php 
include("config/database.php"); 

$login_history_id=$_SESSION['login_history_id'];
$logout_datetime=date("Y-m-d H:i:s");

$qry="UPDATE login_history SET logout_datetime='$logout_datetime' where id='$login_history_id'";
$sq1=$con->query($qry);
session_destroy();
header("Location: index.php");


