<?php
setlocale (LC_TIME, 'fr_FR','fra'); 
?>
<div style="margin:10px;width:100%;">
		<div class="no-print" style="margin:1%;width:99%;">
			<div style="text-align:right;">
				<input type="button" value="Imprimer" onclick="window.print();" />
			</div>
		</div>
		<div style="width:100%;border-top:1px solid #000;border-bottom:1px solid #000;">
			<div style="width:80%;float:left;padding-top:13px;" class="entete"><?php echo $mWinpunch->rptTitle;?></div>
			<div style="width:20%;float:right;text-align:right;"><img src="<?php echo $path_logo;?>" style="width:40px;"/></div>
			<div style="clear:both;"></div>
		</div>
		
		

	<?php 
	
	$mWinpunch->show_report();
	$user_name = $mWinpunch->get_user();
	$rptUID = $mWinpunch->winpunch_params["rptUID"];
	
	$SQL = "SELECT desc_etat,print_landscape FROM rapports_web WHERE id_etat = ".$rptUID;
	$results = $link->query($SQL);
	if ($results)
	{
		$row = $results->fetch_object();
		$print_mode = $row->print_landscape;
	
		if ($print_mode == 1)
		{
		?>
		<script>$("#modestyle").attr("href","css/landscape.css");
		$("#bodyCss").addClass("landScape");
		</script>
		<?php
		}
		else
		{
		?>
		<script>$("#modestyle").attr("href","css/portrait.css");
		$("#bodyCss").removeClass("landScape");</script>
		<?php
		}
	}
	?>
	<hr/>
	<div style="width:50%;float: left;" class="date">Imprimé le : <?php echo utf8_encode(strftime("%d %B %Y"));?></div>
	<div style="width:50%;float:right;text-align:right;" class="date"><?php echo $user_name;?></div>
</div>



	
