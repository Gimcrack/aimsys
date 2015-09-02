$(document).ready( function() { 
	/* global code */
	
	// -------------- FILTER FORM CODE ------------------
	// Submit filter form when enter is pressed
	$('.input-qv-cell-search',active_part).unbind('keypress').keypress( function(e) {
		if(e.which == 13) {
			$(this).blur();
			$('#hid_page_number',active_part).val( 1 );
			ajax_submit_filter();
		}
	});
	
	// Submit filter form when rows per page is changed
	$('select#rows_per_page',active_part).unbind('change').change( function() {
		$('#hid_rows_per_page',active_part).val( $(this).val() );
		$('#hid_page_number',active_part).val( 1 );
		ajax_submit_filter();
	});
	
	// Submit filter form when page number is pressed
	$('input#txt_page_number',active_part).unbind('keypress').keypress(function(e) {
		  $(this).val( $(this).val().formatInteger() );
		  if(e.which == 13) {
			$(this).blur();
			ajax_submit_filter();
		}
    }).unbind('change').change( function() {
		if ($(this).val()*1 > $('#hid_total_pages',active_part).val()*1 ) $(this).val( $('#hid_total_pages',active_part).val() );
		$('#hid_page_number',active_part).val( $(this).val() );
		ajax_submit_filter();	
	});
	
	// -------------- QUICKVIEW TABLE STUFF -----------------
	$(".qv-table input[type=checkbox]",active_part).unbind('change').change( function() {
		$index = $(this).parent().index();
		$qv_table = $(this).closest('.qv-table');
		$chk_all = $('#chk_sel_all',$qv_table);
		
		$all_checks = $("input[type=checkbox]",$qv_table);
		
		if ($index == 0) { // the select/deselect all checkbox was clicked
			$all_checks.prop('checked', $(this).prop('checked') );
		}
		else { // a different checkbox was clicked.
			$num_checked 	= $("input:gt(0):checked",$qv_table).length;
			$num_unchecked 	= $("input:gt(0):not(:checked)",$qv_table).length;
			if($num_checked == 0) 						{	$chk_all.prop('checked',false); $chk_all.prop('indeterminate',false); }
			if($num_unchecked == 0)						{	$chk_all.prop('checked',true);	$chk_all.prop('indeterminate',false); }
			if($num_checked > 0 && $num_unchecked > 0 ) {	$chk_all.prop('indeterminate',true);  }
		}
	});
	
	$('#content-header-bar ul li').unbind('click').click( function() { 
		$(this).parent().find('li.active').removeClass('active');
		$(this).addClass('active');
		
		$index = $(this).index();
		
		$('#main .main-content.active .content-body .part').removeClass('active').eq($index).addClass('active');
		Tabs.activeTab.subtab = $index;
		Tabs.activeTab.refreshBindings();
		//Tabs.activeTab.refreshTab();
	});
	
	$('.qv-table .qv-col .qv-cell').mouseover( function() {
		$table = $(this).closest('.qv-table');
		$index = $(this).index();
		if( $index > 1 ) {
			$('.qv-col',$table).each( function(i,elm) {
				$('.qv-cell',elm).eq($index).addClass('highlight')
			});		
		}
	}).mouseout( function() {
		$table = $(this).closest('.qv-table');
		$index = $(this).index();
		if( $index > 1 ) {
			$('.qv-col',$table).each( function(i,elm) {
				$('.qv-cell',elm).eq($index).removeClass('highlight');
			});
		}
	})
	$('.qv-a',active_part).unbind('click').click( function() {
		var $qvtable = $(this).closest('.qv-table');
		var friendlytable = $qvtable.attr('name'); //friendly table name
		var friendlyrecord = $(this).html();
		var table_cid = $(this).attr('rel');
		
		Tabs[table_cid] = new Tab('editor');
		Tabs[table_cid].newTab(table_cid,friendlytable + ' . ' + friendlyrecord);
	});
	
	$('.input_text',active_part).unbind('keypress').keypress(function(e) {
		if(e.which == 13) {
			$(this).blur();
			ajax_submit_filter();
		}
	});
	
	$('.sb',active_part).focus(function() {
		if ($(this).val() == 'Search Records...') {
			$(this).val('');
		}
	}).blur(function() {
		if ($(this).val() == '') {
			$(this).val('Search Records...');
		}
	});
	
	$('.easyui-linkbutton').linkbutton({plain:true});	
	$('.easyui-splitbutton').splitbutton({plain:true});
	$('.easyui-menubutton').menubutton({plain:true});
	
	$("input[type=text],input[type=password],input[type=textarea]").addClass('ui-state-default').addClass('ui-corner-all');
	$("select.editor-element").addClass('ui-state-default').addClass('ui-corner-left');
	
	$('.jquery-singleselect',active_part).multiselect({
	multiple: false,
   header: false,
   selectedList: 1
	});
	
	$('.jquery-multiselect',active_part).multiselect({
	selectedList: 2,
	multiple: true,
	checkAllText : 'all',
	uncheckAllText : 'all',
	}).multiselectfilter();
	
	
	// Tooltip function
	$('[tt]').unbind('mouseenter');
	$('[tt]').unbind('mouseleave');
	$('[tt]').mouseenter( function() { // status bar message
		status_timeout = setTimeout("noty_status('"+$(this).attr('tt')+"')",1000);
		//noty_status( $(this).attr('tt') );
	}).mouseleave( function() {
		clearTimeout(status_timeout);
		//$.noty.closeAll();
	});
	
	$("input[type=text],input[type=password],input[type=textarea],select.editor-element",active_part).hover(function(){
	  $(this).addClass("ui-state-hover");
	   },function(){
	  $(this).removeClass("ui-state-hover");
	   });
	
	$('.easyui-linkbutton.disabled',active_part).addClass('l-btn-disabled');
	
	$("#lb-first:not('.disabled')",active_part).unbind('click').click( function() {
		$('input#txt_page_number',active_part).val( 1 ).trigger('change');		
	});
	
	$("#lb-prev:not('.disabled')",active_part).unbind('click').click( function() {
		$('input#txt_page_number',active_part).val( $('input#txt_page_number',active_part).val()*1-1 ).trigger('change');		
	});
	
	$("#lb-next:not('.disabled')",active_part).unbind('click').click( function() {
		$('input#txt_page_number',active_part).val( $('input#txt_page_number',active_part).val()*1+1 ).trigger('change');		
	});
	
	$("#lb-last:not('.disabled')",active_part).unbind('click').click( function() {
		$('input#txt_page_number',active_part).val( $('#hid_total_pages',active_part).val() ).trigger('change');		
	});
	$('a.table_heading',active_part).unbind('click').click( function() {
		$col = $(this).attr('rel');
		$dir = $(this).attr('iconcls');
		
		$new_dir = ($col == $('#hid_prev_order',active_part).val() && $dir == 'icon-asc') ? 'DESC' : 'ASC';
		
		$('#hid_order',active_part).val( $col );
		$('#hid_dir',active_part).val( $new_dir );
		
		ajax_submit_filter();
		
	});
	
	$('fieldset').addClass('ui-corner-all');
	
	// Validation stuff
	$("[validType='Phone Number']").unbind('keyup').keyup( function() {
		$(this).val( formatPhone( $(this).val() ) );
	});
	
	$("[validType='Zip Code']").unbind('keyup').keyup( function() {
		$(this).val( formatZip( $(this).val() ) );
	});
	
	$("[validType='US State']").unbind('keyup').keyup( function() {
		$(this).val( formatUC( $(this).val() ) );
	});
	
	$("[validType='number']").unbind('keyup').keyup( function() {
		$(this).val( formatNumber( $(this).val() ) );
	});
	
	$("[validType='integer']").unbind('keyup').keyup( function() {
		$(this).val( formatInteger( $(this).val() ) );
	});

});