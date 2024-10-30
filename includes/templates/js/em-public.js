var em_jQuery = jQuery.noConflict();
var requestInProgress = false;
 function progressStart()
    {
        requestInProgress = true;
    }
    
    function progressStop()
    {
        requestInProgress = false;
    }

function em_show_venues_map(element_id, addresses) { 
    /*
     * To store all the marker object for further operations
     * @type Array
     */
    var allMarkers = [];
  
    /*
     * Map object with default location and zoom level
     * @type google.maps.Map
     */
    
    var map = new google.maps.Map(document.getElementById(element_id), {
          //center: {lat: -34.397, lng: 150.644},
      zoom:15
    });

    /*
     * Textbox to contain formatted address. Same input box can be used to search location either 
     * by lat long or by address.
     * @type Element
     */

    var geocoder = new google.maps.Geocoder;
   

    // Adding marker on map for multiple addresses
    if (addresses) { 
        geocodeAddress(geocoder, map,addresses);
    }


    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < allMarkers.length; i++) {
            allMarkers[i].setMap(map);
        }
    }

    /**
     * @summary Add markers from array of addresses.
     * @param {google.maps.Geocoder} geocoder
     * @param {google.maps.Map} resultsMap
     * @param {String Array} addresses
     * 
     */
    function geocodeAddress(geocoder, resultsMap,addresses) { 
         
        for(var i=0;i<addresses.length;i++)
        {   var address= addresses[i];
            if(address){
              
            geocoder.geocode({'address': address}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    resultsMap.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: resultsMap,
                        position: results[0].geometry.location,
                    //    icon: em_map_info.gmarker
                    });
                    var infowindow = new google.maps.InfoWindow; 
                    
                    infowindow.setContent(results[0].formatted_address);
                    marker.addListener('click', function() {
                        infowindow.open(map, marker);
                     });
        
                    allMarkers.push(marker);
                  //  infowindow.open(map, marker);
                } 
                else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT)
                     {      
                        setTimeout(1000);//   document.getElementById('status').innerHTML +="request failed:"+status+"<br>";
                        }   
                
                else {
                  
                  //alert('Geocode was not successful for the following reason: ' + status);
                }
            });
            }
        };

    }

}

function em_padNumber(number) {
    var ret = new String(number);
    if (ret.length == 1)
        ret = "0" + ret;
    return ret;
}

em_jQuery(document).ready(function () {

    // For Event calendar widget 
    if (em_jQuery("#em_calendar_widget").length > 0) {
        // Send ajax request to get all the event start dates
        em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_event_dates'},
            success: function (response) {
                var data= JSON.parse(response);
                var dates= data.start_dates;
               // var event_ids= data.event_ids;
                  //if(dates.length>0){ 
                      em_show_calendar(dates);
                  //}
            }
        });


    }

    if(em_jQuery("#rm_map_canvas").length>0){
       var venue_name= em_jQuery("#rm_map_canvas").attr("data-venue-name");
        rm_event_map_canvas("rm_map_canvas",venue_name);
    }   
});

function em_start_timer(duration, display) {
    var start = Date.now(),
            diff,
            minutes,
            seconds,
            stop = false,
            counter = 1;
    function timer() {
        if (!stop)
        {



            // get the number of seconds that have elapsed since
            // startTimer() was called
            diff = duration - (((Date.now() - start) / 1000) | 0);

            // does the same job as parseInt truncates the float

            minutes = (diff / 60) | 0;
            seconds = (diff % 60) | 0;
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = minutes + ":" + seconds;


            if (diff <= 0) {
                // add one second so that the count down starts at the full duration
                // example 04:00 not 03:59
                start = Date.now() + 1000;
            }

            if (diff == 0) {
                stop = true;
            } else {
                counter++;
                jQuery("#em_payment_progress").width(counter * (100 / 240) + "%");
            }


        }
    }
    ;
    // we don't want to wait a full second before the timer starts

    timer();
    setInterval(timer, 1000);
}


