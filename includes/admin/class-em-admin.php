<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles all Admin requests for the plugin
 */

class EventM_Admin {

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
        include_once( 'class-em-menus.php' );
    }
    
    /**
    * Load all the static resources (CSS and JS) related to admin
    */
    public function enqueue(){
        wp_enqueue_script( 'em-angular',plugin_dir_url(__DIR__) . '/js/angular.js',array('jquery') );
        wp_enqueue_script( 'jquery-ui-datepicker', array('jquery') );
        wp_enqueue_script( 'em-utility',plugin_dir_url(__DIR__) . '/js/em-utility.js',false );
        wp_enqueue_script( 'em-admin-utility',plugin_dir_url(__DIR__) . '/admin/template/js/em-admin.js',false );
        wp_enqueue_script( 'em-google-map',plugin_dir_url(__DIR__) . '/js/em-map.js',false );
        wp_enqueue_script( 'dir-pagination',plugin_dir_url(__DIR__) . '/admin/template/js/dirPagination.js',false );
        wp_enqueue_script( 'em-angular-module',plugin_dir_url(__DIR__) . '/admin/template/js/em-module.js',false );
        wp_localize_script( 'em-angular-module', 'em_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'jquery-ui-draggable', false, array('jquery'), false, false );
        wp_enqueue_script( 'em-admin-jscolor',plugin_dir_url(__DIR__) . '/admin/template/js/em-jscolor.js',false );      
        wp_enqueue_style( 'em_admin_css',plugin_dir_url(__DIR__) . '/admin/template/css/em_admin.css',false );
        wp_enqueue_script( 'em_font-awesome',plugin_dir_url(__DIR__) . 'js/font_awesome.js',false);
    }
    
    public function hooks(){
    }
    

}

return new EventM_Admin();
