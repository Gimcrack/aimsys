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
||										editor.php																				||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/

// Do Some Logic stuff
$table 		= req::_('table');
$rowid 		= req::_('rowid','new');
$editing	= req::_('editing',false);

$editing = ($editing == "false") ? false : $editing;

// Determine if new record or update
$content_header = ($rowid <> 'new') ? 'View' : 'New';

// Initialize table by getting params and populating with data.
$oEditor = new editor($table,$rowid);
$oEditor->_getData();
$editor_html = $oEditor->_refresh_edit_html();

// Get View
$oView = new view($view);

// Display View Script and View HTML
include_once( $oView->_html() );
echo document::_addScript( $oView->_script() );
?>