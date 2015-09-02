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
||										popup.php																				||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/

// Do Some Logic stuff
$table 		= req::_('table');
$rowid 		= req::_('rowid','new');
$editing	= true;

// Initialize table by getting params and populating with data.
$oEditor = new editor($table,$rowid);
$oEditor->_getData();
$editor_html = $oEditor->_html();

// Get View
$oView = new view($view);


// Display View Script and View HTML
echo document::_addScript( $oView->_script() );
echo document::_addScript(  "ready.js" );
include_once( $oView->_html() );

echo '<div class="clear"></div>';
//pre($data);

?>