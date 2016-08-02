<?php

date_default_timezone_set("America/Montreal"); //Fix pour php 5.6
require_once("db.php");
require_once("cls_winpunch_reports.php");
class winpunch
{
	//defines
	public $userAcces = 0;
	public $regroupement_1 = "";
	public $regroupement_2 = "";
	public $regroupement_3 = "";
	public $regroupement_4 = "";
	public $regroupement_5 = "";
	public $regroupement_6 = "";

	public $print_mode = 0;
	public $rptTitle = "";
	
	public $winpunch_params = array(); //params set to object
	public $winpunch_params_lbs = array("userUID","rptUID","edate","fonctionP","groupe1","groupe2","groupe3","groupe4","groupe5","groupe6","groupePaie","horaireM","noEmploye","period","sdate","statu","porte","groupby","unit","texceptions","codeAbs","barcode","regroup","job","oper","jobqty");
	//params list
	public $params = array(); //from url
	
	public function reset_params()
	{
		$this->winpunch_params = array();
	}
	public function validate_param()
	{
		$this->winpunch_params["userUID"] = -1;
		$this->winpunch_params["rptUID"] = -1;

		if (isset($_SESSION["userUID"]))
			$this->winpunch_params["userUID"] = $_SESSION["userUID"];
		else
		{
			//Vérification des paramètres url.
			if (isset($_GET["token"]))
			{
				$params = base64_decode($_GET["token"]);
				$arr_params = explode("&",$params);
				$this->params = $arr_params;
				//set all params
				foreach($this->winpunch_params_lbs as $current_param)
				{
					$this->winpunch_params[$current_param] = winpunch::search_param($current_param);
				}
			}	
		}

		if ($this->winpunch_params["userUID"] != -1) { return true; } else { return false; }	 
	}
	
	public function get_param($param_name)
	{
		return $this->winpunch_params[$param_name];
	}

	public function search_param($param_name)
	{
		$params = $this->params;
		foreach($params as $param)
		{
		 	if (strpos($param,$param_name) !== false)
			{
				$content = $param;
				$arr_content = explode("=",$content);
				if (isset($arr_content[1])) { return $arr_content[1]; } else { return ""; }
			}		
		}
	}
	
	public function filter_user_access()
	{
		global $link;

		if (isset($this->winpunch_params["userUID"]))
		{
			$SQL = "SELECT id_droits,regroupement_1,regroupement_2,regroupement_3,regroupement_4,regroupement_5,regroupement_6
				FROM users WHERE userid = ". $this->winpunch_params["userUID"];
			
			$results = $link->query($SQL);
			if ($results)
			{
				if ($results->num_rows == 1)
				{
					$row = $results->fetch_object();
					$this->userAcces = $row->id_droits;
					$this->regroupement_1 = $row->regroupement_1;
					$this->regroupement_2 = $row->regroupement_2;
					$this->regroupement_3 = $row->regroupement_3;
					$this->regroupement_4 = $row->regroupement_4;
					$this->regroupement_5 = $row->regroupement_5;
					$this->regroupement_6 = $row->regroupement_6;
				}
			}	
		}
		else
		  winpunch::winpunch_die();
	}
	
	public function list_cat_reports()
	{
		global $link;
		
		winpunch::filter_user_access();
		
		if ($this->userAcces == -1)
			winpunch::winpunch_die();
		else
		{
			$SQL = "SELECT * FROM categories_rapports WHERE disponible = 1 ORDER BY desc_cat_rapport ASC";
			$results = $link->query($SQL);
			if ($results)
			{
			?>
			<fieldset>
				<span class="form_text">Catégories :</span>
				<select id="sel_cats" onchange="load_reports('',0);">
				<option value="-1">Tous</option>
				<?php
				while($rows = $results->fetch_object())
				{
				?><option value="<?php echo $rows->id_cat_rapport;?>"><?php echo $rows->desc_cat_rapport;?></option><?php
				}
				?>
				</select>
			</fieldset>
			<?php
			}
		}
	}

