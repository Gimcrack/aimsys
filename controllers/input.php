<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  input.php - controllers
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Called by update_select_options function in class.tab.js
 *  
 *  Ingenious Design - http://in.genio.us 
 *  Jeremy Bloomstrom | jeremy@in.genio.us
 *  
 *  Created: 		3/27/2013
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
 *   PROCESS THE REQUEST
 */
switch ($option) {

	case 'update_options' :
		
		$table = req::_('table');
		$col = req::_('col');
		$val = req::_('value');
		
		$oParams = new colparams($table,$col);
		$params = $oParams->_getvalues();
		$return = editor::_getOptions($params,$val);
		echo $return;
	break;	
	
}

?>



