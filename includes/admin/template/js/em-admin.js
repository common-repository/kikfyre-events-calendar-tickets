/**
 * 
 * @summary Callback function for map loading in admin
 * @since 1.0
 * @description Calls em_initMap with required parameters to load all the map related operations.
 */



function em_venue_mapCallback()
{  
         
}

function em_adjust_card_height()
{
    jQuery('.kikfyre').each(function(){  
        var highestBox = 0;
        jQuery(this).find('.kf-card').each(function(){ 
            if(jQuery(this).height() > highestBox){  
                highestBox = jQuery(this).height();  
            }
        })
        jQuery(this).find('.kf-card').height(highestBox);
    });
}

/*
 * Configuring Datepicker to avoid regional settings
 */
function em_set_date_defaults()
{
  jQuery.datepicker.setDefaults(jQuery.datepicker.regional[""]);
}







