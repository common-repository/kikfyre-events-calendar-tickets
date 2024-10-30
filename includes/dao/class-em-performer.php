<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @summary Dao class for Performer. 
 * 
 * Functions
 * 1. edit_performer :  Responsible for adding and editing the record.
 */
class EventM_Performer_DAO extends EventM_Post_Dao
{
    public function __construct() {
        parent::__construct(EM_PERFORMER_POST_TYPE);
    }
    public function set_thumbnail($id,$img_id)
    { 
        set_post_thumbnail($id, $img_id);
    }
    
    public function getPerformers(){
        $args = array(
	'orderby'          => 'date',
        'numberposts'      => -1,
        'offset'           => 0,     
	'order'            => 'DESC',
	'post_type'        => EM_PERFORMER_POST_TYPE,
	'post_status'      => 'publish');
          
        $posts= get_posts($args);
        if(empty($posts))
            return null;
        
        return $posts;
    }
    
    public function get_upcoming_events($performer_id,$events= array()){
        $performer_events= array();
        if(is_array($events)):
            foreach($events as $event):
                if(!is_object($event))
                    $event= get_post ($event);
                $performers= em_get_post_meta($event->ID, 'performer', true);               
                if(!empty($performers) && is_array($performers)):
                    if(in_array($performer_id, $performers))
                        $performer_events[]= $event;
                endif;
                
            endforeach;
        endif;
      
        return $performer_events;
    }
    
    public function get($id)
    {
        $post= get_post($id);
          if(empty($post))
              return new EventM_Performer_Model(0);
          
        $attributes= EventM_Constants::get_performer_cons();
        $performer= new EventM_Performer_Model($id);
        foreach($attributes['meta'] as $attr)
        {
            $method= 'set_'.$attr;
            if(method_exists($performer, $method))
            {
                $performer->$method(em_get_post_meta($id, $attr, true));
            }
        }
        
        // Setting core attributes
        $performer->set_name($post->post_title);
        $performer->set_slug($post->post_name);
        
        return $performer;
    }
    
}
