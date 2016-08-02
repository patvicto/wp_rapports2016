<?php
session_start();
error_reporting(-1);

require_once("includes/cls_winpunch.php");
if (isset($_SESSION['mWinpunch'])) { $mWinpunch = unserialize($_SESSION['mWinpunch']);  } else { $mWinpunch = new winpunch();  }
//Récupère paramètres génération html. Filters etc...
//Si NULL aucun filtre.... query complète ... pas le scénario idéal... faudrait pas car avec 0 filtre la query pourrait être lourde pas juste un peu!

require_once("includes/cls_winpunch_pdf.php");
$mWinpunchPDF = new winpunch_pdf();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Winpunch - Générateur de rapports - PDF</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<!-- ADD FOR PDF OUTPUT -->
<script language="javascript" type="text/javascript" src="js/jspdf.js"></script>
<script language="javascript" type="text/javascript" src="js/plugins/standard_fonts_metrics.js"></script>
<script language="javascript" type="text/javascript" src="js/plugins/split_text_to_size.js"></script>
<script language="javascript" type="text/javascript" src="js/plugins/cell.js"></script>
<script language="javascript" type="text/javascript" src="js/plugins/addimage.js"></script>

<!-- ADD FOR PDF OUTPUT -->
<!-- OBJECT WINPUNCH CREATE PDF -->
<script language="javascript" type="text/javascript" src="js/object_winpunch.js"></script>
</head>
<body>
<?php
$today = date("Y-m-d");
//get_module_name

//$_GET["module"];

$module_name = "modules/employes/employes.json";

$mWinpunchPDF->init_module_json($module_name);
$results = false;
if ($mWinpunchPDF->JSon != NULL)
{
	//JSon params
	$mWinpunchPDF->set_params_json();

	//filters() obj_winpunch
	
	$results =  $mWinpunchPDF->execute_query_get_len_json($mWinpunch);
	if (!$results)  { die(); }		
}

$arrHeader = array();
foreach($mWinpunchPDF->columns as $column)
{
	$width = $column["max_length"];	
	$tmp["label"] = $column["label"];
	$tmp["width"] = $width;
	$tmp["height"] = $mWinpunchPDF->lineheight;
	array_push($arrHeader,$tmp);
}
$table_header = json_encode($arrHeader);	
?>
	<script>
	createReport(<?php echo $mWinpunchPDF->marginwidth;?>,<?php echo $mWinpunchPDF->marginheight;?>,<?php echo $mWinpunchPDF->page_width;?>,<?php echo $mWinpunchPDF->page_height;?>);
	var i= 0;
	set_header(i,'<?php echo $mWinpunchPDF->title;?>');	
	i = i+1;
	apply_margins(); 
	remainV = docHeight;  //max
	aff_line_header();
	
	//headers
	table_header_json('<?php echo $table_header;?>');
	set_counter_page();
	<?php 
	while ($row = $results->fetch_object())
	{
		$maxlenvalue =0;
		$arrDataLine = array();
		$tmp = array();

		foreach($mWinpunchPDF->columns as $column)
		{
			$col_name = $column["name"];
			$width = $mWinpunchPDF->array_search_result($mWinpunchPDF->columns,"name",$col_name);
			$tmp["width"] = $width;
			$tmp["value"] = $row->$column["name"];
			array_push($arrDataLine,$tmp);
		}
		$table_data = json_encode($arrDataLine);
		$table_data = base64_encode($table_data);
		?>table_line_json('<?php echo $table_data;?>',<?php echo $results->num_rows;?>);<?php
	}
	?>
	doc.output('dataurl'); //final output.
	</script>
</body>
</html>


