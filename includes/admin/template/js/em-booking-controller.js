/************************************* Booking Controller ***********************************/

eventMagicApp.controller('bookingCtrl',function($scope, $http,MediaUploader,PostUtility,EMRequest){
    $scope.data={};
    
    $scope.requestInProgress= false;
    $scope.formErrors='';
    $scope.paged=1;
    $scope.selections= [];
    $scope.showDates= false;
    $scope.resetPagination= false;
    
    /* Show/Hide element while performing HTTP request */
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    /*
     * Controller intitialization for data load 
     */
    $scope.initialize= function(type){
        if(type=="edit"){
            $scope.preparePageData();
        }
        
        if(type=="list"){
            $scope.resetListPage();
            $scope.prepareListPage();
        }
    }
    
     /*
     * 
     * Loading Event data on init
     */ 
    $scope.preparePageData = function () {
        $scope.data.em_request_context= 'admin_booking';
        $scope.data.post_id= em_get('post_id');
        
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();           
            $scope.data= response.data;
            $scope.calculateDiscount(); 
        });   
        
        var request= {};
        request.post_id=$scope.data.post_id
        $scope.progressStart();
        // Fetches RM data in any
        EMRequest.send('em_rm_custom_datas',request).then(function(res){
                    $scope.progressStop();      
                    return jQuery("#em_rm_custom_data").html(res.data);
        }); 
    };
    
    
    /***************************** Single Booking Page ****************
     /*
     * Save Booking information
     */
    $scope.savePost = function () {
        // If form is valid against all the angular validations
        $scope.progressStart();
       
        EMRequest.send('em_save_booking',$scope.data.post).then(function(response){
            $scope.progressStop();
            if(!response.error_status)
            {
                $scope.preparePageData();
            }
        });
        
    }
    
    
    /*
     * Calculating discount on individual booking page
     */
    $scope.calculateDiscount= function(){
        $scope.data.price= $scope.data.post.order_info.quantity*$scope.data.post.order_info.item_price;
        $scope.data.final_price=  $scope.data.price-$scope.data.post.order_info.discount;
    }

   
    /*
     * Initiating refund from admin 
     */
    $scope.cancelBooking= function()
    {
        var flag= confirm("Are  you sure you want to refund this transaction");
        if(flag)
        {   $scope.progressStart();
            EMRequest.send('em_cancel_booking',$scope.data.post).then(function(response){
            $scope.progressStop();
            $scope.preparePageData();
            $scope.refund_status= response.data.msg;
            }); 
        }
    }
    
    /*
     * Requesting reset password mail
     */
    $scope.reset_password_mail= function(){
       var request= {};
       request.post_id=$scope.data.post.post_id;
       $scope.progressStart();
       EMRequest.send('em_resend_mail',request).then(function(response){
            $scope.progressStop();   
            alert("Reset Password Mail has been sent successfully");
        });
    }
    
    /*
     * Requesting cancelation mail
     */
    $scope.cancellation_mail= function(){
       var request= {};
     
        request.post_id=$scope.data.post.post_id;
        $scope.progressStart();
        EMRequest.send('em_booking_cancellation_mail',request).then(function(response){
            $scope.progressStop(); 
            alert("Cancellation Mail has been sent successfully");
        });
    }
    
    /*
     * Requesting confirm mail
     */
    $scope.confirm_mail= function(){
       var request= {};
        request.post_id=$scope.data.post.post_id;
        $scope.progressStart();
        EMRequest.send('em_booking_confirm_mail',request).then(function(response){
            $scope.progressStop(); 
            alert("Confirm Mail has been sent successfully");
        });
    }
    
    /*
     * Requesting refund mail
     */
    $scope.refund_mail= function(){
       var request= {};
       request.post_id=$scope.data.post.post_id;
       $scope.progressStart();
       EMRequest.send('em_booking_refund_mail',request).then(function(response){
            $scope.progressStop(); 
            alert("Refund Mail has been sent successfully");
        });
    }
    
    /*
     * Requesting pending mail
     */
    $scope.pending_mail= function(){
       var request= {};
       request.post_id=$scope.data.post.post_id;
       $scope.progressStart();
       EMRequest.send('em_booking_pending_mail',request).then(function(response){
            $scope.progressStop(); 
            alert("Pending Mail has been sent successfully");
          
        });
    }
    
    
    
    /******************* Booking List Page **************************/
    /*
     * 
     * Loading list data on init
    */
    $scope.prepareListPage= function(){
        $scope.data.em_request_context= 'admin_bookings';
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
            $scope.data= response.data;
            console.log( $scope.data.posts);
        });
    }
    
    /*
     * Reseting pagination
     */
    $scope.resetListPage= function()
    {
        $scope.data.paged=$scope.paged;
        $scope.data.em_request_context= 'admin_bookings';
        $scope.data.date_from= jQuery("#em_date_from").val();
        $scope.data.date_to= jQuery("#em_date_to").val();
    }
    
    /*
     * Deleting booking
     */
    $scope.deletePosts= function(){
    
      
        var confirmed= confirm("This will delete all data associated with this booking .");
        { 
            if(confirmed){        
                $scope.progressStart();
                PostUtility.delete($scope.selections).then(function(data){
                    location.reload();
                });
            }
        }
         
    }
    
    
       
    
    
    /*
     * Chaining filter params
     */
    $scope.filter= function(){
       
        if($scope.data.filter_between=="range")
        {   
            $scope.data.date_from= jQuery("#em_date_from").val();
            $scope.data.date_to= jQuery("#em_date_to").val();
        }
        
        $scope.data.paged=1;
        $scope.prepareListPage();
       
    }
    
    /*
     * Called when pagination item clicked
     */
    $scope.pageChanged = function(newPage) {
            $scope.data.paged= newPage;
            $scope.prepareListPage();
             $scope.selectedAll=false;
    };
    
    /*
     * Select all events
    */
   $scope.markAll= function(){ 

//       if(jQuery("#em_bulk_selector").prop('checked'))
//       {
//           angular.forEach($scope.data.posts,function(post,key){
//              
//             $scope.selections.push(post.id);
//            });
//           jQuery(".em_card_check").prop('checked', true);
//         
//       }
//       else
//       {
//            angular.forEach($scope.data.posts,function(post,key){
//            $scope.selections.splice(post.id,1);
//            });
//           jQuery(".em_card_check").prop('checked', false);
//            
//           $scope.selections= [];
//       }

  angular.forEach($scope.data.posts, function (post) {
               if ($scope.selectedAll) { 
                 $scope.selections.push(post.id);
               // console.log($scope.selections.push(post.id));
                  post.selected = $scope.selectedAll ? post.id : 0; 
                  console.log( post.selected);
                   }
                   else{
                        $scope.selections= [];
                        post.selected = 0;
                   }
            });
 
       $scope.prepare_export_link(false); 

    };
    
    /*
     * Adding or removing items on selection/deselection
     */
  $scope.updateSelection= function(post_id){
       
        if($scope.selections.indexOf(post_id)>=0){
            $scope.selections= em_remove_from_array($scope.selections,post_id);
        }
        else{
            $scope.selections.push(post_id);
        } 
        $scope.prepare_export_link(false);
    }
    
    /*
     * Watching date fields for fitler
     */
    $scope.$watch('data.filter_between',function(newVal,oldVal){
        if(newVal=="range")
            $scope.showDates= true;
        else
            $scope.showDates= false;
    });
    
     $scope.prepare_export_link = function(submitStatus){
       jQuery("#em_post_query").val(encodeURIComponent(JSON.stringify($scope.data.post_query)));
      
       if($scope.selections.length>0)
        jQuery("#em_selected_bookings").val($scope.selections);
       if(submitStatus)
        em_booking_export.submit(); 
   } 
   $scope.printTicket = function(booking_id,seat_no){
        var request= {};
        request.booking_id=booking_id;
        request.seat_no= seat_no;
       
        EMRequest.send('em_print_ticket',request).then(function(response){
            var ticket = jQuery("#em_printable").html(response.data);
            jQuery(ticket).remove();
            jQuery(ticket).show();
            jQuery('body').after(ticket);
            jQuery('body').hide();
            window.print();
            setTimeout(function(){
                jQuery('body').show();
            jQuery(ticket).hide();
            },100);
            
       });
   }
   $scope.printTicketStand = function(booking_id){
       
       var request= {};
        request.booking_id=booking_id;
       
        EMRequest.send('em_print_ticket',request).then(function(response){
            var ticket = jQuery("#em_printable").html(response.data);
            jQuery(ticket).remove();
            jQuery(ticket).show();
            jQuery('body').after(ticket);
            jQuery('body').hide();
            window.print();
            setTimeout(function(){
                jQuery('body').show();
            jQuery(ticket).hide();
            },100);
       });
   }
});
