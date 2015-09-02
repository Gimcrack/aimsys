// JavaScript Document
var Tabs = {};
Tabs.activeTab = null;
Tabs.lastActiveTab = null;
Tabs.relatedCol = null;

function closeAllTabs() {
	delete Tabs['activeTab'];
	delete Tabs['lastActiveTab'];
	delete Tabs['relatedCol'];
	$.each( Tabs, function(key,tab) {
		tab.closeTab();
	});
}

function firstNonSpecial(o) {
    $keys = $.map(o, function(value,key) {
		if (key != "activeTab" && key != "lastActiveTab" && key != "relatedCol" && typeof key !== "function") {
			return key;
		}
	});
	return $keys[0];
}

function Tab (type) {
	this.id 		= null; // id of tab e.g. users
	this.table		= null; // table of the current tab
	this.$id 		= null; // jquery id of tab e.g. #users
	this.name 		= null; // name of tab e.g. Users
	this.$tab 		= null; // li of tab
	this.$content 	= null; // div of content
	this.type		= (typeof type === "undefined") ? 'qv' : type; // type of tab
	this.editing	= false; // is this tab currently is edit mode.
	this.json		= []; // json string of data
	this.oJSON		= []; // json objects
	this.subtab 	= 0; // index of active subtab;
	this.popupTab	= [];
	this.tempVals   = [];
	
	this.newTab = function(id,name) {
		this.id	  	= id;
		this.table  = id.substr(0,id.indexOf('__') );
		this.$id 	= '#'+this.id;
		this.name 	= name;
		this.is_new = ( this.id.indexOf('__new') !== -1 ) ? true : false;
		this.editing = (this.is_new) ? true : this.editing;
	
		// if tab exists activate it, otherwise create it
		( this.tabExists() ) ? this.showTab() : this.createTab();
		
	}
	
	this.createTab = function() {
		
		// Create html elements and append them to the document
		(this.type == 'qv') ? 
		$('<li/>',{"class":"ui-corner-top","id":this.id,"type":this.type,"name":this.name}).html(this.name + '<div class="close-icon"></div>').appendTo('#tab-bar ul') :
		$('<li/>',{"class":"ui-corner-top","id":this.id,"type":this.type,"name":this.name}).html(this.name).appendTo('#tab-bar ul');
		$('<div/>',{"class":"main-content","id":this.id}).appendTo('#main');
		
		this.$tab = $(this.$id,'#tab-bar ul');
		this.$content = $(this.$id,'#main');
		
		this.getHTML();
		this.showTab();
		
		this.resetTabBindings();
	}
	
	this.update_select_options = function($selected_value) {
		$o = {};
		$o.table = Tabs.activeTab.table;
		$o.col = this.popupTab.$select_id;
		$o.value = $selected_value;
		
		$target = $('#'+Tabs.activeTab.popupTab.$select_id,Tabs.activeTab.$content);
		
		$url = "index.php?controller=input&option=update_options";
		$qs = $.param($o);
		
		maskAll('Updating...');
		
		aj = $.ajax({
			url:$url,
			data:$qs,
			type:'POST',
			success:function(response){ 
				$target.html(response);
				unmaskAll();
			}
	   });
		
	}
	
	this.popup = function(table,$select_id,popuptitle) {
		if( $('#div_tab_popup','#main').length > 0) $('#div_tab_popup','#main').remove();
		$('<div/>',{id:'div_tab_popup'}).hide().appendTo('#main');
		
		this.popupTab.table = table;
		this.popupTab.title = popuptitle;
		this.popupTab.dlog = null;
		this.popupTab.$divid = '#div_tab_popup'; 
		this.popupTab.$select_id = $select_id;
		
		this.popupTab.showPopup = function() {
			Tabs.activeTab.popupTab.dlog = $(Tabs.activeTab.popupTab.$divid).dialog( {
					resizable: true,
					width:	600,
					modal:	true,
					title: popuptitle,
					position: { my: "top", at: "top+200", of: window },
					buttons: {
						"Cancel" : function() {
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
									Tabs.activeTab.popupTab.dlog.dialog("destroy");
									$('#'+Tabs.relatedCol,Tabs.activeTab.$content).prop('selectedIndex',0);
								  }
								}
							  ]
							})
						},
						"Reset" : function() {
							$('#frm_editor',this)[0].reset();
						},
						"Save & Close" : function() {
							$frm = $('#frm_editor',this);
							$frm_valid = validate_form($frm);
							if ($frm_valid) {
								editor_submit_popup( $frm ); // all further action will be handled by the editor_submit_popup function
							}
							
						}
					}
			});
		}
		
		this.popupTab.hidePopup = function() {
			Tabs.activeTab.popupTab.dlog.dialog("close");
		}
		
		this.popupTab.loadHTML = function() {
				
			$target = $('#div_tab_popup','#main');
			$url = "index.php?controller=tab&option=popup&table=" + Tabs.activeTab.popupTab.table + "&rowid=new" ;
			aj = $.ajax({
				url:$url,
				success:function(response){ 
					$target.html(response);
					Tabs.activeTab.popupTab.showPopup();
				}
		   });
		}
		
		this.popupTab.loadHTML();
	}
	
	this.loadJSON = function(json) { // triggered when pagination buttons are used or the number of rows per page is updated.
		if (this.json[this.subtab] != json ) { // only if data has changed
			this.json[this.subtab] = json;
			this.oJSON[this.subtab] = o = $.parseJSON(json) // parse JSON string into js object
			$target = $(this.$content).find('.part.active');
			
			o[0].page = Number( parseInt( o[0].page ))  ;
			o[0].total_pages = Number( parseInt( o[0].total_pages )) ;
			o[0].total_rows = Number( parseInt( o[0].total_rows )) ;
			
			
			// remove old data
			$cols = $('.qv-col',$target);
			$cols.each( function(col_index,col) {
				$('.qv-cell:gt(1)',$(this)).remove();
			});
			
			// add new data
			$.each(o, function(col_index, col) {
				if (col_index > 0) {
					$.each(col, function(row_index, row) {
						$('<div/>',{'class':'qv-cell'}).html(row).appendTo( $cols.eq( Number(col_index-1) ) );
					});
				}
			});
			
			
			// update form filter buttons and row message
			if( Boolean( o[0].buttons.first_page ) ) 				{ $target.find('#lb-first, #lb-prev').removeClass('disabled').removeClass('l-btn-disabled'); } 
			if( Boolean( o[0].buttons.next_page )  )				{ $target.find('#lb-next, #lb-last').removeClass('disabled').removeClass('l-btn-disabled'); }
			if( o[0].page == 1 ) 									{ $target.find('#lb-first, #lb-prev').addClass('disabled').addClass('l-btn-disabled'); } 
			if( o[0].page == o[0].total_pages )						{ $target.find('#lb-next, #lb-last').addClass('disabled').addClass('l-btn-disabled');  }
			
			if( o[0].total_pages == 1  ) { 
				$target.find('#lb-first, #lb-prev, #lb-next, #lb-last' ).addClass('disabled').addClass('l-btn-disabled'); 
				$target.find('#txt_page_number').prop( 'readonly',true ).addClass('ui-state-disabled');
			}
			else {
				$target.find('#txt_page_number').prop( 'readonly',false ).removeClass('ui-state-disabled');
			}
			(o[0].total_rows != 0) ? $target.find('#spn_pagination_controls').show() : $target.find('#spn_pagination_controls').hide()
			
			$target.find('#txt_page_number').val( o[0].page );
			$target.find('#rows_per_page').val( o[0].rows_per_page )
			$target.find('#spn_total_pages').html( o[0].total_pages );
			$target.find('#row_message').html( o[0].row_message ).removeClass('ui-state-error').removeClass('ui-state-success').addClass( o[0].row_message_class );
			
			this.resetQVBindings();
			
		}
	}
	
	this.storeTabs = function() {
		var aj;
		var tabs = {};
		
		$('#tab-bar ul li').each( function(key) {
			($(this).hasClass('active') ) ? 
				tabs[$(this).attr('id')] = { "id":$(this).attr('id'), "name":$(this).attr('name'), "type":$(this).attr('type'), "active": true } :
				tabs[$(this).attr('id')] = { "id":$(this).attr('id'), "name":$(this).attr('name'), "type":$(this).attr('type'), "active": false };
		});
		
		if (typeof aj !== "undefined") aj.abort();
		
		aj = $.ajax({
			url: "index.php?controller=form&frm_name=frm_storetabs",
			data: "Tabs="+JSON.stringify(tabs),
			success: function() { noty_message('Tab Layout Saved','success'); },
			type:'POST',
		})
		
	}
	
	this.restoreTabs = function() {
		var aj;
		if (typeof aj !== "undefined") aj.abort();
		
		aj = $.ajax({
			url: "index.php?controller=form&frm_name=frm_gettabs",
			success: function() { console.log('Get tabs... done'); },
			type:'POST',
			dataType: 'script',
		})
	}
	
	this.tabExists = function() {
		return Boolean( $(this.$id,'#main').length > 0 && $(this.$id,'#main').html() != '' );
	}
	
	this.showTab = function() {
	
		// Hide other tabs first
		Tabs['lastActiveTab'] = Tabs.activeTab;
		$('.active',"#tab-bar").removeClass('active');
		$('.main-content.active','#main').removeClass('active');
		
		// Show this tab
		$(this.$id,"#tab-bar ul").addClass('active');
		$(this.$id,"#main").addClass('active');
		Tabs['activeTab'] = this;
		$('#tab-bar ul').sortable();

		
	}
	
	this.new2view = function(table,cid) {
		this.closeTab();
		var table_cid = table + "__" + cid;
		
		Tabs[table_cid] = new Tab('editor');
		var newtabname = this.name.replace('[new]',$("input[type=text]",this.$content).eq(0).val() );
		Tabs[table_cid].newTab(table_cid,newtabname);
	}
	
	this.refreshEditing = function(relatedColID) {
		this.editing = true;
		Tabs.activeTab.tempVals = [];
		$fields = $('input,select',Tabs.activeTab.$content);
		$.each($fields, function(i) {
			Tabs.activeTab.tempVals.push( {
				'key' : $(this).attr('id'),
				'value' : $(this).val(),
			});
		});
		//
		
		// Get HTML
		isasync = false;
		temp = this.id.split('__');
		table = temp[0];
		cid = (this.is_new) ? 'new' : 1*Number(temp[1]);
		$url = "index.php?controller=tab&option=refresh_edit&table=" + table + "&rowid=" + cid + "&editing=1";
		target = $('#div-editor',this.$content);
		
		aj = $.ajax({
			url:$url,
			success:function(response){ 
				//
				if (target.html() != response) {
					target.html(response);
				}
				$.each(Tabs.activeTab.tempVals, function(i,v) {
					//
					$("[name="+v.key+"]",Tabs.activeTab.$content).val(v.value);
				});
				target_select = $('#'+Tabs.relatedCol);
				$('option',target_select).each( function(i) {
					$('option',target_select).eq(i).prop("selected", $('option',target_select).eq(i).attr('value') == relatedColID  );
				});
				//Tabs.relatedCol = null;
				
			}
	   });
		
		
	}
	
	this.getHTML = function() {
		var aj;
		var target = this.$content;
		maskAll();
		switch (this.type) {
			case 'qv' :
				$url = $(this.$id,'#left').find('a').eq(0).attr('href');
				isasync = true;
			break;
			
			case 'editor' :
				isasync = true;
				temp = this.id.split('__');
				table = temp[0];
				cid = (this.is_new) ? 'new' : 1*Number(temp[1]);
				popup = (this.format == 'popup') ? 1 : 0;
				editing = (this.editing) ? 1 : 0;
				$url = "index.php?controller=tab&option=editor&table=" + table + "&rowid=" + cid + "&popup=" + popup + "&editing=" + editing;
			break;
		}
		
		aj = $.ajax({
			url:$url,
			async: isasync,
			success:function(response){ 
				if (target.html() != response) {
					target.html(response);
				}
				unmaskAll();
			}
	   });
		
	}
	
	this.closeTab = function() {

		this.$tab = $(this.$id,'#tab-bar ul');
		this.$content = $(this.$id,'#main');
		
		//Remove old tab and content
		Tabs[this.id].$tab.remove();
		Tabs[this.id].$content.remove();
		delete Tabs[this.id];
		//Tabs['lastActiveTab'] = Tabs['home'];
		if(!Tabs.activeTab.editing) {
			Tabs.activeTab.refreshTab();
		}
		
		//Activate home tab
		if ($(Tabs.lastActiveTab.$id).length > 0) {
			Tabs.lastActiveTab.showTab();
		}
		else {
			Tabs[ firstNonSpecial(Tabs) ].showTab();
		}
			
	}
	
	this.refreshTab = function() {
		switch (this.type) {
			case 'qv' :
				ajax_submit_filter();
			break;
			
			case 'editor' :
			case 'other' :
				//
				this.getHTML();
			break;
		}
	}
	
	this.refreshBindings = function() {
		switch (this.type) {
			case 'qv' :
				this.resetQVBindings();
			break;
			
			case 'editor' :
			case 'other' :
				this.resetEditorBindings();
			break;
		}
	}
		
	
	this.resetTabBindings = function() {
		$("#tab-bar ul li").off('click.tabBindings').on('click.tabBindings', function() {
			var $id = $(this).attr('id');
			if (Tabs.activeTab.id != $id ) { // only refresh inactive tabs
				Tabs[$id].showTab();
				if(Tabs.activeTab.editing == false && Tabs.activeTab.is_new == false) {
					Tabs[$id].refreshTab();
				}
				Tabs[$id].refreshBindings();
			}
		});
		$('.close-icon').off('click.closeIcon').on( 'click.closeIcon', function() {
			var $id = $(this).parent().attr('id');
			if (Tabs.activeTab.id == $id ) { // only close active tabs
				Tabs[$id].closeTab();
			}
		});
	}
	
	this.resetQVBindings = function() {
		ajax_submit_filter();
		$target = $(this.$content).find('.part.active');
		
		$('.easyui-linkbutton',$target).off();
		
		
		/* var o = this.oJSON[this.subtab];
		
		o[0].page = Number( parseInt( o[0].page ))  ;
		o[0].total_pages = Number( parseInt( o[0].total_pages )) ;
		o[0].total_rows = Number( parseInt( o[0].total_rows )) ;
		
		// update form filter buttons and row message
		if( Boolean( o[0].buttons.first_page ) ) 				{ $target.find('#lb-first, #lb-prev').removeClass('disabled').removeClass('l-btn-disabled'); } 
		if( Boolean( o[0].buttons.next_page )  )				{ $target.find('#lb-next, #lb-last').removeClass('disabled').removeClass('l-btn-disabled'); }
		if( o[0].page == 1 ) 									{ $target.find('#lb-first, #lb-prev').addClass('disabled').addClass('l-btn-disabled'); } 
		if( o[0].page == o[0].total_pages )						{ $target.find('#lb-next, #lb-last').addClass('disabled').addClass('l-btn-disabled');  }
		
		if( o[0].total_pages == 1  ) { 
			$target.find('#lb-first, #lb-prev, #lb-next, #lb-last' ).addClass('disabled').addClass('l-btn-disabled'); 
			$target.find('#txt_page_number').prop( 'readonly',true ).addClass('ui-state-disabled');
		}
		else {
			$target.find('#txt_page_number').prop( 'readonly',false ).removeClass('ui-state-disabled');
		}
		(o[0].total_rows != 0) ? $target.find('#spn_pagination_controls').show() : $target.find('#spn_pagination_controls').hide()
		
		$target.find('#txt_page_number').val( o[0].page );
		$target.find('#rows_per_page').val( o[0].rows_per_page )
		$target.find('#spn_total_pages').html( o[0].total_pages ); */
		
		
		// Submit filter form when rows per page is changed
		$('select#rows_per_page',$target).unbind('change').change( function() {
			$('#hid_rows_per_page',$target).val( $(this).val() );
			$('#hid_page_number',$target).val( 1 );
			ajax_submit_filter();
		});
		
		// Submit filter form when page number is pressed
		$('input#txt_page_number',$target).unbind('keypress').keypress(function(e) {
			  $(this).val( $(this).val().formatInteger() );
			  if(e.which == 13) {
				$(this).blur();
				ajax_submit_filter();
			}
		}).unbind('change').change( function() {
			if ($(this).val()*1 > $('#hid_total_pages',$target).val()*1 ) $(this).val( $('#hid_total_pages',$target).val() );
			$('#hid_page_number',$target).val( $(this).val() );
			ajax_submit_filter();	
		});
		
		$("#lb-first:not('.disabled')",$target).click( function() {
			$('input#txt_page_number',$target).val( 1 ).trigger('change');		
		});
		
		$("#lb-prev:not('.disabled')",$target).click( function() {
			$('input#txt_page_number',$target).val( $('input#txt_page_number',$target).val()*1-1 ).trigger('change');
		});
		
		$("#lb-next:not('.disabled')",$target).click( function() {
			$('input#txt_page_number',$target).val( $('input#txt_page_number',$target).val()*1+1 ).trigger('change');		
		});
		
		$("#lb-last:not('.disabled')",$target).click( function() {
			$('input#txt_page_number',$target).val( $('#hid_total_pages',$target).val() ).trigger('change');		
		});	
		
		$('#lb-go',$target).click( function() { // Go button
			$('#hid_page_number','.part.active .form_filter').val( 1 );
			ajax_submit_filter();
		});
		
		$('#lb-reset',$target).click( function() { // Reset button
			$('.input-qv-cell-search',$target).val('');
			$('#hid_page_number',$target).val( 1 );
			$('#hid_dir',$target).val('ASC');
			$('#hid_order',$target).val('');
			ajax_submit_filter();
		});
		
		$('#lb-refresh',$target).click(function() { // Refresh button
			ajax_submit_filter();
		});
		
		$('#lb-new',$target).click(function() { // New button
			var friendlytable = $(this).attr('name'); //friendly table name
			var table_cid = $(this).attr('rel');
			
			Tabs[table_cid] = new Tab('editor');
			Tabs[table_cid].newTab(table_cid,friendlytable + ' . [new]');
		});
		
		$('.qv-a',$target).unbind('click').click( function() {
			var $qvtable = $(this).closest('.qv-table');
			var friendlytable = $qvtable.attr('name'); //friendly table name
			var friendlyrecord = $(this).html();
			var table_cid = $(this).attr('rel');
			
			Tabs[table_cid] = new Tab('editor');
			Tabs[table_cid].newTab(table_cid,friendlytable + ' . ' + friendlyrecord);
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
		
		// Submit filter form when enter is pressed
		$('.input-qv-cell-search',$target).keypress( function(e) {
			if(e.which == 13) {
				$(this).blur();
				$('#hid_page_number',$target).val( 1 );
				ajax_submit_filter();
			}
		});
		
		// Submit filter form when rows per page is changed
		$('select#rows_per_page',$target).change( function() {
			$('#hid_rows_per_page',$target).val( $(this).val() );
			$('#hid_page_number',$target).val( 1 );
			ajax_submit_filter();
		});
		
		// Submit filter form when page number is pressed
		$('input#txt_page_number',$target).keypress(function(e) {
			  $(this).val( $(this).val().formatInteger() );
			  if(e.which == 13) {
				$(this).blur();
				ajax_submit_filter();
			}
		}).change( function() {
			if ($(this).val()*1 > $('#hid_total_pages',$target).val()*1 ) $(this).val( $('#hid_total_pages',$target).val() );
			$('#hid_page_number',$target).val( $(this).val() );
			ajax_submit_filter();	
		});
	
	}
	
	this.resetEditorBindings = function() {
		$target = $('.part',this.$content).eq(this.subtab);
		$('button',$target).button();
		$frm = $('.main-content.active .part.active #frm_editor');
		//$("input:not(':hidden'),select",$frm).attr('disabled',true);
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
			//;
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
	
	}
	
}

function view_record(table,selectid) {
	$select = $(selectid,Tabs.activeTab.$content);
	$selectedoption = $(':selected',$select);
	$tabid = table + '__' + $selectedoption.attr('value');
	$friendly_table = table.replace('aim','').replace('_',' ').ucwords();
	$friendly_record = $selectedoption.html();
	
	Tabs[$tabid] = new Tab('editor');
	Tabs[$tabid].newTab($tabid,$friendly_table + ' . ' + $friendly_record);	
}