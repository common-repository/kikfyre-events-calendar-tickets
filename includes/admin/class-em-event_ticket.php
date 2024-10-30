<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Event requests (Add,Update,Delete,Show)
 */
class EventM_Event_Ticket {

    function __construct() {
         wp_enqueue_script( 'em-ticket-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-ticket-controller.js',false );
        if (!$screen = get_current_screen()) {
            return;
        }
       
        /*
         * Loading template file from screen ID
         */
        
        switch ($screen->id) {
            case 'admin_page_em_ticket_template_add': $this->add();
                break;
            case 'event-kikfyre_page_em_ticket_templates': $this->listTemplates();
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    public function add() {
        wp_enqueue_media();  
        include_once('template/event_ticket_add.php');
    }
    
   public function listTemplates(){
       include_once('template/event_ticket_templates.php');
   }
}

return new EventM_Event_Ticket();
