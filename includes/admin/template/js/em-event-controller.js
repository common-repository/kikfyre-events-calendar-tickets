
/************************************* Event Controller ***********************************/

eventMagicApp.controller('eventCtrl', function ($scope, $http, MediaUploader, Seat,TermUtility,EMRequest,PostUtility) {
    // Model for Event data
    $scope.data = {};
    $scope.data.sort_option="date";
    $scope.requestInProgress= false;
    $scope.selectedSeats = [];
    $scope.post_id = 0;
    $scope.paged=1;
    $scope.selections= [];
    $scope.post_edit= false;
    $scope.seat_container_width='';
    $scope.progressBarStyle='';
    $scope.multiDay= false;
    $scope.child_for_deletion= [];
    $scope.scheme_popup =  false;
    $scope.seatPopup= false;
    $scope.IsVisible = false;
    $scope.data.hideExpired= 0;
    $scope.data.full_load= true;
    /*
     * ********************* Add/Edit event functions
     */
    
    angular.element(document).ready(function () {
     em_set_date_defaults();
        $scope.event_tour();
    });
   
   /*
    * 
    * Loading seat container width as per initial seating structure
    */ 
   $scope.setSeatContainerWidth= function()
   {
       if ($scope.data.post.seats!== undefined && $scope.data.post.seats.length>0) {

                var seat_container_width= ($scope.data.post.seats[0].length*35) + 80 + "px";
                $scope.seat_container_width={ "width" : seat_container_width };
      
        }
   }
    
    /*
     * 
     * Initializing Recurrence Calendar
     */
    $scope.loadRecurrenceCal= function()
    {  
        jQuery("#r_dates").datepicker({ 
                onSelect: function (dateText, inst) { 
                    $scope.addOrRemoveDate(dateText,$scope.data.post.recurring_specific_dates);
                },minDate: new Date(),
                beforeShowDay: function (date) {
                    var year = date.getFullYear();
                    // months and days are inserted into the array in the form, e.g "01/01/2009", but here the format is "1/1/2009"
                    var month = $scope.padNumber(date.getMonth() + 1);
                    var day = $scope.padNumber(date.getDate());
                    // This depends on the datepicker's date format
                    var dateString = year + "-" + month + "-" + day;
                    var gotDate = jQuery.inArray(dateString, $scope.data.post.recurring_specific_dates);
                    if (gotDate >= 0) {
                        // Enable date so it can be deselected. Set style to be highlighted
                        return [true, "kf-cal-state-highlight"];
                    }
                    // Dates not in the array are left enabled, but with no extra style
                    return [true, ""];

                }, changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
            });
    }
    
    /*
     * 
     * Initializing Multiday calendar
     */
    $scope.loadMultidayCal= function()
    {  
        jQuery("#m_dates").datepicker({
                onSelect: function (dateText, inst) {
                    $scope.addOrRemoveDate(dateText,$scope.data.post.multi_dates);  
                },
                beforeShowDay: function (date) {
                    var year = date.getFullYear();
                    // months and days are inserted into the array in the form, e.g "01/01/2009", but here the format is "1/1/2009"
                    var month = $scope.padNumber(date.getMonth() + 1);
                    var day = $scope.padNumber(date.getDate());
                    // This depends on the datepicker's date format
                    var dateString = year + "-" + month + "-" + day;
                    
                    var gotDate = jQuery.inArray(dateString, $scope.data.post.multi_dates);
                    
                    if (gotDate >= 0) {
                        // Enable date so it can be deselected. Set style to be highlighted
                        return [true, "kf-cal-state-highlight"];
                    }
                    
                    // Dates not in the array are left enabled, but with no extra style
                    return [true, ""];
                },changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd"
            });
            
    }
    
    /*
     * 
     * Updating minDate and maxDate in Multiday calendar
     */
    $scope.changeMultiCalDates= function()
    {
        jQuery('#m_dates').datepicker("change",{ minDate: jQuery.datepicker.parseDate($scope.data.post.date_format,$scope.data.post.start_date) });
        jQuery('#m_dates').datepicker('change', { maxDate: jQuery.datepicker.parseDate($scope.data.post.date_format,$scope.data.post.end_date) });
        setTimeout(function(){
            $scope.resetMultiDates();
        },2500);
    }
    
    /*
     * 
     * WordPress default media uploader to choose image
     */
    $scope.mediaUploader = function (multiImage) {
        var mediaUploader = MediaUploader.openUploader(multiImage);
        mediaUploader.on('select', function () {
            attachments = mediaUploader.state().get('selection');
            attachments.map(function (attachment) {
                attachment = attachment.toJSON();
                if (multiImage) {
                    // For gallery images
                   // var imageObj = {src: [attachment.sizes.thumbnail.url], id: attachment.id};
                  var imageObj = attachment.sizes.thumbnail===undefined ? {src: [attachment.sizes.full.url], id: attachment.id} : {src: [attachment.sizes.thumbnail.url], id: attachment.id};
                    $scope.data.post.images.push(imageObj);

                    $scope.data.post.gallery_image_ids.push(attachment.id);

                } else {
                    // For cover image
                    $scope.data.post.cover_image_id = attachment.id;
                   $scope.data.post.cover_image_url = attachment.sizes.thumbnail===undefined ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;
                }

                $scope.$apply();
            });
        });
        // Open the uploader dialog
        mediaUploader.open();
    }

    /*
     * Empty gallery images
     */  
    $scope.deleteGalleryImage = function (image_id, index, image_model, ids) { 
        image_model.splice(index, 1);
        ids = em_remove_from_array(ids, image_id);
    }
    
    $scope.$watch('data.post.allow_discount', function (newVal, oldVal) {
        if (parseInt(newVal) == 1)
            jQuery("#em_volume_discount").show("slow");
        else
            jQuery("#em_volume_discount").hide("slow");
    });
    
    /*
     * Verifying event capacity with venue capcity 
     */
    $scope.verify_capacity= function(newVal)
    {
        var data= {};
        data.venue_id= $scope.data.post.venue;
        data.event_id= $scope.data.post.id;
         
        $scope.progressStart();
        EMRequest.send('em_get_venue_capcity',data).then(function(response){
        //    alert(response.data.capacity);  
                $scope.data.venue_capacity= response.data.capacity;
                if($scope.data.venue_capacity>0 && newVal>$scope.data.venue_capacity)
                    $scope.postForm.seating_capacity.$setValidity("capacityExceeded", false);
                else
                    $scope.postForm.seating_capacity.$setValidity("capacityExceeded", true);
                $scope.progressStop();
            });
    }
    
    /*
     * 
     * Adding new performer
     */
    $scope.addPerformer = function () {
        var performer = {role: "", name: ""};

        // Add custom performer fields only once
        if ($scope.data.post.custom_performers.length == 0)
            $scope.data.post.custom_performers.push(performer);
    }

    /* Adding date in calendar */
    $scope.addDate = function (date,element) {
     
        if (jQuery.inArray(date, element) < 0){
        element.push(date);
        }
//            element.push(date);
//        $scope.$apply();

    }
    
    /* 
     * Removing date from calendar
     */
    $scope.removeDate = function (index,element) {
        if(element===undefined)
            return;
        element.splice(index, 1);
        $scope.$apply();
    }

    // Adds a date if we don't have it yet, else remove it
    $scope.addOrRemoveDate = function (date,element) {
        var index = jQuery.inArray(date, element);
        if (index >= 0)
            $scope.removeDate(index,element);
        else           
            $scope.addDate(date,element);
        
            
    }
    
    
    // Remove all the dates and deselect them from Calendar
    $scope.resetRecurringDates= function(){
      $scope.data.post.recurring_specific_dates= [];
      jQuery("#r_dates .kf-cal-state-highlight").removeClass('kf-cal-state-highlight'); 
      
    }
    
    // Remove all the dates and deselect them from Calendar
    $scope.resetMultiDates= function(){
      $scope.data.post.multi_dates= [];
      jQuery("#m_dates").datepicker("refresh");
      jQuery("#m_dates .kf-cal-state-highlight").removeClass('kf-cal-state-highlight'); 
     // console.log('after refresh');
    }
    
    // Takes a 1-digit number and inserts a zero before it
    $scope.padNumber = function (number) {
        var ret = new String(number);
        if (ret.length == 1)
            ret = "0" + ret;
        return ret;
    }
    
    /*
     * 
     * Uploading sponser images
     */
    $scope.sponserUploader = function (multiImage) {
        var mediaUploader = MediaUploader.openUploader(multiImage);
        mediaUploader.on('select', function () {
            attachments = mediaUploader.state().get('selection');
            attachments.map(function (attachment) {
                attachment = attachment.toJSON();
                // For gallery images
                var imageObj = attachment.sizes.thumbnail===undefined ? {src: [attachment.sizes.full.url], id: attachment.id} : {src: [attachment.sizes.thumbnail.url], id: attachment.id};
                $scope.data.post.sponser_images.push(imageObj);
                $scope.data.post.sponser_image_ids.push(attachment.id);
                $scope.$apply();
            });
        });
        // Open the uploader dialog
        mediaUploader.open();
    }

    /*Saving event
     */
    $scope.savePost = function (isValid) { 
        $scope.resetSelections();
        // If form is valid against all the angular validations
        if (isValid) {
            if( jQuery('#description').is(':visible') ) {
                $scope.data.post.description= jQuery('#description').val();
            } 
            else 
            {
                $scope.data.post.description= tinymce.get('description').getContent();   
            }
         //   alert($scope.data.post);
            $scope.data.post.id= $scope.post_id;
            
            $scope.progressStart();
            EMRequest.send('em_save_event',$scope.data.post).then(function(response){
                $scope.progressStop();
                // If servers sends Error object
                var data= response.data;
                if (data.error_status) {
                    $scope.formErrors= data.errors;
                    jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                }
                else{
                   if(data.redirect){
                        
                      location.href=data.redirect;
                    } 
                }

                
            });
        }
        
    }
    
    /*
     * 
     * Loading Event data on init
     */
    $scope.preparePageData = function () {
        $scope.progressStart();
        
        $scope.data.em_request_context= 'admin_event';
        // If "Edit" page
        if (em_get('post_id') > 0) {
            $scope.post_id = em_get('post_id');
            $scope.data.post_id= $scope.post_id;
            $scope.post_edit= true;
        } 
        
        // HTTP request to load data
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.data= response.data;
            $scope.initializeDateTimePickers();
            $scope.setSeatContainerWidth();
            $scope.loadRecurrenceCal();
            $scope.loadMultidayCal();
            $scope.data.full_load= false;
        });
        $scope.progressStop();
    };
    
    $scope.initializeDateTimePickers= function()
    {
        var minDate= new Date();
        var maxDate;
        
        jQuery('#event_start_date').datetimepicker({controlType: 'select',oneLine: true,changeYear: true,minDate: new Date()});
        jQuery("#event_end_date").datetimepicker({controlType: 'select',changeYear: true,oneLine: true,timeFormat: 'HH:mm',minDate: new Date()});
        jQuery("#event_start_booking_date").datetimepicker({controlType: 'select',oneLine: true,timeFormat: 'HH:mm',changeYear: true});
        jQuery("#event_last_booking_date").datetimepicker({controlType: 'select',oneLine: true,timeFormat: 'HH:mm',changeYear: true});
       
    }
     
     $scope.update_start_booking_date=function(){
                
        var maxDate=document.getElementById("event_end_date").value;
        jQuery("#event_start_booking_date").datetimepicker('change', {controlType: 'select',oneLine: true,timeFormat: 'HH:mm',changeYear: true,maxDate:maxDate});

        
     }
    /*
     * Compare dates
     */
    $scope.compareDate= function(start_date,end_date,pattern){
        if(jQuery.datepicker.parseDate(pattern,start_date)>  jQuery.datepicker.parseDate(pattern,end_date))
            return true
        else 
            return false;
    }
    
    /*
     * Compare start date and end date to assert MultiDay event
     */
    $scope.isMultiDayEvent= function(start_date,end_date,pattern){ 
         var start= jQuery.datepicker.parseDate(pattern,start_date);
         var end= jQuery.datepicker.parseDate(pattern,end_date);
         var days   = (end - start)/1000/60/60/24;
         if(days>0)
             return true;
         else
             return false;
    }

    /*
     * Fetch capacity and seating structure from venue.
     */
    $scope.getCapacity= function()
    {
        var data= {};
        data.venue_id= $scope.data.post.venue; 
        data.event_id= $scope.data.post.id;
        $scope.progressStart();
        EMRequest.send('em_get_venue_capcity',data).then(function(response){
                $scope.progressStop();
                $scope.data.post.seating_capacity= parseInt(response.data.capacity);
                $scope.data.post.seats= response.data.seats;
                $scope.setSeatContainerWidth();
        });
    }
    
    /*
     * Select seat column for bulk opeartion (like: Reserver or reset)
     */
      $scope.selectColumn = function (index) {
        $scope.resetSelections();
        for (var i = 0; i < $scope.data.post.seats.length; i++) {
            
            if($scope.data.post.seats[i][index].type == 'general' || $scope.data.post.seats[i][index].type =='selected'){
                
                $scope.data.post.seats[i][index].type = 'selected';
                $scope.selectedSeats.push($scope.data.post.seats[i][index]);
            }
            
        }
        $scope.currentSelection = 'col'
        $scope.currentSelectionIndex = index;
    };

    /*
     * Select individual seat
     */
    $scope.selectSeat = function (seat, row, col) {
        if ($scope.currentSelection == 'row' || $scope.currentSelection == 'col') {
            $scope.resetSelections();
        }

        if (seat.type == 'selected' && seat.type != 'general')
        {
            var index = $scope.selectedSeats.indexOf(seat);
            if (index >= 0) {
                $scope.selectedSeats.splice(index, 1);
            }
            seat.type = 'general';
        } else {
            seat.type = 'selected';
            $scope.selectedSeats.push(seat);
        }
        $scope.currentSelection = 'seat';

        //$scope.showPopup();
          $scope.em_call_popup("#pm-change-password");
    }
    
    /* 
     * Select seats row for bulk operation(like: Reserve or reset)
     */
   $scope.selectRow = function (index) {

        $scope.resetSelections();
         for (var i = 0; i < $scope.data.post.seats[index].length; i++) {
            if($scope.data.post.seats[index][i].type == 'general' || $scope.data.post.seats[index][i].type =='selected')
            {
                 $scope.data.post.seats[index][i].type = 'selected';
                 $scope.selectedSeats.push($scope.data.post.seats[index][i]);
            }
           
       }
        $scope.currentSelection = 'row';
        $scope.currentSelectionIndex = index;

    };
    
    // Resetting current selections
    $scope.resetSelections = function () {
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            $scope.selectedSeats[i].type = 'general';
        }
        $scope.selectedSeats = [];
        $scope.currentSelection = '';
        $scope.currentSelectionIndex = '';
    }
    
    /*
     * Reserving seat. Changing selected seat status to "reserve"
     */
    $scope.reserveSeat = function () {
        var type = 'reserve';
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            $scope.selectedSeats[i].type = type;
        }
        $scope.selectedSeats = [];
        $scope.currentSelection = '';
    }
    
    /*
     * Calculating margins(aisles) for seat container width
     */
    $scope.adjustContainerWidth= function(columnMargin,index)
    {
        var width= parseInt($scope.seat_container_width.width);
        if(index==0 && columnMargin>0)
        {  
            width += columnMargin;
            $scope.seat_container_width.width = width + "px";
        }
        
    }
    
    /*
     * Watches
     */
    
    /*
     * Watch to show/hide recurrence options
     */
    $scope.$watch('data.post.recurring_option', function (newVal, oldVal) {
        if (newVal == "recurring") {
            jQuery("#recurring_section").show("slow");
            jQuery("#recurring_dates_section").hide("slow");
        }
        if (newVal == "specific_dates") {
            jQuery("#recurring_section").hide("slow");
            jQuery("#recurring_dates_section").show("slow");
        }

    });
    
    /*
     * Watch to show/hide recurrence options
     */
    $scope.$watch('data.post.recurring_option', function (newVal, oldVal) {
        if (newVal == "recurring") {
            jQuery("#recurring_section").show("slow");
            jQuery("#recurring_dates_section").hide("slow");
        }
        if (newVal == "specific_dates") {
            jQuery("#recurring_section").hide("slow");
            jQuery("#recurring_dates_section").show("slow");
        }

    });
    
    /*
     * Watch for performers dropdown
     */
    $scope.$watch('data.post.performer', function (newVal, oldVal) {
       if(newVal)
       {

            if(newVal!==undefined && newVal.length>2 && $scope.data.post.match==1)
               $scope.postForm.performer.$setValidity("invalidPerForMatch", false);
            else
            $scope.postForm.performer.$setValidity("invalidPerForMatch", true);
       }
    });
    
    /*
     * Watch for match field
     */
    $scope.$watch('data.post.match', function (newVal, oldVal) {
        if(newVal)
        {
            if(newVal==1 && $scope.data.post.performer.length>2)
            $scope.postForm.performer.$setValidity("invalidPerForMatch", false);
            else
            $scope.postForm.performer.$setValidity("invalidPerForMatch", true); 
        }
        
       
    });

    /*
     * Watch End Date 
     */
    $scope.$watch('data.post.end_date',function(newVal,oldVal){ 
        // Return in case event/post data is not loaded.
        if(!$scope.data.hasOwnProperty('post'))
            return;
        
        // Copying end date into last booking date
        if(!$scope.postForm.end_date.$pristine)
           $scope.data.post.last_booking_date= $scope.data.post.end_date;
            
        // End date must be greater than start date
        if($scope.compareDate($scope.data.post.start_date,$scope.data.post.end_date,$scope.data.post.date_format))
            $scope.postForm.end_date.$setValidity("invalidEndDate", false);
        else
            $scope.postForm.end_date.$setValidity("invalidEndDate", true);
        
        // End date must be greater than or equal to last booking date
        if($scope.compareDate($scope.data.post.last_booking_date,$scope.data.post.end_date,$scope.data.post.date_format))
            $scope.postForm.last_booking_date.$setValidity("invalidBookingDate", false);
        else
            $scope.postForm.last_booking_date.$setValidity("invalidBookingDate", true);
        
        
        //copying event start date into startbooking date
         if(!$scope.postForm.start_date.$pristine)
           $scope.data.post.start_booking_date= $scope.data.post.start_date;
        
        
        // Changing event status to active
        if(newVal!=undefined && oldVal!=undefined)
            $scope.data.post.status= 'publish';
        
        // Checking if event is multiday
        if($scope.data.post.child_events.length>0 || $scope.isMultiDayEvent($scope.data.post.start_date,$scope.data.post.end_date,$scope.data.post.date_format))
        {   
            $scope.multiDay= true;
            $scope.changeMultiCalDates();
        }
        else
            $scope.multiDay= false;
       
    });
    
    /*
     * Watch Start Date 
     */
    $scope.$watch('data.post.start_date',function(newVal,oldVal){ 
        // Return in case event/post data is not loaded.
        if(!$scope.data.hasOwnProperty('post'))
            return;
        
        // Start date must be less than end date
        if($scope.compareDate($scope.data.post.start_date,$scope.data.post.end_date,$scope.data.post.date_format)){
            if(!$scope.postForm.end_date.$pristine)
                $scope.postForm.end_date.$setValidity("invalidEndDate", false);
        }else
             $scope.postForm.end_date.$setValidity("invalidEndDate", true);
       
        // Checking if event is multiday 
        if($scope.data.post.child_events.length>0 || $scope.isMultiDayEvent($scope.data.post.start_date,$scope.data.post.end_date,$scope.data.post.date_format))
        {
            $scope.multiDay= true;
            $scope.changeMultiCalDates();
        } 
        else
           $scope.multiDay= false;
    });
    
    /*
     * Watch Last booking date
     */
    $scope.$watch('data.post.last_booking_date',function(newVal,oldVal){
        // Return in case event/post data is not loaded.
        if(!$scope.data.hasOwnProperty('post'))
            return;
        
        // Last booking date should be less than or equal to end date
        if($scope.compareDate($scope.data.post.last_booking_date,$scope.data.post.end_date,$scope.data.post.date_format))
            $scope.postForm.last_booking_date.$setValidity("invalidBookingDate", false);
        else
             $scope.postForm.last_booking_date.$setValidity("invalidBookingDate", true);
    });
    
    /*
     * Watch Dicount No. of tickets
     */
    $scope.$watch('data.post.discount_no_tickets',function(newVal,oldVal)
    {   // Return in case event/post data is not loaded.
        if(!$scope.data.hasOwnProperty('post'))
            return;

        // No. of tickets should not be excedded seating capacity
        if(newVal>0 && $scope.data.post.seating_capacity>0 && newVal>$scope.data.post.seating_capacity)
           $scope.postForm.discount_no_tickets.$setValidity("exceededCapacity", false);
        else
        {
            $scope.postForm.discount_no_tickets.$setValidity("exceededCapacity", true);
        }
           
        
    });
    
    /*
     * Watch Maximum ticket per person
     */
    $scope.$watch('data.post.max_tickets_per_person',function(newVal,oldVal)
    { 
        if(newVal>0 && $scope.data.post.seating_capacity>0 && newVal>$scope.data.post.seating_capacity)
           $scope.postForm.max_tickets_per_person.$setValidity("exceededCapacity", false);
        else
        {
            // Event/post data must be loaded.
            if($scope.data.hasOwnProperty('post'))
                $scope.postForm.max_tickets_per_person.$setValidity("exceededCapacity", true);
        }
    });
    
    
    /*
     * Watch Seating Capacity
     */
    
