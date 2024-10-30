<?php
/**
 * Plugin Name: Kikfyre
 * Plugin URI: http://kikfyre.com
 * Description: A complete Event Registration toolkit.
 * Version: 2.1.8
 * Author:  kikfyre
 * Author URI: https://profiles.wordpress.org/kikfyre/
 * Requires at least: 4.1
 * Tested up to: 4.3
 *
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Event_Magic' ) ) :

/**
 * Main  Class.
 *
 * @class Event_Magic
 * @version	2.1.8
 */
final class Event_Magic {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '2.1.8';

	/**
	 * The single instance of the class.
	 *
	 * @var Event_Magic
	 */
	protected static $_instance = null;


	/**
	 * Query instance.
	 *
	 * @var EM_Query
	 */
	public $query = null;
        public $em_errors = array();
	/**
	 *
	 * Ensures only one instance of Event_Magic is loaded or can be loaded.
	 *
	 * @static
	 * @return Event_Magic - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
                    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'event_magic' ), $this->version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'event_magic' ), $this->version );
	}

	/**
	 * Event_Magic Constructor.
	 */
	public function __construct() {            
		$this->define_constants();
                $this->check_requirements();
		$this->includes();  
                $this->register_shortcodes();
		$this->init_hooks();
		do_action( 'event_magic_loaded' );
	}
        
