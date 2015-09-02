$(document).ready( function() {
	/* page specific code */

	$('#lb-go',active_part).unbind('click').click( function() { // Go button
		$('#hid_page_number','.part.active .form_filter').val( 1 );
		ajax_submit_filter();
	});
	
	$('#lb-reset',active_part).unbind('click').click( function() { // Reset button
		$('.input-qv-cell-search',active_part).val('');
		$('#hid_page_number',active_part).val( 1 );
		$('#hid_dir',active_part).val('ASC');
		$('#hid_order',active_part).val('');
		ajax_submit_filter();
	});
	
	$('#lb-refresh',active_part).unbind('click').click(function() { // Refresh button
		ajax_submit_filter();
	});
	
	$('#lb-new',active_part).unbind('click').click(function() { // New button
		var friendlytable = $(this).attr('name'); //friendly table name
		var table_cid = $(this).attr('rel');
		
		Tabs[table_cid] = new Tab('editor');
		Tabs[table_cid].newTab(table_cid,friendlytable + ' . [new]');
	});
});

