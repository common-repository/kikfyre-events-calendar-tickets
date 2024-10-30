
/*
 * Event Ticket  Controller
 */

eventMagicApp.controller('eventTicketCtrl',function($scope, $http,MediaUploader,PostUtility,EMRequest){
    $scope.data={};
    
    
    $scope.requestInProgress= false;
    $scope.formErrors='';
    $scope.paged=1;
    $scope.selections= [];
    
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    $scope.initialize= function(type){
        if(type=="edit"){
            $scope.preparePageData();
        }
        
        if(type=="list"){
            $scope.prepareListPage();
        }
    }
    
    //Loads page data for Add/Edit page 
    $scope.preparePageData = function () {
        $scope.data.em_request_context= 'admin_ticket_template';
        
        // If "Edit" page
        var post_id= em_get('post_id');
        if (post_id > 0) {
             $scope.data.post_id=post_id;
        }
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            if(true){
                $scope.data= response.data;
                
                // Initializing color type fields border and bckground
                if($scope.data.post.background_color)
                    jQuery("#em_background_color_picker").css("background-color","#" + $scope.data.post.background_color);
                else{
                      $scope.data.post.background_color='E2C699';
                     jQuery("#em_background_color_picker").css("background-color","#" + $scope.data.post.background_color);                
                }
                    

                if($scope.data.post.border_color)
                    jQuery("#em_border_color_picker").css("background-color","#" + $scope.data.post.border_color);
                else{
                     $scope.data.post.border_color='C8A366';
                     jQuery("#em_border_color_picker").css("background-color","#" + $scope.data.post.border_color);
                }
                
                // Initializing color Font Color 1 and Font Color 2
                if($scope.data.post.font_color1)
                    jQuery("#em_font1_color_picker").css("background-color","#" + $scope.data.post.font_color1);
                else{
                      $scope.data.post.font_color1='865c16';
                     jQuery("#em_font1_color_picker").css("background-color","#" + $scope.data.post.font_color1);                
                }
                
                if($scope.data.post.font_color2)
                    jQuery("#em_font2_color_picker").css("background-color","#" + $scope.data.post.font_color2);
                else{
                     $scope.data.post.font_color2='C8A366';
                     jQuery("#em_font2_color_picker").css("background-color","#" + $scope.data.post.font_color2);
                }
                  

                $scope.setColors();
                $scope.changeStyle();
                $scope.progressStop();
                
            }
        });
    };
    
    /*
     * 
     * WordPress default media uploader to choose image
     */
    $scope.mediaUploader = function (multiImage) {
        var mediaUploader= MediaUploader.openUploader(multiImage);
        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function () {
            attachments = mediaUploader.state().get('selection');
            attachments.map(function (attachment) {
                attachment = attachment.toJSON();
                if(!multiImage){
                    $scope.data.post.logo= attachment.id;
                    $scope.data.post.logo_image= attachment.sizes.thumbnail===undefined ? attachment.sizes.full.url:attachment.sizes.thumbnail.url;
                    $scope.$apply();
                }
                
            });
        });
        // Open the uploader dialog
        mediaUploader.open();
    }
    
    
     /*
     * Save information
     */
    $scope.saveEventTicket = function (isValid) {
        // If form is valid against all the angular validations
        if (isValid) {
            jQuery(".kf-buttonarea .btn").addClass( "kf-saving" );
            $scope.progressStart();
            EMRequest.send('em_save_event_ticket',$scope.data.post).then(function(response){
                $scope.progressStop();
                    // If servers sends Error object
                    data= response.data;
                    if(!data.success){
                         angular.forEach(data, function (value, key) { 
                         $scope.formErrors += value.message;
                         });
                     }

                        if(data.redirect)
                            location.href= data.redirect;
            });
        }
    }

    $scope.prepareListPage= function(){
        $scope.data.paged=$scope.paged;
        $scope.data.em_request_context= 'admin_ticket_templates';
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
            $scope.data= response.data;
        });
    }
    
    $scope.selectPost= function(post_id){
        if($scope.selections.indexOf(post_id)>=0){
            em_remove_from_array($scope.selections,post_id)
        }
        else{
            $scope.selections.push(post_id);
        }  
    }
    
    $scope.deletePosts= function(){
        $scope.progressStart();
        PostUtility.delete($scope.selections).then(function(data){
            location.reload();
        });
    }
    
    $scope.duplicatePosts= function(){
         $scope.progressStart();
         PostUtility.duplicate($scope.selections).then(function(data){
             location.reload();
         });

    }

    $scope.pageChanged = function(newPage) {
        $scope.selectedAll=false;
        $scope.paged= newPage;
        $scope.prepareListPage();
    };
    
    
    $scope.changeStyle= function(){
        var font1= "";
        var font2= "";
        // If Font1 is set
        if($scope.data.post.font1)
            font1 = $scope.data.post.font1;
        
        // If Font2 is set
        if($scope.data.post.font2){
            font2 = $scope.data.post.font2 ; 
        }
        
        
        $scope.setColors();    
        $scope.data.ticket_font_family1= {"font-family":  font1};
        $scope.data.ticket_font_family2= {"font-family":  font2};
    }
    
    $scope.setColors= function(){
        // Check if background color set
        if($scope.data.post.background_color)
        {
           jQuery('.kf-ticket-wrapper').css('background', "#" + $scope.data.post.background_color);           
        }
      
        // Check if border color is set
        if($scope.data.post.border_color)
        {
             jQuery('.kf-event-details-wrap').css('border-right', "10px solid #" + $scope.data.post.border_color);

        }
        
        
        {
           jQuery('.kf-font-color1').css('color', "#" + $scope.data.post.font_color1);           
        }
      
        // Check if border color is set
        if($scope.data.post.border_color)
        {
             jQuery('.kf-font-color2').css('color', "#" + $scope.data.post.font_color2);

        }
        
       
             
    }
    
     $scope.markAll = function () {
//        if ($scope.selectedAll) {          
//            $scope.selectedAll = true;
//        } else {
//            $scope.selectedAll = false;
//        }        
//           angular.forEach($scope.data.posts, function (post) {
//               $scope.selections.push(post.id);
//               post.Selected = $scope.selectedAll ? post.id : 0; 
//        });
        
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
    
//    $scope.markAll= function(){ 
//        angular.forEach($scope.data.posts,function(post,key){
//             $scope.selections.push(post.id);
//        });
// 
//       jQuery(".em_card_check").prop('checked', "checked");
//        
//    };
    
});