function rm_event_map_canvas(element_id,venue){
   // alert(venue);
//     em_jQuery.ajax({
//            type: "POST",
//            url: em_ajax_object.ajax_url,
//            data: {action: 'em_load_venue_addresses',venue_id: venue},
//            success: function (response) {
//              
//                var data= JSON.parse(response);
             //  alert(data);
                em_show_venues_map(element_id,venue);
          //  }
        //});
}
function em_show_venue_map(element_id){
   
    em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_venue_addresses'},
            success: function (response) {
              
                var data= JSON.parse(response);
                em_show_venues_map(element_id,data);
            }
        });
}

function em_event_booking(id){
    if(id>0)
    {
        var formName = 'em_booking' + id;
        em_jQuery('form[name=' + formName + ']').submit();
    }
    else
    document.em_booking.submit();
}



function em_event_map_canvas(element_id,venue){
  // alert(venue);
    em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_venue_addresses',venue_id: venue},
            success: function (response) {
              
                var data= JSON.parse(response);
             //  alert(data);
                em_show_venues_map(element_id,data);
            }
        });
}

 function em_single_venue_map_canvas(element_id,venue){
 //  alert(venue);
    em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_venue_addresses',venue_id: venue},
            success: function (response) {
              
                var data= JSON.parse(response);
             //  alert(data);
                em_show_venues_map(element_id,data);
            }
        });
}

function em_booking_map_canvas(element_id,venue){
 //  alert(venue);
    em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_venue_addresses',venue_id: venue},
            success: function (response) {
              
                var data= JSON.parse(response);
             //  alert(data);
                em_show_venues_map(element_id,data);
            }
        });
}

function em_user_event_venue(element_id,venue){

    em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_load_venue_addresses',venue_id: venue},
            success: function (response) {
              
              var data= JSON.parse(response);               
                em_show_venues_map(element_id,data);
            }
        });
}
function em_change_dp_css()
{
    em_jQuery( ".em_widget_container .ui-datepicker-header" ).removeClass( "ui-widget-header" );
    var emColor = em_jQuery('.em_widget_container').find('a').css('color');
    em_jQuery(".em_color").css('color', emColor);
    em_jQuery(".em_widget_container .ui-datepicker-header").css('background-color', emColor);
    em_jQuery( ".em_widget_container .ui-datepicker-current-day" ).css('background-color', emColor);
}


function em_show_calendar(dates){
    console.log(dates);
    em_jQuery("#em_calendar_widget").datepicker({
                    onChangeMonthYear: function() {
                                setTimeout(em_change_dp_css,40);
                                return;
                    },
                    onHover: function() {
                                alert('o');
                    },
                    onSelect: function (dateText, inst) {
                       // console.log(dateText); 
                        var gotDate = em_jQuery.inArray(dateText, dates);
                       // console.log(gotDate);
                        if(gotDate>=0)
                        {
                            // Accessing only first element to avoid conflict if duplicate element exists on page
                            em_jQuery("#em_start_date:first").val(dateText);
                            var search_url= em_jQuery("form[name='em_calendar_event_form']:first").attr('action');
                            search_url= em_addParameterToURL("em_s=" + em_jQuery("input[name='em_s']:first").val(),search_url);
                            search_url= em_addParameterToURL("em_sd=" + dateText,search_url);
                            location.href=search_url;
                            console.log(search_url);
                          // em_jQuery("form[name='em_calendar_event_form']:first").submit();
                        }
                        
                    },
                    beforeShowDay: function (date) {
                    setTimeout(em_change_dp_css,10);
                    var year = date.getFullYear();
                    // months and days are inserted into the array in the form, e.g "01/01/2009", but here the format is "1/1/2009"
                    var month = em_padNumber(date.getMonth() + 1);
                    var day = em_padNumber(date.getDate());
                    // This depends on the datepicker's date format
                    var dateString = year + "-" + month + "-" + day;
                    
                    var gotDate = jQuery.inArray(dateString, dates);
                    
                    //  console.log(dateString + "   " + $scope.recurring_specific_dates);
                    if (gotDate >= 0) {
                        // Enable date so it can be deselected. Set style to be highlighted
                        return [true, "em-cal-state-highlight"];
                    }
                    // Dates not in the array are left enabled, but with no extra style
                    return [true, ""];
                    }, changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
                 });
                 
                 em_change_dp_css();
}

