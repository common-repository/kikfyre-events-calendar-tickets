<?php
/**
 * Setup menus in WP admin.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EventM_Admin_Menus' ) ) :

/**
 * EventM_Admin_Menus Class.
 */
class EventM_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
                
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() { 
		global $menu;
                 $global_options= get_option(EM_GLOBAL_SETTINGS); 
                 add_menu_page( __( 'Event Kikfyre', EM_TEXT_DOMAIN ), __( 'Event Kikfyre', EM_TEXT_DOMAIN ), 'manage_options', "event_magic", array($this, 'events'), 'dashicons-tickets-alt','25');
                add_submenu_page("event_magic", __( 'All Events', EM_TEXT_DOMAIN ), __( 'All Events', EM_TEXT_DOMAIN ), "manage_options", "event_magic", array($this, 'events'));
                add_submenu_page("event_magic", __( 'Add Event', EM_TEXT_DOMAIN ), __( 'Add Event', EM_TEXT_DOMAIN ), "manage_options", "em_add", array($this, 'events'));
                add_submenu_page("", __( 'Child Event', EM_TEXT_DOMAIN ), __( 'Child Event', EM_TEXT_DOMAIN ), "manage_options", "em_child_edit", array($this, 'events'));

                // Event Types
                add_submenu_page("event_magic", __( 'Event Types', EM_TEXT_DOMAIN ), __( 'Event Types', EM_TEXT_DOMAIN ), "manage_options", "em_event_types", array($this, 'eventTypes'));
                add_submenu_page("", __( 'New Event Type', EM_TEXT_DOMAIN ), __( 'New Event Type', EM_TEXT_DOMAIN ), "manage_options", "em_event_type_add", array($this, 'eventTypes'));
                
                // Venues
                add_submenu_page("event_magic", __( 'Venues & Locations', EM_TEXT_DOMAIN ), __( 'Venues & Locations', EM_TEXT_DOMAIN ), "manage_options", "em_venues", array($this, 'venues'));
                add_submenu_page("", __( 'Add Venue', EM_TEXT_DOMAIN ), __( 'Add Venue', EM_TEXT_DOMAIN ), "manage_options", "em_venue_add", array($this, 'venues'));
                
                // Ticket manager
                add_submenu_page("", EventM_UI_Strings::get("LABEL_TICKET_MANAGER"), EventM_UI_Strings::get("LABEL_TICKET_MANAGER"), "manage_options", "em_ticket_template_add", array($this, 'ticketTemplates'));
                add_submenu_page("event_magic", EventM_UI_Strings::get("LABEL_TICKET_MANAGER"), EventM_UI_Strings::get("LABEL_TICKET_MANAGER"), "manage_options", "em_ticket_templates", array($this, 'ticketTemplates'));
                
                // Booking
                add_submenu_page("", EventM_UI_Strings::get("LABEL_BOOKING"), EventM_UI_Strings::get("LABEL_BOOKING"), "manage_options", "em_booking", array($this, 'bookings'));
                add_submenu_page("event_magic", EventM_UI_Strings::get("LABEL_BOOKINGS"), EventM_UI_Strings::get("LABEL_BOOKINGS"), "manage_options", "em_bookings", array($this, 'bookings'));
                
                // Performer
                add_submenu_page("event_magic", EventM_UI_Strings::get("LABEL_PERFORMERS"), EventM_UI_Strings::get("LABEL_PERFORMERS"), "manage_options", "em_performers", array($this, 'performers'));
                add_submenu_page("", EventM_UI_Strings::get("LABEL_ADD_PERFORMER"), EventM_UI_Strings::get("LABEL_ADD_PERFORMER"), "manage_options", "em_performer_add", array($this, 'performers'));
                
                // Analytics
                add_submenu_page("event_magic", EventM_UI_Strings::get("LABEL_ANALYTICS"), EventM_UI_Strings::get("LABEL_ANALYTICS"), "manage_options", "em_analytics", array($this, 'analytics'));
                
                // Settings
                add_submenu_page("event_magic", EventM_UI_Strings::get("LABEL_GLOBAL_SETTINGS"), EventM_UI_Strings::get("LABEL_GLOBAL_SETTINGS"), "manage_options", "em_global_settings", array($this, 'globalSettings'));
                //Frontend
                add_submenu_page("event_magic",'Frontend', 'Frontend', "manage_options", "em_frontend", array($this, 'frontend'));
               
                //Extensions
                add_submenu_page("event_magic",'Extensions', 'Extensions', "manage_options", "em_extensions", array($this, 'extensions'));

                if(isset($global_options['is_visit_welcome_page']) && $global_options['is_visit_welcome_page']==0){
                       $global_options['is_visit_welcome_page'] = 1;
                        update_option(EM_GLOBAL_SETTINGS, $global_options); 
                        $redirect = self_admin_url().'admin.php?page=em_frontend'; 
                        wp_redirect($redirect);
                        exit;
                       
                   
                   
                }
                
            }
        
        public function events(){
            include( 'class-em-event.php' );
        }
        
        public function venues(){
            include_once( 'class-em-venue.php' );
        }
        
        public function bookings(){ 
            include_once( 'class-em-booking.php' );
        }
        
        public function analytics(){
            include_once( 'class-em-analytics.php' );
        }
        
        public function eventTypes(){
            include_once('class-em-event_type.php' );
        }
        public function performers(){
            include_once( 'class-em-performer.php' );
        }
        
        public function ticketTemplates(){
            include_once( 'class-em-event_ticket.php' );
        }
        
        public function globalSettings(){
            include_once( 'class-em-global-settings.php' );
        }
        
         public function frontend()
        {
            include_once( 'template/frontend.php' );
        }
        
        public function extensions()
        {
            include_once( 'template/extensions.php' );
        }
        
}

endif;

return new EventM_Admin_Menus();
