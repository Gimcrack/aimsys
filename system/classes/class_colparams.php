<?php
include_once("class_db.php");

class paramlist {
	
	public $editable			;//	Is column editable
	public $visible				;//	Is column visible
	public $qv_enabled			;//	Is column quickview enabled
	public $qv_default			;//	If quickview is enabled, is it on by default
	public $qv_order			;//	If quickview is enabled, order of the col
	//public $qv_colminwidth		;//	If quickview is enabled, what is the col min width
	public $qv_label			;//	If quickview is enabled, what is the col label
	public $qv_sub_col			;// Substitute a column in the quickview
	public $qv_sub_col_table	;// Table that substitute col comes from
	public $input_type			;//	If editable, what type of form input to use
	public $input_required		;//	If editable, is input required?
	public $input_validtype		;//	If editable, what validation type if any.
	public $input_firstvalue	;//	If select, what is the first value
	public $input_firstlabel	;//	If select, what is the first label
	public $input_multiple		;//	If select, are multiple selections ok
	public $input_values		;//	If select or radio, what are the values
	public $input_labels		;//	If select or radio, what are the labels
	public $input_label			;//	If editable, what is the form input label
	public $input_attrs			;//	Additional input attributes
	public $fieldset			;// Is the column part of a fieldset?
	public $input_order			;// Order in the fieldset
	
}

// Setup the default col parameter parameters
class paramdefaults {
	
	public $editable 			= array("type" => "bool","label" => "Is Editable?","helptext" => "bool - Is the form field editable?","defaultvalue" => 1); 					//	Is column editable
	public $visible				= array("type" => "bool","label" => "Is Visible?","helptext" => "bool - Is the form field visible?","defaultvalue" => 1);					//	Is column visible
	public $qv_enabled			= array("type" => "bool","label" => "QV Enabled?","helptext" => "bool - Is the field enabled for QuickView?","defaultvalue" => 1);			//	Is column quickview enabled
	public $qv_default			= array("type" => "bool","label" => "QV Default?","helptext" => "bool - Is the form field in QuickView by default?","defaultvalue" => 1);			//	If quickview is enabled, is it on by default
	public $input_required		= array("type" => "bool","label" => "Required?","helptext" => "Is the input required?","defaultvalue" => 1);
	public $input_multiple		= array("type" => "bool","label" => "Multiple Select?","helptext" => "Allow multiple selection in select menu.","defaultvalue" => '');
	public $qv_order			= array("type" => "int","label" => "QV Order","helptext" => "int - Order of the column in QuickView.","defaultvalue" => 0);
	//public $qv_colminwidth		= array("type" => "int","label" => "QV Col. Min Width","helptext" => "int - The minimum width of the column, in pixels.","defaultvalue" => 100);
	public $qv_label			= array("type" => "text","label" => "QV Label","helptext" => "text - The label of the column in QuickView.","defaultvalue" => "");
	public $qv_sub_col			= array("type" => "text","label" => "QV Sub Column","helptext" => "text - Optionally substitute another column in its place. Use format `colname`","defaultvalue" => '');
	public $qv_sub_col_table	= array("type" => "text","label" => "Sub Col Table","helptext" => "text - Table that sub col comes from","defaultvalue" => '');
	public $input_type			= array("type" => array('text','hidden','password','textarea','checkbox','radio','select'),"label" => "Input Type","helptext" => "Select the input type from the dropdown list.","defaultvalue" => '');
	public $input_validtype		= array("type" => "text","label" => "Valid Type","helptext" => "What type of input is accepted?","defaultvalue" => "");
	public $input_firstvalue	= array("type" => "text","label" => "First Value, Select","helptext" => "Replace default first value in select menu.","defaultvalue" => '');
	public $input_firstlabel	= array("type" => "text","label" => "First Label, Select","helptext" => "Replace first label in select menu.","defaultvalue" => '');
	public $input_size			= array("type" => "int","label" => "Input Size","helptext" => "Input size for select menu or text","defaultvalue" => '');
	public $input_values		= array("type" => "text","label" => "Input Values","helptext" => "If select or radio, what are the input options?","defaultvalue" => '');
	public $input_labels		= array("type" => "text","label" => "Input Labels","helptext" => "If select or radio, what are the input labels?","defaultvalue" => '');
	public $input_label			= array("type" => "text","label" => "Input Label","helptext" => "If editable, what is the form input label?","defaultvalue" => "");
	public $input_attrs			= array("type" => "text","label" => "Input Attributes","helptext" => "Enter any additional html attributes.","defaultvalue" => '');
	public $fieldset		= array("type" => "text","label" => "Fieldset","helptext" => "Is this input part of a fieldset?","defaultvalue" => '');
	public $input_order			= array("type" => "text","label" => "Input Order","helptext" => "The order that this appears in the fieldset","defaultvalue" => '');

}

class tableoptions {
	