//    $scope.$watch('data.post.seating_capacity',function(newVal,oldVal)
//    { 
//        // Verify capacity
//        if(newVal>0 && newVal !== oldVal)
//           $scope.verify_capacity(newVal);
//    });
 
    /*
     * Watches ends here
     */
    
    /****************** Add/Edit event functions ends here ***************/
    
    /*
     * Controller intitialization for data load 
     */
    $scope.initialize = function (screen) {
        if (screen == "edit") {
            $scope.preparePageData();
            // Adding Jquery Sortable function for Gallery
            jQuery("#em_draggable").sortable({
                stop: function (event, ui) {
                    $scope.data.post.gallery_image_ids = jQuery("#em_draggable").sortable('toArray');
                    $scope.$apply();
                }
            });

            // Adding Jquery Sortable function for Sponser Gallery
            jQuery("#em_sponser_image_draggable").sortable({
                stop: function (event, ui) {
                    $scope.data.post.sponser_image_ids = jQuery("#em_sponser_image_draggable").sortable('toArray');
                    $scope.$apply();
                }
            });
        }

        if (screen == "list") {
            $scope.prepareEventListPage();
        }
    }
    
    /* Show/Hide element while performing HTTP request */
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    $scope.deleteChildren= function(child_id,start_date)
    {
        $scope.request = {};
        $scope.request.id= $scope.post_id;
        $scope.request.child_id= child_id;
        $scope.request.start_date= start_date;

        var confirmed= confirm("This will delete all data associated with the event day.");
        if(confirmed)
        {
            EMRequest.send('em_delete_child_events',$scope.request).then(function(response){    
              location.reload();
            });
        }
    }
    /*
     * ************************ Event List page functions 
     */
    
    
    /*
     * 
     * Loading data for list page
     */
    $scope.prepareEventListPage = function () {
        
        $scope.data.em_request_context= 'admin_events';
        $scope.data.paged= $scope.paged;
       
        if($scope.data.sort_option=="title" || $scope.data.sort_option=="date")
            $scope.order="ASC";
        else
            $scope.order="DESC";
        
        
        $scope.data.order=$scope.order;
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.data= response.data;
                $scope.deleteSelections = response.data.total_posts;
            $scope.progressStop();
        });
    }
    
    /*
     * Booking fill bar for event list page
     */
    $scope.getProgressStyle= function(post)
    {   
        var style= {"height":"10px","background-color": "#80c9ff", "width": (post.sum/post.capacity) * 100 + "%"}
        return style;
    }
    
    /*
     * Select event
     */
    $scope.selectPost= function(post_id){
        if($scope.selections.indexOf(post_id)>=0){
            em_remove_from_array($scope.selections,post_id);
        }
        else{
            $scope.selections.push(post_id);
        }
        
    }
    
    /*
     * Delete event
     */
     $scope.deletePost= function(){

        $scope.isSelectAll = jQuery('#em_select_all').is(':checked'); 
    if($scope.isSelectAll ){
        $scope.deleteAllPost();
    }
    else{
       var confirmed= confirm("Are you sure you want to delete "+ $scope.selections.length +" event(s). Please confirm");
        if(confirmed){
           //$scope.progressStart(); 
         
                PostUtility.delete($scope.selections).then(function(data){
                   location.reload();
                });
            
        }
    }
    }
    
     $scope.deleteAllPost= function(){
console.log($scope.deleteSelections);
       var confirmed= confirm("Are you sure you want to delete "+ $scope.deleteSelections.length +" event(s). Please confirm");
        if(confirmed){
           $scope.progressStart(); 
           PostUtility.delete($scope.deleteSelections).then(function(data){
               $scope.deleteSelections=[];
               location.reload();
           });
        }
    }
    
    /*
     * Duplicate event(s)
     */
    $scope.duplicatePosts= function(){
        $scope.progressStart();
        PostUtility.duplicate($scope.selections).then(function(data){
            $scope.progressStop(); 
            location.reload();
        });
    }
    
    /*
     * Pagination navigation
     */
    $scope.pageChanged = function(newPage) {
        $scope.selectedAll=false;
        $scope.paged= newPage;
        $scope.prepareEventListPage();
    };
    
    /*
     * Select all events
     */
    $scope.checkAll = function () {
//        if ($scope.selectedAll) {          
//             angular.forEach($scope.data.posts, function (post) {
//                $scope.selections.push(post.id);
//                  post.Selected = $scope.selectedAll ? post.id : 0; 
//            });
//        } else {
//            $scope.selections= [];
//        } 


  angular.forEach($scope.data.posts, function (post) {
               if ($scope.selectedAll) { 
                 $scope.selections.push(post.id);
               // console.log($scope.selections.push(post.id));
                  post.Selected = $scope.selectedAll ? post.id : 0; 
                   }
                   else{
                        $scope.selections= [];
                        post.Selected = 0;
                   }
            });
      
          
    };
   
    /********************** Generate Alphabet seat sequence ****************/
    $scope.getRowAlphabet = function (rowIndex) {
        var indexNumber = "";
        if (rowIndex > 25) {
            indexNumber = parseInt(rowIndex / 26);
            rowIndex = rowIndex % 26;
        }

        return String.fromCharCode(65 + parseInt(rowIndex)) + indexNumber;
    }
    
      $scope.add_event_tourCompleted= function()
    {
        EMRequest.send('em_add_event_tour_completed',$scope.data.post).then(function(response){
           
        });
    }
    $scope.events_tourCompleted= function()
    {
        EMRequest.send('em_event_tour_completed',$scope.data.post).then(function(response){
        
        });
    }
