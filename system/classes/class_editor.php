<?php


class editor {
	
	public $option;		// controller option
	
	public $table;		// name of the table
	public $cols;		// array of the table colums
	public $rowid;		// id of the row
	public $prikey;		// name of the primary key
	public $params;		// parameters of the columns
	
	public $oDB;		// the oDB object
	public $fields;		// the fields that will be affected by the query
	public $datadb;		// the data as stored in the db
	public $data;		// current working data
	public $dataout;	// the formatted data ready to be exported as html
	
	public $is_update;  // is this an update operation
	public $changes;	// changes made to the data
	public $popup;		// is this a popup or full tab
	public $editing;	// is this record currently being edited
	
	public function __construct($table,$rowid='new') {
		global $view;
		
		$this->option 	= $view;
		$this->table 	= $table;
		$this->cols  	= dbfn::_getfields($table);
		$this->rowid 	= $rowid;
		$this->prikey	= dbfn::_getprimary($table);
		$this->popup	= req::_('popup');
		$this->editing	= req::_('editing');
		$this->params = array();
		foreach($this->cols as $col) {
			$oTemp = new colparams($this->table,$col);
			$this->params[$col] = $oTemp->_getvalues();
		}	
	}
	
	public function _set($key,$value) {
		$this->$key = $value;
	}
	
	public function _getData() {
		if ( is_numeric($this->rowid) ) {
			$select 		= dbfn::_buildselect2($this->table,$this->prikey,$this->rowid);
			$this->oDB 		= new db($select);
			$this->datadb 	= $this->data = $this->oDB->_getRow();
		}
	}
	
	public function _setData($data) {
		$this->data 	= $data;
	}
	
	public function _storeData() {
		#setup some variables
		$user_id = $_SESSION['use_id'];
		$this->_isUpd();
		
		#get fields
		$this->fields = ( $this->is_update ) ? 
			dbfn::_getupdatefields($this->table) :
			dbfn::_getinsertfields($this->table);
		
		#build query
		$query = ( $this->is_update ) ? 
			dbfn::_buildupdate($this->fields,$this->table,$this->prikey) : 
			dbfn::_buildinsert($this->fields,$this->table);
			
		#get changes to the data
		$this->_getDataChanges();
		
		#execute query
		$oResult = new db($query);
		$this->rowid = ($this->is_update) ? $this->rowid : $oResult->insert_id;
		
		#format output
		$output = $oResult->ret;
		
		#determine if operation was a success and store in history
		if ($output['msg_class'] == "success") {
			aimsys::_history($query,$this->changes,$this->table,$this->rowid);
			$output['js'] = ($this->is_update || req::_('popup') <> 0) ? "" : "Tabs.activeTab.new2view('{$this->table}','{$this->rowid}');"; //additional javascript to run for new records
			$output['rowid'] = $this->rowid;
			
		}
		
		return $output;
	}
	
	public function _getDataChanges() {
		#setup variables
		$changes = array();
		
		#determine differences between old values and new
		$delta = ( $this->is_update ) ? 
			array_diff_assoc($this->datadb, $this->data) :  // if editing existing record
			$this->data;									// if new record
		
		#iterate through to build list of changes									
		foreach($delta as $key => $value) {
			if 	(array_search($key,$this->fields) === false) { unset($delta[$key]); } // remove fields from POST that do not appear in the database
			else {
				$oParams = new colparams($this->table,$key);
				$params = $oParams->_getvalues();
				$field_name = $params->qv_label;
				if ( strpos( strtolower( $field_name ), "password" ) === false ) {
					$changes[] = ( $this->is_update ) ? 
						"{$field_name} changed from '{$this->datadb[$key]}' to '{$this->data[$key]}'" : 
						"{$field_name} initially set to '{$this->data[$key]}'";
				}
				else {
					$changes[] = ( $this->is_update ) ? 
						"{$field_name} changed" : 
						"{$field_name} set";
				}
			}
		}
		$this->changes = implode("\n",$changes);
		return $this->changes;
	}
	
	public function _isUpd() {
		$this->is_update = (!empty($this->datadb)) ? true : false;
		return $this->is_update;
	}
	
