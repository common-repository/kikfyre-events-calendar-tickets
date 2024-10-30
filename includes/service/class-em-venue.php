<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EventM_Venue_Service  
{
 
    private $dao;
    
    public function __construct() {
        $this->dao= new EventM_Venue_DAO();
    }
    
    /*
     * Load Add/Edit page for REST 
    */
    public function load_edit_page()
    {
        
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        $id= event_m_get_param('id');
        $venue= $this->load_model_from_db($id);
        $response->term= $venue->to_array();
        $response->trans= $stringsModel->addVenue();
        $gmap_api_key= em_global_settings('gmap_api_key');
        if(!empty($gmap_api_key)):
            $response->term['map_configured']= true;
        else:
            $response->term['map_configured']= false;
            $response->term['map_notice']= EventM_UI_Strings::get("NOTICE_VENUE_MAP_NOT_CONFIGURED");
        endif;
        $response->term['types']= $this->seat_dropdown();
        $response->term['addresses']= array();
        $response->term['images']= $this->get_gallery_images($venue->get_gallery_images());
        $response->links= new stdClass();
        $response->links->cancel= admin_url('/admin.php?page=em_venues');
        echo json_encode($response);
        wp_die();
    }
    
    /*
     * Load List page for REST 
     */
    public function load_list_page()
    {   
     
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        
        $response->terms= array();
        $sort_option= event_m_get_param('sort_option');
        // Get terms 
        $venues= get_terms(EM_EVENT_VENUE_TAX,
                 array( 'hide_empty' => 0,
                        'orderby'=>$sort_option,
                        'order'=>event_m_get_param('order'),
                        'offset'=> (int) (event_m_get_param('paged')-1) * EM_PAGINATION_LIMIT,
                        'number'=>EM_PAGINATION_LIMIT));
        
        foreach($venues as $tmp){
            
         $venue= $this->load_model_from_db($tmp->term_id);   
        
           $data= $venue->to_array();
           // Cover image
      
           if(!empty($data['gallery_images']))
           {
               $images= $data['gallery_images'];
               $feature_image=wp_get_attachment_image_src($images[0],'large');
               $data['feature_image']= $feature_image[0];
           }
           
           // Number of upcoming events
           $args = array('tax_query' => array(array('taxonomy'=>'em_venue','field'=>'term_id','terms'=>$venue->get_id())));
           $tmp_posts = query_posts( $args );
          
            foreach($tmp_posts as $value):  
               $child_events = em_get_post_meta($value->ID,'child_events');
               if(isset($child_events[0])):
                    $data['event_count']=  count($child_events[0]);
               else:
                   $data['event_count']=  1;
               endif;
            endforeach;
           $response->terms[]= $data;
        }
        
         // Loading default sorting options
        $response->sort_options= em_array_to_options(array("count"=>EventM_UI_Strings::get('LABEL_NU_EVENTS'),
                                    "name"=>EventM_UI_Strings::get('LABEL_ALPHABETICALLY')));
        $response->sort_option= $sort_option;
        $response->links= new stdClass();
        $response->links->add_new= admin_url('/admin.php?page=em_venue_add');
        $response->trans= $stringsModel->listVenues();
        $response->tax_type=EM_EVENT_VENUE_TAX;
        $terms_count= wp_count_terms($response->tax_type, array('hide_empty' => false) );
        $response->total_count= range(1,$terms_count);  
        $response->pagination_limit= EM_PAGINATION_LIMIT;
         
        echo json_encode($response);
        wp_die();
    }
    
    public function save($model)
    {   
        $id = isset($model->id) ? $model->id : 0;
        $venue_model= $this->map_request_to_model($id,$model);
        if($venue_model->type=="standings")
        {
            $venue_model->seats= array();
            $venue_model->seating_capacity=0;
        }
//        else{
//            $this->update_events_capacity($venue_model);
//        }
            
        $venue= $this->dao->save($venue_model);
         $this->update_events_seats($venue_model);
        // In case of any errors
        if ($venue instanceof WP_Error) {
            return $venue;
        }
        return $venue;
         
    }   
    
    // Updating capacity in associated events
    private function update_events_capacity($venue_id,$event_id)
    {   
      
        $venue_model = $this->load_model_from_db($venue_id);
       
     
         $filter = array('post_type' => EM_EVENT_POST_TYPE,
         'post_status'=> array('publish','em-expired'),    
         'tax_query' => array(
            array(
                'taxonomy' => EM_EVENT_VENUE_TAX,
                'field' => 'term_id',
                'terms' => $venue_model->id,
                ),
            ),
         );
      
         $event_service= new EventM_Service();
         $events= $event_service->get_events($filter);
        
         if(!empty($events) && $venue_model->seating_capacity>0 &&  $venue_model->type=="seats")
         {   
             foreach($events as $event)
             {  
                
            
                 if($event_id==$event->ID){
                    em_update_post_meta($event->ID, 'seating_capacity', $venue_model->seating_capacity);
                 }
             }
         }
       

    }
    
     private function update_events_seats($venue_model)
    {   
       
         $filter = array('post_type' => EM_EVENT_POST_TYPE,
         'post_status'=> array('publish','em-expired'),    
         'tax_query' => array(
            array(
                'taxonomy' => EM_EVENT_VENUE_TAX,
                'field' => 'term_id',
                'terms' => $venue_model->id,
                ),
            ),
         );
      
         $event_service= new EventM_Service();
         $events= $event_service->get_events($filter);
        
         if(!empty($events) && $venue_model->seating_capacity>0 &&  $venue_model->type=="seats")
         {   
             foreach($events as $event)
             { 
                  $event_service->create_seats_from_venue($event->ID, $venue_model->id);
                  //em_update_post_meta($event->ID, 'seating_capacity', $venue_model->seating_capacity);

                 
             }
         }
    }
    
    public function get_upcoming_events($venue_id){
             $venue_dao = new EventM_Venue_DAO();             
             $venue_events= $venue_dao->get_upcoming_events($venue_id);       
             return $venue_events;
        
    }
    
    public function venue_addresses_for_marker(){
        
       $venue_dao = new EventM_Venue_DAO(); 
       $venues = array();
       $venue_id= (int) event_m_get_param("venue_id");      
       
       if (!empty($venue_id) && is_string($venue_id)):
          $id = explode(',',$venue_id);
          foreach($id as $data):
              $venue = get_term($data);          
            $venues[]= $venue;       
          endforeach;
          
       elseif($venue_id>0):
           $venue= get_term($venue_id);
           $venues= array($venue);    
      
       else:
           $venues= $venue_dao->get_all();   
       endif;
       
        
       
       $addresses= array();
       if(!empty($venues)):
           foreach($venues as $venue):
                $address= em_get_term_meta($venue->term_id, 'address', true);
                if(!empty($address)):
                    $addresses[]= $address;
                endif;
           endforeach;
       endif;
       
       return $addresses; 
    }  
    
    public function get_venues()
    {
          $venue_dao = new EventM_Venue_DAO();             
             $venues= $venue_dao->get_all();       
             return $venues;
    }
    
    
    public function get_venue_addresses_by_events($events= array())
    {
        $data= array();
        
        foreach($events as $event)
        {  
            $venues = wp_get_post_terms($event->ID, EM_VENUE_TYPE_TAX);
            if(!empty($venues))
            {
                foreach($venues as $venue)
                {
                    $tmp= new stdClass();
                    $tmp->name= $venue->name;
                    $tmp->venue_id= $venue->term_id;
                    $tmp->address= em_get_term_meta($venue->term_id, 'address', true);
                    $data[]= $tmp;
                }
            }
        }
        return $data;
    }
    
    public function capacity($venue_id)
    {   $venue_id= (int) $venue_id;
        if($venue_id==0)
            return 0;

        return $this->dao->get_capacity($venue_id); 
    }
    
    public function get_seats($venue_id,$event_id)
    {
        $this->update_events_capacity($venue_id,$event_id);
        return em_get_term_meta($venue_id, 'seats', true);
    }
    
    public function seat_dropdown()
    {
       $types= array(""=>EventM_UI_Strings::get('LABEL_SELECT'),
               "standings"=>"Standing",
               "seats"=>"Seating");

       return em_array_to_options($types);
    }
    
     
    public function get_gallery_images($ids)
    {
        $images= array();
        if(is_array($ids))
        {
            $image_ids= array_unique($ids);
            foreach($image_ids as $image_id){
                $tmp= new stdClass();
                $tmp->src=wp_get_attachment_image_src($image_id);
                $tmp->id=$image_id;
                $images[]= $tmp;
            }
        }
        return $images;
    }
    
    public function load_model_from_db($id)
    {
        return $this->dao->get($id);
    }
    
    public function map_request_to_model($id,$model=null)
    {  
       
        $venue= new EventM_Venue_Model($id);
        $data= (array) $model;
  
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key=>$val)
            {
         
                $method= "set_".$key;
                if(method_exists($venue, $method))
                {
                    $venue->$method($val);
                }
            }
        }
      
        return $venue;
        
    }
    
    public function validate($model, $response) {
        $venue_error= false;
        $response= new stdClass();
        $term= term_exists($model->name,EM_EVENT_VENUE_TAX);
        if(!empty($term) && isset($term['term_id']) && $term['term_id']!=$model->id)
        {
             $response->error_status = true;
             $response->errors[] = "Please use different Venue name";
             $response->redirect = false;
        }
        return $response;
    }
    
}
