<?php

// Form filter and pagination class
class formfilterpage {
	
	function __construct($table,$joins=false) {
		global $option;
		global $view;
		
		$this->table = $table;
		$this->friendlytable = ucfirst( str_replace(TABLEPREFIX,'',$table));
		$this->option = $option;
		$this->view = $view;
		$this->frm_name = "{$table}_filter_form";
		$this->joins = $joins;
		
		// Are we actively performing a filter
		$this->apply = ( req::_('frm_name') == $this->frm_name ) ? true : false;
		
		if ($this->apply) { // actively applying this filter to the current table
			// Filter Params 
			$filter 		= req::_('filter',-1);
			$this->filter 	= (is_array($filter) ) ? implode(',',$filter) : $filter;
			
			// Build Pagination params
			$_SESSION['rows_per_page'] = req::_('rows_per_page',ROWS_PER_PAGE,'_SESSION');
			$rows_per_page 	= $_SESSION['rows_per_page'] = req::_('rows_per_page',$_SESSION['rows_per_page']);
			$first_record 	= (req::_chk('page')) ? ( req::_('page') -1 ) * $rows_per_page +1 : 1;
			$last_record 	= $first_record + $rows_per_page - 1;
			
			// Build where statement
			$search_criteria		= req::_('search_criteria','');
			if( !empty($search_criteria) ) {
				$search_criteria		= urldecode($search_criteria);
				$pairs					= explode('&',$search_criteria);
				$this->search_criteria  = array();
				foreach($pairs as $pair) {
					 $p		=	explode('=',$pair);
					 $this->search_criteria[$p[0]] = $p[1];
				}
			}
			$this->where 					= (!empty($this->search_criteria)) ? dbfn::_buildwhere2( $this->search_criteria ) : 1;
			
			// Build order criteria
			$this->order = req::_('order', dbfn::_getPrimary($table) );
			$this->dir   = req::_('dir','ASC');
			
			
		} else {			// not actively applying any filters to this table
			$this->filter 		= -1;
			$rows_per_page 		= $_SESSION['rows_per_page'];
			$first_record 		= 1;
			$last_record 		= $first_record + $rows_per_page - 1;
			$this->where		= 1;
			
			$this->order		= dbfn::_getPrimary($table);
			$this->dir			= 'ASC';
		}
		
		$this->limit			= $first_record-1 .",".$rows_per_page;
		$this->page 			= req::_('page',1);
		$this->rows_per_page 	= $rows_per_page; 
		
		// Find total records 
		$select2 = "SELECT count(*) AS `c` FROM `$table` {$joins} WHERE {$this->where}";
		$oTemp = new db($select2);
		$this->total_rows 	= $oTemp->_getResult();
		$this->first_record	= ($this->rows_per_page < $this->total_rows) ? $first_record : 1;
		$this->last_record 	= ($this->total_rows <= $last_record) ? $this->total_rows : $last_record;
		$this->total_pages	= ceil( $this->total_rows / $this->rows_per_page );
		
		// Update limit and page number if necessary
		$this->limit			= $this->first_record-1 .",".$rows_per_page;
		$this->page 			= ($this->rows_per_page < $this->total_rows) ? $this->page : 1;
		
		
		// Row message: Displaying x - y of z records...
		$this->row_message = ($this->total_rows > 0) ? 
			"Showing ".$this->first_record." - ".$this->last_record." of ".$this->total_rows ." rows." :
			"<font style='color:red'>No records found.</font>";	
		
		$this->row_message_class = ($this->total_rows > 0) ? 
			"ui-state-success" :
			"ui-state-error";
	}
	
	function _getWhere() {
		return $this->where;
	}
	
	function _getLimit() {
		return $this->limit;
	}
	
	function _getOrder() {
		return "`{$this->order}` {$this->dir}";
	}
	
	function _getQuery() {
		$return = "SELECT * FROM `{$this->table}`
	{$this->joins}
	WHERE {$this->where} ORDER BY `{$this->order}` {$this->dir} LIMIT {$this->limit}";
		return $return;
	}
	
	function _update($oData) {
		$this->message 		= $oData->ret['message'];
		$this->msg_class 	= @$oData->ret['msg_class']; 	
	}
	
