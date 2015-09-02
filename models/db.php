<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  db.php - model
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Establishes MySQL db connection
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
 
 
/**  **  **  **  **  **  **  **  **  **
 *   SETUP CONNECTION PARAMETERS
 */
$mode = (strpos($_SERVER['SERVER_NAME'],'in.genio.us') !== false) ? "production" : "testing";
switch($mode) {
	
	case 'production' :
		// PRODUCTION SERVER VARIABLES
		define("HN","mysql.in.genio.us");
		define("UN","ingenious_db_un");
		define("PW","QoA9ImefL0hu7Z");
		define("DB","aimsysdb");
	break;
	
	case 'testing' :
		// TESTING SERVER VARIABLES
		define("HN","10.0.100.250");
		define("UN","aimsys_db_un");
		define("PW","P@ssw0rd");
		define("DB","aimsysdb");
	break;
}


/**  **  **  **  **  **  **  **  **  **
 *   ESTABLISH DB CONNECTION
 */
global $dbc;
$dbc = new MySQLi(HN,UN,PW);
$dbc->select_db(DB);