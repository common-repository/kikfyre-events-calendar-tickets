/**
 * 
 * Venue controller
 */
eventMagicApp.controller('venueCtrl', function ($scope, $http, MediaUploader, Seat,TermUtility,EMRequest) {
    $scope.data = {};
    $scope.data.sort_option="name";
    
    $scope.selectedSeats = [];
    $scope.currentSelection = '';
    $scope.paged=1;
    $scope.order= 'ASC';
    $scope.term_edit= false;
    $scope.requestInProgress= false;
    $scope.seatPopup= false;
    $scope.seat_container_width='';
    $scope.currentSeat='';
    $scope.IsVisible = false;
    $scope.scheme_popup =  false;
    $scope.formErrors=[];
   
    angular.element(document).ready(function () {
         
     em_set_date_defaults();

    });
    
    $scope.showPopup=function(){
          $scope.seatPopup= true;
    }
    
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    //Loads page data for Add/Edit Venue page 
    $scope.preparePageData = function () {
        
         // If "Edit" page
          var id = em_get('term_id');
          if (id > 0) 
                $scope.data.id=id;
        
         $scope.data.em_request_context= 'admin_venue';
         $scope.progressStart();
         EMRequest.send('em_load_strings',$scope.data).then(function(response){
                
                $scope.data= response.data;
            
                /*
                * Incase of edit page
                */
                if (id > 0) { 
                    $scope.term_edit= true;
                    // Dispatching event to load marker on Map as per the current address
                   // em_event_dispatcher('change', 'em-pac-input');
                        $scope.data.term.addresses.push($scope.data.term.address);
                        if($scope.data.term.map_configured){
                            em_initMap('map', 'em-pac-input', 'type-selector', $scope.data.term.addresses);
                        }
                        
                        // Updating rows column values in seating structure
                        if($scope.data.term.seats.length>0)
                        {
                            $scope.rows= $scope.data.term.seats.length;
                            $scope.columns= $scope.data.term.seats[0].length;
                            var seat_container_width= ($scope.data.term.seats[0].length*35) + 80 + "px";
                            $scope.seat_container_width={ "width" : seat_container_width };
                        }
                }
                else{
                    if($scope.data.term.map_configured){
                         em_initMap();
                    }
                   
                }
                $scope.progressStop();
                
         });
    };

    /*
     * 
     * WordPress default media uploader to choose gallery images for Venue
     */
    $scope.mediaUploader = function (multiImage) {
        var mediaUploader = MediaUploader.openUploader(multiImage);
        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function () {
            attachments = mediaUploader.state().get('selection');
            attachments.map(function (attachment) {
                attachment = attachment.toJSON();
                console.log(attachment);
                var imageObj = attachment.sizes.thumbnail===undefined ? {src: [attachment.sizes.full.url], id: attachment.id} : {src: [attachment.sizes.thumbnail.url], id: attachment.id};
                $scope.data.term.images.push(imageObj);
                // Pushing attachment ID in model
                $scope.data.term.gallery_images.push(attachment.id);
                $scope.$apply();

            });
        });
        // Open the uploader dialog
        mediaUploader.open();
    };

    $scope.adjustContainerWidth= function(seat)
    { 
        var width= parseInt($scope.seat_container_width.width);
        var columnMargin= seat.columnMargin;
        if(seat.row==0 && columnMargin>0)
        {  
            width += columnMargin;
            $scope.seat_container_width.width = width + "px";
        }
        
        $scope.updateSeatSequence(seat);
    }

    /*
     * Save venue information
     */
    $scope.saveTerm = function (isValid) {
        $scope.resetSelections();
        $scope.formErrors=[];
        // If form is valid against all the angular validations
        if (isValid) {
            if( jQuery('#description').is(':visible') ) {
                $scope.data.term.description= jQuery('#description').val();
            } 
            else 
            {
                $scope.data.term.description= tinymce.get('description').getContent();   
            }
            
            $scope.progressStart();
            if($scope.data.term.type=="seats" &&  $scope.data.term.seats.length==0)
            {
                //$scope.createSeats($scope.rows,$scope.columns);
             
            }
            
          
            EMRequest.send('em_save_venue',$scope.data.term).then(function(response){
                data= response.data;
                if (data.error_status) {  
                    $scope.formErrors = data.errors;
                    jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                    $scope.progressStop();
                    return;
                }
                else{
                       location.href=data.redirect;
                }   
                $scope.progressStop();
            });        
        }
    }

    // Deletion of gallery images from current Venue model (Actual deletion will be done after save)
    $scope.deleteGalleryImage = function (image_id, index) {
        $scope.data.term.images.splice(index, 1);
        $scope.data.term.gallery_images = em_remove_from_array($scope.data.term.gallery_images, image_id);
    }

    /*
     * Stage Booking system
     * 
     */
    
    // Creating seat objects as per the value in rows and columns
    $scope.createSeats = function (rows, columns) {
        $scope.progressStart();
        $scope.data.term.seats = new Array(rows);
        for (var i = 0; i < rows; i++) {
            $scope.data.term.seats[i] = [];
        }

        for (var i = 0; i < rows; i++) {
            for (var j = 0; j < columns; j++) {
                $scope.data.term.seats[i][j] = new Seat('general', 9, i, j);
            }
        }
        
        // Update Seating capacity field
        if(rows * columns>0)
            $scope.data.term.seating_capacity= rows * columns;
        else
            $scope.data.term.seating_capacity= 0;
        $scope.progressStop();
       
       
        var seat_container_width= ($scope.data.term.seats[0].length*40) + 80 + "px";
        $scope.seat_container_width={ "width" : seat_container_width };

        if($scope.data.term.seating_capacity==rows*columns){
            $scope.termForm.seating_capacity.$setValidity("invalidCapacity", true);
            
        }
            
         
        
    }

    $scope.resetSelections = function () {
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            $scope.selectedSeats[i].type = 'general';
        }
        $scope.selectedSeats = [];
        $scope.currentSelection = '';
        $scope.currentSelectionIndex = '';
    }

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

    $scope.selectRow = function (index) {

        $scope.resetSelections();
        for (var i = 0; i < $scope.data.term.seats[index].length; i++) {
            $scope.data.term.seats[index][i].type = 'selected';
            $scope.selectedSeats.push($scope.data.term.seats[index][i]);            
        }
       
        $scope.currentSelection = 'row';
        $scope.currentSelectionIndex = index;

    };

    $scope.selectColumn = function (index) {
        $scope.resetSelections();
        for (var i = 0; i < $scope.data.term.seats.length; i++) {

            $scope.data.term.seats[i][index].type = 'selected';
            $scope.selectedSeats.push($scope.data.term.seats[i][index]);
        }
        $scope.currentSelection = 'col'
        $scope.currentSelectionIndex = index;
    };

    $scope.createAisles = function () {
        var type = 'isles';
        var selectedRow;
        var selectedColumn;
        var width=  parseInt($scope.seat_container_width.width);
        
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            $scope.selectedSeats[i].type = type;

            if ($scope.currentSelection == 'col') {
                if ($scope.selectedSeats[i].columnMargin == 0){
                    $scope.selectedSeats[i].columnMargin += 30;
                    $scope.seat_container_width.width = width + 30 + "px";
                }
                else
                {
                    
                     $scope.selectedSeats[i].columnMargin = 0;
                     $scope.seat_container_width.width = width - 30 + "px";
                }
                    
            }


            if ($scope.currentSelection == 'row') {
                if ($scope.selectedSeats[i].rowMargin == 0)
                    $scope.selectedSeats[i].rowMargin += 30;
                else
                    $scope.selectedSeats[i].rowMargin = 0;
            }


        }

        $scope.resetSelections();

        //  console.log($scope.term.seats);
    }

    $scope.reserveSeat = function () {
        var type = 'reserve';
        for (var i = 0; i < $scope.selectedSeats.length; i++) {
            $scope.selectedSeats[i].type = type;
        }
        $scope.selectedSeats = [];
        $scope.currentSelection = '';
    }


    $scope.initialize = function (type) {
        if (type == "edit") {
         //   $scope.preparePageData();
            jQuery(document).ready(function () {

                // Loading all the required data before form load
                $scope.preparePageData();

                jQuery("#em_draggable").sortable({
                    stop: function (event, ui) {
                        $scope.data.term.gallery_images = jQuery("#em_draggable").sortable('toArray');
                        $scope.$apply();
                        console.log($scope.data.term.gallery_images);
                    }
                });

                jQuery("#established").datepicker({changeMonth: true,yearRange: "-300:+0", changeYear: true,dateFormat: $scope.data.date_format, maxDate: new Date});

            });
        }

        if (type == "list") { 
               $scope.prepareVenueListPage(); 
        }

    };

    $scope.prepareVenueListPage= function(){
        if($scope.data.sort_option=="count")
            $scope.order="DESC";
        else
            $scope.order="ASC";
        
        $scope.data.em_request_context='admin_venues';
        $scope.data.paged=$scope.paged;
        $scope.data.order=$scope.order;
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
           
            $scope.data= response.data;
        });
    };
    
    $scope.selections=[];

    $scope.selectTerm= function(id){
        if($scope.selections.indexOf(id)>=0){
           $scope.selections=  em_remove_from_array($scope.selections,id)
        }
        else{
            $scope.selections.push(id);
        }
        
        
    }
    
    $scope.deleteTerms= function(){
        var confirmed= confirm("All events associated to venue(s) will be deleted. Please confirm");
        if(confirmed){
            $scope.progressStart();
            TermUtility.delete($scope.selections,$scope.data.tax_type).then(function(data){
                $scope.progressStop();
                location.reload();
            });
        }
       
    }
    
    $scope.pageChanged= function(pageNumber){
        $scope.selectedAll=false;
        $scope.paged= pageNumber;
        $scope.prepareVenueListPage();
    }
    
    $scope.checkAllVENUES = function (){
      
//        if ($scope.selectAll) {      
//          
//            $scope.selectAll = true;
//        } else {
//            $scope.selectAll = false;
//        }        
//          angular.forEach($scope.data.terms, function (term) {
//             //   alert(term.id);
//               $scope.selections.push(term.id);
//                term.Selected = $scope.selectAll ? term.id : 0; 
//               
//               
//             
//        });
 angular.forEach($scope.data.terms, function (term) {
               if ($scope.selectedAll) { 
                 $scope.selections.push(term.id);
               // console.log($scope.selections.push(post.id));
                  term.Selected = $scope.selectedAll ? term.id : 0; 
                   }
                   else{
                        $scope.selections= [];
                        term.Selected = 0;
                   }
            });
    };
    
    
   
    
    
    $scope.checkCapacity= function(event)
    {
       if($scope.data.term.seats[0]==undefined)
           return;
       var totalNoSeats= $scope.data.term.seats.length * ($scope.data.term.seats[0].length);
       if(totalNoSeats>0)
       {
            
            if($scope.data.term.seating_capacity>totalNoSeats)
            {
                alert("Seating capacity is not matching with total seats.");
                $scope.data.term.seating_capacity= totalNoSeats;
            }
       }
      
    }
    
    $scope.$watch('data.term.seating_capacity',function(newValue,oldValue){
       if($scope.data.hasOwnProperty('term') && $scope.data.term.type=="seats")
       {    
           if(newValue<=0)
            $scope.termForm.seating_capacity.$setValidity("min", false);
           else
            $scope.termForm.seating_capacity.$setValidity("min", true);
            
           if($scope.data.term.seats[0]==undefined)
                return;
           
            
           var totalNoSeats= $scope.data.term.seats.length * ($scope.data.term.seats[0].length);
       // console.log(angular.element('#row').val());  console.log('--');   console.log(angular.element('#col').val());  console.log('--'); console.log(totalNoSeats);  console.log('--');
           //console.log($scope.data.term.seats.length);  console.log('--'); console.log($scope.data.term.seats[0].length);
           if(newValue!=totalNoSeats)
               $scope.termForm.seating_capacity.$setValidity("invalidCapacity", false);
           else
               $scope.termForm.seating_capacity.$setValidity("invalidCapacity", true);
           
          
       }
    })
    $scope.getRowAlphabet = function (rowIndex) {
        var indexNumber = "";
        if (rowIndex > 25) {
            indexNumber = parseInt(rowIndex / 26);
            rowIndex = rowIndex % 26;
        }

        return String.fromCharCode(65 + parseInt(rowIndex)) + indexNumber;
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
              //  console.log(strval[i]);
              if(strval[i].trim()!="")
                $scope.selectedSeats[i].seatSequence = strval[i];
            }
        }
        else{
           alert('Please verify seating arrangement.');
        }
           
        }
    
});


