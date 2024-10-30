<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Venue requests (Add,Update,Delete,Show)
 */
class EventM_Venue {

    function __construct() {
        wp_enqueue_script( 'em-venue-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-venue-controller.js',false );
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        if (!$screen = get_current_screen()) {
            return;
        }

        /*
         * Loading template file from screen ID
         */
        
        switch ($screen->id) {
            case 'admin_page_em_venue_add': $this->add();
                break;
            case 'event-kikfyre_page_em_venues': $this->venues(); break;
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    public function add() {
        include_once('template/venue_add.php');
    }
    
    /*
     *  Display Venues list
     */

    public function venues() {
        include_once('template/venues.php');
    }

}

return new EventM_Venue();
