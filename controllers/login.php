<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  login.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Displays login view and processes login requests
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
$view = req::_('view','login');


/**  **  **  **  **  **  **  **  **  **
 *   PROCESS LOGIN REQUEST
 */
if ( req::_('frm_name') == 'frm_login') { 
	$username = req::_('use_username');
	$password = req::_('use_password');
	$login_attempt = session::_login($username,$password);
	if ($login_attempt) { // If login is successful, redirect back to router. 
		$message = "Login Successful!";
		header("Location: index.php");
	}

}


/**  **  **  **  **  **  **  **  **  **
 *   SHOW LOGIN FORM VIEW
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="icon" href="http://aimsys.home/favicon.ico" />
<meta http-equiv="Content-Type" content="charset=utf-8; text/html" />
<title>AIMSys - Aircraft Inventory & Maintenance System</title>

<?php include("system/styles.php"); ?>

<?php include("system/scripts.php"); ?>

</head>

<body>
<?php include("views/$view.php"); ?>
</body>
</html>