	public $name				;//	Name of the table
	public $description			;// Description of the table
	public $joins				;// List of joins to use when querying the table
	public $relations			;// Table relations	
	
	public function __construct($table) {
		$select = "SELECT * FROM `aimtableparams` WHERE `tblparam_name` = '{$table}'";
		$oDB = new db($select);
		$row = $oDB->_getRow();
		if ($row !== false) {
			$this->name = $row['tblparam_name'];
			$this->description = $row['tblparam_description'];
			$this->joins = $row['tblparam_joins'];
			$this->related = $row['tblparam_related'];
		}
		else {
			$insert = "INSERT INTO `aimtableparams` SET `tblparam_name` = '{$table}'";
			$oQuery = new db($insert);
			$this->__construct($table);
		}
		
	}
}

// Setup the default col parameter parameters
class tableoptionsdefaults {
	
	public $name 				= array("type" => "text","label" => "Table Name","helptext" => "text - What is the name of the table?","defaultvalue" => ""); 	//	Is column editable
	public $description			= array("type" => "text","label" => "Description","helptext" => "text - Table description","defaultvalue" => "");				//	Is column visible
	public $joins				= array("type" => "textarea","label" => "Table Joins","helptext" => "text - List any table joins.","defaultvalue" => "");			//	Is column quickview enabled
	public $relations			= array("type" => "textarea","label" => "Table Relations","helptext" => "text - List any table relations.","defaultvalue" => "");	//	If quickview is enabled, is it on by default

}


class tableparams {
	
	public $table				; // Table Name
	public $tables				; // List of tables
	public $ar_cols				; // List of db columns
	public $ar_params			; // List of col params
	public $params				; // Array of column params

	
	public function __construct($table,$tables) {
		$this->table = $table;
		$this->tables = $tables;
		$this->ar_cols = dbfn::_getfields($table);
		
		$oPlist = new paramdefaults();
		$aPlist = array();
		foreach ($oPlist as $key => $value) {;
			$aPlist[] = $value['label'];
		}
		$this->ar_params = $aPlist;
		$this->params = array();
		foreach($this->ar_cols as $col) {
			$this->params[ $col ] = new colparams($this->table,$col);
		}
		
			
			
	}
	
	public function _getHtml() {
		$data = $this;
		$return = <<<HTML
<div class="param-table" id="param_table_{$data->table}">
HTML;

// Create first Column
$menu = aimsys::_array_sel_menu($data->tables,"sel_table_{$data->table}",$data->table,0,"--Select Table--");

$return .= <<<HTML
<div class="param-col">
	<div class="param-cell">{$data->table}</div>
HTML;

// Create cells for parameters
foreach ($data->ar_params as $key => $value) {
	$return .= <<<HTML
	<div class="param-cell">{$value}</div>
HTML;
}

// End First Column
$return .= <<<HTML
<div class="param-cell"><button type="button" onclick="ajax_submit_all('#param_table_{$data->table}')">Save All</button></div>
</div>
HTML;

// Process rest of the data
foreach ($data->params as $key => $col) {
	// Create next Column
	$return .= <<<HTML
<div class="param-col" id="param_col_{$key}">
	<div class="param-cell">{$key}</div>
	<form action="index.php?controller=form&option=tableparam" method="post" id="frm_tableparam_{$key}" target="#param_col_{$key}" targetScript="1">
    <input type="hidden" name="frm_name" value="frm_tableparam" />
    <input type="hidden" name="_table" value="{$col->_table}" />
    <input type="hidden" name="_col" value="{$col->_col}" />
	
HTML;
	// Create additional cells
	foreach ($col as $key2 => $param) {
		if (is_object($param)) {
			$return .= @<<<HTML
			<div title="{$param->helptext}" class="param-cell">{$param->inputhtml}</div>
HTML;
		}
	}

// End Column
$return .= <<<HTML
<div class="param-cell">
<button type="button" onclick="ajax_submit(this.form)">Save</button></div>
</form>
</div>
HTML;

}

// End Table
$return .= <<<HTML
</div>
<div class="data row footer"></div>
HTML;

	return $return;
	}
	
}

class colparams {
	
	public function __construct($table,$col) {
		$oTempList = new paramdefaults();
		foreach($oTempList as $key => $value) {
			$this->$key = new paramparams($value);	
			
			$this->$key->table = $table;
			$this->$key->col = $col;
			$this->$key->param = $key;
			
			$oTemp = new paramforminput($this->$key);
			$this->$key->inputhtml = $oTemp->inputhtml;
			$this->$key->labelhtml = $oTemp->labelhtml;
			
		}
		
		$this->_table = $table;
		$this->_col = $col;
		$this->_get();
	}
	
	public function _getvalues() {
		$oTemp = $this;
		foreach($this as $key => $param) {
			if ($key{0} <> "_") {
				$oTemp->$key = $param->selvalue;	
			}
		}
		return $oTemp;
		
	}
			
