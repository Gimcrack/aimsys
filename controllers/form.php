<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  form.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Processes form submissions
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
$frm_name = req::_('frm_name');


/**  **  **  **  **  **  **  **  **  **
 *   PROCESS FORM
 */
switch ($frm_name) {
	
	case 'frm_tableparam' :
		$oParams = new colparams(req::_('_table'),req::_('_col'));
		$oParams->_set($_POST);
		$output = $oParams->_store();
	break;	
	
	case 'frm_editor' :
		$table   = req::_('table_name');			// table name
		$prikey  = dbfn::_getprimary($table);		// get primary key name of table
		$id		 = req::_($prikey); 				// get id of record
		
		$oEditor = new editor($table,$id);
		$oEditor->_getData();
		$oEditor->_setData($_POST);
		$output = $oEditor->_storeData();
	break;
	
	case 'frm_storetabs' :
		$tabs = req::_('Tabs','');
		$oResult = aimsys::_storeTabs($tabs);
		$output = $oResult->ret;
	break;
	
	case 'frm_gettabs' :
		$t = aimsys::_getTabs();
	break;
	
}


/**  **  **  **  **  **  **  **  **  **
 *   OUTPUT THE RESULTS
 */
switch ($frm_name) {
	case 'frm_editor' :
	case 'frm_tableparam' :
		if ($option <> 'popup') {
			header('content-type: application/x-javascript'); 
			echo "/*";
			print_r($output);
			echo "*/\n";
			echo aimsys::_message($output['message'],$output['msg_class'],false);
			echo (!empty($output['js']) ) ? "\n".$output['js'] : '';
		}
		else {
			echo str_replace("'","`",json_encode($output));
		}
	break;
	
	case 'frm_storetabs' :
		echo "/*";
		print_r($output);
		echo "*/\n";
	break;
	
	case 'frm_gettabs' :
		header('content-type: application/x-javascript');
		echo <<<HTML
closeAllTabs();
var temp = {$t};
var activetab;
$.each(temp, function(key,tab) {
	Tabs[key] = new Tab(tab.type);
    Tabs[key].newTab(tab.id,tab.name);
    if (tab.active) activetab = key;
});
Tabs[activetab].showTab();
HTML;
	
	break;
	

}

?>



