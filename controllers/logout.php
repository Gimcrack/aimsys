<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  logout.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Processes logout requests
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
 *   PROCESS THE REQUEST
 */
$_SESSION['expires'] = time() - 100;
$_SESSION['expired'] = true;
header("location: index.php");
?>
