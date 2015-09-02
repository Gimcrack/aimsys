$(document).ready( function() {
	$('#username').focus();
	
	$("#tab-bar li").click( function() {
		$(this).parent().find(".active").removeClass("active");	
		$(this).addClass("active");
	});	
	
	$('button').button();
	
	$("#password").keypress(function(event) {
		if (event.which == 13 || event.keyCode == 13) {
			event.preventDefault();
			validate_submit_form(this.form);
		}
	});
	
});