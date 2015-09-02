var aj;
var expiry_warning_message;
var timestamp = Date.now();

function expire_warning(periods) { 
    if ($.countdown.periodsToSeconds(periods) == 30) { 
        expiry_warning_message = noty_message("You will be logged out automatically in less than 30 seconds. Click anywhere to remain logged in.","warning",true,'top'); 
    } 
} 

$(document).ready( function() {
	
	$(window).resize( function() {
		var sum=0;
		$('#tab-bar ul li').each( function(){ sum += $(this).outerWidth() });
		
		
		$container_width = $('#tab-bar').width();
		
		
		if (sum > $container_width-20) {
			
		}
		
	});
	
	$('#lb-scrolltableft').click( function() {
		  $('#tab-bar ul').animate({
			marginLeft: "-=50px"
		  }, "fast");
	});
	
	$('#lb-scrolltabright').click( function() {
		  $('#tab-bar ul').animate({
			marginLeft: "+=50px"
		  }, "fast");
	});
	
	
	// Countdown timer reset
	$(document).unbind('click').bind('click', function() {
		
		if (typeof expiry_warning_message !== "undefined") expiry_warning_message.close();
		
		if (Date.now()-timestamp <= 30000 ) {
			return true; // only check session if it has been at least 30 seconds;
		}
		
		timestamp = Date.now(); // check session and update timestamp
		
		
	   $.ajax({
			dataType: "script",
			url:'index.php?controller=tab&option=checksession',
	   });
		
	});
	
	$('#countdown').countdown('destroy');
	
	$('#countdown').countdown({
		onTick: expire_warning,
		until: +600, 
		compact: true, 
		format:"MS",
		alwaysExpire:true,
		expiryUrl:"index.php?action=logout"}
	);
	
	// Top tab bar functions
	$('[tt]').unbind('mouseenter');
	$('[tt]').unbind('mouseleave');
	$('[tt]').mouseenter( function() { // status bar message
		//
		status_timeout = setTimeout("noty_status('"+$(this).attr('tt')+"')",600);
		//noty_status( $(this).attr('tt') );
	}).mouseleave( function() {
		clearTimeout(status_timeout);
		status_off();
	});
	
	
	
	$("#tab-bar ul li").off('click.tabBindings').on( 'click.tabBindings', function() {
		Tabs[$(this).attr('id')].showTab();
	});	
	
	$('#left ul li a').off('click.liaClick').on('click.liaClick', function(e) {
		e.preventDefault();
		$(this).parent().trigger('click');
		return false;
	});
	
	// Main Menu Functions
	$('#left ul li').off().on('mousedown.leftli', function() {
		$(this).addClass('active');
	}).on('mouseup.leftli', function() {
		$(this).removeClass('active');
	}).on( 'click.leftli', function() {
		// get tab name
		id = $(this).attr('id');
		name = id.ucfirst();
		// generate new tab
		Tabs[id] = new Tab();
		Tabs[id].newTab( id,name );
	});
	
	//Stylize JqueryUI elements
	$('button').button();
	
	//$('#home','#left').click();
	temp = new Tab();
	temp.restoreTabs();
	
	$('a#su-login').off('click.suLogin').on('click.suLogin', function(e) {
		e.preventDefault();
		Tabs['sulogin'] = new Tab();
		Tabs['sulogin'].newTab('sulogin','Super User', $(this).parent() );
	});
	
	$('a#manage_account').off('click.aAccount').on('click.aAccount', function(e) {
		e.preventDefault();
		Tabs['manageaccount'] = new Tab();
		Tabs['manageaccount'].newTab('manageaccount','Manage Account', $(this).parent() );
	});
	
	$('#lb-savetabs').off('click.lbSaveTabs').on('click.lbSaveTabs', function() {
		var temp = new Tab();
		temp.storeTabs();
	});
		
	$('#left #arrow').off('click.leftArrow').on('click.leftArrow', function() 
	{
		if ($('#left #arrow').css('background-image').indexOf('right') === -1 ) 
		{
			// Hide left pane
			$('#left').animate({
				marginLeft: -1*Number( $('#left').width() - 10),
			  }, 'slow', 'easeOutExpo');
			$('#left #arrow').css('background-image',"url('../../images/arrow-icon-right.png')");
			  
			  // Bring main pane over
			  $('#main').animate({
				 marginLeft: 12,
			  }, 'slow', 'easeOutExpo');
		} 
		else 
		{
			// Show the left pane
			$('#left').show().animate({
			marginLeft: 0,
			}, 1000, 'easeInExpo');
			$('#left #arrow').css('background-image',"url('../../images/arrow-icon-left.png')");
					  
			$('#main').animate({
			 marginLeft: Number($('#left').width() + 3),
			}, 1000, 'easeInExpo');
		}
	});
});