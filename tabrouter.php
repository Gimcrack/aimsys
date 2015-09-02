<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  tabrouter.php - router
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Loads the bare essentials for loading tabs
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
include_once "system/minimal.php";


/**  **  **  **  **  **  **  **  **  **
 *   VALIDATE SESSION
 */
$valid_session = session::_check_session();


/**  **  **  **  **  **  **  **  **  **
 *   LOAD CONTROLLER
 */
if ($valid_session) {
	include_once "controllers/main.php";
}
else {
	include_once "controllers/login.php";
}

?>