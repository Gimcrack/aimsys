<?php

class form extends aimsys {
	
	public $name;
	public $id;
	public $action;
	public $class;
	public $autocomplete = "off";
	public $enctype = "multipart/form-data";
	public $onsubmit = 'return(false)';
	public $_elms; 
	public $_html;
	public $_header;
	
	function __construct($table,$id=0) {

		$fields = ($id==0) ? aimsys::_getinsertfields($table) : aimsys::_getupdatefields($table);
		$this->_elms = (object) array();
		$ii=0;
			
		foreach ($fields as $f) {
			
			$pri = aimsys::_getprimary($table);
			
			if ($id > 0) {
				$select = "SELECT * FROM `$table` WHERE `$pri` = $id";
				$result = new db($select);
				$row = $result[0];
			}
		
			$p = new params($table,$f); // Setup the parameters for the input field. params('table','col')
			$p->input_value = $row[$f]; // Set the value of the field, if edit.
			$p->update_allowed = ($id == 0) ? 'true' : $p->update_allowed;
			
			$parent = $p->input_type; // Mine the type of input from the param set
			
			$i = new $parent($p); // Create new instance of input type
			
			$this->_elms->$ii->label = $i->_label->get(); // Get the label
			$this->_elms->$ii->input = $i->get(); // Get the element
			$this->_elms->$ii->params = $i; // Get the element params
			$ii++;
			
		}
		
		$p = new params();
		$p->input_name = 'frm_name';
		$p->input_type = 'hidden';
		$p->input_value = $this->name;
		$i = new hidden($p);
		
		$this->_elms->$ii->input = $i->get(); // Get the element
		$this->_elms->$ii->params = $i; // Get the element params
		$ii++;
		
		$p = new params();
		$p->input_name = 'table_name';
		$p->input_type = 'hidden';
		$p->input_value = $table;
		$i = new hidden($p);
		
		$this->_elms->$ii->input = $i->get(); // Get the element
		$this->_elms->$ii->params = $i; // Get the element params
		$ii++;
		
	}
	
	public function attr() {
		$a = array();
		foreach ($this as $key => $value) {
			if ($value <> 'false' && strpos($key,'_') === false) {
				$a[] = $key.'='."\"$value\"";
			}
		}
		return implode(" ",$a);
	}
	
	function get($qv=false,$vert=true) {
		$return .= "<form class='form_table'";
		$return .= $this->attr();
		$return .= "><table align=\"center\" >\n";
		if ($vert) {
			$return .= "<col width=\"20%\"><col><tr><th colspan=\"2\">" . $this->_header . "</th></tr>";
		}
		foreach($this->_elms as $elm) {
			if (!$qv or $elm->params->_quickview) {
				if ($vert) { // Vertical alignment with label in left column and field in right
					$return .= "<tr><th nowrap=\"nowrap\" align=\"right\">" . $elm->label . "</th>\n";
					$return .= "<td>" . $elm->input . "</td></tr>\n";
				}
			}
		}
		
		$return .= '<tr>
  <td align="right"><button type="reset" name="btn_reset" id="btn_reset">Reset</button></td>
  <td><button type="button" name="btn_submit" id="btn_submit" onclick="ajax_submit(this.form);">Submit</button></td>
  </tr></table></form>';
	
		return $return;
	}

}

class params extends aimsys {
	
	// Insert Form Params
	public $input_prompt		= NULL;
	public $input_type 			= NULL;
	public $input_required		= NULL;
	public $input_validtype		= NULL;
	
	// View Params
	public $view_quickview		= NULL;
	
	// Update Form Params
	public $update_allowed		= NULL;
	
	public $input_name			= NULL;
	public $input_value			= NULL;
	
	function __construct($table='',$col='') {
		
		if ($table <> '' and $col <> '') {
			$select = "SHOW FULL COLUMNS FROM `$table` WHERE `field` LIKE '$col'";
			$result =  parent::_query($select);
			$comment = $result[0]['Comment'];
			$comment = explode('||',$comment);
			$ii = 0;
			foreach ($this as $key => $value) {
				$this->$key = $comment[$ii];
				$ii++;
			}
			$this->input_name = $col;
		}
		
		
	}
}

class text extends aimsys {
	
	// Set up attributes	
	public $type;
	public $name;
	public $id;
	public $class = 'false';
	public $value = 'false';
	public $validtype = 'false';
	public $readonly = 'false';
	public $disabled = 'false';
	
	public $_child = '';
	public $_childfirst = 1;
	public $_quickview = 1;
	
