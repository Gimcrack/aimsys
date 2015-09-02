$(document).ready( function() {
	$frm = $('.main-content.active .part.active #frm_editor');
	//$("input:not(':hidden'),select",$frm).attr('disabled',true);
	
	if (Tabs.activeTab.editing && !Tabs.activeTab.is_new) {
		$frm = $('.main-content.active .part.active #frm_editor');
		$qs = $('input:hidden',$frm).serialize();
		checkout($qs);
	}
	
	$target = $('.part.active','.main-content.active');
	$('.easyui-linkbutton',$target).unbind('click');
			
	// Refresh Record
	$('#lb-refresh',$target).click(function() {
		Tabs.activeTab.refreshTab();
	});
	
	// Edit Record
	$('#lb-edit',$target).click(function() {
		Tabs.activeTab.editing = true;
		checkout_message('Refreshing...');
		Tabs.activeTab.refreshTab();
	});
	
	//Save changes and checkin
	$('#lb-save',$target).click(function() {
		$frm = $('.main-content.active .part.active #frm_editor');
		$frm_valid = validate_form($frm);
		if ($frm_valid) {
			editor_submit( $frm );
		}
	});
	
	//Reset Record
	$('#lb-reset',$target).click(function() {
		$frm = $('.main-content.active .part.active #frm_editor');
		$frm[0].reset();
	});
	
	//Close record
	$('#lb-close',$target).click(function() {
		Tabs.activeTab.editing = false;
		Tabs.activeTab.closeTab();
	});
	
	//Cancel changes
	$('#lb-cancel',$target).click(function() {
		$frm = $('.main-content.active .part.active #frm_editor');
		(Tabs.activeTab.is_new) ?
		// Prompt the user if they want to cancel the new record
		noty({
		  text: 'Are you sure you want to cancel new record?<br/>This action cannot be undone!',
		  modal: true,
		  layout:'center',
		  buttons: [
			{addClass: 'btn btn-primary', text: 'Keep Editing', onClick: function($noty) {
				$noty.close();
			  }
			},
			{addClass: 'btn btn-danger', text: 'Cancel New Record', onClick: function($noty) {
				$noty.close();
				Tabs.activeTab.editing = false;
				Tabs.activeTab.closeTab();
			  }
			}
		  ]
		}) : // Prompt the user if they want to cancel the changes to the old record
		noty({
		  text: 'Are you sure you want to cancel changes?<br/>This action cannot be undone!',
		  modal: true,
		  layout:'center',
		  buttons: [
			{addClass: 'btn btn-primary', text: 'Keep Editing', onClick: function($noty) {
				$noty.close();
			  }
			},
			{addClass: 'btn btn-danger', text: 'Cancel Changes', onClick: function($noty) {
				$noty.close();
				Tabs.activeTab.editing = false;
				$frm[0].reset();
				checkin($qs);
			  }
			}
		  ]
		});
	});
	
	$('button',$target).button();
	
	$('select',$target).change( function() {
		if ( $(this).val() == 'addnew') {
			$temp = $(':selected',this).attr('id').split('||');
			$table = $temp[0];
			$col = $temp[1];
			console.log($col);
			$friendly_table = $table.replace('aim','').replace('_',' ').ucwords();
			$friendly_record = '[new]';
			Tabs.activeTab.popup($table,$(this).attr('id'),$friendly_table + ' . ' + $friendly_record);
			//Tabs.relatedCol = $(this).attr('id');
		}
	});
	
});