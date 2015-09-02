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
||										su.php																					||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/

// Do Some Logic stuff
$tables = array("aimusers","aimgroups","aimparts","aimpart_templates","aimpart_categories","aimfleet","aimfleet_templates","aimlocations","aimhistory","aimnotes");
$tabs_html = "";
$html = "";
//$options = array();


foreach($tables as $key => $table) {
	$active = ($key == 0) ? "active" : "";
	$tabs_html .= "<li id=\"setup_{$table}\" class=\"{$active} ui-corner-top\">{$table}</li>\n";
	
	
	//$options[$table] = new tableoptions($table);
	
	
	$data = new tableparams($table,$tables);
	$html .= <<<HTML
<div id="setup-tables" class="{$active} part"> 
{$data->_getHtml()}
</div>
HTML;
}


// Get View
$oView = new view($view);

// Display View Script and View HTML
echo document::_addScript( $oView->_script() );
echo document::_addScript(  "ready.js" );
include_once( $oView->_html() );

echo '<div class="clear"></div>';
//pre($data);

?>