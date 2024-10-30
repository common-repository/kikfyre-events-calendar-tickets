<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @summary Dao class for Event Ticket. 
 * 
 * Functions
 * 1. edit_event_ticket :  Responsible for adding and editing the record.
 */
class EventM_Event_Ticket_DAO extends EventM_Post_Dao
{
    
    public function __construct() {
        $this->post_type= EM_TICKET_POST_TYPE;
    }
    public function getTemplates(){
        $args = array(
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => EM_TICKET_POST_TYPE,
	'post_status'      => 'publish',
        'numberposts'      =>  -1
            );
          
        $posts= get_posts($args);
        if(empty($posts))
            return null;
        
        return $posts;
    }
    
    public function get($id)
    {
        $post= get_post($id);
          if(empty($post))
              return new EventM_Event_Ticket_Model(0);
          
        $attributes= EventM_Constants::get_ticket_cons();
        $ticket= new EventM_Event_Ticket_Model($id);
        foreach($attributes['meta'] as $attr)
        {
            $method= 'set_'.$attr;
            if(method_exists($ticket, $method))
            {
                $ticket->$method(em_get_post_meta($id, $attr, true));
            }
        }
        
        // Setting core attributes
        $ticket->set_name($post->post_title);
        
        return $ticket;
    }
    
}
