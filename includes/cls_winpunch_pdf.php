<?php

date_default_timezone_set("America/Montreal"); //Fix pour php 5.6
require_once("db.php");

class winpunch_pdf 
{	
	public $columns = array();
	public $total_len = 0;

	public $setup =  NULL;
	public $sens = NULL;
	public $title = "";
	public $margin_width = 0;
	public $marginwidth = 0;
	public $margin_height = 0;
	public $lineheight =0;
	
	public $page_width = 0;
	public $page_height = 0;

	public $calc_page_width = 0; // - margins width
	//public $xmlDoc = NULL;
	public $JSon = NULL;
	public $sql_report = "";
	
	public function execute_query_get_len_json($mWinpunch)
	{
		global $link;
		
		$len_cols = array();

		$total_len = 0;

		$results = $this->execute_query($mWinpunch);

		$nbLines = $results->num_rows;
		
		//SUM all length
		while ($row = $results->fetch_assoc())
		{
			foreach($row as $key => $value)
			{
				if (isset($len_cols[$key]))
					$len_cols[$key] += strlen($value);
				else
					$len_cols[$key] = strlen($value);
			}
		}
			
		$arr = $len_cols;
		
		foreach($arr as $key => $col)
		{	
			$total_len += ($col / $results->num_rows);
			//total par champ / num lines	
		}

		$arrkey = $this->JSon["col"];//columns Json
		
		foreach($arrkey as $key => $arr)
		{
			//get avg len ...
			
			$tmp["name"] = $arr["sql"];
			if ($tmp["name"] != "")
			{
				$tmp["label"] = $arr["label"];
			
				$col_tot = $this->array_find($len_cols,$arr["sql"],$arr["sql"]);
				
				$len = ($col_tot / $nbLines);
					
				$max = round(($len/$total_len) * $this->calc_page_width);   //len avg / max_len line * page
		
				$tmp["max_length"] = $max;

				array_push($this->columns,$tmp);
				
			}	
		
		}
	
		$results->data_seek(0);  //reset cursor to start after fetch all
		
		return $results;
		
	}

	public function execute_query($mWinpunch)
	{
		global $link;

		//Apply Filters
		
		if (isset($mWinpunch->winpunch_params["groupby"]))
		{		
			if ($mWinpunch->winpunch_params["groupby"] != -1)
			{
				$argrpby = $mWinpunch->get_groupby();
				$this->sql .= " ORDER BY ".$argrpby["desc_groupby"]." ASC";  //sort by
			}
		}	
		
		$results = $link->query($this->sql);
		
		
		if ($results) { return $results; } else {  return NULL; } 
	}
	
	

	function init_module_json($module_name)
	{
		$str = file_get_contents($module_name);
		$data = json_decode($str,true);
		$this->JSon = $data;
	}
	
	function set_params_json()
	{
		foreach($this->JSon as $key => $val)
		{
			if ($key == "orientation")
			{
				if ( $val == "portrait") 
					{ $this->page_width = 612; $this->page_height = 792; } else { $this->page_width = 792; $this->page_height = 612;  }
			}

			$this->$key = $val;
		}
		
		$this->calc_page_width = $this->page_width - ($this->marginwidth *2) ;  //enlève margins du calcul.
	}

	function array_find($arr,$key,$value)
	{	
		foreach($arr as $key => $col)
		{	
			if ($key == $value)
				return $col;
		}
	}
	
	function array_search_result($array,$key,$value)
	{
	    	foreach($array as $k=>$v)
	    	{
			if ($v[$key] == $value)	 
				 return $v["max_length"];	  
	   	}
		return -1;
	}

	//XML

	/*
	function execute_query_get_len($mWinpunch)
	{
		global $link;
		
		$len_cols = array();

		$total_len = 0;

		$results = $this->execute_query($mWinpunch);

		$nbLines = $results->num_rows;
		
		//SUM all length
		while ($row = $results->fetch_assoc())
		{
			foreach($row as $key => $value)
			{
				if (isset($len_cols[$key]))
					$len_cols[$key] += strlen($value);
				else
					$len_cols[$key] = strlen($value);
			}
		}
			
		$arr = $len_cols;
		
		foreach($arr as $key => $col)
		{	
			$total_len += ($col / $results->num_rows);	
		}

		//total par champ / num lines	
		$arrkey = $this->xmlDoc->col;
		//
			
		foreach($arrkey as $key => $arr)
		{
			//get avg len ...
			
			$tmp["name"] = $arr["sql"];
			if ($tmp["name"] != "")
			{
				$tmp["label"] = $arr["label"];
			
				$col_tot = $this->array_find($len_cols,$arr["sql"],$arr["sql"]);
				$len = ($col_tot / $nbLines);
					
				$max = round(($len/$total_len) * $this->calc_page_width);   //len avg / max_len line * page
		
				$tmp["max_length"] = $max;

				array_push($this->columns,$tmp);
				
			}	
			
			
		}
	
		$results->data_seek(0);  //reset cursor to start after fetch all
		
		return $results;
		
	}

	function init_module($module_name)
	{
		//lire fichier xml correspondant au rapport.
		$xmlstr = file_get_contents($module_name);
		$xmlDoc = simplexml_load_string($xmlstr);
		$this->xmlDoc = $xmlDoc;				
	}

	function set_params()
	{
		$setup =  $this->xmlDoc->setup;
		$this->sens = $setup->orientation;
		$this->title = base64_encode(utf8_decode($setup->title));
		$this->margin_width = $setup->marginwidth;
		$this->margin_height = $setup->marginheight;
		$this->lineheight = $setup->lineheight;

		$this->sql_report = $this->xmlDoc->query->sql;  // basic query no filter

		if ($this->sens == "portrait") { $this->page_width = 612; $this->page_height = 792; } else { $this->page_width = 792; $this->page_height = 612;  }

		$this->calc_page_width = $this->page_width - ($this->margin_width *2) ;  //enlève margins du calcul.
	}
	*/

	
}

