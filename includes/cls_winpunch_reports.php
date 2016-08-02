<?php

class winpunch_reports
{
	public $arr_cumul;
	public $arr_cumul_day;
	public $arr_cumul_tot;

	public $arr_cumul_col;

	public $arr_cumul_month;
		
	public $current_user = "";
	public $flag_block = 'N';
	
	public $day_total =0;
	public $total = 0;
	public $gtotal = 0;


	public $dprev = "";
	public $fprev = "";
	public $dreel = "";
	public $freel = "";
	public $dpaye = "";
	public $fpaye = "";
	
	public $note = "";
	public $repas_paye = "";
		
	public $switch_tr = false;
		
	public $arrCols = array();
	public $nb = 0;
	public $cpt = 0;
	public $paye = 0;


	public function reset_cumul()
	{
		$this->arr_cumul = array();
		$this->arr_cumul["REG"] =0;
		$this->arr_cumul["SUP"] =0;
		$this->arr_cumul["PRM"] =0;	
	}

	public function reset_cumul_day()
	{
		$this->arr_cumul_day = array();
	}
	
	public function reset_cumul_tot()
	{
		$this->arr_cumul_tot = array();		
	}
	
	public function reset_cumul_col()	
	{
		$this->arr_cumul_col = array();
	}

	public function reset_dtotal($prefix)
	{
		if (($prefix == "N")|| ($prefix == "n"))
			$this->day_total = 0.00;
		else
			$this->day_total = "00:00:00";
	}

	public function reset_total($prefix)
	{
		if (($prefix == "N")|| ($prefix == "n"))
			$this->total = 0.00;
		else
			$this->total = "00:00:00";
	}

	
	public function reset_gtotal($prefix)
	{
		if (($prefix == "N")|| ($prefix == "n"))
			$this->gtotal = 0.00;
		else
			$this->gtotal = "00:00:00";
	}


	public function reset_paye($prefix)
	{
		if (($prefix == "N")|| ($prefix == "n"))
			$this->paye = 0.00;
		else
			$this->paye = "00:00:00";
	}

	
	public function reset_cumul_month()	
	{
		$this->arr_cumul_month = array();
	}
	public function bloc_header_temps_hebdo($arrCols)
	{
		?>
		<tr style="vertical-align:top;" class="cols2">
		<td style="border:1px solid #000;border-bottom:1px solid #000;width:225px;">Employé</td>
		<td style="border:1px solid #000;border-bottom:1px solid #000;width:50px;">No.</td>
		<?php
		$i=1;
		$ratio = "33.33%";  //R | S | P
		while ($i <= 8)
		{
			?>
			<td>
			<table border="1" style="width:140px;text-align:center;">			
			<?php 
			winpunch_reports::add_split_cols($arrCols,$ratio);
			$i++;
			?>
			</table>
			</td>
			<?php
		}
		?>
		</td>
		</tr>
		<?php
	}

	public function bloc_data_temps_hebdo($rows,$arrCols,$prefix)
	{
		?>
		<tr style="vertical-align:top;">
		<td style="border:1px solid #000;border-bottom:1px solid #000;width:250px;">
		<div style="height:15px;overflow:hidden;"><?php echo $rows->nom_complet;?></div></td>
		<td style="border:1px solid #000;border-bottom:1px solid #000;width:50px;"><?php echo $rows->no_employe;?></td>
			<?php
			$i=1;
			$ratio = "33.33%";  //R | S | P
			while ($i <= 8) //jours
			{
				?>
				<td style="width:140px;">
				<table style="width:140px;text-align:right;" cellspacing="0" cellpading="0">			
				<?php
				winpunch_reports::add_split_results($rows,$arrCols,$ratio,$i,$prefix);
				$i++
				?>
				</table>
				</td>
				<?php	
			}
			?>
		</td>
		</tr>
		<?php
	}
	
	public function bloc_total_temps_hebdo($arrCols,$prefix)
	{
		?>
		<tr style="vertical-align:top;">
			<td colspan="2" style="text-align:right;font-weight:bold;">&nbsp;</td>
			<td colspan="26" style="text-align:right;">
			<div style="border-top:3px solid #000;width:100%;margin-top:5px;margin-bottom:5px;"></div></td>
			</tr>
			<tr style="vertical-align:top;">
				<td colspan="2" style="text-align:right;font-weight:bold;">Total :</td>
					<?php
					$i=1;
					$ratio = "33%";  //R | S | P
					while ($i <= 8) //jours
					{
						?>
						<td style="width:140px;">
						<table style="width:140px;text-align:right;" 
						cellspacing="0" cellpading="0" border="1">			
						<?php
						winpunch_reports::add_split_results_day($arrCols,$ratio,$i,$prefix);
						$i++;
						?>
						</table>
						</td>
						<?php	
					}
					?>
		</tr>
		<?php
		
	}
	
	public function bloc_data_temps_quotidien($rows,$arrCols,$grpby,$current_rup,$prefix)
	{
		if ($prefix == "N")
			$arrCalc = array("REG_N","DEMI_N","DOUBLE_N","ABS_N","PRIME_N","FERIE_N","TOTAL_N","PREVU_N");
		else
			$arrCalc = array("REG_H","DEMI_H","DOUBLE_H","ABS_H","PRIME_H","FERIE_H","TOTAL_H","PREVU_H");
		?>
		<tr style="vertical-align:top;">
		<?php
		foreach($arrCols as $col)
   		{
			$width = $col["WIDTH"];
			$align= "";
			
			if (isset($col["ALIGN"]))
				$align = $col["ALIGN"];

			if ($align == "")
				$align= "left";
			
			if (($col["SQL"] != $grpby) && ($col["SQL"] != "IN_OUT"))
			{
				if (($col["SQL"] != "no_employe") && ($col["SQL"] != "nom_complet"))
				{
					
					$data = $rows->$col["SQL"];
	
					if (in_array($col["SQL"],$arrCalc))
						winpunch_reports::cumul_data_col($col["SQL"],$data,$prefix);
					


					$data = winpunch_reports::set_real_value($data,$prefix);

					

					?>
					<td style="border:1px solid #000;width:<?php echo $width;?>;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
					<?php
				}
				else
				{
					$data = $rows->$col["SQL"];
					if ($col["SQL"] == "no_employe")
					{
						if ($this->current_user == $rows->$col["SQL"])
						{
							?>
							<td colspan="2"></td>
							<?php
						}
						else
						{
							$nom_complet = $rows->nom_complet;
							
							if ($this->current_user != "")
							{
								if ($this->flag_block == "N")
								{
									//winpunch_reports::bloc_total_temps_hebdo_complet($arrCols,$grpby,$current_rup,$prefix);
									?>
									</tr>
									<!--<tr class="results">
									<td rowspan="1" style="font-size:20px;font-weight:bold;">&nbsp;</td>
									</tr>-->
									<?php
								}
								else
									 $this->flag_block = "N";
								
								
								//winpunch_reports::reset_cumul_col();
							}
							
							?>
							
							<td style="width:100px;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
							<td style="width:200px;text-align:<?php echo $align;?>;"><?php echo $nom_complet;?></td>
							<?php
							$this->current_user = $data;
							//winpunch_reports::reset_cumul_col();
							
						}
					}
					
					
					
					
				}
			}

		}
		
	
		$name = "";
		//punch entrée sorties
		foreach($arrCols as $col)
   		{
				if ($col["SQL"] == "IN_OUT")
				{
						
						$align= "";
						if (isset($col["ALIGN"]))
							$align = $col["ALIGN"];

						if ($align == "")
							$align= "left";
						
						$arrvalues = explode(";",$rows->$col["SQL"]);
						$max = count($arrvalues);

						?>
						<td style="width:200px;text-align:center;">
						<table style="width:100%;">
						<tr>
						<td style="width:200px;text-align:left;">
						
						<?php
						$i=0;
						foreach($arrvalues as $values)
						{
							
						?>
						<div style="width:46px;float:left;margin-right:2px;"><?php echo $values;?></div>
						
						<?php
						$i++;
						}
						?>
						</td>
						</tr>
						</table>
						</td>
						<?php
				}
		
		}
		?>
		
		</td>
		</tr>
		<?php
	}

