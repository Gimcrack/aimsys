<?php



// Form filter and pagination class
class formfilterpage {
	
	function __construct() {
		// What table are we dealing with.
		$table = req::_('table');
		
		// Filter Params 
		$filter = req::_('filter',-1);
		
		//Store the values in this object
		$this->where = req::_('where',1);
		$this->filter_by = req::_('filter_by',-1);
		$this->filter = (is_array($filter) ) ? implode(',',$filter) : $filter;
		
		// Build Pagination params
		$_SESSION['rows_per_page'] = req::_('rows_per_page',10,'_SESSION');
		$rows_per_page 	= $_SESSION['rows_per_page'] = req::_('rows_per_page',$_SESSION['rows_per_page']);
		$first_record 	= (req::_chk('page')) ? ( req::_('page') -1 ) * $rows_per_page +1 : 1;
		$last_record 	= $first_record + $rows_per_page - 1;
		
		//Store the values in this object
		$this->limit	= $first_record-1 .",".$rows_per_page;
		
		// Build search criteria if needed.
		$q = (req::_chk('q') ) ? req::_('q') : '';
		if ($q == 'Search Records...') {$q = '';}
		$this->where = ($q <> '') ? dbfn::_buildwhere($table,$q) : 1;
		$this->where .= ($this->filter <> -1) ? " AND `{$this->filter_by}` IN ({$this->filter})" : '';
			
	}
	
	function _getWhere() {
		echo $this->where;
	}
	
	function _getLimit() {
		echo $this->limit;
	}
}


?>