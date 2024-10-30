<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EventM_Analytics {

    function __construct() {
        wp_enqueue_script( 'jquery-ui-datepicker', array('jquery') );
        wp_enqueue_script('google_charts',"https://www.gstatic.com/charts/loader.js");
        wp_enqueue_script( 'em-analytic-controller',plugin_dir_url(__DIR__) . '/admin/template/js/em-analytic-controller.js',false );
         wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        if (!$screen = get_current_screen()) {
            return;
        }
        
        /*
         * Loading template file from screen ID
         */
       // echo $screen->id;
        switch ($screen->id) {
            case 'event-kikfyre_page_em_analytics': $this->analytics();
                break;
        }
    }
    
    public function analytics(){
        include_once('template/analytics.php');
    }
    
   
        

}

return new EventM_Analytics();