	public function bloc_data_temps_hebdo_complet($rows,$arrCols,$grpby,$current_rup,$prefix)
	{
		if ($prefix == "N")
			$arrCalc = array("REG_N","DEMI_N","DOUBLE_N","ABS_N","PRIME_N","FERIE_N","TOTAL_N","PREVU_N");
		else
			$arrCalc = array("REG_H","DEMI_H","DOUBLE_H","ABS_H","PRIME_H","FERIE_H","TOTAL_H","PREVU_H");
		?>
		<tr style="vertical-align:top;">
		<?php
		foreach($arrCols as $col)
   		{
			$width = $col["WIDTH"];
			$align= "";
			
			if (isset($col["ALIGN"]))
				$align = $col["ALIGN"];

			if ($align == "")
				$align= "left";
			
			if (($col["SQL"] != $grpby) && ($col["SQL"] != "IN_OUT"))
			{
				if (($col["SQL"] != "no_employe") && ($col["SQL"] != "nom_complet"))
				{
					
					$data = $rows->$col["SQL"];
	
					if (in_array($col["SQL"],$arrCalc))
						winpunch_reports::cumul_data_col($col["SQL"],$data,$prefix);
					


					$data = winpunch_reports::set_real_value($data,$prefix);

					

					?>
					<td style="border:1px solid #000;width:<?php echo $width;?>;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
					<?php
				}
				else
				{
					$data = $rows->$col["SQL"];
					if ($col["SQL"] == "no_employe")
					{
						if ($this->current_user == $rows->$col["SQL"])
						{
							?>
							<td colspan="2"></td>
							<?php
						}
						else
						{
							$nom_complet = $rows->nom_complet;
							
							if ($this->current_user != "")
							{
								if ($this->flag_block == "N")
								{
									winpunch_reports::bloc_total_temps_hebdo_complet($arrCols,$grpby,$current_rup,$prefix);
									?>
									</tr>
									<tr class="results">
									<td rowspan="1" style="font-size:20px;font-weight:bold;">&nbsp;</td>
									</tr>
									<?php
								}
								else
									 $this->flag_block = "N";
								
								
								winpunch_reports::reset_cumul_col();
							}
							
							?>
							
							<td style="width:100px;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
							<td style="width:200px;text-align:<?php echo $align;?>;"><?php echo $nom_complet;?></td>
							<?php
							$this->current_user = $data;
							winpunch_reports::reset_cumul_col();
							
						}
					}
					
					
					
					
				}
			}

		}
		
	
		$name = "";
		//punch entrée sorties
		foreach($arrCols as $col)
   		{
				if ($col["SQL"] == "IN_OUT")
				{
						
						$align= "";
						if (isset($col["ALIGN"]))
							$align = $col["ALIGN"];

						if ($align == "")
							$align= "left";
						
						$arrvalues = explode(";",$rows->$col["SQL"]);
						$max = count($arrvalues);

						?>
						<td style="width:200px;text-align:center;">
						<table style="width:100%;">
						<tr>
						<td style="width:200px;text-align:left;">
						
						<?php
						$i=0;
						foreach($arrvalues as $values)
						{
							
						?>
						<div style="width:46px;float:left;margin-right:2px;"><?php echo $values;?></div>
						
						<?php
						$i++;
						}
						?>
						</td>
						</tr>
						</table>
						</td>
						<?php
				}
		
		}
		?>
		
		</td>
		</tr>
		<?php
	}

	public function bloc_total_temps_hebdo_complet($arrCols,$grpby,$current_rup,$prefix)
	{
		?>
		<tr style="vertical-align:top;">
		<?php
		foreach($arrCols as $col)
   		{
			$width = $col["WIDTH"];
			$align= "";
			
			if (isset($col["ALIGN"]))
				$align = $col["ALIGN"];

			if ($align == "")
				$align= "left";

			if ($col["SQL"] != $grpby)	
			{	
				if ($col["SQL"] != "IN_OUT")
				{
					if (($col["SQL"] == "no_employe") || ($col["SQL"] == "nom_complet") || ($col["SQL"] == "jour_de_paie"))
					{
						?>
						<td style="width:<?php echo $width;?>;text-align:<?php echo $align;?>;"></td>
						<?php
					}
					else
					{	
						$data = $this->arr_cumul_col[$col["SQL"]];
					
						if ($prefix == "N")
						{
							if (is_numeric($data))
								 $data = number_format($data,2,"."," ");

							if ($data == "0.00") { $data = "&nbsp;"; }
						}
						else
						{
							//show as cumul hours.
							$arrdata = explode(":",$data);
							$data = $arrdata[0]."H".$arrdata[1];

							if ($data == "00H00") { $data = "&nbsp;"; }		
						}
						
						?>
						<td style="font-weight:bold;border:1px solid #000;width:<?php echo $width;?>;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
						<?php
					}
				}
			}

		}
		?>
		</tr>
		
		<?php
		
	}

	/*Add columns for the block*/
	public function add_cols($arrCols,$grpby)
	{
	$align = "left";	
	 ?>
	 <tr class="cols">
	   <?php
		$width = "auto";
	   foreach($arrCols as $col)
	   {
		if (isset($col["ALIGN"]))
				$align = $col["ALIGN"];
			
		
		if (isset($col["WIDTH"]))
		{
			if ($col["WIDTH"] != "")
				$width = $col["WIDTH"]."px";
			else		
				$width = "auto";
		}
 
		if ($col["SQL"] != $grpby)
		{
			
			if (isset($col["HEADER"]))
			{
			 	if ($col["HEADER"] != "Y")
				{
					?>
					<td style="text-align:<?php echo $align;?>;width:<?php echo $width;?>"><?php echo $col["LABEL"];?></td>
					<?php
				}
			}
			else
			{
					
				?>
				<td style="text-align:<?php echo $align;?>;width:<?php echo $width;?>"><?php echo $col["LABEL"];?></td>
				<?php
			}
		}	
		else
		{

			if ($col["SQL"] == "")
			{
			?>
			<td colspan="<?php echo $col['PARTS'];?>" style="text-align:<?php echo $align;?>;width:<?php echo $width;?>"><?php echo $col["LABEL"];?></td>
			<?php
			}
		
		}
	   }
	   ?>
	 </tr>
	 <?php
	}

	/*Add columns for the block*/
	public function add_split_cols($arrCols,$ratio)
	{
	 ?>
	 <tr>
	   <?php
	   foreach($arrCols as $col)
	   {
		if ($col["ADD"] == "Y")
		{
		?>
		<td class="cols2" style="width:33%;width:<?php echo $ratio;?>"><?php echo $col["LABEL"];?></td>
		<?php
		}
	   }
	   ?>
	 </tr>
	 <?php
	}

	/*Add columns for the block*/
	public function add_split_results($rows,$arrCols,$ratio,$i,$prefix)
	{
	 ?>
	 <tr>
	   <?php
	   foreach($arrCols as $col)
	   {
		if ($col["ADD"] == "Y")
		{
			if ($i <= 7)
			{	
				$col_name = $col["SQL"]."_".$i;


				//Cumul numeric ou time
				if ($prefix == "N")
					$data = number_format($rows->$col_name,2);
				else
					$data = $rows->$col_name;

				if (strpos($col_name,"REG_") !== false)
					winpunch_reports::cumul_data("REG",$data,$i,$prefix);
					
				if (strpos($col_name,"SUP_") !== false)
					winpunch_reports::cumul_data("SUP",$data,$i,$prefix);

				if (strpos($col_name,"PRM_") !== false)
					winpunch_reports::cumul_data("PRM",$data,$i,$prefix);

				$data = winpunch_reports::set_real_value($data,$prefix);
			}
			else
			{
				$col_name = $col["SQL"];
				
				if (strpos($col_name,"REG_") !== false)
					$data =  winpunch_reports::cumul_col("REG",$prefix);
				
				if (strpos($col_name,"SUP_") !== false)
					$data =  winpunch_reports::cumul_col("SUP",$prefix);
				
				if (strpos($col_name,"PRM_") !== false)
					$data =  winpunch_reports::cumul_col("PRM",$prefix);

				
			}
		?>
		<td style="width:33.33%;text-align:right;border:1px solid #000;"><?php echo $data;?></td>
		<?php
		}
	   }
	   ?>
	 </tr>
	 <?php
	}