        /**
	 * Register the shortcodes
	 */
	private function register_shortcodes() {
		new EM_Shortcodes();
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() { 
            
                add_action( 'init','em_recurringEventCheck');  
                add_action( 'init','em_check_event_status'); 
                add_action( 'init','em_delete_tmp_bookings'); 
                add_action( 'init','em_check_paypal_ipn');
              //  add_action('admin_notices', 'em_activation_notice');
                add_action('widgets_init','em_widgets_init');
                add_action('init','em_redirect_event_posts');
                add_action('admin_notices','em_check_required_pages');
                
		register_activation_hook( __FILE__, array( 'EventM_Install', 'install' ) );  
	}

	/**
	 * Define  Constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir();
                $this->define('EM_TEXT_DOMAIN','event-magic');
                $this->define('EM_VENUE_TYPE_TAX','em_venue');
                $this->define('EM_EVENT_POST_TYPE','em_event');
                $this->define('EM_PERFORMER_POST_TYPE','em_performer');
                $this->define('EM_BOOKING_POST_TYPE','em_booking');
                $this->define('EM_TICKET_POST_TYPE','em_ticket');
                $this->define('EM_EVENT_TYPE_TAX','em_event_type');
                $this->define('EM_EVENT_VENUE_TAX','em_venue');
                $this->define('EM_GLOBAL_SETTINGS','em_global_settings');
                $this->define('EM_DB_VERSION','emagic_db_version');
                $this->define('EM_PAGINATION_LIMIT',8);
                $this->define('EM_DEFAULT_CURRENCY','USD');
                $this->define('EM_BASE_URL', plugin_dir_url(__FILE__));
                $this->define('EM_BASE_DIR', plugin_dir_path(__FILE__));
                $this->define('EM_REQ_EXT_MCRYPT',1);
                $this->define('EM_REQ_EXT_CURL',2);
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
                include_once('includes/class-em-ui-strings.php');
                include_once('includes/class-em-constants.php');
                include_once('includes/core/class-em-request.php');
                include_once('includes/core/class-em-raw-request.php');
                include_once('includes/core/class-em-utility.php');
                include_once('includes/em-core-functions.php');
                include_once('includes/models/class-em-model.php');
                include_once( 'includes/class-em-ajax.php' );
                
                // Dao
                include_once('includes/dao/class-em-term.php');
                include_once('includes/dao/class-em-post.php');
                include_once('includes/dao/class-em-venue.php');
                include_once('includes/dao/class-em-performer.php');
                include_once('includes/dao/class-em-event-type.php');
                include_once('includes/dao/class-em-event-ticket.php');
                include_once('includes/dao/class-em-event.php');
                include_once('includes/dao/class-em-event.php');
                include_once('includes/dao/class-em-global-settings.php');
                include_once('includes/dao/class-em-booking.php');
                include_once('includes/dao/class-em-user.php');
               
                
     
                if ( $this->is_request( 'admin' ) ) { 
			include_once( 'includes/admin/class-em-admin.php' );     
		} else{
                    include_once('includes/class-em-public.php');
                }
                
                 // Models
                include_once('includes/models/class-em-base-model.php');
                include_once('includes/models/class-em-array-model.php');
                include_once('includes/models/class-em-strings.php');
                include_once('includes/models/class-em-venue.php');
                include_once('includes/models/class-em-event.php');
                include_once('includes/models/class-em-performer.php');
                include_once('includes/models/class-em-event-type.php'); 
                include_once('includes/models/class-em-event-ticket.php');
                include_once('includes/models/class-em-global-settings.php');
                include_once('includes/models/class-em-booking.php');
                        
		include_once( 'includes/class-em-install.php' );  
                include_once( 'includes/class-em-post-types.php' ); 
                include_once( 'includes/class-em-shortcodes.php' );
                
                // Lib
                include_once('includes/lib/class-em-paypal-utility.php');
          
               // include_once('includes/lib/tcpdf_min/tcpdf.php');
                
                
                // Services
                include_once('includes/service/class-em-event.php'); 
                include_once('includes/service/class-em-performer.php'); 
                include_once('includes/service/class-em-venue.php'); 
                include_once('includes/service/class-em-event-type.php');
                include_once('includes/service/class-em-user.php');
                include_once('includes/service/class-em-booking.php');
                include_once('includes/service/class-em-payment.php');
                include_once('includes/service/class-em-paypal.php');
                include_once('includes/service/class-em-booking.php');
                include_once('includes/service/class-em-notification.php');
                include_once('includes/service/class-em-print.php');
                include_once('includes/service/class-em-setting.php');
                include_once('includes/service/class-em-ticket.php');
                include_once('includes/service/class-em-analytics.php');
                 include_once('includes/service/class-download-booking.php');
                 include_once('includes/service/class-em-extensions.php');
                
                // Widgets
                include_once('includes/widgets/event_calendar.php'); 
                include_once('includes/widgets/venue_map.php'); 
                include_once('includes/widgets/event_slider.php'); 
                include_once('includes/widgets/event_filter.php');
                include_once('includes/widgets/event_countdown.php');
                
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
	}


	/**
	 * Init when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_event_magic_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'event_magic_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/rm-events/rm-events-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/rm-events-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'event-magic' );

		load_textdomain( 'event-magic', WP_LANG_DIR . '/event-magic/event-magic-' . $locale . '.mo' );
		load_plugin_textdomain( 'event-magic', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}
        

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}


	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}
        

	/**
	 * Return the API URL for a given request.
	 *
	 * @param string $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) ) {
			$scheme = parse_url( home_url(), PHP_URL_SCHEME );
		} elseif ( $ssl ) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}

		if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
			$api_request_url = trailingslashit( home_url( '/index.php/event-magic-api/' . $request, $scheme ) );
		} elseif ( get_option( 'permalink_structure' ) ) {
			$api_request_url = trailingslashit( home_url( '/event-magic-api/' . $request, $scheme ) );
		} else {
			$api_request_url = add_query_arg( 'event-magic-api', $request, trailingslashit( home_url( '', $scheme ) ) );
		}

		return esc_url_raw( apply_filters( 'event_magic_api_request_url', $api_request_url, $request, $ssl ) );
	}

	/**
	 * Email Class.
	 * @return WC_Emails
	 */
	public function mailer() {
		//return WC_Emails::instance();
	}
        
        public function check_requirements()
        {
            if(!extension_loaded('curl'))
                $this->em_errors[EM_REQ_EXT_CURL] = (object)array('type' => 'non-fatal');
            
            if(!extension_loaded('mcrypt'))
                $this->em_errors[EM_REQ_EXT_MCRYPT] = (object)array('type' => 'non-fatal');
        }
}

endif;

/**
 * Main instance of Event_Magic.
 *
 * @return Event_Magic
 */
function get_event_magic_instance() {
	return Event_Magic::instance();
}


$GLOBALS['event-magic'] = get_event_magic_instance();