	public function _html() {
		$output = <<<HTML
        
        <div id="tabs-editor">
        <div id="div-editor">
        <form target=".main-content.active" id="frm_editor" name="frm_editor" enctype="multipart/form-data" targetScript="1" onsubmit="return(false)" action="index.php?controller=form&option={$this->option}&popup={$this->popup}" method="post">
        <div class="fieldset ui-corner-all">
        <input type="hidden" value="frm_editor" name="frm_name" id="frm_name" />
		<input type="hidden" value="{$this->table}" name="table_name" id="table_name" />
        <input type="hidden" value="{$this->rowid}" name="rowid" id="rowid" />     
        <div class="div-editor-col legend">
        	<div class="div-editor-cell legend ui-corner-all">Details</div>
        </div>
        <div class="clear"></div>
        <div class="div-editor-col">
        </div>
HTML;
		// Loop through all the columns, create form elements for editable items.
		
		$fs = array(); // fieldsets
		$counttemp = array();
		
		foreach($this->cols as $col) {
			$editable = $this->params[$col]->editable;
			$visible  = $this->params[$col]->visible;
			$type	  = $this->params[$col]->input_type;
			$fieldset = $this->params[$col]->fieldset;
			$val	  = (!empty($this->data) ) ? $this->data[$col] : false;
			
			if (empty($fs[$fieldset]["labels"])) $fs[$fieldset]["labels"] = array();
			if (empty($fs[$fieldset]["elements"])) $fs[$fieldset]["elements"] = array();
			if (empty($counttemp[$fieldset])) $counttemp[$fieldset] = array();
			
			if ($visible && !empty($type) ) {
				$temp 		= $this->_constructElement($this->params[$col],$val);
				
				switch ($type) {
					case 'hidden' :
						$output .= $temp[1];
					break;
					
					default :
						@$fs[$fieldset]["labels"][] 	= $temp[0];
						@$fs[$fieldset]["elements"][] = $temp[1];
						@$counttemp[$fieldset][] = $temp[1];
					break;
				}
				
			}
			
		}
		reset($fs);
		$keys = array_keys($fs);
		
		
		@$fs[ $keys[1] ]["labels"][] = '<label class="required-label">&nbsp;</label>';
		@$fs[ $keys[1] ]["elements"][] = '<span class="input-required-message">Indicates input is required.</span>';
		@$counttemp[ $keys[1] ][] = $temp[1];
		
		
        $max_fieldset_items = max( array_map( 'count', $counttemp) );
     	   

		//$fs[end((array_keys($fs)))]["elements"][] = $temp;
		//iterate through the fieldsets and add the elements and labels to the output
		foreach ($fs as $key => $fieldset) :
		$output .= <<<HTML
				<div class="div-editor-col">      	       
HTML;
		foreach($fieldset["labels"] as $label) {
			$output .= <<<HTML
				<div class="div-editor-cell">
                {$label}&nbsp;
                </div>      	       
HTML;
		}
		if ( count($fieldset["labels"]) ) {
			for($ii=count($fieldset["labels"]);$ii<$max_fieldset_items;$ii++) {
				$output .= <<<HTML
					<div class="div-editor-cell">&nbsp;
					</div>      	       
HTML;
			}
		}
		
		$output .= <<<HTML
        		</div>
                
                <div class="div-editor-col">
HTML;
		foreach($fieldset["elements"] as $element) {
			$output .= <<<HTML
				<div class="div-editor-cell">
                {$element}&nbsp;
                </div>      	       
HTML;
		}
		if ( count($fieldset["labels"]) ) {
			for($ii=count($fieldset["elements"]);$ii<$max_fieldset_items;$ii++) {
				$output .= <<<HTML
					<div class="div-editor-cell">&nbsp;
					</div>      	       
HTML;
			}
		}
		
		$output .= <<<HTML
        		
        		<div class="clear"></div>
        		</div>
                
HTML;
		endforeach;
		
		$output .= <<<HTML
        <div class="div-editor-col">
        	<div class="div-editor-cell"></div>
        </div>
        </div>
        
        </form>
        </div>
        
        <div id="div-notes"></div>
        <div id="div-history"></div>   
        
        </div>
HTML;
		
		//pre($this);
		return $output;
	}
	
