jQuery.noConflict();
jQuery(document).ready(function(){

	jQuery( '.form .datepicker' ).each( function() {
		
		var field = jQuery(this),
			min = field.data('datepicker-min'),
		 	max = field.data('datepicker-max');
		
		if ( min === 0 ) { min = null; }
		if ( max === 0 ) { max = null; }
		
		jQuery(this).datepicker({ 
			dateFormat: "dd.mm.yy",
			changeYear: true,
			changeMonth: true,
			buttonImageOnly: true,
			buttonImage: "/1dk8ekm5/wp-content/themes/rsa/assets/jquery-ui/datepicker-icon.png",
			showOn: 'both',
			buttonText: 'wähle Datum…',
			minDate: min,
			maxDate: max
		});
	});
	
});