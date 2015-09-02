// JavaScript Document

$(document).ready( function() {
	$('input[type=checkbox]').button();
	$('button').button();
	
	$('select#sel_table').change( function() {
		$('.param-table.active').hide().removeClass('active');
		$('#param_table_'+$(this).val()).addClass('active').show("blind", {direction: "horizontal"}, 1000);
		
	})
	
	$('.param-table .param-col .param-cell').each( function($index) {
		
		$(this).mouseover( function() {
			$table = $(this).closest('.param-table');
			if( $index > 0 ) {
				$('.param-col',$table).each( function(i,elm) {
					$('.param-cell',elm).eq($index).addClass('highlight')
				});		
			}
		}).mouseout( function() {
			$table = $(this).closest('.param-table');
			if( $index > 0 ) {
				$('.param-col',$table).each( function(i,elm) {
					$('.param-cell',elm).eq($index).removeClass('highlight');
				});
			}
		});
	})
		
});