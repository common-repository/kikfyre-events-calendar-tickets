<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Event requests (Add,Update,Delete,Show)
 */
class EventM_Performer {

    function __construct() {
        wp_enqueue_script( 'em-performer-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-performer-controller.js',false );
        if (!$screen = get_current_screen()) {
            return;
        }
       
        /*
         * Loading template file from screen ID
         */
       // echo $screen->id;
        switch ($screen->id) {
            case 'admin_page_em_performer_add': $this->add();
                break;
            case 'event-kikfyre_page_em_performers': $this->performers();
                break;
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    public function add() {
        include_once('template/performer_add.php');
    }
    
    public function performers(){
        include_once('template/performers.php');
    }
        

}

return new EventM_Performer();
