<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @summary Dao class for menu. 
 * 
 * Functions
 * 1. edit_venue :  Responsible for adding and editing the Venue record.
 */
class EventM_Venue_DAO extends EventM_Term_Dao {

    public function __construct() {
        parent::__construct(EM_EVENT_VENUE_TAX);
    }

    // Get list of published venues
    public function get_all($empty= false) {
       $args = array('taxonomy' => EM_EVENT_VENUE_TAX,'hide_empty' => $empty);
       return parent::get_all($args);
    }

    public function get_upcoming_events($venues_id) {

        $events = new EventM_Event_DAO();
        $filter = array(
            'meta_key' => em_append_meta_key('start_date'),
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'post_status' => 'publish',
            'posts_per_page' => '-1', // Let's show them all.  
            'meta_query' => array(// WordPress has all the results, now, return only the events after today's date
                array(
                    'relation' => 'AND',
                    array(
                        'key' => em_append_meta_key('hide_event_from_events'),
                        'value' => '1', //
                        'compare' => '!='
                    ),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => em_append_meta_key('start_date'), // Check the start date field
                            'value' => current_time('timestamp'), // Set today's date (note the similar format)
                            'compare' => '>='// Return the ones greater than today's date
                        ),
                        array(
                            'key' => em_append_meta_key('end_date'), // Check the start date field
                            'value' => current_time('timestamp'), // Set today's date (note the similar format)
                            'compare' => '>=' // Return the ones greater than today's date
                        )),
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => em_append_meta_key('parent_event'),
                            'value' => 0,
                            'compare' => '=',
                            'type' => 'NUMERIC,'
                        ),
                        array(
                            'key' => em_append_meta_key('parent_event'),
                            'compare' => 'NOT EXISTS',
                        )
                    ))),
            'tax_query' => array(array('taxonomy' => 'em_venue', 'field' => 'term_id', 'terms' => $venues_id)),
            'post_type' => EM_EVENT_POST_TYPE);


        return $events->get_events($filter);
    }

    public function get_capacity($venue_id) {
        $capacity= (int) $this->get_meta($venue_id,'seating_capacity');
        return $capacity>0 ? $capacity : 0;
    }

    public function get($id) { 
        $venue= $this->get_single($id);
        if (empty($venue))
            return new EventM_Venue_Model(0);

        $attributes = EventM_Constants::get_venue_cons();
        $model = new EventM_Venue_Model($id);
        foreach ($attributes['meta'] as $attr) {
            $method = 'set_' . $attr;
            if (method_exists($model, $method)) {
                $val = $this->get_meta($id, $attr, true);
                if (!empty($val))
                    $model->$method($val);
            }
        }
        // Setting core attributes
        $model->set_name($venue->name);
        $model->set_slug($venue->slug);

        
        return $model;
    }

    public function create($venue, $id = 0) {
        return $this->insert_or_update($venue, EM_EVENT_VENUE_TAX, $id);
    }

}
