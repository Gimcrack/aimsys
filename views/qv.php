<?php 
/*********************************************************************************************************************************
||																																||
||										AIMSys - Aircraft Inventory & Maintenance System										||
||										Jeremy Bloomstrom																		||
||										Ingenious Design																		||
||										jeremy@in.genio.us																		||
||										March 27, 2013																			||
||																																||
|________________________________________________________________________________________________________________________________|
||																																||
||																																||
||										qv.php																					||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/
$tab_exists = req::_('tab_exists',false); // if the tab is new or if loading data into an existing tab
$content_header = key($data); 	// the first key of the data array contains the primary table name
$subtabs_html = '';				// initialize html for subtabs menu
$content_html = '';				// initialize html for content

if ($tab_exists) {
	$subtab = req::_('subtab',"manage-{$content_header}");
	$v	 = str_replace("manage-","",$subtab);
	$json_data = array();
	
	$table 			= TABLEPREFIX . $v;
	
	//Prepare table object
	$oTable = new table($table,$data[$v]->rows);
	echo trim($oTable->_getJSON() ); 
	
} else {
	// Iterate through each object in the data array
	foreach($data as $view => $datum) {
		$table 			= TABLEPREFIX . $view;
		
		//Prepare table object
		$oTable = new table($table,$datum->rows);
		$body_html = $oTable->_quickview();  
		
		//Prepare form object
		$form_html 		= $formdata[$view]->_getFormHTML();
		$footer_html 	= $formdata[$view]->_getFooterHTML();
		
		// Prepare subtabs
		$subtab_name = "Manage ".ucwords(str_replace("_"," ",$view));
		$subtab_class = (req::_('subtab',"manage-{$content_header}") == "manage-{$view}") ? 'active' : '';
		$subtabs_html .= <<<HTML
    <li id="manage-{$view}" class="{$subtab_class} ui-corner-top">{$subtab_name}</li>
HTML;
	
		// Prepare content
		$content_html .= <<<HTML
	<div id="manage-{$view}" class="{$subtab_class} part">
		<div class="data row controls">{$form_html}</div>
		{$body_html}
		<div class="data row footer">{$footer_html}</div>
	</div>
HTML;
	
	}
	
	// Get View
	$oView = new view('qv');
	
	// Display View Script and View HTML
	echo document::_addScript( $oView->_script() );
	echo document::_addScript(  "ready.js" );
	include_once( $oView->_html() );

}

?>