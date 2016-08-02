<?php
session_start();

require_once("../includes/cls_winpunch.php");

$catUID = $_POST["catUID"];

$mWinpunch = new winpunch();

$mWinpunch = unserialize($_SESSION['mWinpunch']);

$mWinpunch->list_reports($catUID);
?>
