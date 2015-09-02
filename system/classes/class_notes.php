<?php


class Notes {
	
	public $option;		// controller option
	
	public $table;		// name of the table
	public $rowid;		// id of the row
	
	public $oDB;		// the oDB object
	public $notes;		// the data as stored in the db
	
	public $is_update;  // is this an update operation
	public $changes;	// changes made to the data
	public $popup;		// is this a popup or full tab
	public $editing;	// is this record currently being edited
	
	public function __construct($table,$cid='new') {
		global $view;
		
		$this->option 	= $view;
		$this->table 	= $table;
		$this->cid 		= $cid;
		$this->cols  	= dbfn::_getfields("aimnotes");
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
	
	public function _getNotes() {
		if ( is_numeric($this->cid) ) {
			$select 		= "SELECT * FROM `aimnotes` WHERE `not_table` = '{$this->table}' AND `not_cid` = '{$this->cid}' ORDER BY `not_timestamp` DESC";
			$this->oDB 		= new db($select);
			$this->notes 	= $this->oDB->rows;
		}
	}
	
	public function _setData($data) {
		$this->data 	= $data;
	}
		
	public function _isUpd() {
		$this->is_update = (!empty($this->datadb)) ? true : false;
		return $this->is_update;
	}
	
	
	
}
		





?>