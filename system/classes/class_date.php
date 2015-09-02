<?php

date_default_timezone_set('America/Anchorage');

class Date {
	
	/* Day */
	public $d;	// d - 2 digit day of month 	- 01-31
	public $D; // D - three leter day of week 	- Mon-Sun 
	public $j;	// j - day of month, no lead 0	- 1-31
	public $l; 	// l - full text day of week	- Sunday-Saturday
	public $N;	// N - numeric day of week		- 1(Mon)-7(Saturday)
	public $S; // S - ordinal suffix			- st,nd,rd, th
	public $w; 	// w - numeric day of week		- 0(Sun)-6(Sat)
	public $z;	// z - Day of year				- 0-365
	
	/* Week */
	public $W;	// W - number of week
	
	/* Month */
	public $F;	// F - January-December
	public $m; 	// m - 01-12
	public $M; // M - Jan-Dec
	public $n;	// n - 1-12
	public $t; 	// t - days in month - 28-31
	
	/* Year */
	public $L; // L - Leap year - 0-1
	public $o;	// o - year number ISO-8601, e.g. 1999
	public $Y; // Y - e.g. 1999
	public $y;	// y - e.g. 99
	
	/* Time */
	public $a;	// a - am / pm
	public $A; // A - AM / PM
	public $B; // B - Swatch Internet Time - 000-999
	public $g;	// g - 12 hour - 1-12
	public $G; // G - 24 hour - 0-23
	public $h; 	// h - 12 hour - 01-12
	public $H; // hh - 24 hour - 00-23
	public $i;	// i - minutes - 00-59
	public $s;	// s - seconds - 00-59
	public $u;	// u - microseconds
	
	/* Timezone */
	public $e;  // e - timezone identifier e.g. GMT
	public $I;	// I - 1 if DST, 0 if otherwise
	public $O;	// O - Difference to GMT eg. +0200
	public $P;  // P - Difference to GMT eg. +02:00
	public $T;  // T - timezone abbreviation e.g. EST
	public $Z;	// Z - timezone offest in seconds
	
	/* Full datetime */
	public $c;	// ISO 8601 Date
	public $r;	// RFC 2822 Date
	public $U;	// Seconds since Unix Epoch
	
	
	function __construct($ts) {
		foreach ($this as $key => $value) {
			$this->$key = date($key,$ts);
		}
		$this->mysql_datetime = date("Y-m-d H:i:s",$ts);
		$this->mysql_date = date("Y-m-d",$ts);
		$this->mysql_time = date("H:i:s",$ts);
	}
	
	function _format($s) {
		return date($s,$this->U);
	}
	
	function _s2t($s) {
		return strtotime($s,$this->U);
	}
	function _isToday() {
		return (date('Y m d') == $this->_format('Y m d')) ? true : false;
	}
	function _isPast() {
		return (date('U') > $this->U);
	}
	function _isMatch($ys,$ye) {
		/*
		$ys = event_start
		$ye = event_end
		$xs = day_start
		$xe = day_end
		*/
		
		$xs 	= strtotime($this->_format('Y-m-d 12:00:00am'));
		$xe		= strtotime($this->_format('Y-m-d 11:59:59pm'));
		
		// 4 cases where an event y could match a date x
		
		// Case 1: Event starts and stops on day x.
		if ( $ys >= $xs && $ye <= $xe ) {
			return true;
		}
		
		// Case 2: Event starts on day x.
		elseif ( $ys >= $xs && $ys <= $xe ) {
			return true;
		}
		
		// Case 3: Event ends on day x.
		elseif ( $ye >= $xs && $ye <= $xe ) {
			return true;
		}
		
		// Case 4: Event starts before day x and ends after day x.
		elseif ( $ys < $xs && $ye > $xe ) {
			return true;
		}
		
		// Else not a match
		else {
			return false;
		}
			
	}
	
	function _dayMatch($o) {
		return ($o->_format('Y m d') == $this->_format('Y m d') ) ? true : false;
	}
	
	public static function _formatDuration($oSt,$oEn) {
		if ($oSt->Y == $oEn->Y) { //same year
			if ($oSt->m == $oEn->m) { // same month
				if ($oSt->d == $oEn->d) { // same day 
					if ($oSt->A == $oEn->A) { // same AM or PM
						$st = $oSt->_format('g:i');
						$en = $oEn->_format('g:iA');
					}
					else { // not same AM or PM
						$st = $oSt->_format('g:iA');
						$en = $oEn->_format('g:iA');
					}
				}
				else { // not same day
					$st = $oSt->_format('m/d g:iA');
					$en = $oEn->_format('m/d g:iA');
				}
			}
			else { // not same month
				$st = $oSt->_format('m/d g:iA');
				$en = $oEn->_format('m/d g:iA');
			}
		}
		else { // not same year
			$st = $oSt->_format('m/d/Y g:iA');
			$en = $oEn->_format('m/d/Y g:iA');
		}
		return "$st - $en";
	}
	
	public static function _daysFromDates($oSt,$oEn) {
		$return[] = $oSt->_format("Y/m/d");
		if ( $oSt->_dayMatch($oEn) ) {
			return $return;
		}
		else {
			$temp = $oSt->U;
			do {
				$temp = ( strtotime("+1 day",$temp) <= $oEn->U ) ? strtotime("+1 day",$temp) : $oEn->U;
				$return[] = date("Y/m/d",$temp);
				
			} while ( $temp < $oEn->U ) ;
		}
		return array_unique($return);
	}
		
	public static function _hoursFromTime($time) {
		$t = explode(':',$time);
		if (count($t) > 1) {
			$hrs = 1*$t[0];
			$mins = 1*$t[1];
			
			return $hrs + $mins/60;
		}
		elseif (is_numeric($t[0]) ) {
			return 1*$t[0];
		}
		else {
			return 0;
		}
	}
	
	public static function _secFromTime($time) {
		$t = explode(':',$time);
		if (count($t) > 1) {
			$hrs = 1*$t[0];
			$mins = 1*$t[1];
			$sec = 1*$t[2];
			
			return 3600*$hrs + 60*$mins + $sec;
		}
		else {
			return 0;
		}
	}
	
	function _os2t($s) {
		$o = new Date( $this->_s2t($s) );
		return $o;
	}
	
	function _osubtime($time) {
		$sec = Date::_secFromTime($time);
		return $this->_os2t("-$sec seconds");	
	}
	
	function _oaddtime($time) {
		$sec = Date::_secFromTime($time);
		return $this->_os2t("+$sec seconds");
	}
	
	function _oformat($s) {
		$o = new Date( strtotime(date($s,$this->U)) );
		return $o;
	}
		
}

?>