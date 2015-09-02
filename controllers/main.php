<?php
/**  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **  **
 *  
 *  main.php - controller
 *  
 *  AIMSys - Aircraft Inventory and Maintenance System
 *  
 *  Main controller, displays the main view of the app
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
 *   DISPLAY THE VIEW
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="charset=utf-8; text/html" />
<link rel="icon" href="http://aimsys.home/favicon.ico" />
<title>AIMSys - Aircraft Inventory & Maintenance System</title>

<?php include("system/styles.php"); ?>

<?php include("system/scripts.php"); ?>

</head>

<body>
<?php 

include("views/$option.php");
?>
<div id="su-login">
	<a href="index.php?controller=tab&option=su" id="su-login">.::.</a>
</div>
</body>
</html>