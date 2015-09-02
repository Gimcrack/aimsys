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
||										default.php																				||
||										VIEW																					||
||										Default view of main controller															||
*********************************************************************************************************************************/

// Do Some Logic stuff

//User Info
$user = $_SESSION;	

// Get Session Expiry
$ts = $user['expires'];
$oExpires = new Date($ts);
$year 	= $oExpires->_format("Y");
$month 	= $oExpires->_format("n")-1;
$day	= $oExpires->_format("j");
$hour	= 1*$oExpires->_format("H");
$minute	= 1*$oExpires->_format("i");
$second	= 1*$oExpires->_format("s");

$js_date = "$year,$month,$day,$hour,$minute,$second";

// Get View
$oView = new view($view);

// Display View Script and View HTML
echo document::_addScript( $oView->_script() );
include_once( $oView->_html() );


?>