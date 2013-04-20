jQuery.noConflict();
jQuery(document).ready(function(){
    
    function is_devices_with_own_date_input() {
        
        var ret = false;
        
     		// is iPad
     		if(navigator.userAgent.match(/iPad/i) != null) 	ret = true;

     		// is iPhone
     		if(navigator.userAgent.match(/iPhone/i) != null) ret = true;

        return ret;
    }
    
    /* Datepicker */
    (function(){
        
        if ( !is_devices_with_own_date_input() ) {
        
            var regional = jQuery('.yerform').data('language');
            if ( regional ) jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ regional ] );

            jQuery('.yerform').each( function() {

                jQuery(this).find('.datepicker' ).each( function() {
                
                    var field = jQuery(this),
                        fieldname = field.attr('id'),
                        mindate = field.data('datepicker-mindate'),
                        maxdate = field.data('datepicker-maxdate'),
                        dateformat = field.data('datepicker-dateformat'),
                        yerformformat = field.data('datepicker-yerformdateformat'),
                        iconurl = field.data('datepicker-iconurl'),
                        regional = field.data('datepicker-regional');
                
                    field
                        .attr('readonly','readonly');
                
                    if ( mindate === 0 ) { mindate = null; }
                    if ( maxdate === 0 ) { maxdate = null; }
                
                    var p = {};

                    p.dateFormat = dateformat;
                    p.altFormat = yerformformat;
                    p.altField = '#' + fieldname + '_yerform';
                    p.changeYear = true;
                    p.changeMonth = true;
                    if ( iconurl ) {
                        p.buttonImageOnly = true;
                        p.buttonImage = iconurl;
                        p.showOn = 'both';
                    }
                    p.minDate = mindate;
                    p.maxDate = maxdate;
                
                    field
                        .datepicker(p)
                        .prop('type','text');
                });
            });
        }

    }());
    
        
});