$(document).ready( function() {
	/* page specific code */

	$('#lb1.easyui-linkbutton').click( function() { // Go button
		$('#hid_page_number','.part.active .form_filter').val( 1 );
		ajax_submit_filter();
	});
	
	$('#lb2.easyui-linkbutton').click( function() { // Reset button
		$('.input-qv-cell-search',active_part).val('');
		$('#hid_page_number',active_part).val( 1 );
		$('#hid_dir',active_part).val('ASC');
		$('#hid_order',active_part).val('');
		ajax_submit_filter();
	});
	
	$('.main-content.active .part.active #lb-refresh').click(function() { // Refresh button
		ajax_submit_filter();
	});
});

