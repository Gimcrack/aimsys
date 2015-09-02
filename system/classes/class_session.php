<?php
class session {
	
	public static function _login($username,$password) {
		global $session_length;
		$select = "SELECT * FROM `aimusers` WHERE `use_username` = %s AND `use_password` = md5(%s) LIMIT 1";
		$select = dbfn::_sanitize($select,$username,$password);
		$oResult = new db($select);
		$row = $oResult->_getRow();
		if ($row) {
			foreach ($row as $key => $value) {
				if ( strpos($key,"password") === false) {
					$_SESSION[$key] = $value;
				}
			}
			
			$_SESSION['rows_per_page'] = ROWS_PER_PAGE;
			session::_refresh_session();
			
			return true;
		}
		else {
			$_SESSION['message'] = "That username and password combination is not recognized. Plese try again.";
			$_SESSION['msg_class'] = "error";
			return false; // bad login attempt
		}
	}
	
	public static function _check_session() {
		global $session_length;
		// check to make sure user is logged in first
		
		if (	empty($_SESSION['use_id']) 		) { return false; } // no logged on user
		if (	$_SESSION['expired']			) { 
			$_SESSION['expired'] = false; 
			$_SESSION['message'] = "Your session has ended. Please log in again.";
			$_SESSION['msg_class'] = "warning";
			return false; 
		}
		
		if ( 	time() > $_SESSION['expires'] 	) { 
			$_SESSION['expired'] = true; 
			$_SESSION['message'] = "Your session has ended. Please log in again.";
			$_SESSION['msg_class'] = "warning";
			return false; 
		}
				
		
		//Session is good, refresh.
		session::_refresh_session();
		
		return true;
				
	}
	
	public static function _refresh_session() {
		global $session_length;
		$_SESSION['expires'] = time() + $session_length;
		$_SESSION['expired'] = false;
		$_SESSION['remaining'] = $_SESSION['expires'] - time();
		unset($_SESSION['message']);
		unset($_SESSION['msg_class']);
	}
	
	public static function _get_message() {
		$message = (!empty ($_SESSION['message']) ) ?  
			aimsys::_message($_SESSION['message'],$_SESSION['msg_class'],true,true) : //"<div class=\"msg active {$_SESSION['msg_class']} ui-corner-all\">{$_SESSION['message']}</div>" : 
			'';
		return $message;	
	}

}