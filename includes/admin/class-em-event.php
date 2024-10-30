<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handles Event requests (Add,Update,Delete,Show)
 */
class EventM_Event {

    function __construct() {
        wp_enqueue_script( 'em-event-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-event-controller.js',false );
        wp_enqueue_script( 'em-child-event-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-child-event-controller.js',false );
        wp_enqueue_script( 'em-timepicker',plugin_dir_url(__DIR__) . '/admin/template/js/timepicker-addon.js',false );
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        if (!$screen = get_current_screen()) {
            return;
        }
        
        //joyride
        wp_enqueue_script( 'em_joyride_js',plugin_dir_url(__DIR__) . '/admin/template/js/jquery.joyride-2.1.js',false );
        wp_enqueue_style( 'em_joyride_css',plugin_dir_url(__DIR__) . '/admin/template/css/joyride-2.1.css',false );
        /*
         * Loading template file from screen ID
         */

        switch ($screen->id) {
            case 'event-kikfyre_page_em_add': $this->add();break;
            case 'toplevel_page_event_magic' : $this->events(); break;
            case 'admin_page_em_child_edit':   $this->child(); break;
        }
    }

    /*
     *  Display Venue Add/Edit template
     */

    public function add() {
        add_thickbox();
        include_once('template/event_add.php');
    }
    
    public function child()
    {
        add_thickbox();
        show_admin_bar(false);
        include_once('template/child_event.php');
    }
    
    public function events(){
        include_once('template/events.php');
    }

}

return new EventM_Event();
