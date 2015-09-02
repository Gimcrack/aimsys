$(document).ready( function() { 
	/* global code */
	
	$('.easyui-linkbutton').linkbutton({plain:true});	
	$('.easyui-splitbutton').splitbutton({plain:true});
	$('.easyui-menubutton').menubutton({plain:true});
	
	$("input[type=text],input[type=password],input[type=textarea]").addClass('ui-state-default').addClass('ui-corner-all');
	$("select.editor-element").addClass('ui-state-default').addClass('ui-corner-left');
	
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
	
	$('select',active_part).change( function() {
		if ( $(this).val() == 'addnew') {
			$temp = $(':selected',this).attr('id').split('||');
			$table = $temp[0];
			$col = $temp[1];
			console.log($col);
			$friendly_table = $table.replace('aim','').replace('_',' ').ucwords();
			$friendly_record = '[new]';
			Tabs.activeTab.popup($table,$friendly_table + ' . ' + $friendly_record);
			Tabs.relatedCol = $(this).attr('id');
		}
	});

});