function em_cancel_booking(booking_id)
{
     em_jQuery("#em_booking_details_modal").addClass("kf_progress_screen");
     em_jQuery.ajax({
            type: "POST",
            url: em_ajax_object.ajax_url,
            data: {action: 'em_cancel_booking_by_user',post_id: booking_id},
            success: function (response) {
              em_jQuery("#em_booking_details_modal").removeClass('kf_progress_screen');
              var data= JSON.parse(response);
              if(data.error==false)
              {
                  em_jQuery("#em_booking_status").html(data.status);
                  em_jQuery("#em_action_bar").remove();
                   em_jQuery("#em_print_bar").remove();
                
              }
              else
              {
                  alert("Booking could not be cancelled");
              }

            }
        });
        
}

 function em_get_dominent_color()
 {
      var emDominentColor = jQuery('.emagic').prepend('<a></a>');
      var emColor= jQuery('.emagic').find('a').css('color');
        jQuery(".em_color").css('color', emColor);
       
        jQuery(".em_bg").css('background-color', emColor);
        jQuery("li").css('border-color', emColor);
        //jQuery("#kf-seat-table-parent .kf-modal-close svg").css('fill', emColor);
 }
//CSS Manipulation

jQuery(document).ready(function(){
    widthAdjust(".em_card");
    widthAdjust(".em_performer_card");
    jQuery("#em_register").click(function() {
        jQuery('html, body').animate({
            scrollTop: jQuery("#em_register_section").offset().top
        }, 500);
    });
    em_get_dominent_color();
});

jQuery(document).ready(function($) {
    var a = jQuery('.emagic .em_card.em_card2');
    for( var i = 0; i < a.length; i+=2 ) {
    a.slice(i, i+2).wrapAll('<div class="em-cards-wrap"></div>');
    

}

    var a = jQuery('.emagic .em_card.em_card3');
    for( var i = 0; i < a.length; i+=3 ) {
    a.slice(i, i+3).wrapAll('<div class="em-cards-wrap"></div>');
    

}

    var a = jQuery('.emagic .em_card.em_card4');
    for( var i = 0; i < a.length; i+=4 ) {
    a.slice(i, i+4).wrapAll('<div class="em-cards-wrap"></div>');
    

}
 

jQuery('.em_event_cover a').each(function () {
     var jQuerywrapper = jQuery(this),
      imgUrl = jQuerywrapper.find('img').prop('src');
      if (imgUrl) {
         jQuerywrapper
         .css('backgroundImage', 'url(' + imgUrl + ')')
       .addClass('em-compat-object-fit')
       .children('img').hide();
     }  
 });


});


function widthAdjust(cardClass) {
    kfWidth = jQuery(".emagic").innerWidth();
    if (kfWidth < 720) {jQuery(".emagic").addClass("narrow");}
    switch (true) {
            case kfWidth <= 650:
            jQuery(cardClass).addClass("em_card1");
            break;
        case kfWidth <= 850:
            jQuery(cardClass).addClass("em_card2");
            break;
        case kfWidth <= 1150:
            jQuery(cardClass).addClass("em_card3");
            break;
        case kfWidth <= 1280:
            jQuery(cardClass).addClass("em_card4");
            break;
        case kfWidth > 1280:
            jQuery(cardClass).addClass("em_card5");
            break;
        default:
            jQuery(cardClass).addClass("em_card2");
            break;
    }
}

function hide_entry_title(){
    if ((window.location.href.indexOf("performer") > -1) || (window.location.href.indexOf("event") > -1) || (window.location.href.indexOf("venue") > -1) ){
          jQuery('.entry-title,.entry-meta ').css('display', 'none');
    }
            
}
function em_addParameterToURL(param,url){
    _url = url;
    _url += (_url.split('?')[1] ? '&':'?') + param;
    return _url;
}


