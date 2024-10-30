<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class EventM_UserProfile_Service {

        public function get_userName(){ 
         //  print_r(_wp_get_current_user()); 
    return _wp_get_current_user();

       // $args = array('fields'=>array('ID','user_email','user_login')); 
      //  echo'<pre>'; print_r(get_users($args));          
       // return get_users($args);        
    }
    
}
?>