	public static function _getOptions($params,$val=NULL) {
		$output = array();
		// Determine if we need label,value pairs.
		if (!empty($params->input_labels) && !empty($params->input_values) ) {
			//Determine source of data - local or db
			$pair_source = (strpos($params->input_labels,'||') !== false) ? "local" : "db";
			if ($pair_source == "local") {
				$labels = explode("||",$params->input_labels);
				$values = explode("||",$params->input_values);	
			}
			else {
				$a = dbfn::_get_options($params->input_labels,$params->input_values); 
				$labels = $a['labels'];
				$values = $a['values'];
			}
		}
		$selected = ( empty($val) ) ? "selected=\"selected\"" : '';
		$temp = <<<HTML
			<option {$selected} value="{$params->input_firstvalue}">{$params->input_firstlabel}</option>
HTML;
		$output[] = trim($temp);
		if (!empty($params->qv_sub_col_table)) { $temp = <<<HTML
		<option value="addnew" id="{$params->qv_sub_col_table}||{$params->qv_sub_col}">--Add New--</option>
HTML;
		$output[] = trim($temp);
		}
		foreach ($labels as $key => $option) {
			$selected = ($val === $values[$key] ) ? "selected=\"selected\"" : '';
			$temp = <<<HTML
			<option {$selected} value="{$values[$key]}">$option</option>
HTML;
			$output[] = trim($temp);
		}
		
		
		
		return implode("\n",$output);
	}
	
