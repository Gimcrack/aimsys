<?php


class table {
	
	public $table;
	public $cols;
	public $params;
	public $datain;
	public $dataout;
	
	public function __construct($table,$data) {
		
		$this->table = $table;
		$this->friendlytable = ucfirst( str_replace(TABLEPREFIX,'',$table));
		$this->cols  = dbfn::_getfields($table);
		
		$this->params = array();
		foreach($this->cols as $col) {
			$oTemp = new colparams($this->table,$col);
			$this->params[$col] = $oTemp->_getvalues();
		}
		
		$this->datain = $data;
		$this->process();
		
		//fb($this,FIREPHP::TRACE);
	}
	
	public function _getJSON() {
		$data = array();
		$data[] = array();
		$id = key($this->dataout);
		
	
		$joins			= dbfn::_getJoins($this->table);
		
		// Prepare the parameters for the query.
		$oQ = new formfilterpage($this->table,$joins); 	#Get filter and pagination data
		$data[0] = $oQ->_getJSON();
				
		for ($ii=0;$ii<count($this->dataout[$id]);$ii++) {
			$data[1][] = "<input type=\"checkbox\" name=\"cid[]\" id=\"cid_{$ii}\" value=\"{$this->dataout[$id][$ii]}\" />";
		}
		foreach ($this->cols as $col_index => $col) {
			
			if ($this->params[$col]->visible && $this->params[$col]->qv_enabled) { // make sure column is visible and quickview enabled
				$temp = array();
				foreach($this->dataout[$col] as $cell_index => $cell_value) {
					$is_sub = false;
					// Determine if we are dealing with a substitute value
					if ( empty($this->params[$col]->qv_sub_col) ) { # no sub
						$cid = $this->dataout[$id][$cell_index]; 	# pid of current row
						$table_temp = $this->table;
						$cell_val_temp = ( strlen($cell_value) <= 100) ? $cell_value : substr($cell_value,0,100)."...";
						
					} else { # sub one or more cols
						$is_sub = true;
						$sub_col = $this->params[$col]->qv_sub_col;
						$cid = $cell_value; #pid of subbed col
						$table_temp = $this->params[$col]->qv_sub_col_table;
						$sub_col_temp = explode(",,",$sub_col);
						$cell_val_temp = array();
						foreach($sub_col_temp as $sub_col_col) {
							$cell_val_temp[] = $this->dataout[$sub_col_col][$cell_index];
						}
						$cell_val_temp = implode(" - ",$cell_val_temp);
					}
					
					$temp[] =  ($col_index <> 1 && $is_sub === false ) ? 
					$cell_val_temp :
					"<a href=\"javascript:void(0)\" rel=\"{$table_temp}__{$cid}\" class=\"qv-a\" tt=\"View more details for: {$cell_val_temp}\">{$cell_val_temp}</a>";
				}
				$data[] = $temp;
			}
		}
		return json_encode($data);
	}
	
	public function _quickview() {
		if (!empty($this->dataout)) {
		$return = <<<HTML
<div class="qv-table" id="qv_table_{$this->table}" name="{$this->friendlytable}">
	<div class="qv-col">
    	<div class="qv-cell">
        	<input type="checkbox" name="sel_all_{$this->table}" id="chk_sel_all" title="Check/Uncheck All" tt="Check/Uncheck All Rows" />
        </div>
        <div class="qv-cell search"></div>
HTML;
		$id = key($this->dataout);
		for ($ii=0;$ii<count($this->dataout[$id]);$ii++) {
			
			$return .= <<<HTML
    	<div class="qv-cell">
        	<input type="checkbox" name="cid[]" id="cid_{$ii}" value="{$this->dataout[$id][$ii]}" />
        </div>
HTML;
		}
		$return .= "</div>";
		$prev_order = req::_('prev_order');
		foreach ($this->cols as $col_index => $col) {
		
			if ($this->params[$col]->visible && $this->params[$col]->qv_enabled) { // make sure column is visible and quickview enabled
			
			// Prepare the column header
				$class 		= (req::_('order') == $col) ? 'active' : '';
				$dir_icon 	= ($prev_order == $col && req::_('dir') == 'DESC' ) ? 'icon-desc' : 'icon-asc';
				
				$input_qv_search_name = ( empty($this->params[$col]->qv_sub_col)) ? $col : $this->params[$col]->qv_sub_col;
				
				$val = req::_($input_qv_search_name);
				
				$return .= <<<HTML
				<div class="qv-col">
					<div class="qv-cell">
                    <a tt="Click to sort by {$this->params[$col]->qv_label} in ascending order. Click again to sort descending." class="easyui-linkbutton table_heading {$class}" iconcls="{$dir_icon}" href="#" rel="{$input_qv_search_name}"> {$this->params[$col]->qv_label} </a>	<span class="icon"></span>
                    </div>
                    <div class="qv-cell search">
                    	<input type="text" name="{$input_qv_search_name}" size="{$this->params[$col]->input_size}" value="{$val}" tt="Search records by {$this->params[$col]->qv_label}" class="input-qv-cell-search"  />
                    </div>  
HTML;
			// Prepare the cell data
				
					foreach($this->dataout[$col] as $cell_index => $cell_value) {
						$is_sub = false;
						// Determine if we are dealing with a substitute value
						if ( empty($this->params[$col]->qv_sub_col) ) { # no sub
							$cid = $this->dataout[$id][$cell_index]; 	# pid of current row
							$table_temp = $this->table;
							$cell_val_temp = ( strlen($cell_value) <= 100) ? $cell_value : substr($cell_value,0,100)."...";
							
						} else { # sub one or more cols
							$is_sub = true;
							$sub_col = $this->params[$col]->qv_sub_col;
							$cid = $cell_value; #pid of subbed col
							$table_temp = $this->params[$col]->qv_sub_col_table;
							$sub_col_temp = explode(",,",$sub_col);
							$cell_val_temp = array();
							foreach($sub_col_temp as $sub_col_col) {
								@$cell_val_temp[] = $this->dataout[$sub_col_col][$cell_index];
							}
							$cell_val_temp = implode(" - ",$cell_val_temp);
							
						}
						
						$return .=  ($col_index <> 1 && $is_sub === false ) ? 
						"<div class=\"qv-cell\">{$cell_val_temp}</div>" :
						"<div class=\"qv-cell\"><a href=\"javascript:void(0)\" rel=\"{$table_temp}__{$cid}\" class=\"qv-a\" tt=\"View more details for: {$cell_val_temp}\">{$cell_val_temp}</a></div>";
					}
					
								
				$return .= <<<HTML
				</div>
HTML;
			
				
			}
			
			
			
		}
		
		$return .= <<<HTML
            <div class="qv-col">
            	<div class="qv-cell">
                <a href="#" id="lb-go" class="easyui-linkbutton" iconcls="icon-search" tt="Perform search">Go</a> 
               	</div>
                <div class="qv-cell search">
                <a href="#" id="lb-reset" class="easyui-linkbutton" iconcls="icon-reload" tt="Reset search results">Reset</a> 
                </div>
            </div>
HTML;
		
		$return .= <<<HTML
				</div>
HTML;
		//print_r($this->dataout);
		return $return;
		}
		else {
			return false;
		}
	}
	
	private function process() {
		if( !empty($this->datain) ) {
			foreach( $this->datain[0] as $key => $value) {
				$this->dataout[$key] = array();
			}
			
			reset( $this->datain );
			foreach( $this->datain as $row ) {
				
				foreach($row as $key => $value) {
					$this->dataout[$key][] = $value;
				}
			}
		}
	}
	
}



?>