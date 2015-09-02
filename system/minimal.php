<?php

/*********************************************************************************************************************************
||																																||
||										AIMSys - Aircraft Inventory & Maintenance System										||
||										Jeremy Bloomstrom																		||
||										Ingenious Design																		||
||										jeremy@in.genio.us																		||
||										November 27, 2012																		||
||																																||
|________________________________________________________________________________________________________________________________|
||																																||
||																																||
||										minimal.php																			||
||																																||
||																																||
*********************************************************************************************************************************/

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** DEBUGGING ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
 
require_once('FirePHPCore/fb.php');
 
$firephp->setEnabled(true);  // or FB::
 
FB::send('firephp enabled');

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** GLOBAL VARS ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
global $session_length;
$session_length = 60 * 10 - 2; // 30 second sessions for debugging; 10 mins for production

include_once "./models/db.php";

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** CLASSES ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/

include_once "classes/class_session.php";
include_once "classes/class_req.php";
include_once "classes/class_document.php";
include_once "classes/class_date.php";


/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** OTHERS ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
include_once "php/functions.php";