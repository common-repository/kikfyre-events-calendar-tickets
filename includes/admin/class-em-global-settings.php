<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Global Setting
 */
class EventM_Global_Settings {
    
    function __construct() {
       // Enqueue scripts here
       wp_enqueue_script( 'em-global-settings-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-global-settings-controller.js',false );
        /*
         * Loading template file from screen ID
         */
       if (!$screen = get_current_screen()) {
            return;
        }     
       
        switch ($screen->id) {
            case 'event-kikfyre_page_em_global_settings': $this->global_settings();
                break;
        }
     }
     
     private function global_settings(){
         include_once('template/global_settings.php');
     }
        
}

return new EventM_Global_Settings();