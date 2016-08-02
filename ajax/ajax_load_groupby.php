<?php
session_start();

require_once("../includes/cls_winpunch.php");

$rptUID = $_POST["repUID"];

$mWinpunch = new winpunch();
$mWinpunch->load_groupby($rptUID);
?>
