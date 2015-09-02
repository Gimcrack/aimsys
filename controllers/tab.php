<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  tab.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Controller that generates views for tabs
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
 *   SETUP PARAMETERS
 */
global $view;
global $option;
$option = req::_('option','default');
$view = req::_('view',$option);


/**  **  **  **  **  **  **  **  **  **
 *   SHOW THE VIEW
 */
include("views/$option.php");
?>



