// JavaScript Document

// *************************** GLOBAL VARIABLES ***************************
var msg;
var msg_class;
var timeOuts = [];
var checkedOut = [];
var _rF = [];
var aj;
var to;
var activetab;
var lastactivetab;
var active_part = '.main-content.active .part.active';
var active_filter_form = '.main-content.active .part.active .form_filter';
var status_noty;
var modal_noty;

// Ajax Setup
$.ajaxSetup({
	cache:false,
	timeout:10000,  
	type:'GET',
	error:function(){ 
		_rF.ajax_general_error();
	}
});

// *************************** MESSAGE / STATUS FUNCTIONS ***************************

function noty_status(text) {
	//$.noty.closeAll();
	if (status_noty !== undefined) { status_noty.close(); }
	status_noty = noty({
  		text: text,
  		type: 'success', // alert,success,error,warning,information,confirm
  		layout: 'bottomLeft',
  		theme: 'defaultTheme',
		timeout:1300,
		killer:true
  	});
}

function noty_modal(text) {
	//$.noty.closeAll();
	modal_noty = noty({
  		text: text,
  		type: 'success', // alert,success,error,warning,information,confirm
      dismissQueue: true,
  		layout: 'bottomLeft',
  		theme: 'defaultTheme',
		modal:true
  	});
}

function noty_message(text,type,sticky,l) {
	
	sticky = (sticky !== undefined && sticky !== 0 ) ? true : false;
	to = (sticky) ? 0 : 4000; // timeout
	if (l === undefined) { l = 'topCenter'; }
	
	var n = noty({
  		text: text,
  		type: type, // alert,success,error,warning,information,confirm
      dismissQueue: true,
  		layout: l,
  		theme: 'defaultTheme',
		timeout: Number(to),
		animation: {
			open: {height: 'toggle'},
			close: {height: 'toggle'},
			easing: 'swing',
			speed: 700 // opening & closing animation speed
		}
  	});
	return n;
  }
  
 function checkout_message(text,type,sticky,l) {
	
	sticky = (sticky !== undefined && sticky !== 0 ) ? true : false;
	to = (sticky) ? 0 : 1500; // timeout
	if (l === undefined) { l = 'topCenter'; }
	
	var n = $('.main-content.active .part.active .checkout_message_msg').noty({
  		text: text,
  		type: type, // alert,success,error,warning,information,confirm
      dismissQueue: true,
  		layout: l,
  		theme: 'defaultTheme',
		timeout: Number(to),
		force:1,
		maxVisible:1,
		animation: {
			open: {height: 'toggle'},
			close: {height: 'toggle'},
			easing: 'swing',
			speed: 300 // opening & closing animation speed
		},
  	});
	return n;
  }


function raise_error(text) {
	$('#spn_message').addClass('ui-state-error').html(text);
}

function validate_submit_form( frm ) {
	$frm = $(frm);
	if (validate_form($frm) ) {
		frm.submit();
	}
}

function ucfirst(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

String.prototype.ucfirst = function() {
	return this.charAt(0).toUpperCase() + this.slice(1);
}

String.prototype.ucwords = function() {
    str = this.toLowerCase();
    return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
        function($1){
            return $1.toUpperCase();
        });
}


function formatInteger(z) {
   return z.replace(/[^0-9]/gi,null);
}

String.prototype.formatInteger = function() {
	return this.replace(/[^0-9]/gi,null);
}


// *************************** FORM PROCESSING / NAVIGATION ***************************

function ajax_submit_all( target ) {
	$target = $(target);
	$forms = $('form',$target);
	$forms.each( function( i, elm) {
		ajax_submit( '#' + $(elm).attr('id') );
	});
}

function ajax_submit(frm) {
	$frm_valid = validate_form(frm);
	if( $frm_valid ) {
		page = $(frm).attr('action');
		target = (typeof $(frm).attr('target') !== 'undefined') ? $(frm).attr('target') : '#spn_content_1';
		targetScript = eval($(frm).attr('targetScript'));
		qs = $(frm).serialize();
		
		maskAll('Saving...');
		
		
		
		$.ajax({
			dataType: (targetScript) ? "script" : "html",
			url:page,
			data:qs,
			type:'POST',
			success:function(response){ 
				unmaskAll();
			},
			error:function(response){
				unmaskAll();
				noty_message('There was an error processing your request. Please try again later.','error');
			}
			
	   });
		
	}
	
}

function editor_submit(frm) {
	$frm_valid = validate_form(frm);
	if( $frm_valid ) {
		page = $(frm).attr('action');
		target = (typeof $(frm).attr('target') !== 'undefined') ? $(frm).attr('target') : '#spn_content_1';
		targetScript = eval($(frm).attr('targetScript'));
		qs = $(frm).serialize();
		
		maskAll('Saving...');

		$.ajax({
			dataType: (targetScript) ? "script" : "html",
			url:page,
			data:qs,
			type:'POST',
			success:function(response){ 
				$frm = $('.main-content.active .part.active #frm_editor');
				Tabs.activeTab.editing = false;
				$qs = $('input:hidden',$frm).serialize();
				checkin($qs);
				unmaskAll();
			},
			error:function(response){
				unmaskAll();
				noty_message('There was an error processing your request. Please try again later.','error');
			}
			
	   });
		
	}
	
}

