/************************************* EventType Controller ***********************************/

eventMagicApp.controller('eventTypeCtrl',function($scope, $http,TermUtility,EMRequest){
    $scope.data={};
    $scope.requestInProgress= false;
    $scope.formErrors=[];
    $scope.selections = [];
    $scope.paged=1;
    
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
     * Setting background color
    */
    $scope.bgColor= function(term)
    {   
        var style= {"background-color": "#" + term.color};
        return style;
    }
    
    /*
     * Loading Event type page data
     */
    $scope.preparePageData = function () {
        
        $scope.data.em_request_context= 'admin_event_type';
        $scope.data.term_id= em_get('term_id');
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
             $scope.progressStop();
             $scope.data= response.data;
             if($scope.data.term.color)
                jQuery("#em_color_picker").css("background-color","#" + $scope.data.term.color);    
         });
    };
    
     /*
     * Save information
     */
    $scope.saveEventType = function (isValid) {
         // Return if form invalid
         if (!isValid)
             return;
         $scope.formErrors= [];
         if( jQuery('#description').is(':visible') ) 
            $scope.data.term.description= jQuery('#description').val();
         else 
            $scope.data.term.description= tinymce.get('description').getContent();   
            
         $scope.progressStart();
         EMRequest.send('em_save_event_type',$scope.data.term).then(function(response){
            $scope.progressStop();
            $scope.request_in_progress= false;
            data= response.data;
            if(data.error_status){
                $scope.formErrors= data.errors;
                jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
                $scope.progressStop();
                return;
            }
            
             if(data.redirect)
                location.href= data.redirect;
        });
    }
    
    /*
     * Initializa page data on init
     */
    $scope.initialize= function(type){

        if(type=="edit")
            $scope.preparePageData();
        
        if(type=="list")  
            $scope.prepareListPage();   
    }
    
    /*
     * Fetch list page data
     */
    $scope.prepareListPage= function(){ 
        $scope.request_in_progress= true;
        $scope.data.em_request_context='admin_event_types';
        $scope.data.paged= $scope.paged;
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
            $scope.data= response.data;
            console.log($scope.data);
            $scope.request_in_progress= false;
        });
    }
    
    /*
     * Select item
     */
    $scope.selectTerm= function(term_id){
        if($scope.selections.indexOf(term_id)>=0)
           $scope.selections= em_remove_from_array($scope.selections,term_id);
        else
            $scope.selections.push(term_id);
    }
    
    /*
     * Called when pagination changeds
     */
    $scope.pageChanged= function(pageNumber){
       
        $scope.paged= pageNumber;
        $scope.prepareListPage();
         $scope.selectedAll=false;
    }
    
    /*
     * Request for Event Type deletion
     */
    $scope.deleteTerms= function(){
        var confirmed= confirm("All Events associated to Event-Type(s) will be deleted. Please confirm");
        if(confirmed){
           $scope.progressStart();
           TermUtility.delete($scope.selections,$scope.data.tax_type).then(function(data) {
               location.reload();
            });
        }
       
    }
    
    /*
     * Selection in bulk
     */
    $scope.checkAll = function () { 
//        if ($scope.selectedAll)         
//            $scope.selectedAll = true;
//        else 
//            $scope.selectedAll = false;   
//        
//        angular.forEach($scope.data.terms, function (term) {
//               $scope.selections.push(term.id);
//               term.Selected = $scope.selectedAll ? term.id : 0; 
//        });
//    };
    
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
});





