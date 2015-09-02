<?php
// MYSQL DB CLASS

class db {
	public $rows;
	public $result;
	public $statement;
	public $action;
	public $ret = array();
	public $insert_id;
	
	public function __construct($statement = NULL) {
		if ($statement !== NULL) {
			$this->statement = $statement;
			$this->_query();
		}
	}
	
	public function __toString() {
		echo "<pre>";
		print_r($this);
		echo "</pre>";
	}	
	
	public function _query() {
		global $dbc;
		$this->result = $dbc->query($this->statement);
		if ($dbc->error) { die( "<pre>" . $this->statement . "<br>" . $dbc->error . "</pre>" ); }
		$this->action = substr($this->statement,0, strpos($this->statement," ") );
		//fb($this->statement,FirePHP::TRACE);
		
		switch($this->action) {
			case "UPDATE":
			case "DELETE":
			case "INSERT":
			
				if ($this->action <> "DELETE") {
					$this->insert_id = $dbc->insert_id;
				}
				
				$num_rows = $dbc->affected_rows;
				$this->ret['num_rows'] = $num_rows;
		
				if ($num_rows < 0) { //mysql error
					$this->ret['message'] = "Error : ".$dbc->error;
					$this->ret['msg_class'] = "error";
				}
				
				elseif ($num_rows == 0) { //no rows affected
					$this->ret['message'] = "No rows affected";
					$this->ret['msg_class'] = "information";
				}
				
				else { //1 or more rows affected
					$this->ret['message'] = "Operation Successful!";
					$this->ret['msg_class'] = "success";
				}
			
			break;
			
			case "SELECT":
			case "SHOW":
			default :
				
				$num_rows = $this->result->num_rows;
				$this->ret['num_rows'] 	= $num_rows;
				$this->ret['message'] 	= "";
				
				while ($row = $this->result->fetch_assoc()) {
					$this->rows[] = $row;
				} 
				
			break;
		}
		return true;
	}
	
	public function _buildOptions($valueKey,$optionKey,$selectedValue = NULL) {
		foreach ($this->rows as $row) {
			if ($row[$valueKey] == $selectedValue) {
				$return[] = "<option selected=\"selected\" value=\"{$row[$valueKey]}\">{$row[$optionKey]}</option>";
			}
			else {
				$return[] = "<option value=\"{$row[$valueKey]}\">{$row[$optionKey]}</option>";
			}
		}
		return $return;
	}
	
	public function _getResult() {
		$row = $this->rows[0];
		reset($row);
		$key = key($row);
		return $row[$key];
	}
	
	public function _getRow() {
		$return = (!empty($this->rows) ) ? $this->rows[0] : false;
		return $return;
	}
	
	public function _showTable() {
		$return = "<table border=\"1\">\n<tr>";	
		
		$keys = array_keys($this->rows[0]);
		
		foreach($keys as $key) {
			$return .= "<th>$key</th>\n";
		}
		
		$return .= "</tr>\n";
		
		foreach($this->rows as $row) {
			$return .= "<tr>\n";
			foreach($row as $value) {
				$return .= "<td>{$value}&nbsp;</td>\n";
			}
			$return .= "</tr>\n";
		}
		
		$return .= "</table>\n";
		
		return $return;
	}
	
	
			
}

class dbfn extends db {

	public static function _buildselect($table,$pkey) {
		$select = "SELECT * FROM `%s` WHERE `%s` = %s LIMIT 1";
		$s[] = $select;
		$s[] = $table;
		$s[] = $pkey;
		$s[] = req::_($pkey);
		$select = call_user_func_array('sprintf', $s);
		return $select;
	}
	
	public static function _buildselect2($table,$prikey,$id) {
		$select = "SELECT * FROM `%s` WHERE `%s` = %s LIMIT 1";
		$s[] = $select;
		$s[] = $table;
		$s[] = $prikey;
		$s[] = dbfn::_adq($id);
		$select = call_user_func_array('sprintf', $s);
		return $select;
	}
	
	public static function _buildinsert($fields,$table) { 
		foreach($_REQUEST as $key => $value) {
			$$key = (is_array($value) ) ? implode("|",$value) : $value;
		}
		$insert = "INSERT INTO `%s` ( %s ) VALUES ( %s )";
		$s = array('','','','');
		$s[0] = $insert;
		$s[1] = $table;
		$s[2] = array();
		$s[3] = array();
		
		foreach ($fields as $key => $field) {
			$s[2][] = "`" . $field . "`";
			$s[3][] = (strpos($field,'password') === false) ?  
				dbfn::_adq( $$field ) :
				dbfn::_adq( md5( $$field) );
		}
		$s[2] = implode(", ",$s[2]);
		$s[3] = implode(", ",$s[3]);
		$insert = call_user_func_array('sprintf',$s);
		return $insert;
	}
	
