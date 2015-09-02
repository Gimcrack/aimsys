<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  su.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Superuser view, where table parameters are set up
 *  
 *  Ingenious Design - http://in.genio.us 
 *  Jeremy Bloomstrom | jeremy@in.genio.us
 *  
 *  Created: 	11/27/2012
 *  Last Updated: 	8/21/2014
 *  
 *  Changelog:
 *   8/21/2014 - Added comment header
 */


/**  **  **  **  **  **  **  **  **  **
 *   SETUP PARAMETERS
 */
global $view;
global $option;
$option = req::_('option','login');
$view = req::_('view',$option);


/**  **  **  **  **  **  **  **  **  **
 *   SHOW THE SUPERUSER VIEW
 */
switch($option) {
	
	default:
		include("views/su.php"); 
	break;
}
?>



