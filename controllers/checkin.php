<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  checkin.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Checks records back into the database after being checked out
 * 
 *  Return Codes
 * -1  = Record checked in already
 *  0  = Error checking in record
 *  1  = Record checked in successfully
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


/**  **  **  **  **  **  **  **  **  **
 *   SEE IF RECORD IS ALREADY CHECKED IN
 */
$query = "SELECT `checked_out`,`checked_out_id`,`checked_out_time` FROM `{$table}` WHERE `{$prikey}` = {$id} LIMIT 1";
$oDB = new db($query);
$checkedout 		= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out'] : 0;
$checkedout_id		= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out_id'] : 0;
$checkedout_time 	= (count($oDB->rows) ) ? $oDB->rows[0]['checked_out_time'] : 0;

if ($checkedout === 0) { // record is already checked in
	$return = <<<HTML
checkout_message('That record has been checked in already.','information');
$('.main-content.active .part.active #lb-edit').show();
$('.main-content.active .part.active #lb-save').hide();
$('.main-content.active .part.active #lb-reset').hide();
$("input:not(':hidden'),select",frm).prop('disabled',true);
$('.main-content.active #content-header').html('View');
$('.main-content.active .part.active #lb-save').removeClass('ui-disabled').attr('disabled',false);
HTML;
}

/**  **  **  **  **  **  **  **  **  **
 *   RECORD IS NOT CHECKED IN YET
 */
else {					// checkin record
	$update = "UPDATE `{$table}` SET `checked_out` = 0,`checked_out_id` = 0, `checked_out_time` = '' WHERE `{$prikey}` = {$id} LIMIT 1";
	$oDB = new db($update);
	if ($oDB->ret['msg_class'] == 'success') {
		$return = <<<HTML
checkout_message('Record closed.','alert');
$('.main-content.active .part.active #lb-edit').show();
$('.main-content.active .part.active #lb-refresh').show();
$('.main-content.active .part.active #lb-close').show();
$('.main-content.active .part.active #lb-save').hide();
$('.main-content.active .part.active #lb-reset').hide();
$('.main-content.active .part.active #lb-cancel').hide();
$("input:not(':hidden'),select",frm).attr('disabled',true);
$('.main-content.active #content-header').html('View');
$('.main-content.active .part.active #lb-save').removeClass('ui-disabled').attr('disabled',false);
HTML;
	}


/**  **  **  **  **  **  **  **  **  **
 *   THERE WAS AN ERROR CHECKING IT IN
 */
	else {
		$return = <<<HTM
checkout_message('There was an error processing your request.','error');
$('.main-content.active .part.active #lb-save').removeClass('ui-disabled').attr('disabled',false);
HTM;
	}
}


/**  **  **  **  **  **  **  **  **  **
 *   RETURN THE RESULTS
 */		
header('content-type: application/x-javascript');
echo "frm = $('.main-content.active .part.active #frm_editor')\n";
echo $return;

?>



