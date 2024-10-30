<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles all Front requests for the plugin
 */

class EventM_Public {

    /**
     * Constructor.
     */
    public function __construct() { 
            add_action( 'init', array( $this, 'hooks' ) );
            add_action( 'init', array( $this, 'enqueue' ) );
            add_action( 'init', array( $this, 'includes' ) );
    }

    /**
     * Include any classes we need within admin.
     */
    public function includes() {
        
    }
    
    /**
    * Load all the static resources (CSS and JS) related to admin
    */
    public function enqueue(){    
        wp_enqueue_script('jquery');
        wp_enqueue_style( 'em_public_css',plugin_dir_url(__DIR__) . '/includes/templates/css/em_public.css',false );
        wp_enqueue_script('jquery-ui-datepicker',null,array('jquery'));
        wp_enqueue_script('jquery-ui-dialog',null,array('jquery'));
        wp_enqueue_script( 'em-angular',plugin_dir_url(__DIR__) . 'includes/js/angular.js',array('jquery') );
        wp_enqueue_script( 'dir-pagination',plugin_dir_url(__DIR__) . 'includes/admin/template/js/dirPagination.js',false );
        wp_enqueue_script( 'em-angular-module',plugin_dir_url(__DIR__) . 'includes/admin/template/js/em-module.js',false );
        wp_localize_script( 'em-angular-module', 'em_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'em-booking-controller',plugin_dir_url(__DIR__) . 'includes/templates/js/em-booking-controller.js',false );
        wp_enqueue_script( 'em-user-register-controller',plugin_dir_url(__DIR__) . 'includes/templates/js/em-register-controller.js',false );
        wp_enqueue_script( "em-public",plugin_dir_url(__DIR__) . 'includes/templates/js/em-public.js',false );
        wp_enqueue_style("em_colorbox_css", plugin_dir_url(__DIR__).'includes/templates/css/colorbox.css');
        wp_enqueue_script("em_colorbox", plugin_dir_url(__DIR__).'includes/templates/js/jquery.colorbox.js');
        wp_enqueue_script( 'em_font-awesome',plugin_dir_url(__DIR__) . 'includes/js/font_awesome.js',false);

        
    }
    
    public function hooks(){
    }
    

}

return new EventM_Public();
