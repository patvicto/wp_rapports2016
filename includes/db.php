<?php
//Mysqli


$host = "localhost";
$user = "root";
$pass = "";
$db = "winpunch";

$link = mysqli_connect($host,$user,$pass,$db) or die("Error " . mysqli_error($link));		
$link->set_charset("utf8");

?>