	public function list_reports($catUID=NULL)
	{
		global $link;
		
		winpunch::filter_user_access();
		
	        if ($this->userAcces == -1)
			winpunch::winpunch_die();
		else
		{
			//Affiche uniquement les rapports dont tu as l'accès et qui sont existants (associé vers un template)
			$SQL = "SELECT * FROM rapports_web WHERE 1=1 ";

			if (($catUID != NULL) && ($catUID != -1 ))
				$SQL .= " AND id_cat_rapport = ".$catUID;

			$SQL .= " AND page_etat IS NOT NULL AND droits >= ".$this->userAcces. "  ORDER BY desc_etat ASC";
		
			$results = $link->query($SQL);
			if ($results)
			{
			?>
			<fieldset>
				<span class="form_text" style="padding-right:12px;">Rapports :</span>
				<select id="sel_report" onchange="load_groupby();load_options();load_params('',0);">
					<option value="-1">*** Sélectionner ***</option>
					<?php
					$selected = "";
					while($rows = $results->fetch_object())
					{
						if ($this->winpunch_params["rptUID"] == $rows->id_etat)
							$selected = "selected=selected";
						else
							$selected =  "";
					?><option value="<?php echo $rows->id_etat;?>" <?php echo $selected;?>><?php echo $rows->desc_etat;?></option><?php
					}
					?>
				</select>
			</fieldset>
			<?php
		        }
		}
	}

	public function get_user()
	{
		global $link;	
		$user_id = $this->winpunch_params["userUID"];
		$SQL = "SELECT user_name FROM users WHERE userid = ".$user_id;
		$results = $link->query($SQL);
		if ($results)
		{
			if ($results->num_rows >= 1)
			{
				$rows = $results->fetch_object();
				return $rows->user_name;
			}
		}
	}

	public function winpunch_die()
	{
		echo "Vous n'êtes pas autoriser dans cette section !";
		die();
	}

	public function load_groupby($rptUID)
	{
		global $link;
		
		$SQL = "SELECT default_grp_id FROM rapports_web WHERE id_etat= ".$rptUID;
		$results_rpt = $link->query($SQL);
		if ($results_rpt)
		{
			if ($results_rpt->num_rows >= 1)
			{
				$rows_rpt = $results_rpt->fetch_object();
				$default_grp_id = $rows_rpt->default_grp_id;
				$SQL = "SELECT id_groupby,nom_groupby FROM rapports_groupby_web WHERE id_etat = ".$rptUID. " ORDER BY desc_groupby ASC ";
				$results = $link->query($SQL);
				if ($results)
				{
					if ($results->num_rows >= 1)
					{
						$content = "";
						ob_start();
						?>
						<div style="margin-top:20px;">
						<fieldset>
							<span class="form_text">Paramètres de groupement</span>
							<div style="margin-top:10px;">
								<table>
								<tr>
								<td style="width:100px;font-weight:bold;">Grouper par :</td>	
								<td>
									<select id="groupby">
									<?php 
									if ($default_grp_id == NULL)
									{
									?><option value="-1">Aucun</option><?php
									}
									$selected="";
									while($rows_sel = $results->fetch_object())
									{
									  if (!empty($rows_sel->nom_groupby))
										if ($default_grp_id != NULL)
											if ($rows_sel->id_groupby == $default_grp_id)
												$selected = "selected=selected";
										?><option 
										   value="<?php echo $rows_sel->id_groupby;?>" 
										   <?php echo $selected;?>> <?php echo $rows_sel->nom_groupby;?></option>
										   <?php								
										   $selected ="";
									}	
									?>
									</select>
								</td>
								</tr>
								</table>
							</div>
						</fieldset>
						</div>
						<?php
						$content = ob_get_contents();
						ob_end_flush();
					}
				}
			}
		}
	}

