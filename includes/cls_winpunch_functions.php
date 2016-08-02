<?php

class winpunch_reports
{
	public function add_cols($arrCols,$grpby)
	{
	 ?>
	 <tr class="cols">
	   <?php
	   foreach($arrCols as $col)
	   {
		if ($col["SQL"] != $grpby)	
		{
		?>
		<td><?php echo $col["LABEL"];?></td>
		<?php
		}
	   }
	   ?>
	 </tr>
	 <?php
	}
	
	public function add_results($rows,$arrCols,$grpby)
	{
	?>
	<tr class="results">
	<?php
	foreach($arrCols as $col)
	{
	        if ($col["SQL"] != $grpby)
		{
		?>
		<td><?php echo $rows->$col["SQL"];?></td>
		<?php
		}
	}
	?>
	</tr>
	<?php

	}

	public function add_line($rows,$arrCols,$grpby)
	{
		global $current_rup;

		foreach($arrCols as $col)
		{
		      if ($col["SQL"] == $grpby)
		      {	
				if (($rows->$col["SQL"] != $current_rup) || ($current_rup == ""))
				{
				    ?>
				    <tr class="results">
				    <td class="entete"><?php echo $rows->$col["SQL"];
					$current_rup = $rows->$col["SQL"];
					?>
				    </td>
				   </tr>
				   <?php
				   winpunch_reports::add_cols($arrCols,$grpby);
				}	
		      }
		}
		winpunch_reports::add_results($rows,$arrCols,$grpby);
	
		return $current_rup;
	}
}
