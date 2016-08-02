<?php
session_start();

require_once("includes/cls_winpunch.php");
$mWinpunch = new winpunch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Winpunch - Générateur de rapports</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/ajax_reports.js"></script>
<script type="text/javascript" src="js/jquery.multiple.select.js"></script>
<link rel="stylesheet" type="text/css" href="css/jscalendar.css">
<link rel="stylesheet" type="text/css" href="css/report.css">
<link rel="stylesheet" type="text/css" href="" id="modestyle">
<link rel="stylesheet" href="css/multiple-select.css" />
</head>
<body id="bodyCss">
	<div id="header" class="no-print">
		<table style="width:100%;">
		<tr style="vertical-align:top;">
		<td style="width:80px;height:120px;">
			<img src="<?php echo $path_logo;?>"/>
		</td>
		<td style="width:auto;">
		<div id="content_right" style="width:100%;">
		<?php
		//load params
		if ($mWinpunch->validate_param())
		{
			$_SESSION['mWinpunch'] = serialize($mWinpunch);
			?>
			<div id="div_catlist">
			<?php
			$mWinpunch->list_cat_reports();
			?>
			</div>
			<div id="div_list" style="margin-top:10px;">
			<?php
			$mWinpunch->list_reports();
			?>
			</div>
			
			<script type="text/javascript" src="<?php echo $path_js;?>jscalendar.js"></script>
			<div id="div_params">
			</div>
			<div id="div_groupby" style="width:50%;float:left;">
			</div>
			<div id="div_options" style="width:50%;float:left;">
			</div>
			<div style="clear:both;"></div>
			<div id="div_generate" style="margin-top:20px;">
				<div style="margin:10px;"><span id="wait_generate" style="display:none;">Génération en cours,veuillez patienter...</span></div>
				<input type="button" id="btn_generate" value="Générer" onclick="create_report();"/>
			</div>
			<?php
			
		}
		else
			$mWinpunch->winpunch_die();
		
		?>
		</div>
		</td>
		</tr>
		</table>
	</div>
	<div id="div_report" style="margin:10px;">
	</div>
	<?php
	$Token = 0;
	if (isset($_GET["token"]))
	{
		$arrExcept = array("userUID","rptUID");
		$selectedValues="";
		
		$Token = 1;
		foreach($mWinpunch->winpunch_params as $key=>$current_param)
		{
			if (!in_array($key,$arrExcept))
			{
	  		  if ($mWinpunch->get_param($key) != "") { $selectedValues .= $key."::".$mWinpunch->get_param($key)."&"; }
			}
		}
				
		?>
		<script type="text/javascript">
			$( document ).ready(function() 
			{
			  load_cat_report('<?php echo $selectedValues;?>',<?php echo $Token;?>);	 
  			  
			});
		</script>
	<?php
	}
	?>
</body>
</html>

