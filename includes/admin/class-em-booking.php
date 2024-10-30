<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Event requests (Add,Update,Delete,Show)
 */
class EventM_Booking {

    function __construct() {
        wp_enqueue_script( 'em-booking-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-booking-controller.js',false );
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        if (!$screen = get_current_screen()) {
            return;
        }
    
        /*
         * Loading template file from screen ID
         */
       // echo $screen->id;
        switch ($screen->id) {
            case 'event-kikfyre_page_em_bookings': $this->bookings();
                break;
            case 'admin_page_em_booking': $this->singleBooking();
                break;
            
            
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    
    public function bookings(){
        include_once('template/bookings.php');
    }
    
    public function singleBooking(){ 
        include_once('template/booking.php');
    }
        

}

return new EventM_Booking();