	public static function _buildupdate($fields,$table,$where) { 
		foreach($_REQUEST as $key => $value) {
			$$key = (is_array($value) ) ? implode("|",$value) : $value;
		}
		$update = "UPDATE `%s` SET %s WHERE %s";
		$s = array('','','','');
		$s[0] = $update;
		$s[1] = $table;
		$s[2] = array();
		
		foreach ($fields as $key => $field) {
			if (isset($$field) ) {
				$s[2][] = (strpos($field,'password') === false) ? 
					"`{$field}` = " . dbfn::_adq( $$field ) :
					"`{$field}` = " . dbfn::_adq( md5( $$field ) );
			}
		}
		$s[2] = implode(", ",$s[2]);
		$s[3] = "`" . $where . "` = " . dbfn::_adq( $$where ) ;
		
		$update = call_user_func_array('sprintf',$s);
		return $update;
	}
	
	public static function _builddelete($table,$id) { 
		foreach($_REQUEST as $key => $value) {
			$$key = (is_array($value) ) ? implode("|",$value) : $value;
		}
		$delete = "DELETE FROM `%s` WHERE `%s` = %s";
		$s = array();
		$s[] = $delete;
		$s[] = $table;
		$s[] = $id;
		$s[] = $$id;
		
		$delete = call_user_func_array('sprintf',$s);
		return $delete;
	}
	
	public static function _buildwhere($table,$q) {
		global $dbc;
		$select = "SHOW COLUMNS FROM `$table` WHERE `type` NOT LIKE '%int%'";
		$r = new db($select);
		$where = array();
		
		foreach ($r->rows as $key => $value) {
			if (is_numeric($key) ) {
				$where[] = "`" . $value['Field'] . "` LIKE '%" . dbfn::_escape($q) . "%'";
			}
		}
		$where = "( " . implode(" OR ",$where) . " )";
		return $where;
	}
	
	public static function _buildwhere2($a) {
		$where = array();
		foreach($a as $key => $val) {
			$where[] = ( is_numeric($val) ) ? "`" . $key . "` = " . dbfn::_escape($val) : "`" . $key . "` LIKE '%" . dbfn::_escape($val) . "%'";
		}
		$where = "( " . implode(" OR ",$where) . " )";
		return $where;
	}
	
	public static function _sanitize($query) { 
		$numParams = func_num_args(); 
		$params = func_get_args(); 
		
		if ($numParams > 1) { 
			for ($i = 1; $i < $numParams; $i++){ 
				$params[$i] = dbfn::_adq( $params[$i] ); 
			} 
			
			$query = call_user_func_array('sprintf', $params); 
		} 
		
	  return $query;
	}
	
	public static function _escape($text) {
		global $dbc;
		return $dbc->real_escape_string($text);
	}
	
	public static function _adq($text) {
		if (is_numeric($text) ) {
			return dbfn::_escape($text);
		}
		else {
			return '"'. dbfn::_escape($text).'"';
		}
	}
	
	public static function _serial($a) {
		$r = array();
		foreach ($a as $k => $v) {
			$r[] .= $k."=".$v;
		}
		return implode("\n",$r);
	}
	
	public static function _getfields($table) {
		$select = "SHOW COLUMNS FROM `$table`";
		$oDB = new db($select);
		$result = $oDB->rows;
		
		$fields = array();
		foreach ($result as $key => $value) {
			$fields[] = $value['Field'];
		}
		return $fields;
	}
	
	public static function _getinsertfields($table,$ignore_pri=true) {
		$select = ($ignore_pri) ? 
			"SHOW COLUMNS FROM `$table` WHERE `key` NOT LIKE '%PRI%' AND `field` NOT LIKE '%checked_out%' AND `field` NOT LIKE 'sys_%'" : 
			"SHOW COLUMNS FROM `$table` WHERE `field` NOT LIKE '%checked_out%' AND `field` NOT LIKE 'sys_%'";
		$oDB = new db($select);
		$result = $oDB->rows;
		
		$fields = array();
		foreach ($result as $key => $value) {
			$fields[] = $value['Field'];
		}
		return $fields;
	}
	
	public static function _getupdatefields($table) {
		$select = "SHOW COLUMNS FROM `$table` WHERE `key` NOT LIKE '%PRI%' AND `field` NOT LIKE '%checked_out%' AND `field` NOT LIKE 'sys_%'";
		$oDB = new db($select);
		$result = $oDB->rows;
		
		$fields = array();
		foreach ($result as $key => $value) {
			$fields[] = $value['Field'];
		}
		return $fields;
	}	
	