	public function validateDate($date, $format = 'Y-m-d')
	{
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}

	public function set_real_value($data,$prefix)
	{
		if (($prefix == "N") || ($prefix == "n"))
		{
			if ($data == "0.00")
				return "&nbsp;";
			else
			{
				if (is_numeric($data))
					return number_format($data,2,".","");
				else
					return $data;
			}
		}
		else
		{
			
			if ($data == "00:00:00")
				return "&nbsp;";
			else
			{
				if (strpos($data,":") !== false)
				{
					$arr_type = explode(":",$data);
					
					if (isset($arr_type[1]))
					{
						if ($arr_type[1] != "")
							$data = $arr_type[0]."H".$arr_type[1];	

					}
							
				
				}
				else
				{
					if (is_numeric($data)) { $data .= "H00"; }
				}
			
				if ($data == "00H00")
					return "&nbsp;";
				else
					return $data;	
			}

		}
	}

	/*Add columns for the block*/
	public function add_split_results_day($arrCols,$ratio,$i,$prefix)
	{
	 ?>
	 <tr>
	   <?php
	   foreach($arrCols as $col)
	   {
		if ($col["ADD"] == "Y")
		{
			if ($i <= 7)
			{	
				$col_name = $col["SQL"]."_".$i;

				if (strpos($col_name,"REG_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];
				
					$data = winpunch_reports::set_real_value($this->arr_cumul_day[$i][$colshort],$prefix);

					?>
					<td style="width:33.33%;text-align:right;"><?php echo $data;?></td>
					<?php
				}
	
				if (strpos($col_name,"SUP_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];

					$data = winpunch_reports::set_real_value($this->arr_cumul_day[$i][$colshort],$prefix);
					
					?>
					<td style="width:33.33%;text-align:right;"><?php echo $data;?></td>
					<?php
				}
				
				if (strpos($col_name,"PRM_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];

					$data = winpunch_reports::set_real_value($this->arr_cumul_day[$i][$colshort],$prefix);
					?>
					<td style="width:33.33%;text-align:right;"><?php echo $data;?></td>
					<?php
				}		
			}
			else
			{
				if (strpos($col["SQL"],"REG_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];
				
					
					if ($prefix == "N")
						$data = number_format($this->arr_cumul_tot[$colshort],2,".","");
					else	
					{
						$arr_type = explode(":",$this->arr_cumul_tot[$colshort]);
						$data = $arr_type[0]."H".$arr_type[1];
					}
					
				}
	
				if (strpos($col["SQL"],"SUP_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];

					if ($prefix == "N")
						$data = number_format($this->arr_cumul_tot[$colshort],2,".","");
					else	
					{
						$arr_type = explode(":",$this->arr_cumul_tot[$colshort]);
						$data = $arr_type[0]."H".$arr_type[1];
					}
				}
				
				if (strpos($col["SQL"],"PRM_") !== false)
				{
					$asql = explode("_",$col["SQL"]);
					$colshort = $asql[0];

					
					if ($prefix == "N")
						$data = number_format($this->arr_cumul_tot[$colshort],2,".","");
					else	
					{
						$arr_type = explode(":",$this->arr_cumul_tot[$colshort]);
						$data = $arr_type[0]."H".$arr_type[1];
					}
				}	
				?>
				<td style="width:33.33%;text-align:right;"><?php echo $data;?></td>
				<?php
			}
		}
	   }
	   ?>
	 </tr>
	 <?php
	}

	function cumul_data_col($col,$data,$prefix)
	{
		if ($prefix == "N")
		{
			if (isset($this->arr_cumul_col[$col]))
				$this->arr_cumul_col[$col] += $data;
			else
				$this->arr_cumul_col[$col] = $data;
		}
		else
		{
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];

			if (isset($this->arr_cumul_col[$col]))
				$this->arr_cumul_col[$col] = winpunch_reports::addTime($this->arr_cumul_col[$col],$hours,$mins,0);
			else
				$this->arr_cumul_col[$col] = winpunch_reports::addTime("00:00:00",$hours,$mins);
		}
	}

	function cumul_data($col,$data,$i,$prefix)
	{
		if ($prefix == "N")
		{
			if (isset($this->arr_cumul[$col]))
				$this->arr_cumul[$col] += $data;
			else
				$this->arr_cumul[$col] = $data;


			if (isset($this->arr_cumul_day[$i][$col]))
				$this->arr_cumul_day[$i][$col] += $data;
			else
				$this->arr_cumul_day[$i][$col] = $data;

		
			if (isset($this->arr_cumul_tot[$col]))
				$this->arr_cumul_tot[$col] += $data;
			else
				$this->arr_cumul_tot[$col] = $data;
		}
		else
		{
			//as time
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
			
			if (isset($this->arr_cumul[$col]))
				$this->arr_cumul[$col] = winpunch_reports::addTime($this->arr_cumul[$col],$hours,$mins,0);
			else
				$this->arr_cumul[$col] = winpunch_reports::addTime("00:00:00",$hours,$mins,0);

		
			if (isset($this->arr_cumul_day[$i][$col]))
				$this->arr_cumul_day[$i][$col] = winpunch_reports::addTime($this->arr_cumul_day[$i][$col],$hours,$mins,0);
			else
				$this->arr_cumul_day[$i][$col] = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
			
			if (isset($this->arr_cumul_tot[$col]))
				$this->arr_cumul_tot[$col] = winpunch_reports::addTime($this->arr_cumul_tot[$col],$hours,$mins,0);
			else	
				$this->arr_cumul_tot[$col] = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
		}

		
	}

	function cumul_col($type,$prefix)
	{
		$arr_cumul = $this->arr_cumul;
		
		$arr_results = array();
		foreach($arr_cumul as $cumul=>$value)
		{
			
			if ($cumul == "REG")
				$arr_results["REG"] = $value;
			
			if ($cumul == "SUP")
				$arr_results["SUP"] = $value;
		
			if ($cumul == "PRM")
				$arr_results["PRM"] = $value;		
		}

		if ($prefix == "N")
			return number_format($arr_results[$type],2);
		else	
		{
			$arr_type = explode(":",$arr_results[$type]);
			return $arr_type[0]."H".$arr_type[1];
		}

 
	}
	
	public function cumul_data_month($data,$i,$prefix)
	{
		if (($prefix == "N")  || ($prefix == "n"))
		{

			if (isset($this->arr_cumul_month[$i]))
				$this->arr_cumul_month[$i] += $data;
			else
				$this->arr_cumul_month[$i] = $data;

		}
		else
		{
			//as time
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
		
			if (isset($this->arr_cumul_month[$i]))
				$this->arr_cumul_month[$i] = winpunch_reports::addTime($this->arr_cumul_month[$i],$hours,$mins,0);
			else
				$this->arr_cumul_month[$i] = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
			
		}

		
	}
	
	public function cumul_data_month_get($i)
	{
		return $this->arr_cumul_month[$i];
	}
	
	/*Add data for the block*/
	public function add_results($rows,$arrCols,$grpby,$prefix=NULL)
	{	
	$align = "left";
	?>
	<tr class="results">
	<?php

	

	foreach($arrCols as $col)
	{
		if (isset($col["ALIGN"]))
		   $align = $col["ALIGN"];
		
		
		
		if ($col["SQL"] == "")
		{
			$val = strftime("%A",strtotime($rows->jour));
			?>
			<td style="text-align:<?php echo $align;?>;"><?php echo $val;?></td>
			<?php
		}
		else
		{
			
			if ($col["SQL"] != $grpby)
			{
				if (isset($col["BARCODE"]))
				{
					
					if ($col["BARCODE"] == "Y")
						$val = 	winpunch_reports::print_code39_barcode($rows->$col["SQL"]);
					else	
					{
						if (isset($col["TEXT"]))
					        {

							if ($col["TEXT"] == "Y")
								$val = $rows->$col["SQL"];
							else
							$val =  winpunch_reports::set_real_value($rows->$col["SQL"],$prefix);
						}
						else
							$val =  winpunch_reports::set_real_value($rows->$col["SQL"],$prefix);
					}
						
				}
				else	
				{
					
					if (isset($col["TEXT"]))
					{
						if ($col["TEXT"] == "Y")
							$val = $rows->$col["SQL"];
						else
							$val =  winpunch_reports::set_real_value($rows->$col["SQL"],$prefix);
					}
					else
						$val =  winpunch_reports::set_real_value($rows->$col["SQL"],$prefix);
					
				}
				
				if (isset($col["HEADER"]))
				{
					if ($col["HEADER"] == "N")
					{
						?>
						<td style="text-align:<?php echo $align;?>;"><?php echo $val;?></td>
						<?php
					}
				}
				else
				{
						?>
						<td style="text-align:<?php echo $align;?>;"><?php echo $val;?></td>
						<?php
				}

			}
		}
	}
	?>
	</tr>
	<?php
	}

	public function add_split($lb_split,$current_grpby)
	{
		?>
		<tr class="results">
		<td colspan="28" style="font-size:20px;font-weight:bold;"><?php echo $lb_split["nom_groupby"];?> : <?php echo $current_grpby;?></td>
		</tr>
		<?php
	
	}

	/*Add line for the block*/
	public function add_line($rows,$arrCols,$grpby,$prefix=NULL)
	{
		global $current_rup;
		$nbcols = sizeof($arrCols)-1;
		
		if ($grpby != "")
		{
			foreach($arrCols as $col)
			{
			      if ($col["SQL"] == $grpby)
			      {	
					if (($rows->$col["SQL"] != $current_rup) || ($current_rup == ""))
					{
						if (strpos($col["SQL"],"regroupement_") === false)
							$value =$rows->$col["SQL"];
						else
						{
							$arr_split = explode("_",$col["SQL"]);

							$colgrp = "desc_grp_".$arr_split[1];
							
							$value =$rows->$colgrp;
						}
					    ?>
					    <tr class="results">
					    <td class="entete" colspan="<?php echo $nbcols;?>"><?php echo $value;
						$current_rup = $rows->$col["SQL"];
						?>
					    </td>
					   </tr>
					   <?php
					   winpunch_reports::add_cols($arrCols,$grpby);
					}	
			      }
			}
		}

		winpunch_reports::add_results($rows,$arrCols,$grpby,$prefix);
	
		return $current_rup;
	}

	
	
	public function counts_cols($rows)
	{
		$nb =0;
		$this->arrCols = Array();
			
		$tcol["SQL"] = $rows->employe;
		$tcol["LABEL"] =  "Nom de l'employé";
		$tcol["HEADER"] =  "Y";
		$tcol["ORDER"] =  count($this->arrCols);
		array_push($this->arrCols,$tcol);
		
		$tcol["SQL"] = $rows->picture;
		$tcol["LABEL"] =  "";
		$tcol["HEADER"] =  "Y";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);

		$tcol["SQL"] = $rows->no_employe;
		$tcol["LABEL"] =  "No employé";
		$tcol["HEADER"] =  "N";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);


		$cols = explode(";",$rows->cols);
		
		
		foreach($cols as $col)
		{
				$arr_values = explode(":",$col);
				
				if (isset($arr_values[1]))
				{
					if (!empty($arr_values[1]))
					{
						$tcol["SQL"] = $arr_values[1];
						$tcol["LABEL"] =  $arr_values[0];
						$tcol["HEADER"] =  "N";
						array_push($this->arrCols,$tcol);	
					}
				}
		}
		
		//Add others fields
		//periode.h_maitre, periode.groupe_paie, periode.poste,periode.libel_grp1, periode.libel_grp2, periode.libel_grp3, periode.libel_grp4, periode.libel_grp5, periode.libel_grp6, periode.grp1, periode.grp2, periode.grp3, periode.grp4, periode.grp5, periode.grp6

		$tcol["SQL"] = $rows->sexe_employe;
		$tcol["LABEL"] =  "Sexe";
		$tcol["HEADER"] =  "N";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);

		$tcol["SQL"] = $rows->h_maitre;
		$tcol["LABEL"] =  "Horaire maître";
		$tcol["HEADER"] =  "N";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);

		$tcol["SQL"] = $rows->groupe_paie;
		$tcol["LABEL"] =  "Groupe de paye";
		$tcol["HEADER"] =  "N";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);
		
