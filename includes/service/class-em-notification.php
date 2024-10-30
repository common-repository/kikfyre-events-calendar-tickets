<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventM_Notification_Service {
    
    
     public static function booking_confirmed($order_id)
    {
         $global_options= get_option(EM_GLOBAL_SETTINGS); 
       $send_booking_confirm_email= $global_options['send_booking_confirm_email'];
       if($send_booking_confirm_email!=1)
           return;
        self::configure_mail();
        $order= get_post($order_id);
        if(empty($order))
            return false;
        
        $user_id= em_get_post_meta($order_id, 'user_id', true);
        $user= get_user_by('ID', $user_id);
         ob_start();
         $mail_body = ob_get_clean();
         $mail_body =  em_global_settings('booking_confirmed_email');   
         $body_content = get_mail_body($mail_body,$order_id);    
        
                 /* send Mail to User */    
                $to = $user->user_email;
                $subject = 'Booking Confirmation';
                $body = $body_content;
                wp_mail( $to, $subject, $body);
        
                /* send Mail to Admin */
                
                 $user_id = em_get_post_meta($order_id, 'user_id', true); 
                 $user = get_user_by('ID',$user_id);    
               
                 
               
                $admin_mail_body = ob_get_clean();
                $admin_mail_body =   file_get_contents(EM_BASE_URL.'includes/mail/admin_confirm.html');  
                $admin_email = get_option('admin_email'); 
                $to = $admin_email;
                $subject = 'Booking Confirmation';
                //$message = em_admin_get_mail_confirm_content($order_id);
               
               $body = get_mail_body($admin_mail_body,$order_id,$user->user_email);  

                wp_mail( $to, $subject, $body); 
      
    }
    
    public static function booking_pending($order_id)
    {
        self::configure_mail();
        $order= get_post($order_id);
        if(empty($order))
            return false;
        
        $user_id= em_get_post_meta($order_id, 'user_id', true);
        $user= get_user_by('ID', $user_id);
        
        ob_start();
         $mail_body = ob_get_clean();
         $mail_body =  em_global_settings('booking_pending_email');   
         $body_content = get_mail_body($mail_body,$order_id);   
        
        if(empty($user))
            return false;
        
        $to = $user->user_email;
        $subject = 'Booking Pending';
        $body = $body_content;
        wp_mail( $to, $subject, $body );
        
      
        $admin_email = get_option('admin_email'); 
        
        $to = $admin_email;
        $subject = 'Booking Pending';        
        $body = 'User '.$user->user_email.'has Booking Pending with Booking ID #'.$order_id;

        wp_mail( $to, $subject, $body); 
    }
    

    
    public static function booking_cancel($order_id)
    {
        $global_options= get_option(EM_GLOBAL_SETTINGS); 
       $send_booking_cancellation_email= $global_options['send_booking_cancellation_email'];
       if($send_booking_cancellation_email!=1)
           return;
        self::configure_mail();
        $order= get_post($order_id);
        if(empty($order))
            return false;
        
        $user_id= em_get_post_meta($order_id, 'user_id', true);
        $user= get_user_by('ID', $user_id);
        
        if(empty($user))
            return false;            
            
        ob_start();
        $mail_body = ob_get_clean(); 
        $mail_body =  em_global_settings('booking_cancelation_email');
        
        $body_content = get_mail_body($mail_body,$order_id);
         
        $to = $user->user_email;
        $subject = 'Booking Cancellation';
        $body = $body_content;
        wp_mail( $to, $subject, $body );
        
        $user_id = em_get_post_meta($order_id, 'user_id', true); 
        $user = get_user_by('ID',$user_id);      
        
        $admin_mail_body = ob_get_clean();
        $admin_mail_body =   file_get_contents(EM_BASE_URL.'includes/mail/admin_cancellation.html');  
        $admin_email = get_option('admin_email'); 
        $to = $admin_email;
        $subject = 'Booking Cancellation';
        //$message = em_admin_get_mail_confirm_content($order_id);

       $body = get_mail_body($admin_mail_body,$order_id,$user->user_email);  
         wp_mail( $to, $subject, $body); 
    }
    
    
    public static function reset_password_mail($order_id)
    {
     self::configure_mail();
        $order= get_post($order_id);
       
        if(empty($order))
            return false;
        
        $user_id= em_get_post_meta($order_id, 'user_id', true);
        $user= get_user_by('ID', $user_id);
        
        ob_start();
        $mail_body = ob_get_clean(); 
        $new_user_password = wp_generate_password(5);
        $mail_body= em_global_settings('reset_password_mail');
        
        $mail_body = str_replace("@username",$user->user_email,$mail_body);
        $mail_body = str_replace("@password",$new_user_password,$mail_body);
        error_log("Replaced : ".$mail_body);    
         
        if(empty($user))
            return false;
        
        $to = $user->user_email;
        $subject = 'New Password';
        $body = $mail_body;
        wp_mail( $to, $subject, $body );
        
        $admin_email = get_option('admin_email'); 
         $to = $admin_email;
        $subject = 'Reset User Password';        
        $body = 'Password of user '.$user->user_email.' is Reset.';
        wp_mail( $to, $subject, $body);
    }
    
    public static function booking_refund($order_id)
    {
        self::configure_mail();
        $order= get_post($order_id);
        if(empty($order))
            return false;
        
        $user_id= em_get_post_meta($order_id, 'user_id', true);
        $user= get_user_by('ID', $user_id);
        
        if(empty($user))
            return false;
            
         ob_start();
         $mail_body = ob_get_clean();  
        
      $mail_body =  em_global_settings('booking_refund_email');      
      // error_log("mail body: ".$mail_body);  
       $body_content = get_mail_body($mail_body,$order_id);
        
        $to = $user->user_email;
        $subject = 'Booking Refund';
        $body = $body_content;
        wp_mail( $to, $subject, $body );
        
        
        $booking_service = new EventM_Booking_Service();
        $event_detail = $booking_service->get_event_by_booking($order_id);
       $data = (array) $event_detail;
        
        $admin_email = get_option('admin_email'); 
        
         $user_id = em_get_post_meta($order_id, 'user_id', true); 
                 $user = get_user_by('ID',$user_id);   
        $to = $admin_email;
        $subject = 'Booking Refund on Booking ID#'.$order_id;        
        $body = 'A refund of '.$data['total_price'].' has been issued to booking #'.$order_id.' for '. $data['event_name'];
        wp_mail( $to, $subject, $body);
    }
    
     public static function user_registration($user_data=null ) { 
        self::configure_mail();
        
        ob_start();
        $mail_body = ob_get_clean();  
         
        $mail_body= em_global_settings('registration_email_content');
        
        $mail_body = str_replace("@username",$user_data->email,$mail_body);
        $mail_body = str_replace("@password",$user_data->password,$mail_body);
        
        //$body_content .= "Your auto generated password is ".$user_data->password;
        $registration_email_subject= em_global_settings('registration_email_subject');
        
        if(empty($registration_email_subject)):
            $registration_email_subject= get_bloginfo('name');
        endif;
        
        if(!empty($user_data)):
             wp_mail( $user_data->email, $registration_email_subject, $mail_body );
        
               $admin_email = get_option('admin_email'); 
               $to = $admin_email;
               $subject = 'New User Registered.'; 
               $body = 'New user '.$user_data->email.' has Registered';
               wp_mail( $to, $subject, $body);
             
             return true;
        endif;
        
        return false;
        
    }
    
    private static function configure_mail()
    {  
        add_filter('wp_mail_content_type', 'em_set_mail_content_type_html');
        add_filter( 'wp_mail_from', 'em_set_mail_from' );
        add_filter( 'wp_mail_from_name', 'em_set_mail_from_name' );
    }
    
    
        
}
?>
