<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  qv.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  QuickView controller, builds the view for the quickview tabs
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
global $option;		
$option = req::_('option','default');
global $view;		
$view = req::_('view',$option); 
$views = explode('|',req::_('view',$option));
$data = array();	// array containing select query db object
$formdata = array(); // array containing filter/pagination form data objects


/**  **  **  **  **  **  **  **  **  **
 *   INITIALIZE VIEWS FOR EACH TAB
 */
foreach($views as $v) {
	
	// Set some params
	$table 			= TABLEPREFIX . $v;
	$joins			= dbfn::_getJoins($table);
	
	// Prepare the parameters for the query.
	$oQ = new formfilterpage($table,$joins); 	#Get filter and pagination data
	$select = $oQ->_getQuery();					#Get query
	$data[$v] = new db($select);				#Perform query and load the results into the data array
	$oQ->_update($data[$v]);					#Update the filter/pagination object
	$formdata[$v] = $oQ;						#Load filter/pagination object into formdata array
}


/**  **  **  **  **  **  **  **  **  **
 *   LOAD UP THE QUICKVIEW VIEW
 */
include("views/qv.php");
?>



