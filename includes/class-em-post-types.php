<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     EM_Post_types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EM_Post_types Class.
 */
class EventM_Post_types {

	/**
	 * Hooks in method.
	 */
	public static function init() {
             
                add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
                
                // Single Event template
               add_filter( 'single_template', array( __CLASS__,'em_locate_single_template') );
               add_filter('taxonomy_template', array( __CLASS__,'em_locate_single_venue_template'));

	}
        
        
        public static function register_taxonomies(){ 
            if ( taxonomy_exists( 'em_event_type' ) ) {
			return;
		}

		register_taxonomy( 'em_event_type','event_magic',array(
				'hierarchical'          => true,
				'label'                 => __( 'Event Types', __( 'Events', EM_TEXT_DOMAIN ) ),
				'labels' => array(
						'name'              => __( 'Event Types', __( 'Events', EM_TEXT_DOMAIN ) ),
						'singular_name'     => __( 'Event Type', __( 'Events', EM_TEXT_DOMAIN ) ),
						'search_items'      => __( 'Search Event Types', __( 'Events', EM_TEXT_DOMAIN ) ),
						'all_items'         => __( 'All Event Types', __( 'Events', EM_TEXT_DOMAIN ) ),
						'parent_item'       => __( 'Parent Event Type', __( 'Events', EM_TEXT_DOMAIN ) ),
						'parent_item_colon' => __( 'Parent Event Type:', __( 'Events', EM_TEXT_DOMAIN ) ),
						'edit_item'         => __( 'Edit Event Type', __( 'Events', EM_TEXT_DOMAIN ) ),
						'update_item'       => __( 'Update Event Type', __( 'Events', EM_TEXT_DOMAIN ) ),
						'add_new_item'      => __( 'Add New Event Type', __( 'Events', EM_TEXT_DOMAIN ) ),
						'new_item_name'     => __( 'New Event Type', __( 'Events', EM_TEXT_DOMAIN ) )
					),
				'show_ui'               => false,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_event_magic_terms',
					'edit_terms'   => 'edit_event_magic_terms',
					'delete_terms' => 'delete_event_magic_terms',
					'assign_terms' => 'assign_event_magic_terms',
				),
                                'show_in_nav_menus'   => false
			) 
		);
                
