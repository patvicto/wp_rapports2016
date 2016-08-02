var doc = new jsPDF("portrait", 'pt', 'letter');
var docWidth = 612;
var docHeight = 792;

//init
var maxlen = 0;
var verticalOffset = 0;
var nextOffset = 0;
var obj_mH = 0; //margins
var obj_mV = 0; //margins
var page_number = 1;
var header_js = "";
var arr_header = [];  //lignes du header
var fontSizeStd = 10; //Font standard
var fontSizeTitle = 14; //font Title
var hLineStd = 25;

//calc
maxlen = docWidth -(obj_mH * 2);  //max possible en longueur
var widthAdd = obj_mH;
var remainV = 0;
var maxvaline = 0;

function createReport(mH,mV,pW,pH) 
{
	set_type_display(mH,mV,pW,pH);
}

function addPage()
{
	doc.addPage();
	page_number = page_number +1;
	verticalOffset = 0;
	apply_margins();
	remainV = docHeight;
	aff_line_header();
}

function apply_margins()
{
	verticalOffset = obj_mV + fontSizeStd;
	nextOffset = verticalOffset;
}

function set_counter_page()
{
	var vOffset = docHeight - obj_mV  + (hLineStd);
	doc.line(obj_mH, vOffset, docWidth-obj_mH ,vOffset);  //splitter
 
	lines = "Page : " +  page_number;
	var hOffset = docWidth - (obj_mH + (lines.length+0.5) + hLineStd + (fontSizeStd/2) );
	vOffset = vOffset + fontSizeStd;
	doc.text(hOffset, vOffset, lines);

	var d = new Date();

	var month = d.getMonth();
	var days = d.getDate();

	//fix affichage
	if (month <= 9) { month = "0"+month; }
	if (days <= 9) { days = "0"+days; }
		
	lines = d.getFullYear()+"-"+month+"-"+days;

	doc.text(obj_mH, vOffset, lines);
}


function set_header(i,content)
{
	arr_header[i]  = content;	
}

function set_type_display(mH,mV,pH,pV)		
{
	docWidth = pH;
	docHeight = pV;
	
	if (docWidth > docHeight) { doc = new jsPDF("landscape", 'pt', 'letter'); } else { doc = new jsPDF("portrait", 'pt', 'letter'); } 
		
	doc.setFont("courrier", "");
	doc.setFontSize(fontSizeStd);

	obj_mH = mH;
	obj_mV = mV;

	remainV = docHeight-obj_mV;  //Ce qui reste d'espace après la marge du bas.<
	
}

//Array columns len,hauteur,label column
/*
function table_header(xml_header,titleText)
{
	widthAdd = obj_mH; //margin+

	var col_label = "";
	var col_width = 0;
	var col_height = 0;
	
	var objxml = atob(xml_header);
	
	header_js = xml_header;  //save for future uses.

	var xml_jquery_object = jQuery.parseXML(objxml);
	$(xml_jquery_object).find("col").each(function () 
	{
		 col_label = $(this).text();
		 col_width = $(this).attr("width");
		 col_height = $(this).attr("height");
		
		 doc.cell(widthAdd,(verticalOffset+fontSizeStd),col_width,col_height,col_label);
		 widthAdd = widthAdd + parseInt(col_width);	
	});

	//fin de header
	verticalOffset += hLineStd;
}
*/

function table_header_json(js_header)
{
	widthAdd = obj_mH; //margin+

	var col_label = "";
	var col_width = 0;
	var col_height = 0;
	
	header_js = js_header;  //save for future uses.

	var json_jquery_object = jQuery.parseJSON(js_header);

	$.each(json_jquery_object, function(i, object) 
	{
	     col_label  = "";
	     col_width = 0;
	     col_height = 0;
	  
	    $.each(object, function(tag, value) 
	    {
		if (tag == "label") { col_label = value; }
		if (tag == "width") { col_width = value; }
		if (tag == "height") { col_height = value; }	
	    });
	    doc.cell(widthAdd,(verticalOffset+fontSizeStd),col_width,col_height,col_label);
	    widthAdd = widthAdd + parseInt(col_width);	
	});

	//fin de header
	verticalOffset += hLineStd;

	aff_logo();
}

