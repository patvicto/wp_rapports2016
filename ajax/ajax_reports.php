<?php
session_start();

require_once("../includes/cls_winpunch.php");

$mWinpunch = unserialize($_SESSION['mWinpunch']);

if (isset($_POST["rptUID"]))
{
	$rptUID = $_POST["rptUID"];
	if (!isset($_SESSION['mWinpunch'])) { $mWinpunch = new winpunch(); }
		
	if (isset($mWinpunch->winpunch_params["userUID"]))
	{
		$user_id = $mWinpunch->winpunch_params["userUID"];
	
		if ($mWinpunch->winpunch_params["rptUID"] != $rptUID) { $mWinpunch->reset_params(); }
			
		$mWinpunch->winpunch_params["userUID"] = $user_id;
		
		$SQL = "SELECT desc_etat,print_landscape FROM rapports_web WHERE id_etat = ".$rptUID;
		$results = $link->query($SQL);
		if ($results)
		{
			$row = $results->fetch_object();
			$mWinpunch->rptTitle = $row->desc_etat;
			$mWinpunch->print_mode = $row->print_landscape;

			//load template for report

			foreach( $_POST as $key=>$value)
			{
				$mWinpunch->winpunch_params[$key] = $value;
			}
		
			$_SESSION['mWinpunch'] = serialize($mWinpunch);

			
			include_once("templates/report.php");
		}
	}
	else
		echo -1;
}
?>
