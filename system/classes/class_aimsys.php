<?php

class aimsys {
	
	public static function _check_direct_access() {
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] <> 'XMLHttpRequest') { // deny direct access
			header("Location: index.php?m=login");
		}
		else {
			return true;
		}
	}
	
	public static function _check_indirect_access() {
		if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') { // deny indirect access
			header("Location: index.php");
		}
		else {
			return true;
		}
	}
	
	public static function _history($statement,$changes,$table='',$pkey='') {
		global $dbc;
		$action = substr($statement,0, strpos($statement," ") );
		
		$s[] = "INSERT INTO `aimhistory` (`his_user_id`,`his_action`,`his_table`,`his_pkey`,`his_query`,`his_previous_values`) VALUES (%s,%s,%s,%s,%s,%s)";
		$s[] = $_SESSION['use_id'];
		$s[] = $action;
		$s[] = $table;
		$s[] = $pkey;
		$s[] = $statement;
		$s[] = $changes;
		$query = call_user_func_array('dbfn::_sanitize',$s);
		$oDB = new db($query);
	}
	
	public static function _storeTabs($tabs) {
		global $dbc;
		
		$s[] = "UPDATE `aimusers` SET `sys_tabs` = %s WHERE `use_id` = %s LIMIT 1";
		$s[] = $tabs;
		$s[] = $_SESSION['use_id'];
		$query = call_user_func_array('dbfn::_sanitize',$s);
		$oDB = new db($query);
		return $oDB;
	}

	public static function _getTabs() {
		global $dbc;
		
		$s[] = "SELECT `sys_tabs` FROM `aimusers` WHERE `use_id` = %s LIMIT 1";
		$s[] = $_SESSION['use_id'];
		$query = call_user_func_array('dbfn::_sanitize',$s);
		$oDB = new db($query);
		return $oDB->_getResult();
	}
	
	public static function _message($text,$type,$script_tags = true, $sticky = 'false') {
		
		if ($script_tags) {
		
			return <<<HTML
<script>noty_message("{$text}","{$type}",$sticky);</script>
HTML;
		}
		else {
			return <<<HTML
noty_message("{$text}","{$type}",$sticky);         
HTML;
		}
	}
	
	public static function _array_sel_menu($ar,$field,$sel,$first_value = '',$first_label = '',$multiple = false) {
		$selected = (strpos($sel,",") !== false) ? explode(',',$sel) : array($sel);
		$return = ($multiple) ? 
			"<select multiple=\"multiple\" class=\"jquery-multiselect\" id=\"$field\" name=\"" .$field . "[]\" >\n" : 
			"<select class=\"\" name=\"" . $field . "\" >\n";
		$return .= ($first_label <> '') ? "<option value=\"$first_value\">$first_label</option>" : '' ;
		
		foreach($ar as $key => $value) {
			$return .=  (in_array($value,$selected)) ? 	
				"<option value=\"". $value . "\" selected=\"selected\"> ". $value . "</option>\n" : 
				"<option value=\"". $value . "\"> ". $value . "</option>\n";
		}
		$return .= "</select>\n";
		return $return;
	}
	
	public static function _getUsername($id) {
		$select = "SELECT `use_username` FROM `aimusers` WHERE `use_id` = {$id} LIMIT 1";
		$oDB = new db($select);
		return $oDB->_getResult();
	}
	
}
?>