	function __construct($o) {
		$this->type = $o->input_type;
		$this->name = $o->input_name;
		$this->id = $o->input_name;
		$this->class = ($o->input_required ) ? 'required-input' : 'optional-input';
		$this->value = $o->input_value;
		$this->validtype = $o->input_validtype;
		$this->readonly = ($o->update_allowed ) ? 'false' : 'true';
		$this->_label = new inputlabel($o);
		$this->_quickview = $o->view_quickview;
	}
	
	public function attr() {
		$a = array();
		foreach ($this as $key => $value) {
			if ($value <> 'false' && strpos($key,'_') === false) {
				$a[] = $key.'='."\"$value\"";
			}
		}
		return implode(" ",$a);
	}
	
	public function get() {
		$return .= "<input ";
		$return .= $this->attr();
		$return .= " />";
		return $return;
	}
}

class_alias('text','hidden');
class_alias('text','password');

class inputlabel extends aimsys {
	
	// Set up attributes
	public $class = 'required-label';
	public $for = 'id';
	public $label = 'Label : ';
	
	function __construct($o) {
		$this->for = $o->input_name;
		$this->label = $o->input_prompt;
		$this->class = ($this->required <> 'false') ? 'required-label' : 'optional-label';
	}
	
	public function attr() {
		$a = array();
		foreach ($this as $key => $value) {
			if ($key <> 'label') {
				$a[] = $key.'='."\"$value\"";
			}
		}
		return implode(" ",$a);
	}
	
	public function get() {
		$return .= "<label ";
		$return .= $this->attr();
		$return .= ">";
		$return .= $this->label;
		$return .= "</label>";
		return $return;
	}
	
}

class select extends aimsys {
	
	// Set up attributes
	public $name = 'name';
	public $id = 'id';
	public $class = 'class';
	public $title;
	public $multiple = 'true';
	public $readonly = 'false';
	public $disabled = 'false';
	
	public $_label = '';
	public $_child = '';
	public $_childfirst = 0;
	public $_quickview = 1;
	
	function __construct($o) {
		$a = explode(",",$o->input_validtype);
		$table = $a[0];
		$this->name = $o->input_name;
		$this->id = $o->input_name;
		$this->multiple = ($a[1] ) ? 'true' : 'false';
		$this->class = ( $this->multiple == 'true' ) ? 'jquery-multiselect' : 'jquery-singleselect';
		$this->readonly = ($o->update_allowed ) ? 'false' : 'true';
		
		$this->title = $o->input_prompt;
		$this->_label = new inputlabel($o);
		$this->_child = new selectoptions($o);
		$this->_child->getFromTable($table);
		$this->_child->selected = $o->input_value;
		$this->_quickview = $o->view_quickview;
	}
	
	public function attr() {
		$this->name = str_replace('[]','',$this->name);
		if ($this->multiple == 'true') {
			$this->name .= '[]';
		}
		$a = array();
		foreach ($this as $key => $value) {
			if ($value <> 'false' && strpos($key,'_') === false) {
				$a[] = $key.'='."\"$value\"";
			}
		}
		return implode(" ",$a);
	}
	
	public function get() {
		$return .= "<select ";
		$return .= $this->attr();
		$return .= ">\n";
		$return .= $this->_child->get();
		return $return;
	}
	
	public function closetag() {
		return "</select>";
	}
}

class selectoptions extends aimsys {
	
	// Set up attributes
	public $labels;
	public $values;
	public $selected = '1|3|5';
	
	public function get() {
		
		$labels = explode("|",$this->labels);
		$values = explode("|",$this->values);
		$selected = explode("|",$this->selected);
		$selected = (is_array($selected) ) ? $selected : array($selected);
		
		
		foreach($values as $key => $value) {
			if ( in_array($value,$selected) ) {
				$return .= "<option selected=\"selected\" value=\"$value\">" . $labels[$key] . "</option>\n";
			}
			else {
				$return .= "<option value=\"$value\">" . $labels[$key] . "</option>\n";
			}
		}
		
		$return .= "</select>";

		return $return;
	}
	
	public function getFromTable($table) {
		$pri = aimsys::_getprimary($table);
		$query = "SELECT * FROM `$table` ORDER BY `$pri` ASC";
		$result = new db($query,true);
		
		$values = array();
		$labels = array();
		
		foreach ($result as $key => $row) {
			if (is_numeric($key) ) {
				$values[] = $row[0];
				$labels[] = $row[1];
			}
		}
		$this->labels = implode("|",$labels);
		$this->values = implode("|",$values);
	}
	
}