	public function _get() {		// Retrieve the column paramters
		
		global $dbc;
		
		$s[] = "SELECT `par_params` FROM `aimparams` WHERE `par_tablecol` = %s";
		$s[] = $this->_table . "." . $this->_col;
		
		$query = call_user_func_array('dbfn::_sanitize',$s); 
		
		$oDB = new db($query);
		
		if ( empty($oDB->ret['num_rows']) ) return false;
		
		$raw_params = $oDB->_getResult();
		
		$lines = explode("\n",trim($raw_params));
		
		
		foreach ($lines as $line) {
			$temp = explode("||",$line);
			$key = $temp[0];
			$value = $temp[1];
			$this->$key->selvalue = $value;
			
			$oTemp = new paramforminput($this->$key);
			$this->$key->inputhtml = $oTemp->inputhtml;
			$this->$key->labelhtml = $oTemp->labelhtml;
		}
		
	}
	
	public function _set($a) {
		foreach ($this as $key => $value) {
			if ( $key{0} <> '_' ) {
				$this->$key->selvalue = @$a[$key];
			}
		}
	}
	
	public function _store() {
		foreach ($this as $key => $value) {
			if ( $key{0} <> '_' ) {
				$value = @str_replace("\n",'',$this->$key->selvalue);
				$par_params[] = "{$key}||{$value}";
			}
		}
		$par_params = trim(implode("\n",$par_params));
		
		$s[] = "INSERT INTO `aimparams` SET `par_tablecol` = %s, `par_params` = %s ON DUPLICATE KEY UPDATE `par_params` = %s";
		$s[] = $this->_table . "." . $this->_col;
		$s[] = $par_params;
		$s[] = $par_params;
		
		$query = call_user_func_array('dbfn::_sanitize',$s); 
		
		$oDB = new db($query);
		
		return $oDB->ret;
	}
	
	

}

class paramparams {
	public $table;
	public $col;
	public $param;
	public $type;
	public $label;
	public $helptext;
	public $defaultvalue;
	public $selvalue;
	public $labelhtml;
	public $inputhtml;
	
	public function __construct($ar) {
		foreach($ar as $key => $value) {
			$this->$key = $value;
		}
	}
}





class paramforminput {
	
	public $labelhtml; // Generated html of label
	public $inputhtml; // Generated html of input
	
	public function __construct($oParams) {
		
		$this->params = $oParams;
		
		$this->labelhtml 	= $this->_getlabel();
		$this->inputhtml	= $this->_getforminput();
		
		unset($this->params);
			
	}
	
	public function _getlabel() {
		return @<<<HTML
	<label for="{$this->params->table}.{$this->params->col}.{$this->params->param}" title="{$this->params->helptext}">{$this->params->label}</label>
HTML;
}
	
	public function _getforminput() {
		
		$setup = @$this->params->type;
		switch($setup) {
			case 'bool': // Generate checkbox field;
				if ($this->params->selvalue) {
					return @<<<HTML
<input value="1" type="checkbox" name="{$this->params->param}" id="{$this->params->table}.{$this->params->col}.{$this->params->param}" checked="checked" /><label for="{$this->params->table}.{$this->params->col}.{$this->params->param}" title="{$this->params->helptext}">Yes?</label>
HTML;
				} else {
					return @<<<HTML
<input value="1" type="checkbox" name="{$this->params->param}" id="{$this->params->table}.{$this->params->col}.{$this->params->param}"/><label for="{$this->params->table}.{$this->params->col}.{$this->params->param}" title="{$this->params->helptext}">Yes?</label>
HTML;
				}
			break;
			
			case 'int': // Generate text field with numeric requirement
				$value = $this->params->selvalue;
				return @<<<HTML
<input type="text" name="{$this->params->param}" id="{$this->params->table}.{$this->params->col}.{$this->params->param}" value="{$value}" validType="Integer" class="required-input" size="12" />
HTML;
			break;	
			
			case 'text': // Generate text field
				$value = (empty($this->params->selvalue) ) ? $this->params->defaultvalue : $this->params->selvalue;
				return @<<<HTML
<input type="text" name="{$this->params->param}" id="{$this->params->table}.{$this->params->col}.{$this->params->param}" value="{$value}" size="12" />
HTML;
			break;
			
			default:
				$value = (empty($this->params->selvalue) ) ? @$this->params->defaultvalue : $this->params->selvalue;
				if (is_array($setup)) {
					$return = @<<<HTML
<select name="{$this->params->param}" id="{$this->params->table}.{$this->params->col}.{$this->params->param}">
<option value="0">--Choose an option--</option>
HTML;
				foreach($setup as $key => $option) {
					if ($value == $option) {
						$return .= <<<HTML
<option value="{$option}" selected="selected" >{$option}</option>
HTML;
					} else {
						$return .= <<<HTML
<option value="{$option}" >{$option}</option>
HTML;
					}
				}
				$return .= <<<HTML
</select>
HTML;
				return $return;
				}
			break;
		}	
	}
	
	
}



?>