		$tcol["SQL"] = $rows->poste;
		$tcol["LABEL"] =  "Fonction";
		$tcol["HEADER"] =  "N";
		$tcol["ORDER"] =  count($this->arrCols)+1;
		array_push($this->arrCols,$tcol);

		$i=1;
		while ($i < 6)
		{
			$colsql = "grp".$i;
			$collb = "libel_grp".$i;
			
			if (!empty($rows->$colsql))
			{
				$tcol["SQL"] = $rows->$colsql;
				$tcol["LABEL"] =  $rows->$collb;
				$tcol["HEADER"] =  "N";
				$tcol["ORDER"] =  count($this->arrCols)+1;
				array_push($this->arrCols,$tcol);
			}

			$i++;
		}
		$nb = count($this->arrCols);
		$this->nb = $nb;
	}

	public function add_row($rows)
	{
			foreach($this->arrCols as $col)
			{	
				if (!empty($col["SQL"]))
				{
					if ($col["HEADER"] == "N")
					{
						?><div style="width:33%;float:left;">
						    <div style="font-weight:bold;width:40%;float:left;"><?php echo $col["LABEL"];?></div>
						    <div style="width:60%;float:right;"><?php echo $col["SQL"];?></div>
						  </div><?php
					}
					else
					{
						if ($col["LABEL"] != "")
						{
					    		?><div style="clear:both;"></div>
							  <div style="width:100%;font-weight:bold;font-size:18px;"><?php echo $col["SQL"];?></div>
							<?php
						}
						else
						{
							?> <div style="width:100%;"><img src="<?php echo $rows->picture;?>" style="width:100px;"/></div>
					<?php
						}
					}
				}	
			}
	}

	public function search($array, $key, $value)
	{
	    $results = array();

	    if (is_array($array))
	    {
		if (isset($array[$key]) && $array[$key] == $value)
		    $results[] = $array;

		foreach ($array as $subarray)
		    $results = array_merge($results, winpunch_reports::search($subarray, $key, $value));
	    }

	    return $results;
	} 

	public function add_row_day($rows)
	{
			$arrdays= array();
			
			$day["JOUR_ID"] = 1;
			$day["LABEL_JOUR"] =  "Dimanche";
			array_push($arrdays,$day);

			$day["JOUR_ID"] = 2;
			$day["LABEL_JOUR"] =  "Lundi";
			array_push($arrdays,$day);

			$day["JOUR_ID"] = 3;
			$day["LABEL_JOUR"] =  "Mardi";
			array_push($arrdays,$day);

			$day["JOUR_ID"] = 4;
			$day["LABEL_JOUR"] =  "Mercredi";
			array_push($arrdays,$day);

			$day["JOUR_ID"] = 5;
			$day["LABEL_JOUR"] =  "Jeudi";
			array_push($arrdays,$day);

			$day["JOUR_ID"] = 6;
			$day["LABEL_JOUR"] =  "Vendredi";
			array_push($arrdays,$day);

			
			$day["JOUR_ID"] = 7;
			$day["LABEL_JOUR"] =  "Samedi";
			array_push($arrdays,$day);

			$i = $this->cpt;	
			$colsql = "debut".$i;
			$colsql2 = "fin".$i;
			$colsql3 = "jour".$i;
			$colsql4 = "col".$i;

			$colsql5 = "desc_h".$i;
			
			
			$jour = winpunch_reports::search($arrdays,"JOUR_ID",$rows->$colsql3);
			
			
			$border = "1";
			
			?>
			
			<td style="width:120px;border:1px solid #000;vertical-align:top;">
				<table style="width:100%;">
				<tr>
				<td style="width:100%;font-weight:bold;border:1px solid #000;"><?php echo $jour[0]["LABEL_JOUR"];?>
				<?php
				if (isset($rows->$colsql5))
					echo "<br/>".$rows->$colsql5;
				?>
				</td>
				</tr>
				<?php 
				if ($rows->$colsql != NULL)
				{
					$entree = explode(" ",$rows->$colsql);
					$sortie =  explode(" ",$rows->$colsql2);
					
					?>
					<tr>
					<td style="width:100%;border:1px solid #000;">
						Entrées<br/><?php echo $entree[1];?>
					</td>
					</tr>
					<?php
					$colsvalues = explode(";",$rows->$colsql4);
					foreach($colsvalues as $values)
					{
						$arr_item = explode("*",$values);
					
						if (isset($arr_item[0]))
						{
							$start = "";
							$end = "";
							$paye =  "";
						
							$start = array();
							$end = array();
							
							$start = explode(" ",$arr_item[0]);
								
							if (isset($arr_item[1]))
								$end = explode(" ",$arr_item[1]);
							else
								$end[1] = "";
						
							if (isset($arr_item[2]))
								$paye = $arr_item[2];
							else
								$paye  =0;
							
							if ($paye == 1)
								$paye = "Pause";
							else
								$paye = "Repas";
								
							if (isset($start[1]))
								$start1 = $start[1];
							else
								$start1 = "";	
							?>
							<tr>
								<td style="width:100%;border:1px solid #000;">
								<?php echo $paye;?>
								<br/><?php echo $start1;?><br/>
								<?php echo $end[1];?></div>
							
								</td>
								</tr>
							<?php
						}
					}
					?>
					<tr>
					<td style="width:100%;border:1px solid #000;">
					Sortie<br/><?php echo $sortie[1];?>
					
					</td>
					</tr>
					<?php
				}
				
				
				?>
				</table>
			</td>
			
			
			<?php
	}

	public function set_col_nb($exception_id,$msg,$INcolor,$INcolor2,$INcolor3,$INcolor4)
	{
		global $mWinpunch,$nbExceptions,$affEmp,$exceptions,$color,$color2,$color3,$color4;
		

		if (!isset($mWinpunch->winpunch_params["texceptions"]))
		{
			$nbExceptions++;
			$affEmp = true;
			$exceptions .= $msg."<br/>";
			
			if ($INcolor != $color)
				$color = $INcolor;

			if ($INcolor2 != $color2)
				$color2 = $INcolor2;

			if ($INcolor3 != $color3)
				$color3 = $INcolor3;

			if ($INcolor4 != $color4)
				$color4 = $INcolor4;
		}
		else
		{
			//-1 - Tous
			//1 = Au moins une exception

			 //combo                                           //current

			$nbExceptions++;
			$exceptions .= $msg."<br/>";
			
				if ($INcolor != $color)
					$color = $INcolor;

				if ($INcolor2 != $color2)
					$color2 = $INcolor2;

				if ($INcolor3 != $color3)
					$color3 = $INcolor3;

				if ($INcolor4 != $color4)
					$color4 = $INcolor4;

			if (($mWinpunch->winpunch_params["texceptions"] == $exception_id) || ($mWinpunch->winpunch_params["texceptions"] ==  1) || ($mWinpunch->winpunch_params["texceptions"] ==  -1))
			{	
				$affEmp = true;

				
			}
		}

		
	}

	public function validate_exceptions_aucun_prevu($rows,$reeeel_in,$reeel_out,$full_value,$reel_in,$reel_out,$reels_in,$reels_out,$current)
	{
		global $mWinpunch,$nbExceptions,$exceptions,$affEmp;
		$nbExceptions = 0;
		$exceptions = "";

		$affEmp = false;
		$arr = array();

		$color = "#FFF";
		$color2 = "#FFF";
		$color3 = "#FFF";
		$color4 = "#FFF";

		$line_show = false;

		if ($rows->nb_punch >0)
		{
			//Nb poinçons impairs.
			if ($rows->nb_punch % 2 == 1)
				winpunch_reports::set_col_nb(6,"POINÇON IRRÉGULIERS",$color,$color2,$color3,$color4);
			else
			{
					/* temp supp deb */
					$la_date = explode(" ",$full_value);

					$date = new DateTime($la_date[0]." ".$reeeel_in);
					$src = $reel_in;   //now fullcomplet in

					if ($rows->arrondi_horaire > $rows->min_sup_debut_horaire) 
					{ $date->modify("-".$rows->arrondi_horaire ." minutes"); } 
					else { $date->modify("-".$rows->min_sup_debut_horaire." minutes"); }
						
					if ($src <= $date->format('Y-m-d H:i'))
					{
						$color3 = "orange";
						winpunch_reports::set_col_nb(7,"TEMPS SUPP AM",$color,$color2,$color3,$color4);	
					}
					/* temp supp deb */
					
					/* temp supp fin */
					$dfin = explode(";",$rows->reel_complet);
					$madate = explode(" ",$dfin[1]);
					$date = new DateTime($madate[0]." ".$reeel_out);

					$src = $reel_out; //now fullcomplet out

					if ($rows->arrondi_horaire > $rows->min_fin_debut_horaire)
					{ $date->modify("+".$rows->arrondi_horaire ." minutes"); }
					else { $date->modify("+".$rows->min_sup_fin_horaire." minutes"); }

					if ($src >= $date->format('Y-m-d H:i'))
					{
						$color4 = "orange";
						winpunch_reports::set_col_nb(7,"TEMPS SUPP PM",$color,$color2,$color3,$color4);
					}
					/* temp supp fin */
								
			}
			
			if (($affEmp) || (!isset($mWinpunch->winpunch_params["texceptions"])))
			{
				$line_show=true;

				$arr_in = explode(" ",$reel_in);
				$reel_in = $arr_in[1];
	
				if (!empty($reel_out))
				{
					$arr_out = explode(" ",$reel_out);
					$reel_out = $arr_out[1];	
				}

				winpunch_reports::show_bloc($rows,$prevu_in,$prevu_out,$reel_in,$reel_out,$color,$color2,$color3,$color4,$prevus_in,$reels_in,$nbExceptions,$exceptions,false,$current);
			}	
			
		}
		return $line_show;

	}
	public function validate_exceptions($rows,$prevu_in,$prevu_out,$reel_in,$reel_out,$reels_out,$prevus_out,$full_value,$reels_in,$prevus_in,$current)
	{
			global $mWinpunch,$nbExceptions,$exceptions,$affEmp;

			$nbExceptions = 0;
			$exceptions = "";
			
			$affEmp = false;
			$arr = array();

			//couleur et/ou highlight retard et départ hâtif = jaune
			//absence = rouge
			//orange temp supp
		
			$color = "#FFF";
			$color2 = "#FFF";
			$color3 = "#FFF";
			$color4 = "#FFF";

			$line_show = false;
		
			if ((empty($rows->reel)) && (!empty($rows->nb_prevu)))
			{
				$color3 = "red";
				$color4 = "red";
				winpunch_reports::set_col_nb(2,"ABSENCE",$color,$color2,$color3,$color4);
			}
			else
			{	
				if ($rows->nb_punch > $rows->nb_prevu)
				{
					winpunch_reports::set_col_nb(6,"POINÇON IRRÉGULIERS",$color,$color2,$color3,$color4);
				}			
				else
				{
					//$date = date("Y-m-d h:m:s");
				
					$date = $rows->debut_horaire;

					if (($rows->nb_punch < $rows->nb_prevu)  && ($full_value < $date))
					{
					    winpunch_reports::set_col_nb(5,"POINÇON MANQUANTS",$color,$color2,$color3,$color4);
						
					}
					else
					{
							/* temp supp deb */
							$la_date = explode(" ",$full_value);
							$date = new DateTime($la_date[0]." ".$prevu_in);

							$src = $reel_in;   //now fullcomplet in

							if ($rows->arrondi_horaire > $rows->min_sup_debut_horaire) 
							{ $date->modify("-".$rows->arrondi_horaire ." minutes"); } 
							else { $date->modify("-".$rows->min_sup_debut_horaire." minutes"); }
								
							if ($src <= $date->format('Y-m-d H:i'))
							{
								$color3 = "orange";
								winpunch_reports::set_col_nb(7,"TEMPS SUPP AM",$color,$color2,$color3,$color4);	
							}
							/* temp supp deb */






							/* temp supp fin */
							$dfin = explode(";",$rows->prevu_complet);
							$madate = explode(" ",$dfin[1]);
							$date = new DateTime($madate[0]." ".$prevu_out);

							$src = $reel_out; //now fullcomplet out

							if ($rows->arrondi_horaire > $rows->min_fin_debut_horaire)
							{ $date->modify("+".$rows->arrondi_horaire ." minutes"); }
							else { $date->modify("+".$rows->min_sup_fin_horaire." minutes"); }

							if ($src >= $date->format('Y-m-d H:i'))
							{
								$color4 = "orange";
								winpunch_reports::set_col_nb(7,"TEMPS SUPP PM",$color,$color2,$color3,$color4);
							}
							/* temp supp fin */






							/* Départ hâtif */
							
							$date = new DateTime($la_date[0]." ".$prevu_out);
							$date->modify("-".$rows->grace_fin_supp_horaire." minutes");

							$src = $reel_out;  //now fullcomplet out

							if ($src < $date->format('Y-m-d H:i'))
							{
								$color4 = "yellow";
								winpunch_reports::set_col_nb(4,"DÉPART HÂTIF",$color,$color2,$color3,"yellow");
							}
							/* Départ hâtif */




							/* Retard */
							$date = new DateTime($la_date[0]." ".$prevu_in);
							$date->modify("+".$rows->grace_debut_supp_horaire." minutes");
							$src = $reel_in; //now fullcomplet in
							if ($src > $date->format('Y-m-d H:i'))
							{
								$color3 = "yellow";
								winpunch_reports::set_col_nb(3,"RETARD",$color,$color2,"yellow",$color4);
							}
							/* Retard */
					}
				}

			}
	
			//si IN reel  > (IN prevu + delai_grace_debut) = RETARD
			//si OUT reel < (OUT prevu - delai_grace_fin) = DÉPART HÂTIF
			//si pas de punch réel et IN prévu < NOW = ABSENCE
			//Si nb_punch < nb_prevu ET dernier OUT prevu < NOW = Poinçons manquants
			//Si nb_punch > nb_prevu = POINÇON IRRÉGULIERS

			
			if (($affEmp) || (!isset($mWinpunch->winpunch_params["texceptions"])))
			{
				$line_show=true;

				$arr_in = explode(" ",$reel_in);
				$reel_in = $arr_in[1];
	
				if (!empty($reel_out))
				{
					$arr_out = explode(" ",$reel_out);
					$reel_out = $arr_out[1];	
				}

				winpunch_reports::show_bloc($rows,$prevu_in,$prevu_out,$reel_in,$reel_out,$color,$color2,$color3,$color4,$prevus_in,$reels_in,$nbExceptions,$exceptions,true,$current);
			}	
			
			
			
			return $line_show;
	}

	public function show_bloc($rows,$prevu_in,$prevu_out,$reel_in,$reel_out,$color,$color2,$color3,$color4,$prevus_in,$reels_in,$nbExceptions,$exceptions,$is_prevu=true,$current)
	{	
		?>
		 <td id="td_<?php echo $current;?>" style="width:33%;vertical-align:top;">
				  <table style="width:100%;" border="1">
				  <tr>
				   <td colspan="4" style="font-weight:bold;"><?php echo $rows->no_employe;?> - <?php echo $rows->nom_complet;?>
				 </td>
				</tr>
				  <tr>
				   <td colspan="2" style="width:50%;text-align:center;">Prévu
				 </td>
				   <td colspan="2" style="width:50%;text-align:center;">Réel
				 </td>
				</tr>
				 <tr>
				   <td style="width:40px;text-align:center;">IN</td>
				   <td style="width:40px;text-align:center;">OUT</td>
				   <td style="width:40px;text-align:center;">IN</td>
				   <td style="width:40px;text-align:center;">OUT</td>
				</tr>
				 <tr>
				 <td style="width:40px;background-color:<?php echo $color;?>"><?php echo $prevu_in;?></td>
				 <td style="width:40px;background-color:<?php echo $color2;?>"><?php echo $prevu_out;?></td>
				 <td style="width:40px;background-color:<?php echo $color3;?>"><?php echo $reel_in;?></td>
				 <td style="width:40px;background-color:<?php echo $color4;?>"><?php echo $reel_out;?></td>
				 </tr>

				<?php
				if ($is_prevu == true)
				{
					//PUNCH RÉELS PLUS SOUVENT QUE PRÉVU.
					if ((sizeof($reels_in)) > (sizeof($prevus_in)))
					{

							for($i = sizeof($prevus_in); $i <  sizeof($reels_in) ; $i++)
							{	
								 $reel_out = "";

								 $reel_in = $reels_in[$i]["VALUE"];
								 if (isset($reels_out[$i]["FULL"]))
								 { 
									 $reel_out = $reels_out[$i]["FULL"];
									 $arr_out = explode(" ",$reel_out);
									 $reel_out = $arr_out[1];
								 }
							?>
							 <tr>
							 <td style="width:40px;background-color:<?php echo $color;?>"></td>
							 <td style="width:40px;background-color:<?php echo $color2;?>"></td>
							 <td style="width:40px;background-color:<?php echo $color3;?>"><?php echo $reel_in;?></td>
							 <td style="width:40px;background-color:<?php echo $color4;?>"><?php echo $reel_out;?></td>
							 </tr>
							<?php
							}		
					} 
				}
				else
				{
					for($i = 1; $i <  sizeof($reels_in) ; $i++)
					{	
								 $reel_out = "";

								 $reel_in = $reels_in[$i]["VALUE"];
								 if (isset($reels_out[$i]["FULL"]))
								 { 
									 $reel_out = $reels_out[$i]["FULL"];
									 $arr_out = explode(" ",$reel_out);
									 $reel_out = $arr_out[1];
								 }
							?>
							 <tr>
							 <td style="width:40px;background-color:<?php echo $color;?>"></td>
							 <td style="width:40px;background-color:<?php echo $color2;?>"></td>
							 <td style="width:40px;background-color:<?php echo $color3;?>"><?php echo $reel_in;?></td>
							 <td style="width:40px;background-color:<?php echo $color4;?>"><?php echo $reel_out;?></td>
							 </tr>
							<?php
					}	
				}

				if ($nbExceptions >= 1)
				{
				?>
				<tr>
				   <td colspan="4">Exceptions :<br/>
				   <?php echo $exceptions;?></td>
				</tr>
				<?php
				}
				?>
				</table>
			</td>
			<?php
	}

	public function bloc_data_in_out($rows,$arrCols,$grpby,$current_rup,$prefix)
	{
		?>
		<tr style="vertical-align:top;">
		<?php
		foreach($arrCols as $col)
   		{
			$width = $col["WIDTH"];
			$align= "";
			
			if (isset($col["ALIGN"]))
				$align = $col["ALIGN"];

			if ($align == "")
				$align= "left";
			
			if (($col["SQL"] != $grpby) && ($col["SQL"] != "IN_OUT"))
			{
				if (($col["SQL"] != "no_employe") && ($col["SQL"] != "nom_complet"))
				{
					
					$data = $rows->$col["SQL"];
					$data = winpunch_reports::set_real_value($data,$prefix);
					?>
					<td style="width:<?php echo $width;?>;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
					<?php
				}
				else
				{
					$data = $rows->$col["SQL"];
					if ($col["SQL"] == "no_employe")
					{
						if ($this->current_user == $rows->$col["SQL"])
						{
							?>
							<td colspan="2"></td>
							<?php
						}
						else
						{
							$nom_complet = $rows->nom_complet;
							?>
							
							<td style="width:100px;text-align:<?php echo $align;?>;"><?php echo $data;?></td>
							<td style="width:200px;text-align:<?php echo $align;?>;"><?php echo $nom_complet;?></td>
							<?php
							$this->current_user = $data;
							
							
						}
					}
					
					
					
					
				}
			}

		}
		
	
		$name = "";
		//punch entrée sorties
		foreach($arrCols as $col)
   		{
				if ($col["SQL"] == "IN_OUT")
				{
						
						$align= "";
						if (isset($col["ALIGN"]))
							$align = $col["ALIGN"];

						if ($align == "")
							$align= "left";
						
						$arrvalues = explode(";",$rows->$col["SQL"]);
						$max = count($arrvalues);

						?>
						<td style="width:200px;text-align:center;">
						<table style="width:100%;font-weight:bold;" border="1" cellspacing="1">
						<tr>
						<td style="width:46px;text-align:left;">IN</td>
						<td style="width:46px;text-align:left;">OUT</td>
						<td style="width:46px;text-align:left;">IN</td>
						<td style="width:46px;text-align:left;">OUT</td>
						<tr style="font-weight:normal;">
						<?php
						$i=0;
						foreach($arrvalues as $values)
						{	
						?>
						<td style="width:46px;text-align:left;"><?php echo $values;?></td>
						<?php
						$i++;

							if ($i % 4 == 0)  //après 4 valeurs , change de ligne.
								if ($i < $max)
									echo "</tr><tr style='font-weight:normal;'>";
						}
						?>
						</tr>
						</table>
						</td>
						<?php
				}
		
		}
		?>
		
		</td>
		</tr>
		<?php
	}

	function addTime($temps,$hours=0, $minutes=0, $seconds=0)
	{

	    // on split le temps
	    $temp_string = explode(":", $temps);
	    $totalHours = $temp_string[0] + $hours;

	    if (isset($temp_string[1]))
	    	$totalMinutes = $temp_string[1] + $minutes;
	    else
		$totalMinutes = $minutes;

	    if (isset($temp_string[2]))
	    	$totalSeconds = $temp_string[2] + $seconds;
	    else
		$totalSeconds = $seconds;

	    if ( $totalMinutes / 60 > 1) {
	     $totalHours = $totalHours + floor($totalMinutes/60);
	     $totalMinutes = $totalMinutes % 60;
	    }

	    if ( $totalSeconds / 60 > 1) {
	     $totalMinutes = $totalHours + floor($totalSeconds/60);
	     $totalSeconds = $totalSeconds % 60;
	    }
	    if( $totalHours < 10 ) {
	     $totalHours = "0" . $totalHours;
	    }
	    if( $totalMinutes < 10 ) {
	     $totalMinutes = "0" . $totalMinutes;
	    }
	    if( $totalSeconds < 10 ) {
	     $totalSeconds = "0" . $totalSeconds;
	    }



	   if ($totalMinutes == 60)
	   {
		 $totalHours ++;
		 $totalMinutes = "00";
	   }
	
	    $myTime = $totalHours . ":" . $totalMinutes . ":" . $totalSeconds;

	    return $myTime;
	}

	function print_code39_barcode($string) 
	{
		$code39 = array(
		'0'=>'NnNwWnWnN', '1'=>'WnNwNnNnW',
		'2'=>'NnWwNnNnW', '3'=>'WnWwNnNnN',
		'4'=>'NnNwWnNnW', '5'=>'WnNwWnNnN',
		'6'=>'NnWwWnNnN', '7'=>'NnNwNnWnW',
		'8'=>'WnNwNnWnN', '9'=>'NnWwNnWnN',
		'A'=>'WnNnNwNnW', 'B'=>'NnWnNwNnW',
		'C'=>'WnWnNwNnN', 'D'=>'NnNnWwNnW',
		'E'=>'WnNnWwNnN', 'F'=>'NnWnWwNnN',
		'G'=>'NnNnNwWnW', 'H'=>'WnNnNwWnN',
		'I'=>'NnWnNwWnN', 'J'=>'NnNnWwWnN',
		'K'=>'WnNnNnNwW', 'L'=>'NnWnNnNwW',
		'M'=>'WnWnNnNwN', 'N'=>'NnNnWnNwW',
		'O'=>'WnNnWnNwN', 'P'=>'NnWnWnNwN',
		'Q'=>'NnNnNnWwW', 'R'=>'WnNnNnWwN',
		'S'=>'NnWnNnWwN', 'T'=>'NnNnWnWwN',
		'U'=>'WwNnNnNnW', 'V'=>'NwWnNnNnW',
		'W'=>'WwWnNnNnN', 'X'=>'NwNnWnNnW',
		'Y'=>'WwNnWnNnN', 'Z'=>'NwWnWnNnN',
		'-'=>'NwNnNnWnW', '.'=>'WwNnNnWnN',
		' '=>'NwWnNnWnN', '$'=>'NwNwNwNnN',
		'/'=>'NwNwNnNwN', '+'=>'NwNnNwNwN',
		'%'=>'NnNwNwNwN', '*'=>'NwNnWnWnN');

	$content = "";
	
	$content = "<div class='barcode'>";	
	
		// Split the string up into its separate characters and iterate over them
		if (substr($string,0,1)!='*') $string = "*$string";
		if (substr($string,-1,1)!='*') $string = "$string*";
		$string = strtoupper($string);
		$chars = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($chars as $char) 
		{
			// Split this character's encoding string up into its separate characters and iterate
			$pattern = preg_split('//', $code39[$char], -1, PREG_SPLIT_NO_EMPTY);
			foreach ($pattern as $bar) 
			{
					// Determine bar's class
					switch ($bar) 
					{
					case 'W': $classes = "wide black"; break;
					case 'N': $classes = "narrow black"; break;
					case 'w': $classes = "wide white"; break;
					case 'n': $classes = "narrow white"; break;
					}
				// Print bar
				$content .= "<div class='$classes'></div>";
			}
				// Separator between characters
				$content .= "<div class='narrow white'></div>";
		}
				$content .= "</div>\n";

		return $content;
	}

	//ATRAHAN
	function atrahan_bloc_total_day($prefix)
	{
		//next split
		$cumul_day = $this->total;
		$duree = winpunch_reports::set_real_value($cumul_day,$prefix);
		
		?>
		<tr style="font-weight:bold;text-align:right;">
		<td colspan="10"></td>
		<td><?php echo $duree;?></td>
		<td><?php echo $this->dprev;?></td>
		<td><?php echo $this->fprev;?></td>	
		<td><?php echo $this->dreel;?></td>
		<td><?php echo $this->freel;?></td>
		</tr>
		<?php
	}
	
	
	
	function atrahan_cumul_data($rows,$data,$prefix)
	{
		if (($prefix == "N") || ($prefix == "n"))
			$this->total += $data;
		else
		{
			//as time
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
			
			if (isset($this->total))
				$this->total = winpunch_reports::addTime($this->total,$hours,$mins,0);
			else
				$this->total = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
		}

		$this->dprev = $rows->debut_prevu;
		$this->fprev = $rows->fin_prevu;
		$this->dreel = $rows->debut_reel;
		$this->freel = $rows->fin_reel;	
	}

	function atrahan_cumul_paye($data,$prefix)
	{
		if (($prefix == "N") || ($prefix == "n"))
			$this->paye += $data;
		else
		{
			//as time
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
			
			if (isset($this->paye))
				$this->paye = winpunch_reports::addTime($this->paye,$hours,$mins,0);
			else
				$this->paye = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
		}
	}

	function atrahan_display_line($rows,$current_date,$prefix,$current_rup)
	{
		$formateur = '<input type="checkbox" disabled="disabled">';	
		$input = '<input type="checkbox" disabled="disabled">';

		if ($rows->formation == 1)  
		{ 
				ob_start();
				?>
				<input type='checkbox' disabled="disabled" checked='checked'> 
				<?php			
				$formation = ob_get_contents();
				ob_end_flush();
		}
	
		if ($rows->formateur == 1)  
		{ 
				ob_start();
				?>
				<input type='checkbox' disabled="disabled" checked='checked'> 
				<?php
				$formateur = ob_get_contents();
				ob_end_flush();	
		}

		$duree_field = "duree_".$prefix;
		$duree = winpunch_reports::set_real_value($rows->$duree_field,$prefix);

		$this->atrahan_cumul_data($rows,$rows->$duree_field,$prefix);
		?>	
		<tr>
		<td><?php echo $rows->jour_de_paie;?></td>
		<td><?php echo $rows->desc_classe_employe;?></td>
		<td><?php echo $rows->desc_echelon;?></td>
		<td><?php echo $rows->desc_tache;?></td>
		<td><?php echo $rows->classe_tache;?></td>
		<td style="text-align:center;"><?php echo $input;?></td>
		<td style="text-align:center;"><?php echo $formateur;?></td>
		<td><?php echo $rows->nom_article;?></td>
		<td><?php echo $rows->desc_classe_paye;?></td>
		<td style="text-align:right;"><?php echo number_format($rows->taux_paye,2,"."," ");?></td>
		<td style="text-align:right;"><?php echo $duree;?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>	
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
		<?php
	}

	function atrahan_bloc_reset($prefix)
	{	
		winpunch_reports::atrahan_bloc_total_day($prefix);
		winpunch_reports::reset_total($prefix);
		$this->dprev = "";
		$this->fprev = "";
		$this->dreel = "";
		$this->freel = "";
	}
	
	//ACCEO
	function acceo_bloc_total_weeks($prefix)
	{
		//next split
		$cumul = $this->gtotal;
		$duree = winpunch_reports::set_real_value($cumul,$prefix);
		
		?>
		<tr style="text-align:right;">
		<td colspan="6" style="font-weight:bold;">GRAND TOTAL : </td>
		<td style="font-weight:bold;"><?php echo $duree;?></td>
		</tr>
		</tr>
		<!--<tr>
		<td colspan="7" style="font-weight:bold;">SIGNATURE :<hr/></td>
		<td colspan="6" style="font-weight:bold;">EMPLOYÉ :<hr/></td>
		</tr>-->
		<?php
	}

	function acceo_bloc_total_day($prefix)
	{
		//next split
		$cumul_day = $this->day_total;
		if (($prefix == "h") && ($prefix == "H"))
		{
			$temp_string = explode(":", $cumul_day);
			$hours = $temp_string[0];
	
			//cas si ajout manuel correctifs.
			if ((strlen($hours) == 1) &&  ($hours < 10))
				 $hours = "0" . $hours;

			$cumul_day = $hours.":".$temp_string[1].":".$temp_string[2];
		}
		$duree = winpunch_reports::set_real_value($cumul_day,$prefix);

		?>
		<tr>
		<td colspan="4">Note : <?php echo $this->note;?></td>
		<td colspan="2" style="font-weight:bold;text-align:right;"><?php echo $this->repas_paye;?></td>
		<td style="font-weight:bold;text-align:right;"><?php echo $duree;?></td>
		<td><?php echo $this->dprev;?></td>
		<td><?php echo $this->dreel;?></td>
		<td style="font-weight:bold;"><?php echo $this->dpaye;?></td>	
		<td><?php echo $this->fprev;?></td>	
		<td><?php echo $this->freel;?></td>
		<td style="font-weight:bold;"><?php echo $this->fpaye;?></td>
		</tr>
		<?php
		
	}


	function acceo_bloc_total_week($prefix)
	{
		//next split
		$cumul_day = $this->total;
		$duree = winpunch_reports::set_real_value($cumul_day,$prefix);
		?>
		<tr>

		<tr style="text-align:right;">
		<td colspan="6" style="font-weight:bold;">TOTAL SEMAINE : </td>
		<td style="font-weight:bold;"><?php echo $duree;?></td>
		</tr>
		<?php
		
	}

	function acceo_bloc_day_reset($prefix)
	{
		winpunch_reports::acceo_bloc_total_day($prefix);
		winpunch_reports::reset_dtotal($prefix); //reset total day
		
		$this->dprev = "";
		$this->dreel = "";
		$this->dpaye = "";
		$this->fprev = "";
		$this->fprevu = "";
		$this->freel = "";
		$this->fpaye = "";


		$this->repas_paye = "";
		$this->note = "";		
	}

	function acceo_bloc_reset($prefix)
	{
		winpunch_reports::acceo_bloc_total_week($prefix);
		winpunch_reports::reset_total($prefix); //reset total semaine
		$this->dprev = "";
		$this->dreel = "";
		$this->dpaye = "";
		$this->fprev = "";
		$this->fprevu = "";
		$this->freel = "";
		$this->fpaye = "";	
	}

	function acceo_display_line($rows,$current_date,$prefix,$current_rup)
	{
		$coldureeField = "duree_".$prefix;
		$duree =  winpunch_reports::set_real_value($rows->$coldureeField,$prefix);
		$this->acceo_cumul_data($rows,$rows->$coldureeField,$prefix);
		?>
		<tr style="text-align:left;">
		<td style="width:50px;"><?php echo $rows->jour_de_paie;?></td>
		<td style="width:30px;"><?php echo $rows->desc_etablissement;?></td>
		<td style="width:150px;"><?php echo $rows->desc_tache;?></td>
		<td style="width:100px;"><?php echo $rows->desc_activite;?></td>
		<td style="width:50px;"><?php echo $rows->nom_article;?></td>
		<td style="width:30px;"><?php echo $rows->verifie;?></td>
		<td style="width:30px;text-align:right;"><?php echo $duree;?></td>
		<td style="width:50px;"></td>
		<td style="width:50px;"></td>
		<td style="width:50px;"></td>
		<td style="width:50px;"></td>
		<td style="width:50px;"></td>
		<td style="width:50px;"></td>	
		</tr>
		<?php
	}

	function acceo_cumul_data($rows,$data,$prefix)
	{
		if (($prefix == "N") || ($prefix == "n"))
		{
			$this->day_total += $data; 
			$this->total += $data;
		
			$this->gtotal += $data;
		}
		else
		{
			//as time
			$arrdata = explode(":",$data);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
			
			if (isset($this->day_total))
				$this->day_total = winpunch_reports::addTime($this->day_total,$hours,$mins,0);
			else
				$this->day_total = winpunch_reports::addTime("00:00:00",$hours,$mins,0);

			if (isset($this->total))
				$this->total = winpunch_reports::addTime($this->total,$hours,$mins,0);
			else
				$this->total = winpunch_reports::addTime("00:00:00",$hours,$mins,0);

			
			if (isset($this->gtotal))
				$this->gtotal = winpunch_reports::addTime($this->gtotal,$hours,$mins,0);
			else
				$this->gtotal = winpunch_reports::addTime("00:00:00",$hours,$mins,0);
		
		}

		$this->dprev = winpunch_reports::set_hours_display($rows->debut_prevu,$prefix);
		$this->dreel = winpunch_reports::set_hours_display($rows->debut_reel,$prefix);
		$this->dpaye = winpunch_reports::set_hours_display($rows->debut_paie,$prefix);
		$this->fprev = winpunch_reports::set_hours_display($rows->fin_prevu,$prefix);
		$this->fprevu = winpunch_reports::set_hours_display($rows->fin_prevu,$prefix);
		$this->freel = winpunch_reports::set_hours_display($rows->fin_reel,$prefix);
		$this->fpaye = winpunch_reports::set_hours_display($rows->fin_paie,$prefix);	

		$this->note = $rows->commentaires;	
		$this->repas_paye = $rows->repas_paye;
	}

	public function set_hours_display($value,$prefix)
	{
		
			$arrdata = explode(":",$value);
			$hours = $arrdata[0];
			$mins = $arrdata[1];
			return $hours.":".$mins;
	}

}
