<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  checkout.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Checks records out of the database
 * 
 *  Return Codes
 * -1  = Record checked out already
 *  0  = Error checked out record
 *  1  = Record checked out successfully
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
$return = 0;
$table = req::_('table_name');
$prikey = dbfn::_getprimary($table);
$id = req::_($prikey);
$userid = $_SESSION['use_id']; #id of currently logged in user
$oDate = new Date(time());
$datetime = $oDate->mysql_datetime;

/**  **  **  **  **  **  **  **  **  **
 *   SEE IF RECORD IS ALREADY CHECKED OUT
 */
$query = "SELECT `checked_out`,`checked_out_id`,`checked_out_time` FROM `{$table}` WHERE `{$prikey}` = {$id} LIMIT 1";
$oDB = new db($query);
$checkedout 		= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out'] : 0;
$checkedout_id		= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out_id'] : 0;
$checkedout_time 	= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out_time'] : 0;


/**  **  **  **  **  **  **  **  **  **
 *   RECORD IS CHECKED OUT SO FIND OUT
 *   WHO HAS IT AND ALERT THE USER
 */
$checkedout_username = ($checkedout_id > 0) ? aimsys::_getUsername($checkedout_id) : false;

if ($checkedout == 1 ) { // record is already checked out by another user
	$return = <<<HTML
checkout_message('That record is currently being edited by {$checkedout_username}.','error');
$('.main-content.active .part.active #lb-edit').removeClass('ui-disabled').attr('disabled',false);
HTML;
}


/**  **  **  **  **  **  **  **  **  **
 *   THE RECORD IS AVAILABLE TO TRY TO 
 *   CHECK IT OUT
 */
else {					// checkout record
	$update = "UPDATE `{$table}` SET `checked_out` = 1,`checked_out_id` = {$userid}, `checked_out_time` = '{$datetime}'  WHERE `{$prikey}` = {$id} LIMIT 1";
	$oDB = new db($update);
	if ($oDB->ret['msg_class'] == 'success') {
		$return = <<<HTML
checkout_message('Record ready to edit.','success');
$('.main-content.active .part.active #lb-edit').hide();
$('.main-content.active .part.active #lb-refresh').hide();
$('.main-content.active .part.active #lb-close').hide();
$('.main-content.active .part.active #lb-save').show();
$('.main-content.active .part.active #lb-reset').show();
$('.main-content.active .part.active #lb-cancel').show();
$(":input[type!='password']",frm).prop('disabled',false);
$('.main-content.active #content-header').html('Edit');
HTML;
	}
	

/**  **  **  **  **  **  **  **  **  **
 *   THERE WAS AN ERROR CHECKING IT OUT
 */	
	else {
		$return = <<<HTML
checkout_message('There was an error processing your request.','error');
$('.main-content.active .part.active #lb-edit').removeClass('ui-disabled').attr('disabled',false);
HTML;
	}
}


/**  **  **  **  **  **  **  **  **  **
 *   RETURN THE RESULTS
 */		
header('content-type: application/x-javascript');
echo "frm = $('.main-content.active .part.active #frm_editor')\n"; 	
echo $return;

?>



