
/**
 * 
 * Global Settings controller
 */
eventMagicApp.controller('globalSettingsCtrl', function ($scope, $http,EMRequest) {
    $scope.data = {};
    $scope.requestInProgress= false;
    $scope.showExternalIntegration= false;
    $scope.showPageIntegration= false;
    $scope.showPayments= false;
    $scope.showNotification= false;
    $scope.showOptions= true;
    $scope.configure_paypal=false;
   
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    //Loads Pre saved setting data
    $scope.preparePageData = function () {
        $scope.data.em_request_context='admin_global_settings';
        $scope.progressStart();
        EMRequest.send('em_load_strings',$scope.data).then(function(response){
            $scope.progressStop();
            $scope.data= response.data;
        })
      
    };


    /*
     * Save Settings
     */
    $scope.saveSettings = function (isValid) { 
        console.log(isValid); 
        $scope.formErrors='';
        // If form is valid against all the angular validations
        if (isValid) {
            $scope.loadContentFromEditors();
            $scope.progressStart();
            EMRequest.send('em_save_global_settings',$scope.data.options).then(function(response){
                $scope.progressStop();
                data= response.data;
                if (!data.redirect) {
                            angular.forEach(data, function (value, key) {
                                $scope.formErrors += value.message;
                            });
                        }
                if(data.redirect){
                    //$scope.showSettingOptions();
                    if($scope.configure_paypal = true){
                       $scope.configure_paypal = false;
                   }
                   if($scope.configure_stripe= true){
                       $scope.configure_stripe= false;
                   }
                    tb_remove();
                    // location.href=window.location.href;
                }        
            });        
        }
    }

    $scope.initialize = function () {
         // Loading all the required data before form load
         $scope.preparePageData();
    };
    
    $scope.loadContentFromEditors= function()
    {
      
        if(tinymce.get('registration_email_content')!=null)
            $scope.data.options.registration_email_content= tinymce.get('registration_email_content').getContent();  
        else
           $scope.data.options.registration_email_content= jQuery('#registration_email_content').val();   

        if(tinymce.get('booking_pending_email')!=null)
            $scope.data.options.booking_pending_email= tinymce.get('booking_pending_email').getContent(); 
        else
            $scope.data.options.booking_pending_email= jQuery('#booking_pending_email').val(); 
        
        if(tinymce.get('booking_confirmed_email')!=null)
            $scope.data.options.booking_confirmed_email= tinymce.get('booking_confirmed_email').getContent();  
        else
            $scope.data.options.booking_confirmed_email= jQuery('#booking_confirmed_email').val(); 
        
        if(tinymce.get('booking_cancelation_email')!=null)
             $scope.data.options.booking_cancelation_email= tinymce.get('booking_cancelation_email').getContent();  
        else
             $scope.data.options.booking_cancelation_email= jQuery('#booking_cancelation_email').val();
        
        if(tinymce.get('booking_refund_email')!=null)
            $scope.data.options.booking_refund_email= tinymce.get('booking_refund_email').getContent();   
        else
            $scope.data.options.booking_refund_email= jQuery('#booking_refund_email').val(); 
        
        if(tinymce.get('reset_password_mail')!=null)
            $scope.data.options.reset_password_mail= tinymce.get('reset_password_mail').getContent();   
        else
            $scope.data.options.reset_password_mail= jQuery('#reset_password_mail').val();

    }
    
    $scope.showSettingOptions= function()
    {
        $scope.showNotification= false;
        $scope.showPayments= false;
        $scope.showExternalIntegration= false;
        $scope.showPageIntegration= false;
        $scope.showPageGSettings= false;
        
        // ng-hide thickboxes
        $scope.configure_paypal = false;
        $scope.configure_stripe = false;
    }
    $scope.show_configure_paypal=function(value){
       
        if(value==true){
         
             $scope.showPayments= true;
            $scope.configure_paypal=true;
        }
            
    }
     $scope.show_payments=function(value){
       
        if(value==true){
         
             $scope.showPayments= true;
           
        }
            
    }
  
    
});


