<?php

/**
 * Shortcodes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'EM_Shortcodes' ) ) :

class EM_Shortcodes {

	/** Vars ******************************************************************/

	/**
	 * @var array Shortcode => function
	 */
	public $codes = array('em_events'=>'load_events','em_performers'=>'load_performers','em_event_types'=>'load_event_types',
                             'em_venues'=>'load_venues','em_booking'=>"load_booking",'em_profile'=>"load_profile"); 

	/** Functions *************************************************************/

	/**
	 *
	 * @uses add_shortcodes()
	 */
	public function __construct() {
		$this->add_shortcodes();
	}

	/**
	 * Register shortcodes
	 *
	 * @uses add_shortcode()
	 */
	private function add_shortcodes() {
		foreach ( (array) $this->codes as $code => $function ) {
			add_shortcode( $code, array($this,$function) );
		}
	}
        
        public function load_profile(){
            include('templates/user_profile.php'); 
        }
        
       public function load_events(){
            $event_id= (int) event_m_get_param('event');
            
            if($event_id){
                $post= get_post($event_id);
                if(!empty($post) && $post->post_type=="em_event" && $post->post_status!="trash")
                    include('templates/single-em_event.php');
                else 
                  return;  
            }
                
            else
                include('templates/events.php'); 
        }
        
         public function load_performers(){
            $performer_id= (int) event_m_get_param('performer');
            if($performer_id){
                $post= get_post($performer_id);
                if(!empty($post) && $post->post_type=="em_performer" && $post->post_status!="trash")
                    include('templates/single-em_performer.php');
                else 
                  return;  
            }
            else
                include('templates/performers.php'); 
        }
        
        public function load_venues(){
            include('templates/venues.php'); 
        }
        
        public function load_booking(){
            // Removing all the temporary bookings for current user
            if(is_user_logged_in())
            {   
                $booking_service= new EventM_Booking_Service();
                $user= wp_get_current_user();
                $booking_service->remove_tmp_bookings($user->ID);
                
            }
            include('templates/booking.php'); 
        }
        
        public function load_event_types()
        {
            include('templates/event_types.php');  
        }
      
}
endif;
