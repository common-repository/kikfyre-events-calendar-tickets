eventMagicApp.controller('emRegisterCtrl',function($scope,$http,EMRequest){
     $scope.data= {};
     $scope.data.showLogin= false;
     
     
     $scope.initialize= function(){  
         
     }
     
     $scope.register= function(isValid){
        if(isValid){
            
            EMRequest.send('em_register_user',$scope.data).then(function(tmp){
                var response = tmp.data;
                
                if(response.errors){
                    if(response.errors.existing_user_email){
                        $scope.data.registerError= response.errors.existing_user_email[0];
                    }
                    
                    if(response.errors.existing_user_login){
                        $scope.data.registerError= response.errors.existing_user_login[0];
                    }
                }
                
                if(response.success){
                    $scope.data.showLogin= true;
                    $scope.data.newRegistration= true;
                }
                
            });
            
        }
     }
     
     $scope.login= function(){
         
         //Removing register error
         $scope.data.registerError="";
          EMRequest.send('em_login_user',$scope.data).then(function(tmp){
                var response = tmp.data;
                
                if(response.errors){
                    if(response.errors.user_not_exists){
                         $scope.data.loginError= response.errors.user_not_exists[0];
                    }
                    
                    if(response.errors.invalid_user){
                         $scope.data.loginError= response.errors.invalid_user[0];
                    }
                }

                
                if(response.success){
                    location.reload();
                }
                
            });
     }
     
  
     
 });
 
 