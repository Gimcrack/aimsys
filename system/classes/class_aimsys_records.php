<?php

class aimsys_records {
	
	// Setup Vars
	public $table;
	
	// Setup table display parameters 
	public $order;
	public $order_dir;
	public $limit;
	
	// Setup intermediate values
	public $page;
	public $rows_per_page;
	public $first_record;
	public $last_record;
	
	// Setup query parameters
	public $filter_group;
	public $q;
	public $where;
	
	public function __construct( $table ) {
		
		$_SESSION['rows_per_page'] = req::_('rows_per_page',10,'_SESSION');
		
		$this->table = $table;
		
		$this->order 			= req::_('order', dbfn::_getprimary($table) );
		$this->order_dir 		= req::_('dir','ASC');
		$this->page				= req::_('page','1');
		$this->rows_per_page 	= $_SESSION['rows_per_page'] = req::_('rows_per_page',$_SESSION['rows_per_page']);
		$this->filter_field		= req::_('filter_field');
		$this->filter_group		= req::_('filter_group',-1);
		$this->filter_group 	= (is_array($this->filter_group) ) ? implode(',',$this->filter_group) : $this->filter_group; 
		$this->first_record 	= (req::_chk('page')) ? ( req::_('page') -1 ) * $this->rows_per_page +1 : 1;
		$this->last_record 		= $this->first_record + $this->rows_per_page - 1;
		$this->limit 			= $this->first_record-1 .",".$this->rows_per_page;
		$this->q				= (req::_('q') == 'Search Records...') ? '' : req::_('q');
		
		
		$this->where = ($this->q <> '') ? dbfn::_buildwhere($table,$this->q) : 1;
		$this->where .= ($this->filter_group <> -1) ? " AND `{$this->filter_field}` IN ({$this->filter_group})" : '';	
	
	}
	
	
	
}


?>