	public static function _getprimary($table) {
		$select = "SHOW COLUMNS FROM `$table` WHERE `key` LIKE '%PRI%'";
		$oDB = new db($select);
		return $oDB->_getResult();
	}
	
	public static function _get_sel_menu($field,$table,$k,$v,$sel,$first_value = '',$first_label = '',$multiple = false) {
		$selected = (strpos($sel,",") !== false) ? explode(',',$sel) : array($sel);
		$pri = dbfn::_getprimary($table);
		$query = "SELECT * FROM `$table` ORDER BY `$pri` ASC";
		$oDB = new db($query);
		$r = $oDB->rows;
		$return = ($multiple) ? 
			"<select multiple=\"multiple\" class=\"jquery-multiselect\" id=\"$field\" name=\"" .$field . "[]\" >\n" : 
			"<select class=\"jquery-singleselect\" name=\"" . $field . "\" >\n";
		$return .= ($first_label <> '') ? "<option value=\"$first_value\">$first_label</option>" : '' ;
		
		foreach($r as $key => $value) {
			$return .=  (in_array($value[$v],$selected)) ? 	
				"<option value=\"". $value[$v] . "\" selected=\"selected\"> ". $value[$k] . "</option>\n" : 
				"<option value=\"". $value[$v] . "\"> ". $value[$k] . "</option>\n";
		}
		$return .= "</select>\n";
		return $return;
	}
	
	public static function _get_options($labels,$values) {
		$table = substr($labels,0,strpos($labels,'.') );
		$labels = trim(str_replace("{$table}.",'',$labels));
		$values = trim(str_replace("{$table}.",'',$values));
		$select = "SELECT {$labels} AS `labelsalias`,{$values} AS `valuesalias` FROM `$table` ORDER BY `labelsalias` ASC";
		$oDB 	= new db($select);
		$r = $oDB->rows;
		$ret = array();
		$ret['labels'] = array();
		$ret['values'] = array();
		
		foreach($r as $row) {
			$ret['labels'][] = $row['labelsalias'];
			$ret['values'][] = $row['valuesalias'];	
		}
		return $ret;
	}
	
	public static function _get_options2($table,$labels,$values) {
		$select = "SELECT {$labels} AS `labelsalias`,{$values} AS `valuesalias` FROM `$table` ORDER BY `labelsalias` ASC";
		$oDB 	= new db($select);
		$r = $oDB->rows;
		$ret = array();
		
		foreach($r as $row) {
			$ret[] = "<option value=\"{$row['valuesalias']}\">{$row['labelsalias']}</option>";	
		}
		return implode("\n",$ret);
	}
	
	public static function _getJoins($table) {
		$query = "SELECT * FROM `information_schema`.`key_column_usage` WHERE `REFERENCED_COLUMN_NAME` <> '' AND `CONSTRAINT_SCHEMA` = 'aimsysdb' AND `TABLE_NAME` = '$table'";
		$oDB = new db($query);
		$rows = $oDB->rows;
		if (empty($rows) ) return false;
		$return = array();
		$reftables = array();
		foreach ($rows as $row) {
			$return[] = "INNER JOIN `{$row['REFERENCED_TABLE_NAME']}` ON `{$row['REFERENCED_TABLE_NAME']}`.`{$row['REFERENCED_COLUMN_NAME']}` = `{$row['TABLE_NAME']}`.`{$row['COLUMN_NAME']}`";
			$reftables[] = $row['REFERENCED_TABLE_NAME'];
		}
		foreach( $reftables as $reftable) {
			$temp = trim(dbfn::_getJoins($reftable));
			if( !empty($temp) ) { 
				$return = array_merge($return, explode("\n",$temp)); }
		}
		return implode("\n",array_unique($return));
			
	}
	
	public static function _getMemberTables($table) {
		$query = "SELECT `TABLE_NAME` AS `table`,`COLUMN_NAME` AS `col` FROM `information_schema`.`key_column_usage` WHERE `REFERENCED_TABLE_NAME` = '$table' AND `CONSTRAINT_SCHEMA` = 'aimsysdb'";
		$oDB = new db($query);
		$rows = $oDB->rows;
		if (empty($rows) ) return false;	
		return $rows;
	}
	
	public static function friendlyTable($table) {
		return ucwords(str_replace( array( TABLEPREFIX, '_' ), array('',' '),$table));	
	}	
	
}

?>