	private function _constructElement($params,$val=NULL) {
			/*
			params:
			[editable] => 
			[visible] => 
			[qv_enabled] => 
			[qv_default] => 
			[qv_order] => 
			[qv_colminwidth] => 
			[qv_label] => 
			[qv_sub_col] => 
			[input_type] => password
			[input_required] => 1
			[input_validtype] => min>6
			[input_firstvalue] => 
			[input_firstlabel] => 
			[input_multiple] => 
			[input_size] => 
			[input_values] => 
			[input_labels] => 
			[input_label] => Password : 
			[input_attrs] => 
			[fieldset] => 
			[_table] => aimusers
			[_col] => use_password
			*/
			$output = array();
			
			// Determine if we need label,value pairs.
			if (!empty($params->input_labels) && !empty($params->input_values) ) {
				//Determine source of data - local or db
				$pair_source = (strpos($params->input_labels,'||') !== false) ? "local" : "db";
				if ($pair_source == "local") {
					$labels = explode("||",$params->input_labels);
					$values = explode("||",$params->input_values);	
				}
				else {
					$a = dbfn::_get_options($params->input_labels,$params->input_values); 
					$labels = $a['labels'];
					$values = $a['values'];
				}
			}
			
			$label_class = ($params->input_required) ? "class=\"editor-label required-label\"" : "class=\"editor-label\"";
			$input_class = ($params->input_required) ? "class=\"editor-element required-input\" validType=\"{$params->input_validtype}\"" : "class=\"editor-element\" ";
			$disabled = ($this->rowid <> 'new' && $this->editing <> 1) ? 'disabled="disabled"' : '';
			$edit_buttons_style = ($this->rowid <> 'new' && $this->editing <> 1) ? 'style="display:none"' : '';
			$readonly = ($params->editable) ? '' : 'readonly="readonly"';
			
			switch($params->input_type) {
				
				case 'text' :
					$output[0] = <<<HTML
                    <label {$label_class} for="{$params->_col}">{$params->input_label}</label>&nbsp;
HTML;
					$output[1] = <<<HTML
                    <input {$readonly} {$disabled} {$input_class} type="{$params->input_type}" name="{$params->_col}" id="{$params->_col}" value="{$val}" {$params->input_attrs}  />&nbsp;
HTML;
				break;
				
				case 'password' :
					$output[0] = <<<HTML
                    <label {$label_class} for="{$params->_col}">{$params->input_label}</label>&nbsp;
HTML;
					$output[1] = <<<HTML
                    <input style="width:100px;" {$input_class} type="{$params->input_type}" name="{$params->_col}" id="{$params->_col}" value="{$val}" disabled="disabled" readonly="readonly" {$params->input_attrs}  />&nbsp;
                    <button {$edit_buttons_style} class="edit-buttons" type="button" onclick="change_password('#{$params->_col}',this)">Change</button>
HTML;
				break;
			
				case 'hidden' :
					$output[0] = <<<HTML
                    <label {$label_class} for="{$params->_col}">{$params->input_label}</label>&nbsp;
HTML;
					$output[1] = <<<HTML
                    <input {$readonly} {$input_class} type="{$params->input_type}" name="{$params->_col}" id="{$params->_col}" value="{$val}" {$params->input_attrs}  />&nbsp;
HTML;
				break;
				
				case 'textarea' :
					$output[0] = <<<HTML
                    <label {$label_class} for="{$params->_col}">{$params->input_label}</label>&nbsp;
HTML;
					$output[1] = <<<HTML
                    <textarea style="width:500px;" {$disabled} {$readonly} name="{$params->_col}" id="{$params->_col}" rows="{$params->input_size}" {$input_class} {$params->input_attrs} >{$val}</textarea>&nbsp;
HTML;
				break;
				
				case 'checkbox' :
					$checked = ($val) ? "checked=\"checked\"" : '';  
					$output[0] = <<<HTML
                    <label {$label_class} for="{$params->_col}">{$params->input_label}</label>
HTML;
					$output[1] = <<<HTML
                    <input {$readonly} {$disabled} {$checked} {$input_class} type="{$params->input_type}" name="{$params->_col}" id="{$params->_col}" value="{$params->input_values}" {$params->input_attrs}  />&nbsp;
HTML;
				break;
				
				case 'radio' :
					$output[0] = '';
					$output[1] = '';
					foreach ($labels as $key => $option) {
						$checked = ($val == $values[$key] ) ? "checked=\"checked\"" : '';
						$output[0] .= <<<HTML
						<label {$label_class} for="{$params->_col}-{$key}">$option</label>
HTML;
						$output[1] .= <<<HTML
                        <input {$readonly} {$disabled} {$input_class} type="{$params->input_type}" name="{$params->_col}" id="{$params->_col}-{$key}" value="{$values[$key]}" {$params->input_attrs}  />&nbsp;<br />
HTML;
					}
				break;
				
				case 'select' :
					$selected = ( empty($val) ) ? "selected=\"selected\"" : '';
					$multiple = ( empty($params->input_multiple) ) ? '' : 'multiple="multiple"';
					$output[0] = <<<HTML
                    	<label {$label_class} for="{$params->_col}">{$params->input_label}</label>
HTML;
						$output[1] = <<<HTML
            			<select {$readonly} {$disabled} {$input_class} {$multiple} name="{$params->_col}" id="{$params->_col}" {$params->input_attrs} >
                        <option {$selected} value="{$params->input_firstvalue}">{$params->input_firstlabel}</option>
HTML;
					if (!empty($params->qv_sub_col_table)) { $output[1] .= <<<HTML
					<option value="addnew" id="{$params->qv_sub_col_table}||{$params->qv_sub_col}">--Add New--</option>
HTML;
					}
					foreach ($labels as $key => $option) {
						$selected = ($val === $values[$key] ) ? "selected=\"selected\"" : '';
						$output[1] .= <<<HTML
						<option {$selected} value="{$values[$key]}">$option</option>
HTML;
					}
					
					$output[1] .= "</select>&nbsp;";
					
					if (!empty($params->qv_sub_col_table)) { $output[1] .= <<<HTML
                    	<!--<button onclick="view_record('{$params->qv_sub_col_table}','#{$params->_col}')" type="button">→</button>-->
                        <a href="#" onclick="view_record('{$params->qv_sub_col_table}','#{$params->_col}')" class="easyui-linkbutton" iconcls="icon-next" tt="View Record"></a>
HTML;
					}
				break;
				
				default:
					$output = '';
				break;

			}
			return $output;
	}
	
}
		





?>