//    $scope.add_event_tour = function() {
//            jQuery("#em-add-event-joytips").joyride({
//                tipLocation: 'top',
//                autoStart: true,
//                nubPosition: 'bottom',
//                tipAnimation: 'fade',
//                scrollSpeed: 500,
//                postRideCallback: $scope.add_event_tourCompleted
//            });
//            
//    }
    var event_tour_status = jQuery("#em_tour-status").val();
    
    $scope.event_tour = function() {
   
        if(event_tour_status == 0){
            jQuery("#em-events-joytips").joyride({
                    tipLocation: 'bottom',
                    autoStart: true,
                    nubPosition: 'top',
                    tipAnimation: 'fade',
                    scrollSpeed: 500,
                    tipAdjustmentY:-30,
                    postRideCallback: $scope.events_tourCompleted
            });
        }
    };
    
    $scope.another_tour = function() {
      event_tour_status=0;
        $scope.event_tour();
        
    }
    
    $scope.deleteAllChildren= function()
    {
        if($scope.data.post.deleteAllChildren==1)
        {
            $scope.request = {};
            $scope.request.id= $scope.post_id;

            var confirmed= confirm("This will delete all data associated with every event day.");
            if(confirmed)
            {  $scope.progressStart(); 
                EMRequest.send('em_delete_all_child_events',$scope.request).then(function(response){    
                  location.reload();
                    $scope.progressStop(); 
                });
            }
            else
               $scope.data.post.deleteAllChildren=0; 
        }
    }
    
    
       $scope.updateSeatSequence= function(seat)
    {
        if(!seat.seatSequence)
            seat.seatSequence= $scope.getRowAlphabet(seat.row) + "-" + seat.col;            
    }
    
     $scope.em_call_popup= function(dialog) {
        
        var pmId = dialog + "-dialog";

        jQuery(pmId).siblings('.pm-popup-mask').show();
        jQuery(pmId).show();
        jQuery('.pm-popup-container').css("animation", "pm-popup-in 0.3s ease-out 1");
        
      //   $scope.IsVisible = $scope.IsVisible ? false : true;
    
    }
    
        $scope.em_call_scheme_popup= function(dialog) {
        var selectedSeatSeq= [];    
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            selectedSeatSeq[i]= $scope.selectedSeats[i].seatSequence;
        }
        jQuery("#custom_seat_sequences").val(selectedSeatSeq.join());
   
        var pmId = dialog + "-dialog";
        
        jQuery(pmId).siblings('.pm-popup-mask').show();
        jQuery(pmId).show();
        jQuery('.pm-popup-container').css("animation", "pm-popup-in 0.3s ease-out 1");
      
         $scope.scheme_popup = $scope.scheme_popup ? false : true;
    }
    
    jQuery('.pm-popup-close, .pm-popup-mask').click(function (){
          jQuery('.pm-popup-mask').hide();
          jQuery('.pm-popup-mask').next().hide();
      });
    
    
    $scope.showSeatOptions= function(seat)
    {
       $scope.currentSeat= seat;
    }
    
    $scope.updateCurrentSeat= function()
    {
       $scope.currentSeat.seatSequence=  jQuery("#custom_seat_seq").val();
    }
    
    $scope.updateCurrentSeatScheme= function()
    {
      
        var str = jQuery("#custom_seat_sequences").val();
     var strval = str.split(',');
     
     if(strval.length == $scope.selectedSeats.length && strval.length > 0){
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            if(strval[i].trim()!="")
                $scope.selectedSeats[i].seatSequence = strval[i];           
        }
    }        
         else{
           alert('Please verify seating arrangement.');
        }
    }
    
    
    
    
});

