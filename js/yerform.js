jQuery.noConflict();
jQuery(document).ready(function(){
    
    /* Datepicker */
    (function(){

        var regional = jQuery('.yerform').data('language');
        if ( regional ) jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ regional ] );

        jQuery('.yerform').each( function() {

            jQuery(this).find('.datepicker' ).each( function() {

                var field = jQuery(this),
                    mindate = field.data('datepicker-mindate'),
                    maxdate = field.data('datepicker-maxdate'),
                    altformat = field.data('datepicker-altformat'),
                    dateformat = field.data('datepicker-dateformat'),
                    iconurl = field.data('datepicker-iconurl'),
                    altfieldname = field.data('datepicker-altfieldname'),
                    regional = field.data('datepicker-regional');

                if ( mindate === 0 ) { mindate = null; }
                if ( maxdate === 0 ) { maxdate = null; }

                field.datepicker({
                    dateFormat: dateformat,
                    altFormat: altformat,
                    altField: '#' + altfieldname,
                    changeYear: true,
                    changeMonth: true,
                    buttonImageOnly: true,
                    buttonImage: iconurl,
                    showOn: 'both',
                    minDate: mindate,
                    maxDate: maxdate
                });

            });
        });

    }());
    
});