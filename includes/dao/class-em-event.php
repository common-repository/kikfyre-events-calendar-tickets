<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @summary Dao class for Event. 
 * 
 */
class EventM_Event_DAO extends EventM_Post_Dao
{
    public function __construct() {
        parent::__construct(EM_EVENT_POST_TYPE);
    }
    
    public function create($event,$id=0)
    {  
        if ($id>0) 
        {
            $event['ID']= $id;
            $id = wp_update_post($event);
        }
        else 
            $id = wp_insert_post($event);
        
        return $id;
    }
    
     public function save($model)
     {
         return parent::save($model);
     }
     
    /**
     * 
     * @param Event ID $id
     * @param Type ID $type
     */
    public function set_type($id,$type)
    {
        wp_set_object_terms($id, $type, EM_EVENT_TYPE_TAX, false);
    }
    
    /**
     * 
     * @param Event ID $id
     * @param Venue ID $type
     */
    public function set_venue($id,$venue)     
    {  
        wp_set_object_terms($id, $venue, EM_EVENT_VENUE_TAX, false);
       
    }
    
    public function set_performer($id,$performer)    
    {  
        update_post_meta($id,EM_PERFORMER_POST_TYPE, $performer,  false);
       
    }
   
    
    public function set_thumbnail($id,$img_id)
    {
        set_post_thumbnail($id, $img_id);
    }

    public function getTemplates() {
        $args = array(
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => EM_TICKET_POST_TYPE,
            'post_status' => 'publish');

        $posts = get_posts($args);
        if (empty($posts))
            return null;

        return $posts;
    }

    // Get all the events  
    public function get_events($filter = array('meta_key'=> 'em_start_date', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'post_type' => EM_EVENT_POST_TYPE)) {
        $posts = get_posts($filter);   
        return $posts;
    }
    
