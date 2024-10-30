<?php

/**
 *
 * Service class for Events
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Service class
 */
class EventM_User_Service {

    public function register_user() {
        $user_data= new stdClass();
        $response= new stdClass();
       
        if ($_POST['action_type'] == ""):
            $user_data->email = event_m_get_param("email",true);
            $user_data->password = wp_generate_password(5);
            
            if (!$this->verify_user()):
                
                $user_id = wp_create_user($user_data->email, $user_data->password , $user_data->email);
                
                if (!is_wp_error($user_id)): 
                    $this->add_user_meta($user_id); 
                    EventM_Notification_Service::user_registration($user_data);                    
                    $response->success= true;
                    echo json_encode($response);
                else:
                    echo json_encode($user_id);
                    die;
                endif;

            endif;

        else:
            echo 'Cheating....';
        endif;

        die;
    }

    public function verify_user($email) {
        // Assuming both email and username are same
        return (username_exists($email) || email_exists($email));
    }

    private function add_user_meta($user_id) { 
        $user = get_user_by('ID', $user_id);
        if ($user):
            update_user_meta($user_id, 'first_name', event_m_get_param('first_name',true));
            update_user_meta($user_id, 'last_name', event_m_get_param('last_name',true));
            update_user_meta($user_id, 'phone', event_m_get_param('phone',true));
        endif;
        
    }


    public function login_user(){ 
        
            $user_name = event_m_get_param("user_name",true);
            $password = event_m_get_param("password",true);
            $response = new stdClass();
            
            $user_id=0;
            $is_disabled=1;
          
            // Check if user exists
            if (username_exists($user_name)) {
                $user = get_user_by('login', $user_name);
                $user_id = $user->ID;                 
            } elseif (email_exists($user_name)) {
                $user = get_user_by('email', $user_name);
                $user_id = $user->ID;               
            }
           
            elseif($user_name=="" ){
                   $error = new WP_Error('user_not_exists', 'Username cannot be blank');
                echo json_encode($error);
                die;
            }
             elseif($password=="" ){
                   $error = new WP_Error('user_not_exists', 'Password cannot be blank');
                echo json_encode($error);
                die;
            }
            else {
                $error = new WP_Error('user_not_exists', EventM_UI_Strings::get("VALIDATION_USER_NOT_EXISTS"));
                echo json_encode($error);
                die;
            }
           
             $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);
           
            if($is_disabled){               
                 $error = new WP_Error('user_not_exists', EventM_UI_Strings::get("VALIDATION_USER_NOT_ACTIVE"));
                echo json_encode($error);
                die;
            }
            
//            $is_disabled = (int) get_user_meta($user_id, 'rm_user_status', true);
//            if($is_disabled==1){
//               $error = new WP_Error('user_not_exists', EventM_UI_Strings::get("VALIDATION_USER_NOT_ACTIVE"));
//                echo json_encode($error);
//                die;
//                
//            }
            
            $info['user_login'] = $user->user_login;
            $info['user_password'] = event_m_get_param("password");
            $info['remember'] = true;

            $user_signon = wp_signon($info, false);
            if (is_wp_error($user_signon)) {
                $error = new WP_Error('invalid_user', EventM_UI_Strings::get("VALIDATION_INVALID_LOGIN"));
                echo json_encode($error);
                die;
            } else {
                wp_set_current_user($user_signon->ID);
                $response->success = true;
                echo json_encode($response);
                die;
            }
        
            die();
    }
    
    public function get_all_user(){
          $user_dao= new EventM_UserProfile_Service();
        $user= $user_dao->get_userName();
        return $user;
    }
    
    
   

}

?>