	public function load_options($rptUID)
	{
		global $link;
		
		$SQL = "SELECT * FROM rapports_params_display_web WHERE id_etat = ".$rptUID. " ORDER BY rang_param ASC ";
		$results = $link->query($SQL);
		if ($results)
		{
			if ($results->num_rows >= 1)
			{
				$content = "";
				ob_start();
				?>
				<div style="margin-top:20px;text-align:left;width:100%;">
				<fieldset>
					<span class="form_text">Options d'affichage</span>
					<div style="margin-top:10px;">
				<?php
				while($rows = $results->fetch_object())
				{
					if ($rows->type_obj  == "select")
					{
						
							$SQL_SELECT = "SELECT ".$rows->champs." FROM ". $rows->data_source. " ORDER BY ".$rows->tri." ".$rows->sens;
							$results_select = $link->query($SQL_SELECT);
							if ($results_select)
							{
								$default_id = $rows->default_id;
								$selected = ""; 
								?>
								<div style="width:400px;float:left;">
									<table>
									<tr>
									<td style="width:100px;">
									<span style="font-weight:bold;" id="lb_<?php echo $rows->nom_param?>">
									<?php echo $rows->desc_param;?> :</span>
									</td>
									<td>
										<select id="<?php echo $rows->nom_param;?>">
										<?php
										if ($rows->ValueAll == 1) { echo "<option value='-1'>Tous</option>"; }
									
										while($rows_sel = $results_select->fetch_object())
										{
										  if (!empty($rows_sel->value))
										  {
											if ($default_id != NULL)
											{
											  if ($default_id == $rows_sel->id) { $selected = "selected=selected"; }		
											}
											?><option 
											   value="<?php echo $rows_sel->id;?>" 
											   <?php echo $selected;?>><?php echo $rows_sel->value;?></option>
											   <?php							
											  $selected= "";
										  }
										}	
										?>
										</select>
									</td>
									</tr>
									</table>
								</div>
							<?php
							}
					}
				}
				?>
				     </div>
				</fieldset>
				<div style="clear:both;"></div>
				</div>
				</div>
				<?php
				$content = ob_get_contents();
				ob_end_flush();
			}
		}	
	}

	//Chargement des paramètres du rapport avec requis ou non.. selon type...
	public function load_params($rptUID)
	{
		global $link,$path_js;
		
		$SQL = "SELECT * FROM rapports_params_web WHERE id_etat = ".$rptUID. " ORDER BY rang_param ASC ";
		$results = $link->query($SQL);
		if ($results)
		{
			if ($results->num_rows >= 1)
			{
				$content = "";
				ob_start();
				?>
				<div style="margin-top:20px;text-align:left;width:100%;">

				<fieldset>
					<span class="form_text">Paramètres de filtrage</span>
					<div style="margin-top:10px;">
					<?php
					while($rows = $results->fetch_object())
					{
						switch ($rows->type_obj)
						{
							case "select" :

								$isMultiple = $rows->multiple;
								$SQL_SELECT = "SELECT ".$rows->champs." FROM ". $rows->data_source;
								$SQL_SELECT .= " ORDER BY ".$rows->tri." ".$rows->sens;	
								$results_select = $link->query($SQL_SELECT);
								if ($results_select)
								{
									?>
									<div style="width:400px;float:left;">
										<table>
										<tr>
										<td style="width:100px;">
										<span style="font-weight:bold;" 
										id="lb_<?php echo $rows->nom_param?>"><?php echo $rows->desc_param;?> :</span>
										<input type="hidden" id="<?php echo $rows->nom_param;?>_req" 
										value="<?php if ($rows->requis == 1) { echo '1'; } else { echo '0'; };?>" />
										</td>
										<td>
											<select style="max-width:200px;" id="<?php echo $rows->nom_param;?>" 
											<?php if ($isMultiple == 1)  { echo  'multiple="multiple"';};?>>
											<?php 
											if ($isMultiple == 0) { echo '<option value="-1">Tous</option>'; }
											while($rows_sel = $results_select->fetch_object())
											{
											  if (!empty($rows_sel->value))
												?><option value="<?php echo $rows_sel->id;?>"><?php echo $rows_sel->value;?></option><?php
											}	
											?>
											</select>
								
										<?php
										if ($rows->requis == 1)
											echo "<span class='req'> * </span>";				
									
										if ($isMultiple == 1)
										{     
											$obj = $rows->nom_param;
											?>
											<script>
											$('#<?php echo $obj;?>').multipleSelect();
											$(".ms-choice div").css("width","200px","overflow","hidden");
											$(".ms-parent").css("width","200px");	
											$(".ms-drop").find("ul").css("width","200px");
											</script>
											<?php
										}
											?>
														
										</td>
										</tr>
										</table>
									</div>
						
								<?php
								}
							break;
					
							case "text" :
								$js = "";
								if ($rows->widget == "calendar") {  $js = 'onclick="ds_sh(this);" readonly="readonly"'; } 
								?>
								<div style="width:400px;float:left;">
									<table>
									<tr>
									<td style="width:100px;">
									<span style="font-weight:bold;" 
									id="lb_<?php echo $rows->nom_param?>"><?php echo $rows->desc_param;?> :</span>
									<input type="hidden" id="<?php echo $rows->nom_param;?>_req" 
									value="<?php if ($rows->requis == 1) { echo '1';} else { echo '0'; };?>" />
									</td>
									<td>
									<input type="text" style="cursor: text;" id="<?php echo $rows->nom_param;?>" value="" 
									<?php echo $js;?> />
									<?php
									if ($rows->requis == 1) { echo "<span class='req'> * </span>"; }	
									?>
									</td>
									</tr>
									</table>
								</div>
								<?php
							break;
						}
					}
				?>
					<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
					<tr><td id="ds_calclass"></td></tr>
					</table>
				   </div>
				</fieldset>
				<div style="clear:both;"></div>
				</div>
				</div>
				<?php
				$content = ob_get_contents();
				ob_end_flush();
			}
		}	
	}