//Array columns len,hauteur,label column
/*
function table_line(xml_data,maxlines)
{
	widthAdd = obj_mH; //margin+

	var objxml = atob(xml_data);

	
	var MaxHeightLine = 0;
	var hauteur = 0;

	//Scan toutes les values de la ligne en cours pour retenir la plus haute valeur en fait de hauteur de points.
	var xml_jquery_object = jQuery.parseXML(objxml);

	$(xml_jquery_object).find("data").each(function () 
	{
		 col_value = $(this).attr("value");
		 col_width = $(this).attr("width");
		 
		 lines = doc.splitTextToSize(col_value,col_width);
		 
		 hauteur = (lines.length+0.5) * fontSizeStd;
		 if (hauteur >  maxvaline)
		 	maxvaline = hauteur;
	});
	
	MaxHeightLine =  maxvaline+2; //border
	maxvaline = 0;

	$(xml_jquery_object).find("data").each(function () 
	{
		 col_value = $(this).attr("value");
		 col_width = $(this).attr("width");
		 col_height = MaxHeightLine;
		 
		 lines = doc.splitTextToSize(col_value,col_width);

		 doc.cell(widthAdd,(verticalOffset+fontSizeStd),col_width,col_height,lines);
		 widthAdd = widthAdd + parseInt(col_width);
	
	});
	
	verticalOffset += MaxHeightLine;

      	nextOffset = (verticalOffset + ((lines.length+0.5) * fontSizeStd)) + fontSizeStd;
	nextOffset += (hLineStd*2);
	
        if(nextOffset > remainV)
        {
		addPage();
		widthAdd = obj_mH;
		table_header(header_js);
		set_counter_page();		
        }
}
*/

function table_line_json(json_data,maxlines)
{
	widthAdd = obj_mH; //margin+

	var json_data = atob(json_data);

	var MaxHeightLine = 0;
	var hauteur = 0;

	var json_jquery_object = jQuery.parseJSON(json_data);
	$.each(json_jquery_object, function(i, object) 
	{
	    $.each(object, function(tag, value) 
	    {
		
		if (tag == "width") { col_width = value; }
		if (tag == "value") { col_value = value; }	
	    });
	
	     lines = doc.splitTextToSize(col_value,col_width);
		 
		 hauteur = (lines.length+0.5) * fontSizeStd;
		 if (hauteur >  maxvaline)
		 	maxvaline = hauteur;

	});

	MaxHeightLine =  maxvaline+2; //border
	maxvaline = 0;

	$.each(json_jquery_object, function(i, object) 
	{
	    col_width =0;
	    col_value = "";
	 
	    $.each(object, function(tag, value) 
	    {
		if (tag == "width") { col_width = value; }
		if (tag == "value") { col_value = value; }	
	    });
	
	     col_height = MaxHeightLine;

             lines = doc.splitTextToSize(col_value,col_width);

	     doc.cell(widthAdd,(verticalOffset+fontSizeStd),col_width,col_height,lines);    
	     widthAdd = widthAdd + parseInt(col_width);
  

	});

	verticalOffset += MaxHeightLine;

      	nextOffset = (verticalOffset + ((lines.length+0.5) * fontSizeStd)) + fontSizeStd;
	nextOffset += (hLineStd*2);
	
        if(nextOffset > remainV)
        {
		addPage();
		widthAdd = obj_mH;
		table_header_json(header_js,logo_64);
		set_counter_page();		
        }
}

function aff_line_header()
{
	//lire array header.
	var fontCalc = 0;

	
	for (index = 0; index < arr_header.length; ++index) 
	{
	      if (index ==0)
	      {
	         lines = doc.splitTextToSize(arr_header[index]);
		 if (page_number == 1)
		 {
	       		  doc.setFontSize(fontSizeTitle);
			  fontCalc  = fontSizeTitle;
		 }
		 else	
		 {
			  doc.setFontSize(fontSizeStd);
			  fontCalc  = fontSizeStd; 
	       	 }
	      }
	      else
	      {
	         doc.setFontSize(fontSizeStd);	
	         lines = doc.splitTextToSize(arr_header[index]);
		 fontCalc  = fontSizeStd;
	      }

	     doc.text(obj_mH, verticalOffset,  lines);

	     verticalOffset = verticalOffset + ((lines.length+0.5) * fontCalc);
	     remainV = remainV -  ((lines.length+0.5) * fontCalc) + fontCalc;  
	     doc.setFontSize(fontSizeStd);	
		
	}
	
	doc.line(obj_mH, verticalOffset, docWidth-obj_mH , verticalOffset);  //splitter 
}

function aff_logo()
{
	var imgData = "data:image/jpeg;base64,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";

	//Aff Logo
	doc.addImage(imgData, 'JPEG', (docWidth-obj_mH-50), 30, 50, 40);
}
