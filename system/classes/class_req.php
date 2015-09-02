<?php

class req { // class to handle request variables
	
	public function __toString() {
		$return = '[<ul>'; 
		foreach($_REQUEST as $key => $value) {
			if(is_array($value) ) {
				$return .= "<li>$key</li><ul>";
				foreach($value as $k => $v) {
					$return .= "<li>$k => $v</li>";
				}
				$return .= "</ul>";
			}
					
			$return .= "<li>$key => $value</li>";
		}
		$return .= "</ul>]";	
		return $return;
	}
	
	public function __invoke($a) {
		return req::_($this->$a,false);
	}
	
	public static function _($var,$default = false,$src = '_GET|_POST') {
		$srcs = explode('|',$src);
		foreach ($srcs as $s) {
			$a = $GLOBALS[$s];
			if ( isset($a[$var]) && $a[$var] !== '' ) return $a[$var];
		}
		return $default;
	}
	
	public static function _chk($var) {
		return ( !empty($_REQUEST[$var]) ) ? true : false;	
	}
	
	public function _serialize($a=NULL,$k=NULL) {
		// a = values to ignore
		// k = keys to ignore
		foreach($_GET as $key=>$value) {
			if (!in_array($value,$a) && !in_array($key,$k) ) { 
				$return[] = "{$key}={$value}";
			}
		}
		return implode("&",$return);
	}
}

?>