function load_groupby()
{
	var frm_sel_report = $("#sel_report").find('option:selected').val();
	var formData = "repUID="+frm_sel_report;

	$.ajax({
    	 url : "ajax/ajax_load_groupby.php",
   	 type: "POST",
   	 data: formData,
   	 success: function(data, textStatus, jqXHR)
	 {
		$("#div_groupby").empty();
		$("#div_groupby").append(data);	
	 },
	 error: function (jqXHR, textStatus, errorThrown)
	 {
 		alert(textStatus);
	 }
	 });

}
function load_params(selectedValues,token)
{
	var frm_sel_report = $("#sel_report").find('option:selected').val();
	var formData = "repUID="+frm_sel_report;
	 
	$("#div_report").empty();
	$.ajax({
    	 url : "ajax/ajax_load_params.php",
   	 type: "POST",
   	 data: formData,
   	 success: function(data, textStatus, jqXHR)
	 {
		$("#div_params").empty();
		$("#div_params").append(data);

		if (selectedValues != '') { get_params(selectedValues); }

		if (token ==1)
		 create_report();  //launch auto.
			
	 },
	 error: function (jqXHR, textStatus, errorThrown)
	 {
 		alert(textStatus);
	 }
	 });
}

function load_cat_report(selected,token)
{
	var formData = "";

	empty_divs();

	$.ajax({
    	 url : "ajax/ajax_load_cat_report.php",
   	 type: "POST",
   	 data: formData,
   	 success: function(data, textStatus, jqXHR)
	 {
		$("#div_catlist").empty();
		$("#div_catlist").append(data);
		
		if ($("#sel_cats").height())
			  load_reports(selected,token);
	 },
	 error: function (jqXHR, textStatus, errorThrown)
	 {
 		alert("test"+textStatus);
	 }
	 });
}

function empty_divs()
{
	$("#div_list").empty();
	$("#div_report").empty();
	$("#div_params").empty();
	$("#div_groupby").empty();
	$("#div_options").empty();
	$("#div_report").empty();
}
function load_reports(selected,token)
{	
	var frm_sel_report = $("#sel_cats").find('option:selected').val();
	var formData = "catUID="+frm_sel_report;

	empty_divs();

	$.ajax({
    	 url : "ajax/ajax_load_reports.php",
   	 type: "POST",
   	 data: formData,
   	 success: function(data, textStatus, jqXHR)
	 {
		$("#div_list").empty();
		$("#div_list").append(data);
		load_groupby();  
		load_options();
		load_params(selected,token); 
	 },
	 error: function (jqXHR, textStatus, errorThrown)
	 {
 		alert(textStatus);
	 }
	 });
}

function load_options()
{
	var frm_sel_report = $("#sel_report").find('option:selected').val();
	var formData = "repUID="+frm_sel_report;
	 
	$("#div_report").empty();
	
	$.ajax({
    	 url : "ajax/ajax_load_options.php",
   	 type: "POST",
   	 data: formData,
   	 success: function(data, textStatus, jqXHR)
	 {
		$("#div_options").empty();
		$("#div_options").append(data);
	 },
	 error: function (jqXHR, textStatus, errorThrown)
	 {
 		alert(textStatus);
	 }
	 });
}

function get_params(selectedValues)
{
	var arrSelect = new Array();
	arrSelect = selectedValues.split("&");

	for(var i= 0; i < arrSelect.length; i++)
	{
		var row = arrSelect[i];
		var arrcontent = new Array();
		arrcontent = row.split("::");
		var key = arrcontent[0];
		var value = arrcontent[1];
		set_params(key,value);
	}
}

function set_params(obj,value)
{
	if ($('#'+obj).length > 0)
	{
		if ($('#'+obj).is("select")) { $('#'+obj+' option[value="'+value+'"]').prop('selected', true);  }
		if ($('#'+obj).is(":text")) { $('#'+obj).val(value);}
	}
}

function valid_field(type,field)
{
	var add = "";
	
	if ($("#"+field).length > 0)
	{
		if (type == "select")
		{
			if (($("#"+field+"_req").val() == 1) && ($("#"+field).find('option:selected').val() == "-1"))
			{
				var field_lb = $("#lb_"+field).text().replace(":","");
				alert(field_lb+"obligatoire");
				return "error";
			}
			else
			{
			
				if ($('#'+field).css('display') == 'none')
				{
					var values = $('#'+field).multipleSelect('getSelects');  // multiple
					add = "&"+field+"="+values;
				}
				else
					add = "&"+field+"="+$("#"+field).find('option:selected').val();
				
				return add; 
			}
		}

		if (type == "text")
		{
			if (($("#"+field+"_req").val() == 1) && ($('#'+field).val() == ""))
			{
				var field_lb = $("#lb_"+field).text().replace(":","");
				alert(field_lb+"obligatoire");
				return "error";
			}
			else
			{
				add = "&"+field+"="+$('#'+field).val();
				return add; 
			}
		}
	}
	else
		return "";
}

function create_report()
{
	var frm_sel_report = $("#sel_report").find('option:selected').val();

	if (frm_sel_report == -1)
	  alert("Veuillez sélectionner un rapport");
	else
	{
		var error = false;

		formData = "rptUID="+frm_sel_report;
		
		//Liste des paramètres actuellement supportés.
		var result  = valid_field("select","period",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","statu",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
			
		var result  = valid_field("select","noEmploye",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("text","sdate",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("text","edate",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupePaie",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","groupe1",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupe2",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupe3",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupe4",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupe5",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupe6",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","porte",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","groupby",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","horaireM",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","unit",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
			
		var result  = valid_field("select","texceptions",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","codeAbs",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","barcode",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
			
		var result  = valid_field("select","regroup",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","job",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}

		var result  = valid_field("select","oper",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		var result  = valid_field("select","jobqty",formData);
		if (result == "error") { error = true; } else { if (result != "") { formData += result; }}
		
		if (!error)
		{
			$("#wait_generate").show();
			$("#div_report").empty();
			$.ajax({
			    	 url : "ajax/ajax_reports.php",
			   	 type: "POST",
			   	 data: formData,
			   	 success: function(data, textStatus, jqXHR)
				 {
					$("#wait_generate").hide();
					
					if (data == -1)
					 alert("Session expirée, veuillez recharger la page."); //période d'inactivité ou non valide.
					else
					   $("#div_report").append(data);
					
				 },
				 error: function (jqXHR, textStatus, errorThrown)
				 {
			 		alert(textStatus);
				 }
			 });
		}
	}
}
