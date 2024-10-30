<?php
/**
 * Installation related functions and actions
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install Class.
 */
class EventM_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
            
	}


	/**
	 * Install EM.
	 */
	public static function install() {
		global $wpdb;
                
		if ( ! defined( 'EM_INSTALLING' ) ) {
			define( 'EM_INSTALLING', true );
		}
                     
                self::create_roles();
                
                // Register Terms
                 EventM_Post_types::register_taxonomies();
		// Register post types
		EventM_Post_types::register_post_types();
                self::default_settings();
                self::insert_demo_data();
	
		// Trigger action
		//do_action( 'em_installed' );
              //  update_site_option('em_post_activation_notice_displayed', 'no');
                
	}
        
        
        /**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}
        
        /**
	 * Get capabilities - these are assigned to admin during installation.
	 *
	 * @return array
	 */
	 private static function get_core_capabilities() {
		$capabilities = array();
                
		$capability_types = array( 'event_magic' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}
        
        static public function create_pages(){
            global $wpdb;
            $global_options= get_option(EM_GLOBAL_SETTINGS);
            
            // Check if there is already any performers page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_performers]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);
            if($page_id>0):
                 $global_options['performers_page']= $page_id;
            else:
                // Creating performers page
                    $args= array("post_content"=>"[em_performers]",
                                 "post_title"=>"Performers",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id= wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['performers_page']= $page_id;
                    endif;
            endif;
            
            // Check if there is already any Venues page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_venues]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);
            if($page_id>0):
                $global_options['venues_page']= $page_id;
            else:
                 // Creating Venues page
                    $args= array("post_content"=>"[em_venues]",
                                 "post_title"=>"Venues",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id=wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['venues_page']= $page_id;
                    endif;
            
            endif;
                
            
            // Check if there is already any Events page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_events]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);
            if($page_id>0):
                $global_options['events_page']= $page_id;
            else:
                    // Creating Events page
                    $args= array("post_content"=>"[em_events]",
                                 "post_title"=>"Events",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id=wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['events_page']= $page_id;
                    endif;
            
            endif;
            
            // Check if there is already any Booking page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_booking]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);          
            if($page_id>0):
                $global_options['booking_page']= $page_id;
            else:
                    // Creating Events page
                    $args= array("post_content"=>"[em_booking]",
                                 "post_title"=>"Booking",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id=wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['booking_page']= $page_id;
                    endif;
            
            endif;
            
            // Check if there is already any User Profile page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_profile]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);
            if($page_id>0):
                $global_options['profile_page']= $page_id;
            else:
                    // Creating Events page
                    $args= array("post_content"=>"[em_profile]",
                                 "post_title"=>"User Profile",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id=wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['profile_page']= $page_id;
                    endif;
            
            endif;
            
            
            // Check if there is already any Event Type page
            $sql= "SELECT ID FROM $wpdb->posts WHERE POST_CONTENT like '%[em_event_types]%' AND POST_TYPE IN ('page','post')";
            $page_id= $wpdb->get_var($sql);
            if($page_id>0):
                $global_options['event_types']= $page_id;
            else:
                    // Creating Events page
                    $args= array("post_content"=>"[em_event_types]",
                                 "post_title"=>"Event Types",
                                 "post_status"=>"publish",
                                 "post_type"=>"page");
                    $page_id=wp_insert_post($args);
                    if(!is_wp_error($page_id)):
                        $global_options['event_types']= $page_id;
                    endif;
            
            endif;
            
            // Update settings
            update_option(EM_GLOBAL_SETTINGS, $global_options);
             
        }
        
        private static function default_settings()
        {   
            global $wp_rewrite;
            
            self::create_pages();
            self::default_notifications();
            $em_instance= get_event_magic_instance();
            
            $global_options= get_option(EM_GLOBAL_SETTINGS);
            // Inheriting options from Registration Magic
            if(is_registration_magic_active())
            {
                // Fetch paypal settings
                $options= new RM_Options();
                $rm_paypal_email= $options->get_value_of('paypal_email');
                
                
                if(!isset($global_options['paypal_email']))
                    $global_options['paypal_email']= $rm_paypal_email;
                
                $paypal_page_style= $options->get_value_of('paypal_page_style');
                if(!isset($global_options['paypal_page_style']))
                    $global_options['paypal_page_style']= $paypal_page_style;
                
                $google_map_key= $options->get_value_of('google_map_key');
                if(!isset($global_options['gmap_api_key']))
                    $global_options['gmap_api_key']= $google_map_key;
            }
            
            if(!isset($global_options['payment_test_mode']))
                $global_options['payment_test_mode']= 1;
            
            if(!isset($global_options['currency']))
                $global_options['currency']= EM_DEFAULT_CURRENCY;
            
            if(!isset($global_options['event_tour']))
            {
                 $global_options['event_tour'] = 0;
            }
             if(!isset($global_options['is_visit_welcome_page'])){
                $global_options['is_visit_welcome_page']= 0;
            }
            if(!isset($global_options['dashboard_hide_past_events'])){
                $global_options['dashboard_hide_past_events']= 0;
            }
            // Update settings
            update_option(EM_GLOBAL_SETTINGS, $global_options);
            $wp_rewrite->flush_rules();
            
            // Update DB version
            update_option(EM_DB_VERSION,$em_instance->version);
        }
        
        private static function default_notifications()
        {  
            $global_options= get_option(EM_GLOBAL_SETTINGS);
            
            $booking_pending_email= em_global_settings('booking_pending_email');
            if(empty($booking_pending_email))
            {
                ob_start();
                include(EM_BASE_DIR.'includes/mail/pending.html');
                $global_options['booking_pending_email']= ob_get_clean();
            }
                
            $booking_confirmed_email= em_global_settings('booking_confirmed_email');
            if(empty($booking_confirmed_email))
            {  
                ob_start();
                include(EM_BASE_DIR.'includes/mail/customer.html');
                $global_options['booking_confirmed_email']=  ob_get_clean();
            }
            
            $booking_cancelation_email= em_global_settings('booking_cancelation_email');
            if(empty($booking_cancelation_email))
            {
                ob_start();
                include(EM_BASE_DIR.'includes/mail/cancellation.html');
                $global_options['booking_cancelation_email']= ob_get_clean();
            }
            
            $reset_password_mail= em_global_settings('reset_password_mail');
            if(empty($reset_password_mail))
            {
                ob_start();
                include(EM_BASE_DIR.'includes/mail/reset_user_password.html');
                $global_options['reset_password_mail']= ob_get_clean();
            }
            
            $registration_email_content= em_global_settings('registration_email_content');
            if(empty($registration_email_content))
            {
                ob_start();
                include(EM_BASE_DIR.'includes/mail/registration.html');
                $global_options['registration_email_content']= ob_get_clean();
            }
            
            $booking_refund_email= em_global_settings('booking_refund_email');
            if(empty($booking_refund_email))
            {
                 ob_start();
                include(EM_BASE_DIR.'includes/mail/refund.html');
                $global_options['booking_refund_email']= ob_get_clean();
            }
            
            // Update settings
            update_option(EM_GLOBAL_SETTINGS, $global_options);
        }
        
        private static function insert_demo_data()
        {   
            $venue_service = new EventM_Venue_Service;
            $get_all_venues = $venue_service->get_venues();
            //Check if venue exist since venue is neccessary for event
            if(!empty($get_all_venues))
                return;
                
                $performer_service = new EventM_Performer_Service;
                $media= array();
                $event_rel= array();
                $base_path= plugin_dir_path(__DIR__).'includes/admin/sample/';
                ob_start();
                include($base_path.'sample-data.json');
                $data= json_decode(ob_get_clean());
                
                if(empty($data))
                    return;
                
                // Saving Performer
                $performers= $data->performers;
                $event_rel['performers']= array();
                if(!empty($performers) && is_array($performers)){
                    foreach($performers as $performer)
                    { 
                        $performer->post_id=0;

                        if(!empty($performer->feature_image))
                        {
                            $file= $base_path.'images/'.$performer->feature_image;
                            $attachment_id= em_upload_into_media($file);
                            if($attachment_id)
                                 $performer->feature_image_id= $attachment_id;
                        }
                        $event_rel['performers'][]= $performer_service->save($performer);
                    }
                }            

                // Event Types
                $event_type_service = new EventTypeM_Service;

                $event_types= $data->event_type;
                if(!empty($event_types)){
                    foreach ($event_types as $event_type){
                        
                    $type_term = $event_type_service->save($event_type);
                    $event_rel['event_type']=$type_term['term_id'];
                    }
                }

                //Saving Venue
                $venue_service=  new EventM_Venue_Service;
                $venue= $data->venue;
                $venue->gallery_images= array();
                if(!empty($venue)){

                    if(!empty($venue->images))
                        {
                           foreach($venue->images as $img)
                           { 
                               $file= $base_path.'images/'.$img;
                               $attachment_id= em_upload_into_media($file);
                               if($attachment_id)
                                 $venue->gallery_images[]= $attachment_id;
                           }
                    }

                    $venue_term = $venue_service->save($venue);
                    $event_rel['venue']=$venue_term['term_id'];
                   // die;
                }

                $event_service=  new EventM_Service;
                $event= $data->event;

                if(!empty($event)){

                    /*cover image*/
                    $cover_image= $base_path.'images/'.$event->cover_image_url;
                    $cover_attachment_id= em_upload_into_media($cover_image);
                    if($cover_attachment_id){
                        $event->cover_image_id = $cover_attachment_id;

                    }


                    /*gallery*/  
                    foreach($event->gallery_images as $gallery_image)
                    { 
                        $file= $base_path.'images/'.$gallery_image;
                        $attachment_id= em_upload_into_media($file);
                        if($attachment_id)
                          $event->gallery_image_ids[]= $attachment_id;
                    }

                    /*sponsor*/  
                    foreach($event->sponser_images as $sponser_image)
                    { 
                        $file= $base_path.'images/'.$sponser_image;
                        $attachment_id= em_upload_into_media($file);
                        if($attachment_id)
                          $event->sponser_image_ids[]= $attachment_id;
                    }

                    $event->venue= $event_rel['venue'];
                    $event->performer= $event_rel['performers'];
                    $event->event_type= $event_rel['event_type'];
                    

                  
                    $event_id= $event_service->save($event);
                    $event_rel['event_id']= $event_id;
                    set_post_thumbnail($event_id, $event->cover_image_id );

                }
                   //$global_options= get_option(EM_GLOBAL_SETTINGS);
                   //$global_options['sample_data_info']= $event_rel;

            }


}

EventM_Install::init();
