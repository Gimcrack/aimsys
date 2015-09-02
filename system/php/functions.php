<?php
global $dbc;

function pre($s) {
	if (is_array($s) || is_object($s) ) {
		echo "<pre>";
		print_r($s);
		echo "</pre>";
	}
	else {
		echo "<pre>".$s."</pre>";
	}
}

function preh($s) {
	echo pre(htmlentities($s));
}

function consoleLog($s) {
	if ( is_array($s) || is_object($s) ) {
		$return = "console.log(\"{\\n\"";
		foreach($s as $key => $value) {
			$return .= " + \"\t[" . addslashes($key) . "] => " . addslashes($s->$key) . " \\n \"";
		}
		$return .= "+\"}\" );";
	}
	else {
		$return = "console.log(\"" . addslashes($s) . "\");";
	}
	return $return;
}



// RESET USER PASSWORD
if (req::_chk('reset') > 0) {
	$id = req::_('reset');
	$_REQUEST['use_id'] = $id;
	$query = "UPDATE `aimusers` SET `use_password` = md5(concat(`use_firstname`,`use_lastname`)) WHERE `use_id` = $id LIMIT 1";
	$oResult = new db($query);
			
	foreach($oResult->ret as $key => $value ) {
		$$key = $value;
	}		
			
}

?>