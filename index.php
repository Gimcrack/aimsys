<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  index.php - router
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Main router of the application
 *  
 *  Ingenious Design - http://in.genio.us 
 *  Jeremy Bloomstrom | jeremy@in.genio.us
 *  
 *  Created: 		11/27/2012
 *  Last Updated: 	8/21/2014
 *  
 *  Changelog:
 *   8/21/2014 - Added comment header
 */
session_start();


/**  **  **  **  **  **  **  **  **  **
 *   INCLUDES
 */
include_once "system/includes.php";


/**  **  **  **  **  **  **  **  **  **
 *   SET PARAMETERS AND DETERMINE ACTION
 */
$action = req::_("action");
$controller = req::_("controller","main");

if ($action == "logout") { 
	include_once "controllers/logout.php"; 
}


/**  **  **  **  **  **  **  **  **  **
 *   DETERMINE IF SESSION IS VALID
 */
$valid_session = session::_check_session();



/**  **  **  **  **  **  **  **  **  **
 *   LOAD CONTROLLER
 */
if ($valid_session) {
	include_once "controllers/$controller.php";
}
else {
	include_once "controllers/login.php";
}

?>