function editor_submit_popup(frm) {
	$url = $(frm).attr('action');
	$qs = $(frm).serialize();

	$.ajax({
		url:$url,
		data:$qs,
		type:'POST',
		success: function(response) {
			$o = $.parseJSON(response);
			//console.log(response);
			//console.log($o);
			if ($o.msg_class.indexOf('success') != -1 ) {
				Tabs.activeTab.update_select_options($o.rowid);
				Tabs.activeTab.popupTab.dlog.dialog("close");
			} else {
				noty_message($o.message,'error');
			}
		},
   });
		
}

function change_password(elm,btn) {
	$('<div/>').html( $('#div_change_password').html() ).dialog( {
		width:410,
		modal:	true,
		title: 'Change Password',
		position: { my: "top", at: "top+200", of: window },
		buttons: {
			"Cancel" : function() {
				$(this).dialog("destroy");
			},
			"Reset" : function() {
				$('#frm_change_password',this)[0].reset();
			},
			"Save & Close" : function() {
				$frm = $('#frm_change_password',this);
				$frm_valid = validate_form($frm);
				if ($frm_valid) {
					$(elm,Tabs.activeTab.$content).prop('disabled',false).val( $('#password_1',this).val() );
					$(this).dialog("destroy");
					noty_message('Remember to click Save to save the new password','information');
				}
				
			}
		}
	});
}

function maskAll() {
	$('#main').mask();
}
function unmaskAll() {
	$('#main').unmask();
}

function ajax_submit_filter() {
	if ( $(active_filter_form).length == 0) return false;
	$active_subtab = $('.main-content.active #content-header-bar li.active').attr('id');
	
	$frm = $(active_filter_form);
	
	target = $frm.attr('target');
	$target = $(target);
	action = $frm.attr('action') + '&subtab=' + $active_subtab;
	qs			= $frm.serialize();
	qs_search 	= $('.input-qv-cell-search',active_part).filter(function(){ return $(this).val();}).serialize(); // serializes only non-empty fields.
	qs			= qs_search + '&' + qs + '&search_criteria=' + escape(qs_search) + '&tab_exists=1';
	
	maskAll('Processing...');
	
	$.ajax({
		url:action,
		data:qs,
		type:'POST',
		success:function(response) {
			Tabs.activeTab.loadJSON(response);
			unmaskAll();
		},
		error:  _rF['ajax_general_error']
			
		
		
	});
}

// Do nothing in case no callback was specified.
_rF[0] = function() {
	1; 
}

_rF.ajax_general_error = function(m,v,t){ 
	if (t==="timeout") {
		unmaskAll();
		raise_error("There was a problem processing your request. Please try again."); 
	}
	else {
		unmaskAll();
		raise_error(m); 

	}
}


function checkout(qs) {
	//checkout_message('Opening record for editing...','alert');
	$.ajax({
		async:false,
		url:"index.php?controller=checkout",
		data:qs,
		type:'POST',
		dataType:'script',
		success:function(response) {
	
		},
		error:  _rF['ajax_general_error']
	});
}

function checkin(qs) {
	$.ajax({
		async:false,
		url:"index.php?controller=checkin",
		data:qs,
		type:'POST',
		dataType:'script',
		success:function(response) {
		},
		error:  _rF['ajax_general_error']
	});
}

function updateDOM(inputField) {
	var i, radioNames;
    // if the inputField ID string has been passed in, get the inputField object
    if (typeof inputField == "string") {
        inputField = document.getElementById(inputField);
    }
    
    if (inputField.type == "select-one") {
        for (i=0; i<inputField.options.length; i++) {
            if (i == inputField.selectedIndex) {    
                inputField.options[inputField.selectedIndex].setAttribute("selected","selected");
            }
        }
    } else if (inputField.type == "select-multiple") {
        for (i=0; i<inputField.options.length; i++) {
            if (inputField.options[i].selected) {
                inputField.options[i].setAttribute("selected","selected");
            } else {
                inputField.options[i].removeAttribute("selected");
            }
        }
    } else if (inputField.type == "text") {
        inputField.setAttribute("value",inputField.value);
    } else if (inputField.type == "textarea") {
        inputField.setAttribute("value",inputField.value);
        inputField.innerHTML = inputField.value;
    } else if (inputField.type == "checkbox") {
        if (inputField.checked) {
            inputField.setAttribute("checked","checked");
        } else {
            inputField.removeAttribute("checked");
        }
    } else if (inputField.type == "radio") {
        radioNames = document.getElementsByName(inputField.name);
        for(i=0; i < radioNames.length; i++) {
            if (radioNames[i].checked) {
                radioNames[i].setAttribute("checked","checked");
            } else {
                radioNames[i].removeAttribute("checked");
            }
        }
    }
}