	//Lance l'affichage du rapport et de son template
	public function show_report()
	{
		global $link;
		$rptUID = $this->winpunch_params["rptUID"];
		$SQL = "SELECT page_etat,print_landscape FROM rapports_web WHERE id_etat = ".$rptUID;
		
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows >= 1)
			 {
				$row = $results->fetch_object();
				$page_etat = $row->page_etat;
			
				
				$mWinpunch = $this;
				
				if (!isset($mWinpunch))
					$mWinpunch = new winpunch();
				
				$mWinPunch->print_mode = $row->print_landscape;
				
				$print_mode = $mWinPunch->print_mode;
				
				
				if (file_exists("../ajax/templates/".$page_etat)) { include_once("../ajax/templates/".$page_etat); }
			}
		}
	}

	public function addTime($temps,$hours=0, $minutes=0, $seconds=0)
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

	    $myTime = $totalHours . ":" . $totalMinutes . ":" . $totalSeconds;

	    return $myTime;
	}	

	public function get_employe()
	{
		global $link;
		
		$SQL = "SELECT CONCAT(nom_employe,' ',prenom_employe) as employe_fullname FROM employes 
			WHERE no_employe = '".$this->winpunch_params["noEmploye"]."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->employe_fullname;
			 }
		}
	}

	public function get_porte()
	{
		global $link;
		
		$SQL = "SELECT CONCAT(no_terminal,' ',desc_porte) as porte FROM portes WHERE no_terminal = '".$this->winpunch_params["porte"]."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->porte;
			 }
		}

	}

	public function get_groupby()
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT nom_groupby,desc_groupby FROM rapports_groupby_web WHERE id_groupby = '".$this->winpunch_params["groupby"]."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["nom_groupby"] =  $row->nom_groupby;
				$argrpby["desc_groupby"] =  $row->desc_groupby;
				return $argrpby;
			 }
		}
	}

	public function get_period_by_date()
	{
		global $link;

		$arr_period = array();
		$SQL = "SELECT id_periode_paie FROM periodes_paie WHERE '".$this->winpunch_params["sdate"]."' BETWEEN debut_periode_paie AND fin_periode_paie";

		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$idperiode = $row->id_periode_paie;
				return $idperiode;
			 }
		}
	}
	public function get_period()
	{
		global $link;

		$arr_period = array();
		$SQL = "SELECT DATE_FORMAT(debut_periode_paie, '%Y-%m-%d') as debut_periode_paie,DATE_FORMAT(fin_periode_paie, '%Y-%m-%d') as fin_periode_paie 
			FROM periodes_paie WHERE id_periode_paie = ".$this->winpunch_params["period"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$arr_period["debut_periode_paie"] =  $row->debut_periode_paie;
				$arr_period["fin_periode_paie"] =  $row->fin_periode_paie;
				return $arr_period;
			 }
		}
	}

	public function get_period_custom($idperiode)
	{
		global $link;

		$arr_period = array();
		$SQL = "SELECT DATE_FORMAT(debut_periode_paie, '%Y-%m-%d') as debut_periode_paie,DATE_FORMAT(fin_periode_paie, '%Y-%m-%d') as fin_periode_paie 
			FROM periodes_paie WHERE id_periode_paie = ".$idperiode;
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$arr_period["debut_periode_paie"] =  $row->debut_periode_paie;
				$arr_period["fin_periode_paie"] =  $row->fin_periode_paie;
				return $arr_period;
			 }
		}
	}

	public function get_horaireM()
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT nom_h_maitre,desc_h_maitre FROM v_liste_h_maitre WHERE nom_h_maitre = '".$this->winpunch_params["horaireM"]."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["nom_h_maitre"] =  $row->nom_h_maitre;
				$argrpby["desc_h_maitre"] =  $row->desc_h_maitre;
				return $argrpby;
			 }
		}
	}

	
	public function get_regroup($grp)
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT desc_regroupement FROM regroupements WHERE id_regroupement = ".$grp;
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["desc_regroupement"] =  $row->desc_regroupement;
				return $argrpby;
			 }
		}
	}

	public function get_regroup_values($V_id,$id)
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT desc_contenu_grp FROM v_regroupement_".$V_id." WHERE id_contenu_grp = ".$id;
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["desc_contenu_grp"] =  $row->desc_contenu_grp;
				return $argrpby;
			 }
		}
	}

	public function get_unit()
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT id_unit,desc_unit FROM v_unit WHERE id_unit = ".$this->winpunch_params["unit"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["id_unit"] =  $row->id_unit;
				$argrpby["desc_unit"] =  $row->desc_unit;
				return $argrpby;
			 }
		}
	}

	public function get_exceptions()
	{
		global $link;

		$argrpby = array();
		$SQL = "SELECT id_exception,desc_exception FROM v_type_exceptions WHERE id_exception = ".$this->winpunch_params["texceptions"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				$argrpby["id_exception"] =  $row->id_exception;
				$argrpby["desc_exception"] =  $row->desc_exception;
				return $argrpby;
			 }
		}
	}

	public function get_codeabs()
	{
		global $link;
		
		$SQL = "SELECT desc_code_absence FROM codes_absences WHERE code_absence = '".$this->winpunch_params["codeAbs"]."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->desc_code_absence;
			 }
		}

	}

	public function get_codeabs_custom($code_abs)
	{
		global $link;
		
		$SQL = "SELECT code_absence,desc_code_absence FROM codes_absences WHERE code_absence = '".$code_abs."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row;
			 }
		}

	}

	public function get_statu()
	{
		global $link;
		
		$SQL = "SELECT description FROM v_statu WHERE id = ".$this->winpunch_params["statu"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->description;
			 }
		}

	}

	
	public function get_job()
	{
		global $link;
		
		$SQL = "SELECT desc_job FROM jobs WHERE no_job = ".$this->winpunch_params["job"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->desc_job;
			 }
		}

	}
	
	public function get_job_custom($no_job)
	{
		global $link;
		
		$SQL = "SELECT no_job,desc_job FROM jobs WHERE no_job = '".$no_job."'";
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row;
			 }
		}
	}

	public function get_oper()
	{
		global $link;
		
		$SQL = "SELECT desc_oper FROM operations WHERE no_oper = ".$this->winpunch_params["oper"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->desc_oper;
			 }
		}

	}

	public function get_jobqty()
	{
		global $link;
		
		$SQL = "SELECT desc_opt_rpt_jobs FROM v_opt_rpt_jobs WHERE id_opt_rpt_jobs = ".$this->winpunch_params["jobqty"];
		$results = $link->query($SQL);
		if ($results)
		{
			 if ($results->num_rows == 1)
			 {
				$row = $results->fetch_object();
				return $row->desc_opt_rpt_jobs;
			 }
		}

	}
}
	
	