     public function get_past_events(){
         $filter = array(
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status'=> 'publish',
            'meta_query' => array(// WordPress has all the results, now, return only the events after today's date
                'relation' => 'AND',
                array(
                    'key' => em_append_meta_key('start_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '<=', // Return the ones less than today's date
                    'type' => 'NUMERIC,' // Let WordPress know we're working with numbers
                ),
                array(
                    'key' => em_append_meta_key('end_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '<=', // Return the ones greater than today's date
                    'type' => 'NUMERIC,' // Let WordPress know we're working with numbers
                )                
            ),
            'post_type' => EM_EVENT_POST_TYPE);
         
         return $this->get_events($filter);
     }

    // Get upcoming events
    public function get_upcoming_events() { 
        $filter = array(
            'meta_key'=> em_append_meta_key('start_date'),         
            'orderby' => 'meta_value_num',
            'numberposts'=>-1,
            'order' => 'ASC',  
            'post_status'=> 'publish',          
            'meta_query' => array('relation'=>'AND',// WordPress has all the results, now, return only the events after today's date
                array('relation'=>'AND',
                array(
              
                array(
                                        'key' => em_append_meta_key('hide_event_from_events'), 
                                        'value' => '1', //
                                        'compare' => '!='
                 ), 
                array(   
               'relation' => 'OR',
                array(
                    'key' => em_append_meta_key('start_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                ),
                array(
                    'key' => em_append_meta_key('end_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                )),
                array(
                'key'     => em_append_meta_key('hide_event_from_calendar'),
		'value'   => '1',
                'compare' => '!='            
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
                                 )
                    
                    )),   
            'post_type' => EM_EVENT_POST_TYPE);

        return $this->get_events($filter);      
    }
    
    
      public function get_upcoming_events_calendar() { 
        $filter = array(
            'meta_key'=> em_append_meta_key('start_date'),         
            'orderby' => 'meta_value_num',
            'numberposts'=>-1,
            'order' => 'ASC',  
            'post_status'=> 'publish',          
            'meta_query' => array('relation'=>'AND',// WordPress has all the results, now, return only the events after today's date
                array('relation'=>'AND',
                array(
              
                array(
                                        'key' => em_append_meta_key('hide_event_from_events'), 
                                        'value' => '1', //
                                        'compare' => '!='
                 ), 
                array(   
               'relation' => 'OR',
                array(
                    'key' => em_append_meta_key('start_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                ),
                array(
                    'key' => em_append_meta_key('end_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                )),
                array(
                'key'     => em_append_meta_key('hide_event_from_calendar'),
		'value'   => '1',
                'compare' => '!='            
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
                                 )
                    
                    )),   
            'post_type' => EM_EVENT_POST_TYPE);
        
       
        return $this->get_events($filter);
        
    }

    // Get upcoming events
    public function get_events_by_start_date($date,$format= false) { 
       
        $start_time = em_time($date);
        $end_time = $start_time + 86340;
        
        $event_ids = array();
        $filter = array(
            'orderby' => 'date',
             'numberposts'=> -1,
            'post_status'=> 'publish',
            'order' => 'DESC',
            'meta_query' => array(
               'relation' => 'AND',
                 array(
               'relation' => 'AND',
                array(
                    'key' => em_append_meta_key('start_date'), 
                    'value' => $start_time, 
                    'compare' => '>=',
                    'type' => 'NUMERIC,' 
                ),
                array(
                    'key' => em_append_meta_key('start_date'), 
                    'value' => $end_time, 
                    'compare' => '<=', 
                    'type' => 'NUMERIC,' 
                )),
              array(
                'key'     => em_append_meta_key('hide_event_from_calendar'),
		'value'   => '1',
                'compare' => '!='            
               )),
            'post_type' => EM_EVENT_POST_TYPE);

        $events = $this->get_events($filter);
        
        if (!empty($events)):
            foreach ($events as $event):
                $event_ids[] = $event->ID;
            endforeach;
        endif;
        
        $recurring_events= $this->get_recurring_events_by_date($date);
        $event_ids= array_merge($event_ids,$recurring_events->ids);
      
        return $event_ids;
    }
    
    // Get events at a date
   
    // get_events_by_recurrence
    public function get_recurring_events($include= array()){ 
         // Get recurring events
        $filter = array(
            'orderby' => 'date',
            'post_status'=> 'publish',
            'order' => 'DESC',
            'numberposts' => -1,
       
            'meta_query' => array(// WordPress has all the results, now, return only the events after today's date
                'relation' => 'AND',
            array(// WordPress has all the results, now, return only the events after today's date
                'relation' => 'OR',
                array(
                    'key' => em_append_meta_key('recurring_option'),
                    'value' => "specific_dates"
                ),
                array(
                    'relation' => 'AND',
                    array(
                        'key' => em_append_meta_key('recurring_option'),
                        'value' => "recurring"
                    )
                    ,
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => em_append_meta_key('recurrence_interval'),
                            'value' => "Weekly"
                        )
                        ,
                        array(
                            'key' => em_append_meta_key('recurrence_interval'),
                            'value' => "Monthly"
                        ),
                        array(
                            'key' => em_append_meta_key('recurrence_interval'),
                            'value' => "annually"
                        )))),
                 array(
                'key'     => em_append_meta_key('hide_event_from_calendar'),
		'value'   => '1',
                'compare' => '!='            
               )
                ),
            'post_type' => EM_EVENT_POST_TYPE);
                $filter['post__in']= $include;

        $posts = get_posts($filter);
        $data= new stdClass();
        $data->event_dates = array();
        $data->ids= array();
        
        foreach ($posts as $post) {
            $recurring_option = em_get_post_meta($post->ID, 'recurring_option', true);
            $recurrence= (int) em_get_post_meta($post->ID, 'recurrence', true);
            if(empty($recurrence))
                $recurrence=1;
            
            if ($recurring_option == "recurring") {
                $recurrence_interval = em_get_post_meta($post->ID, 'recurrence_interval', true);
                $start_date = em_get_post_meta($post->ID, 'start_date', true);
               
                if (!empty($recurrence_interval) && strtolower($recurrence_interval) == "annually") {
                    if (!empty($start_date)){ 
                        $counter=0;
                        while($counter<$recurrence){ 
                            $data->event_dates[] = date('Y-m-d',strtotime('+'.$counter.' years',$start_date));
                            $data->ids[]= $post->ID;
                            $counter= $counter +1; 
                        }
                        
                        
                    }
                };

                if (!empty($recurrence_interval) && $recurrence_interval == "Monthly") {

                    if (!empty($start_date)) {
                        $between_dates= array();
                        
                        $counter=1;
                        while($counter<$recurrence){
                            $tmp_date= date('Y-m-d',strtotime('+'.$counter.' months',$start_date));
                            $start_day= date('d',$start_date); 
                            $tmp_start_day= date('d',strtotime('+'.$counter.' months',$start_date));
                            if($start_day==$tmp_start_day)
                            {
                                $between_dates[]= $tmp_date;
                                
                            }
                            else
                            {
                                $recurrence= $recurrence +1;
                            }
                            $counter= $counter +1;
                            
                        }
            
                        
                        if(count($between_dates)>0){
                            foreach($between_dates as $tmp):
                                $data->event_dates[]= $tmp;
                                $data->ids[]= $post->ID;
                            endforeach;
                        }
                    } 
                }

                if (!empty($recurrence_interval) && $recurrence_interval == "Weekly") {
                    if (!empty($start_date)){
                        $counter=1;
                        
                        $weekday = date('l', $start_date); 
                        while($counter<=$recurrence){ 
                            $tmp_date= date('Y-m-d',  strtotime('+'.$counter.$weekday,$start_date));
                            $data->event_dates[] = $tmp_date;
                            $data->ids[]= $post->ID;
                            $counter++;
                        }
                        
                    }
                        
                }
            }
            
            if($recurring_option=="specific_dates"){ 
                $specific_dates = em_get_post_meta($post->ID, 'recurring_specific_dates', true);
                if(!empty($specific_dates) && count($specific_dates)>0){
                   $data->event_dates= array_merge($data->event_dates, $specific_dates);
                   foreach($specific_dates as $tmp):
                        $data->ids[]= $post->ID;
                   endforeach;
                   
                }
            }
        }
        return $data;
    }
    
    public function get_recurring_events_by_date($start_date){ 
        $data= $this->get_recurring_events();

        foreach($data->event_dates as $key=>$event_start_date){ 
            if($start_date!=$event_start_date){ 
                unset($data->event_dates[$key]);
                unset($data->ids[$key]);
            }
        }
         
        return $data;
        
    }
    
    public function get_upcoming_recurring_events(){
        $data= $this->get_recurring_events();
        
        foreach($data->event_dates as $key=>$event_start_date){
            if(strtotime($event_start_date) < strtotime(date('Y-m-d'))){
                unset($data->event_dates[$key]);
                unset($data->ids[$key]);
            }
            
        }
        
        return $data;
        
    }
    
    public function get_recurring_event_dates($event=null){
        if(!is_object($event))
            $event= get_post($event);
        
        $event_dates= array();
        
        if(!empty($event)) {
            $recurring_option = em_get_post_meta($event->ID, 'recurring_option', true);
            
            if ($recurring_option == "recurring") {
                $recurrence_interval = em_get_post_meta($event->ID, 'recurrence_interval', true);
                $start_date = em_get_post_meta($event->ID, 'start_date', true);
                $end_date = em_get_post_meta($event->ID, 'end_date', true);
                $date_diff= $end_date-$start_date;
               
                if (!empty($recurrence_interval) && strtolower($recurrence_interval) == "annually"){ 
                     
                    if (!empty($start_date)){
                        $event_dates[] = date('Y-m-d', strtotime('+1 years',$start_date));
                    }
                };

                if (!empty($recurrence_interval) && $recurrence_interval == "Monthly") {
                    if (!empty($start_date)) {
                        if($start_date <  current_time('timestamp')){
                            $tmp_date = date('Y-m-d', strtotime('+1 month', $start_date));
                        }

                        $event_dates[] = $tmp_date;
                    }
                }

                if (!empty($recurrence_interval) && $recurrence_interval == "Weekly") {
                    if (!empty($start_date)){
                        $weekday= date('l',$start_date);
                        $day_date=date('Y-m-d', strtotime('next '.$weekday));
                        $event_dates[] = $day_date;
                    }
                        
                }
            }
            
            if($recurring_option=="specific_dates"){ 
                $specific_dates = em_get_post_meta($event->ID, 'recurring_specific_dates', true);
                if(!empty($specific_dates) && count($specific_dates)>0){
                   $event_dates= array_merge($event_dates, $specific_dates);
                }
            }
        }
        
        return $event_dates;
    
    }
    
    public function get_venue($event_id)
    {
        $terms = wp_get_post_terms($event_id, EM_VENUE_TYPE_TAX);
        foreach($terms as $term){
            if($term->taxonomy==EM_VENUE_TYPE_TAX){
                return $term;
            }
        }
        return null;
    }
    
    public function get_type($event_id)
    {
        $terms = wp_get_post_terms($event_id, EM_EVENT_TYPE_TAX);
        foreach($terms as $term){
            if($term->taxonomy==EM_EVENT_TYPE_TAX){
                return $term;
            }
        }
        return null;
    }
    
    public function available_seats($event_id)
    {
        $sum= $this->booked_seats($event_id);
        $capacity= (int) em_get_post_meta($event_id, 'seating_capacity', true);
    
       // $event_service = new EventM_Service();
       // $venues = $event_service->get_venue($event_id);
       // $venue_type = em_get_term_meta($venues->term_id,'type',true);

       if(!empty($capacity))
        {   
            if( $capacity>0)                  
             return $capacity-$sum;
        }

         return 99999999;  
       
    }
    
    
    public function booked_seats($event_id)
    {  
        
     /*  $bookings = array(
                'post_type'=> EM_BOOKING_POST_TYPE,
                'post_status'=>array('publish','em-pending'),
                'numberposts'=>-1,  
                'meta_query'	=>	array(
                        array(
                        'key'     => 'em_event_id',
                        'value'   =>  $event_id,
                        'compare' => '='
                        )));
        $events =  get_posts( $bookings );
        
        $sum=0;
        foreach($events as $event):
             $order_info=  em_get_post_meta($event->ID,'order_info',true);
             $sum= $sum + $order_info['quantity'];
         endforeach;
         */
     //  return $sum;
         
      
       
            return em_get_post_meta($event_id, 'booked_seats', true, true);
        
    
    }
    
    public function exclude_child_query()
    {
        return array(
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
            );
    }
    
    public function get($id)
    {
        if(empty($id))
           return new EventM_Event_Model(0);
         
        $post= get_post($id);
        if(empty($post))
              return new EventM_Event_Model(0);
          
        $attributes= EventM_Constants::get_event_cons();
        $event= new EventM_Event_Model($id);
        $data= array();
      
        foreach($attributes['meta'] as $attr)
        {
           
            $data[$attr]= $this->get_meta($id, $attr, true);
          
        }
       
        $event->from_array($data);
        // Setting core attributes
        $event->set_name($post->post_title);
        $event->set_slug($post->post_name); 
        $event->set_status($post->post_status);
        $event->set_description($post->post_content);
        $event_type= $this->get_type($post->ID);
        $event->set_event_type(!empty($event_type) ? $event_type->term_id : null);
        $venue= $this->get_venue($post->ID);
        $event->set_venue(!empty($venue) ? $venue->term_id : null);
        
        return $event;
    }
    
    public function delete_child_events($ids= array())
    {
        EventM_Utility::delete_posts($ids);
    }
    
    public function getAttachedEvents($term_id) {
        $args = array(
            'post_type' => EM_EVENT_POST_TYPE,
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => EM_VENUE_TYPE_TAX,
                    'field' => 'term_id',
                    'terms' => $term_id
                )
            )
        );

        $events = get_posts($args);
        return $events;
    }

}
