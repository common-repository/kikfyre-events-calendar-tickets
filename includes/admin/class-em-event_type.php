<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Event requests (Add,Update,Delete,Show)
 */
class EventM_Event_Type {

    function __construct() {
        wp_enqueue_script( 'em-event-type-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-event-type-controller.js',false );
        if (!$screen = get_current_screen()) {
            return;
        }
       
        /*
         * Loading template file from screen ID
         */
        
        switch ($screen->id) {
            case 'admin_page_em_event_type_add': $this->add();
                break;
            case 'event-kikfyre_page_em_event_types': $this->event_types();
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    public function add() {
        include_once('template/event_type_add.php');
    }
    
    public function event_types() {
        include_once('template/event_types.php');
    }


}

return new EventM_Event_Type();
