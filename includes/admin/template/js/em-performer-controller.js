
/*
 * Performer Controller
 */
eventMagicApp.controller('performerCtrl',function($scope, $http,MediaUploader,PostUtility,EMRequest){
    $scope.data={};
    var post_id=0;
    $scope.paged=1;
    $scope.selections=[];
    $scope.post_edit= false;
    $scope.requestInProgress= false;
    
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    //Loads page data for Add/Edit page 
    $scope.preparePageData = function () {
        $scope.data.em_request_context='admin_performer';
        // If "Edit" page
        if (em_get('post_id') > 0) {
            $scope.data.post_id= em_get('post_id');
            $scope.post_edit= true;
        }
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.data= response.data;
            $scope.progressStop();
        })
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
                console.log(attachment);
                if(!multiImage){
                   // Performer Image
                    $scope.data.post.feature_image = attachment.sizes.thumbnail === undefined ? attachment.sizes.full.url : attachment.sizes.thumbnail.url;   
                    $scope.data.post.feature_image_id= attachment.id;
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
    $scope.savePerformer = function (isValid) {
        // If form is valid against all the angular validations
        if (isValid) {
            if( jQuery('#description').is(':visible') ) {
                $scope.data.post.description= jQuery('#description').val();
            } 
            else 
            {
                $scope.data.post.description= tinymce.get('description').getContent();   
            }
            $scope.progressStart();
            EMRequest.send('em_save_performer',$scope.data.post).then(function(response){
                $scope.progressStop();
                data= response.data;
                        if(!data.redirect){
                            angular.forEach(response.data, function (value, key) { 
                            $scope.formErrors += value.message;
                            });
                        }
                        
                        if(data.redirect)
                            location.href=data.redirect;
            });
        }
    }
    
    
    $scope.initialize= function(task){
        if(task=="edit"){
            $scope.preparePageData();
        }
        
        if(task=="list"){
            $scope.preparePerformerListPage();
        }
    }

    $scope.preparePerformerListPage= function(){
        $scope.data.em_request_context='admin_performers';
        $scope.data.paged=$scope.paged;
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
             $scope.data= response.data;
         });
    }
    
    $scope.pageChanged= function(pageNumber){
        $scope.selectedAll=false;
        $scope.paged= pageNumber;
        $scope.preparePerformerListPage();
    }
    
    $scope.deletePosts= function(){
         var confirmed= confirm("Are you sure you want to delete. Please confirm");
         if(confirmed){
            $scope.progressStart(); 
            PostUtility.delete($scope.selections).then(function(data){
                location.reload();
            });
         }
    }
    
    $scope.duplicatePosts= function(){
        $scope.progressStart(); 
        PostUtility.duplicate($scope.selections).then(function(data){
            location.reload();
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
    
    $scope.checkAll = function () {
//        if ($scope.selectedAll) {          
//            $scope.selectedAll = true;
//        } else {
//            $scope.selectedAll = false;
//        }        
//           angular.forEach($scope.data.posts, function (post) {
//               post.Selected = $scope.selectedAll ? post.id : 0; 
//        });
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
        
        } 
    
    
// $scope.markAll= function(){ 
//        angular.forEach($scope.data.posts,function(post,key){
//             $scope.selections.push(post.id);
//        });
 
//       jQuery(".em_card_check").prop('checked', "checked");
//        
//    };
});

