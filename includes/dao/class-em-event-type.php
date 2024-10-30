<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @summary Dao class for Event Type. 
 * 
 * Functions
 * 1. edit_event_type :  Responsible for adding and editing the record.
 */
class EventM_Event_Type_DAO extends EventM_Term_Dao
{   
    public function __construct() {
        parent::__construct(EM_EVENT_TYPE_TAX);
    }
    public function getTypes(){
        $terms = get_terms( array(
            'taxonomy' => EM_EVENT_TYPE_TAX,
            'hide_empty' => false,
        ) );
        //print_r($terms); die;
        if(empty($terms)){
            return null;
        }
        
        return $terms; 
    }
    
    public function getAttachedEventCount($term_id){
        $args = array(
            'post_type'=>EM_EVENT_POST_TYPE,
            'numberposts'=>-1,
            'tax_query' => array(
                array(
                    'taxonomy' => EM_EVENT_TYPE_TAX,
                    'field' => 'term_id',
                    'terms' => $term_id
                )),
            
            'meta_query' => array(	
                array(
                'key'     => em_append_meta_key('hide_event_from_events'),
		'value'   => '1',
                'compare' => '!='            
               ))
        );
       
        $events = get_posts( $args); 
        return count($events);
    }
    
    public function getAttachedEvent($term_id){
        $args = array(
            'post_type'=>EM_EVENT_POST_TYPE,
            'numberposts'=>-1,
            'tax_query' => array(
                array(
                    'taxonomy' => EM_EVENT_TYPE_TAX,
                    'field' => 'term_id',
                    'terms' => $term_id
                )
            )
        );
       
        $events = get_posts( $args); 
     //  echo'<pre>'; print_r($events);
        return $events;
    }
    
    public function get($id)
    {
        $term= get_term($id);
          if(empty($term))
              return new EventM_Event_Type_Model(0);
          
        $attributes= EventM_Constants::get_type_cons();
        $type= new EventM_Event_Type_Model($id);
        foreach($attributes['meta'] as $attr)
        {
            $method= 'set_'.$attr;
            if(method_exists($type, $method))
            {
                $val= em_get_term_meta($id, $attr, true);
                if(!empty($val))
                    $type->$method($val);
            }
        }
        
        // Setting core attributes
        $type->set_name($term->name);
        
        return $type;
    }
    
   
    
}