	function _getFormHTML() {
		
		$this->msg_class .= (!empty($this->msg_class) ) ? " active" : '';
		$this->q = (!empty($this->q)) ? $this->q : "Search Records...";
	
	$html = <<<HTML
		<div>
    <form target=".main-content.active" enctype="multipart/form-data" onsubmit="return(false)" name="{$this->frm_name}" id="{$this->frm_name}" class="form_filter" action="index.php?controller=qv&view={$this->view}" method="post">
        <input type="hidden" name="frm_name" value="{$this->frm_name}" />
        <input type="hidden" name="table" value="{$this->table}" />
        <input type="hidden" id="hid_prev_order" name="prev_order" value="{$this->order}" />
        <input type="hidden" id="hid_order" name="order" value="{$this->order}" />
        <input type="hidden" id="hid_dir" name="dir" value="{$this->dir}"  />
        <input type="hidden" id="hid_page_number" name="page" value="{$this->page}" />
        <input type="hidden" id="hid_rows_per_page" name="rows_per_page" value="{$this->rows_per_page}" />
        <input type="hidden" id="hid_total_pages" name="total_pages" value="{$this->total_pages}" />
        
        <a href="#" id="lb-new" class="easyui-linkbutton" name="{$this->friendlytable}" rel="{$this->table}__new" iconcls="icon-add" tt="Create a new record">New</a> 
        <div class="toolbar-separator"></div>
        <a href="#" id="lb-refresh" class="easyui-linkbutton" iconcls="icon-reload" tt="Refresh Record">Refresh</a> 
    </form>    
    
</div>
HTML;
	
	return $html;
			
	}
	
	function _getFooterHTML() {
		$rows_per_page_options 	= array(10,25,50,100,200,10000);
		$rows_per_page_labels	= array(10,25,50,100,200,'All');
		$rows_per_page_select 	= "<label for=\"rows_per_page\">Rows Per Page : </label><select class=\"ui-corner-all\" id=\"rows_per_page\" name=\"rows_per_page\">\n";
		foreach($rows_per_page_options as $key=>$val) {
			$rows_per_page_select .= ($this->rows_per_page == $val) ? "<option selected=\"selected\" value=\"{$val}\">{$val}</option>\n" : "<option value=\"{$val}\">{$val}</option>\n";
		}
		
		$page_number_readonly	= ($this->total_pages > 1) ? "" : "readonly=\"readonly\" class=\"ui-state-disabled\"";
		$rows_per_page_select  .= "</select>\n";
		
		
		$cls_first = ($this->page == 1 || $this->total_pages == 1) ? 'disabled l-btn-disabled' : '';
		$cls_prev  = ($this->page == 1 || $this->total_pages == 1) ? 'disabled l-btn-disabled' : '';
		$cls_next  = ($this->page == $this->total_pages) ? 'disabled l-btn-disabled' : '';
		$cls_last  = ($this->page == $this->total_pages) ? 'disabled l-btn-disabled' : '';
		
		$html = <<<HTML
        	<span id="spn_pagination_controls">
        	{$rows_per_page_select}
             <div class="toolbar-separator"></div>
             <a href="#" id="lb-first" class="easyui-linkbutton {$cls_first}" iconcls="icon-first" tt="First Page"></a>
             <a href="#" id="lb-prev" class="easyui-linkbutton {$cls_prev}" iconcls="icon-prev" tt="Previous Page"></a>
             <div class="toolbar-separator"></div>
             Page <input class="ui-corner-all" size="3" id="txt_page_number" name="page" value="$this->page" {$page_number_readonly}  /> of <span id="spn_total_pages">{$this->total_pages}</span>
             <div class="toolbar-separator"></div>
             <a href="#" id="lb-next" class="easyui-linkbutton {$cls_next}" iconcls="icon-next" tt="Next Page"></a>
             <a href="#" id="lb-last" class="easyui-linkbutton {$cls_last}" iconcls="icon-last" tt="Last Page"></a>
             <div class="toolbar-separator"></div>
             </span>
        	<span id="row_message" class="ui-corner-all {$this->row_message_class}">{$this->row_message}</span>
            <!--<span id="spn_message" class="msg ui-corner-all {$this->msg_class}">-->{$this->message}<!--</span>-->

            
HTML;
		//pre($this);
		return $html;
		
	}
	
	function _getJSON() {
		$btn_first = ($this->page == 1 || $this->total_pages == 1) ? 0 : 1;
		$btn_prev  = ($this->page == 1 || $this->total_pages == 1) ? 0 : 1;
		$btn_next  = ($this->page == $this->total_pages || $this->total_pages == 1) ? 0 : 1;
		$btn_last  = ($this->page == $this->total_pages || $this->total_pages == 1) ? 0 : 1;
		
		$return = array();
		$return['buttons'] = array(
			"first_page" 	=> $btn_first,
			"prev_page"		=> $btn_prev,
			"next_page"		=> $btn_next,
			"last_page"		=> $btn_last,
		);
		$return['page'] = $this->page;
		$return['total_pages'] = $this->total_pages;
		$return['rows_per_page'] = $this->rows_per_page;
		$return['total_rows'] = $this->total_rows;
		$return['row_message'] = ($this->total_rows > 0) ? 
			"Showing ".$this->first_record." - ".$this->last_record." of ".$this->total_rows ." rows." :
			"<font style='color:red'>No records found.</font>";
		$return['row_message_class'] = ($this->total_rows > 0) ? 
			"ui-state-success" :
			"ui-state-error";
		
		//fb($return,FirePHP::TRACE);
		
		return $return;	
		
	}
}


?>