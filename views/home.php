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
||										home.php																				||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/

// Do Some Logic stuff

// Get View
$oView = new view($view);

// Display View Script and View HTML
echo document::_addScript( $oView->_script() );
include_once( $oView->_html() );



?>