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
||										includes.php																			||
||																																||
||																																||
*********************************************************************************************************************************/

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** DEFINES ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
define('VIEW_PATH','./views/');
define('TABLEPREFIX','aim');
define('ROWS_PER_PAGE',10);

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** DEBUGGING ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
 
require_once('FirePHPCore/fb.php');
ob_start();
$firephp->setEnabled(true);  // or FB::


$firephp->registerErrorHandler(
            $throwErrorExceptions=false);
$firephp->registerExceptionHandler();
$firephp->registerAssertionHandler(
            $convertAssertionErrorsToExceptions=true,
            $throwAssertionExceptions=false);

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** GLOBAL VARS ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
global $session_length;
$session_length = 60 * 10; // 30 second sessions for debugging; 10 mins for production

include_once "./models/db.php";

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** CLASSES ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/

include_once "classes/class_db.php";
include_once "classes/class_date.php";
include_once "classes/class_colparams.php";
include_once "classes/class_req.php";
include_once "classes/class_aimsys.php";
include_once "classes/class_session.php";
include_once "classes/class_document.php";
include_once "classes/class_view.php";
include_once "classes/class_table.php";
include_once "classes/class_formfilterpage.php";
include_once "classes/class_editor.php";
include_once "classes/class_notes.php";

/***** ***** ***** ***** ***** ***** ***** ***** ***** ***** OTHERS ***** ***** ***** ***** ***** ***** ***** ***** ***** *****/
include_once "php/functions.php";