                register_taxonomy( 'em_venue','event_magic',array(
				'hierarchical'          => true,
				'label'                 => __( 'Venues', __( 'Venues', EM_TEXT_DOMAIN ) ),
				'labels' => array(
						'name'              => __( 'Venues', __( 'Events', EM_TEXT_DOMAIN ) ),
						'singular_name'     => __( 'Venue', __( 'Events', EM_TEXT_DOMAIN ) ),
						'menu_name'         => __( 'Venue', 'Admin menu name', __( 'Events', EM_TEXT_DOMAIN ) ),
						'search_items'      => __( 'Search Venues', __( 'Events', EM_TEXT_DOMAIN ) ),
						'all_items'         => __( 'All Venues', __( 'Events', EM_TEXT_DOMAIN ) ),
						'parent_item'       => __( 'Parent Venue', __( 'Events', EM_TEXT_DOMAIN ) ),
						'parent_item_colon' => __( 'Parent Venue:', __( 'Events', EM_TEXT_DOMAIN ) ),
						'edit_item'         => __( 'Edit Venue', __( 'Events', EM_TEXT_DOMAIN ) ),
						'update_item'       => __( 'Update Venue', __( 'Events', EM_TEXT_DOMAIN ) ),
						'add_new_item'      => __( 'Add New Venue', __( 'Events', EM_TEXT_DOMAIN ) ),
						'new_item_name'     => __( 'New Venue', __( 'Events', EM_TEXT_DOMAIN ) )
					),
				'show_ui'               => false,
				'query_var'             => true,
                                'rewrite'             => array( 'slug' => 'venues'),
				'capabilities'          => array(
					'manage_terms' => 'manage_event_magic_terms',
					'edit_terms'   => 'edit_event_magic_terms',
					'delete_terms' => 'delete_event_magic_terms',
					'assign_terms' => 'assign_event_magic_terms',
				),
                                'show_in_nav_menus'   => true
			) 
		);
        }
	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('em_event') ) {
			return;
		}

		register_post_type( 'em_event',
				array(
					'labels'              => array(
							'name'                  => __( 'Kikfyre Events', EM_TEXT_DOMAIN ),
							'singular_name'         => __( 'Event', EM_TEXT_DOMAIN ),
							'add_new'               => __( 'Add Event', EM_TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Event', EM_TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', EM_TEXT_DOMAIN),
							'edit_item'             => __( 'Edit Event', EM_TEXT_DOMAIN ),
							'new_item'              => __( 'New Event', EM_TEXT_DOMAIN),
							'view'                  => __( 'View Event', EM_TEXT_DOMAIN),
							'view_item'             => __( 'View Event', EM_TEXT_DOMAIN ),
							'not_found'             => __( 'No Events found', EM_TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Events found in trash', EM_TEXT_DOMAIN ),
							'featured_image'        => __( 'Event Image', EM_TEXT_DOMAIN ),
							'set_featured_image'    => __( 'Set event image', EM_TEXT_DOMAIN ),
							'remove_featured_image' => __( 'Remove event image', EM_TEXT_DOMAIN ),
							'use_featured_image'    => __( 'Use as event image', EM_TEXT_DOMAIN),
						),
					'description'         => __( 'Here you can add new events.', EM_TEXT_DOMAIN),
					'public'              => true,
					'show_ui'             => false,
                                        'has_archive'         => true,  
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => true,
					'query_var'           => true,
                                        'rewrite'             => array( 'slug' => 'event', 'with_front' => true ),  
                                        'capability_type'     => 'event_magic', 
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'show_in_nav_menus'   => true
				)
			);
                
                
                register_post_type( 'em_ticket',
				array(
					'labels'              => array(
							'name'                  => __( 'Tickets', EM_TEXT_DOMAIN ),
							'singular_name'         => __( 'Ticket', EM_TEXT_DOMAIN ),
							'add_new'               => __( 'Add Ticket', EM_TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Ticket', EM_TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', EM_TEXT_DOMAIN),
							'edit_item'             => __( 'Edit Ticket', EM_TEXT_DOMAIN ),
							'new_item'              => __( 'New Ticket', EM_TEXT_DOMAIN),
							'view'                  => __( 'View Ticket', EM_TEXT_DOMAIN),
							'view_item'             => __( 'View Ticket', EM_TEXT_DOMAIN ),
							'not_found'             => __( 'No Tickets found', EM_TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Tickets found in trash', EM_TEXT_DOMAIN ),
							'featured_image'        => __( 'Ticket Image', EM_TEXT_DOMAIN ),
							'set_featured_image'    => __( 'Set ticket image', EM_TEXT_DOMAIN ),
							'remove_featured_image' => __( 'Remove ticket image', EM_TEXT_DOMAIN ),
							'use_featured_image'    => __( 'Use as ticket image', EM_TEXT_DOMAIN),
						),
					'description'         => __( 'Here you can add new events.', EM_TEXT_DOMAIN),
					'public'              => true,
					'show_ui'             => false,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Restrict WP to loads all records!
					'query_var'           => true,
                                        'capability_type'     => 'event_magic', 
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'show_in_nav_menus'   => false
				)
			);
                
                register_post_type( 'em_booking',
				array(
					'labels'              => array(
							'name'                  => __( 'Bookings', EM_TEXT_DOMAIN ),
							'singular_name'         => __( 'Booking', EM_TEXT_DOMAIN ),
							'add_new'               => __( 'Add Booking', EM_TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Booking', EM_TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', EM_TEXT_DOMAIN),
							'edit_item'             => __( 'Edit Booking', EM_TEXT_DOMAIN ),
							'new_item'              => __( 'New Booking', EM_TEXT_DOMAIN),
							'view'                  => __( 'View Booking', EM_TEXT_DOMAIN),
							'view_item'             => __( 'View Booking', EM_TEXT_DOMAIN ),
							'not_found'             => __( 'No Booking found', EM_TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Booking found in trash', EM_TEXT_DOMAIN ),
							'featured_image'        => __( 'Booking Image', EM_TEXT_DOMAIN ),
							'set_featured_image'    => __( 'Set Booking image', EM_TEXT_DOMAIN ),
							'remove_featured_image' => __( 'Remove Booking image', EM_TEXT_DOMAIN ),
							'use_featured_image'    => __( 'Use as Booking image', EM_TEXT_DOMAIN),
						),
					'description'         => __( 'Here you can add new bookings.', EM_TEXT_DOMAIN),
					'public'              => true,
					'show_ui'             => false,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Restrict WP to loads all records!
					'query_var'           => true,
                                        'capability_type'     => 'event_magic', 
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'show_in_nav_menus'   => false
				)
			);
                
                register_post_type( 'em_performer',
				array(
					'labels'              => array(
							'name'                  => __( 'Performers', EM_TEXT_DOMAIN ),
							'singular_name'         => __( 'Performer', EM_TEXT_DOMAIN ),
							'add_new'               => __( 'Add Performer', EM_TEXT_DOMAIN ),
							'add_new_item'          => __( 'Add New Performer', EM_TEXT_DOMAIN ),
							'edit'                  => __( 'Edit', EM_TEXT_DOMAIN),
							'edit_item'             => __( 'Edit Performer', EM_TEXT_DOMAIN ),
							'new_item'              => __( 'New Performer', EM_TEXT_DOMAIN),
							'view'                  => __( 'View Performer', EM_TEXT_DOMAIN),
							'view_item'             => __( 'View Performer', EM_TEXT_DOMAIN ),
							'not_found'             => __( 'No Performer found', EM_TEXT_DOMAIN ),
							'not_found_in_trash'    => __( 'No Performer found in trash', EM_TEXT_DOMAIN ),
							'featured_image'        => __( 'Performer Image', EM_TEXT_DOMAIN ),
							'set_featured_image'    => __( 'Set Performer image', EM_TEXT_DOMAIN ),
							'remove_featured_image' => __( 'Remove Performer image', EM_TEXT_DOMAIN ),
							'use_featured_image'    => __( 'Use as Performer image', EM_TEXT_DOMAIN),
						),
					'description'         => __( 'Here you can add new rerformers.', EM_TEXT_DOMAIN),
					'public'              => true,
					'show_ui'             => false,
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
                                        'rewrite'             => array( 'slug' => 'performer', 'with_front' => true ),
					'hierarchical'        => false, // Restrict WP to loads all records!
					'query_var'           => true,
                                        'capability_type'     => 'event_magic', 
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'show_in_nav_menus'   => true
				)
			);
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                
	}

	/**
	 * Register our custom post statuses, used for event status.
	 */
	public static function register_post_status() { 
		register_post_status( 'em-expired', array(
			'label'                     => __( 'Expired', 'Event status', EM_TEXT_DOMAIN ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', EM_TEXT_DOMAIN )
		) );
                
                register_post_status( 'em-cancelled', array(
			'label'                     => _x( 'Cancelled', 'Event status', EM_TEXT_DOMAIN ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', EM_TEXT_DOMAIN )
		) );
                
                register_post_status( 'em-pending', array(
			'label'                     => _x( 'Pending', 'Event status', EM_TEXT_DOMAIN ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', EM_TEXT_DOMAIN )
		) );
                
                register_post_status( 'em-refunded', array(
			'label'                     => _x( 'Refunded', 'Event status', EM_TEXT_DOMAIN ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', EM_TEXT_DOMAIN )
		) );
	}

	/**s
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'event_magic';
		return $post_types;
	}
        
        // Function to locate template for Single Post page    
        public static function em_locate_single_template( $single_template )
        {      
                global $post;
                if($post->post_type=="em_event")
                {
                    $events_page_id= em_global_settings('events_page');
                    wp_redirect(add_query_arg('event',$post->ID,  get_permalink($events_page_id)));
                    exit;
                }
                elseif($post->post_type=="em_performer")
                {
                    $performers_page_id= em_global_settings('performers_page');
                    wp_redirect(add_query_arg('performer',$post->ID,  get_permalink($performers_page_id)));
                    exit;
                }
                           
                return $single_template;
        }
        
        public static function em_locate_single_venue_template($template){
            $object = get_queried_object();
            if($object->taxonomy=="em_venue")
            {   
                $venue_page= em_global_settings('venues_page');
                if(!empty($venue_page))
                {  
                    $venue_url= add_query_arg( 'venue', $object->term_id, get_permalink($venue_page) );
                    wp_redirect($venue_url);
                }
                
            }
            
            return $template;
        }
        
}

EventM_Post_types::init();
