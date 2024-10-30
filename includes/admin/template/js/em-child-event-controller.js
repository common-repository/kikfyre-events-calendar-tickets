
/************************************* Event Controller ***********************************/

eventMagicApp.controller('childEventCtrl', function ($scope, $http, MediaUploader, Seat,TermUtility,EMRequest,PostUtility) {
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
    
    /*
     * ********************* Add/Edit event functions
     */
    
    
    angular.element(document).ready(function () {
     em_set_date_defaults();
    });
   
  
   
   
    
    // Takes a 1-digit number and inserts a zero before it
    $scope.padNumber = function (number) {
        var ret = new String(number);
        if (ret.length == 1)
            ret = "0" + ret;
        return ret;
    }
    


    /*Saving event
     */
    $scope.savePost = function (isValid) {
        $scope.resetSelections();
        // If form is valid against all the angular validations
        if (isValid) {
            $scope.data.post.description=  jQuery('#description').is(':visible') ? jQuery('#description').val() : tinymce.get('description').getContent();  
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
                     self.parent.tb_remove();                   
                     //window.top.location.reload();
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
            //$scope.syncFromVenue();
            
        });
  
        $scope.progressStop();
      
         
    };
    
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
    * 
    * Loading seat container width as per initial seating structure
    */ 
   $scope.setSeatContainerWidth= function()
   {
       if ($scope.data.post.seats!== undefined && $scope.data.post.seats.length>0) {

                var seat_container_width= ($scope.data.post.seats[0].length*35) + 80 + "px";
                $scope.seat_container_width={ "width" : seat_container_width };
      
        }
   };
   
    $scope.initializeDateTimePickers= function()
    {
        var minDate= new Date();
        var maxDate;
        
        minDate= jQuery.datepicker.parseDate($scope.data.post.date_format,$scope.data.post.start_date);
        maxDate= jQuery.datepicker.parseDate($scope.data.post.date_format,$scope.data.post.end_date);
        maxDate.setHours(23);
        maxDate.setMinutes(59);
        console.log(maxDate);
        jQuery('#event_start_date').datetimepicker({controlType: 'select',oneLine: true,changeYear: true,timeFormat: 'HH:mm', minDate: minDate, maxDate: maxDate});
        jQuery("#event_end_date").datetimepicker({controlType: 'select',changeYear: true,oneLine: true,timeFormat: 'HH:mm', minDate: minDate, maxDate: maxDate});
         jQuery("#event_last_booking_date").datetimepicker({controlType: 'select',changeYear: true,oneLine: true,timeFormat: 'HH:mm'});
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
    
    /****************** Add/Edit event functions ends here ***************/
    
    /*
     * Controller intitialization for data load 
     */
    $scope.initialize = function (screen) {
        if (screen == "edit") {
            $scope.preparePageData();
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
    
   
    /********************** Generate Alphabet seat sequence ****************/
    $scope.getRowAlphabet = function (rowIndex) {
        var indexNumber = "";
        if (rowIndex > 25) {
            indexNumber = parseInt(rowIndex / 26);
            rowIndex = rowIndex % 26;
        }

        return String.fromCharCode(65 + parseInt(rowIndex)) + indexNumber;
    }
     $scope.$watch('data.post.allow_discount', function (newVal, oldVal) {
        if (parseInt(newVal) == 1)
            jQuery("#em_volume_discount").show("slow");
        else
            jQuery("#em_volume_discount").hide("slow");
    });
    
     $scope.verify_capacity= function(newVal)
    {
        var data= {};
        data.venue_id= $scope.data.post.venue;
         data.event_id= $scope.data.post.id;
        $scope.progressStart();
        EMRequest.send('em_get_venue_capcity',data).then(function(response){

                $scope.data.venue_capacity= response.data.capacity;
                if($scope.data.venue_capacity>0 && newVal>$scope.data.venue_capacity)
                    $scope.postForm.seating_capacity.$setValidity("capacityExceeded", false);
                else
                    $scope.postForm.seating_capacity.$setValidity("capacityExceeded", true);
                $scope.progressStop();
            });
    }
    
    $scope.$watch('data.post.seating_capacity',function(newVal,oldVal)
    {
        // Verify capacity
        if(newVal>0 && newValue !== oldValue){ 
           $scope.verify_capacity(newVal);}
    });
    
    
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
        
        
        // Changing event status to active
        if(newVal!=undefined && oldVal!=undefined)
            $scope.data.post.status= 'publish';
        
        // Checking if event is multiday
     
       
    });
    
    
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
        jQuery("#custom_seat_sequences").html(selectedSeatSeq.join());
      
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
    
});
