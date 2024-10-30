<?php

class EventM_Booking_Model extends EventM_Model{
    private $task;
    
    function __construct($task) { 
        parent::__construct(array('trans','post','links'));
        $this->task= $task;
        
        $this->loadTranslationStrings();
        
        $this->loadData();
    }

    protected function loadData() { 
        if($this->task=="edit"){
            $this->loadBooking();
        }
        
        if($this->task=="list"){
            $this->loadBookings();
        }
        
        $this->otherPageInfo();
    }
    
    protected function loadBooking(){ 
        // Intializing all the data variables
        $this->data->post->edit = 0;
        $status_list= array(
                       'publish'=>'Completed',
                       'em-pending'=>'Pending',
                       'em-refunded'=>'Refunded',
                       'em-cancelled'=>'Cancelled'
        );
        /**
         *  Loading data for Edit page.
         *  If id is greater than 0, then fill all the data variables with existing database values
         */
        $post_id= event_m_get_param('post_id');
  
        if($post_id!=null && (int) $post_id!=0){
            
            $event_id = em_get_post_meta($post_id, 'event_id', true);   
            
            
            $terms = em_get_post_meta($event_id,'venue');
            $venue = $terms[0];
            $type = em_get_term_meta($venue, 'type', true); 
          //  print_r('nik');print_r($terms); die;
            $event_post = get_post($event_id);
            $booking= get_post($post_id);
             // Get Post by ID
             $post= get_post($post_id);
             $this->data->post->edit = 1;
             $this->data->post->post_id= $post->ID;             
             $this->data->post->event_name= $event_post->post_title;
             $this->data->post->date= $booking->post_date;
             $this->data->post->order_info= em_get_post_meta($post->ID, 'order_info', true);
             $this->data->post->status= $post->post_status;
             $this->data->post->notes= em_get_post_meta($post_id, 'notes', true);
             $payment_log= maybe_unserialize(em_get_post_meta($post_id, 'payment_log', true));
             $this->data->post->payment_log= (empty($payment_log)) ? "No Transaction Log Available" : $payment_log;
             
              //offline data filter start
             if($this->data->post->payment_log['payment_gateway'] == 'offline'){
                $this->data = apply_filters('em_loadBooking_offline',$this->data);
             }
             // offline data filter close
             
             
             $this->data->post->user_id= (int) em_get_post_meta($post_id, 'user_id', true);           
             $this->data->post->status= $status_list[get_post_status($post_id)];
             $this->data->post->type = $type;
             $this->data->post->event_id=$event_id;
              $this->data->post->event_status = $event_post->post_status;
              // User data
             $tmp_user= get_user_by('ID', $this->data->post->user_id);
             //print_r($tmp_user); die;
              
              $order_info = em_get_post_meta($post->ID, 'order_info', true);
                $currency_symbol= $order_info['currency']; 
                     if( isset($currency_symbol) && !empty($currency_symbol)):
                          $this->data->post->currency_symbol = $currency_symbol;
                     elseif( $this->data->post->payment_log['payment_gateway'] == 'paypal'):
                              $this->data->post->currency_symbol = $currency_symbol=$this->data->post->payment_log['mc_currency'];
                     else:   
                     endif;   
              
             
             $user= new stdClass();
            
            $user->booking_id = $post_id;
             $user->display_name= $tmp_user->display_name;
             $user->email= $tmp_user->user_email;           
             $user->phone= get_user_meta($this->data->post->user_id, 'phone', true);
             
             $current_user = wp_get_current_user(); 
            $user->current_user_email = $current_user->user_email;
            
             $this->data->user= $user; 
             
            
         }
    }
    
     protected function loadBookings(){ 
       // $this->data->posts= array();
        
        // Get all the performers
        $args = array(
            'posts_per_page' => EM_PAGINATION_LIMIT,
            'offset' => ((int) event_m_get_param('paged')-1) * EM_PAGINATION_LIMIT,
            'numberposts' => -1,
            'post_type' => EM_BOOKING_POST_TYPE,
            'post_status' => 'any',
            );
      //   $posts = get_posts($args); print_r($posts);die;
        $filter= array();

        $filter['event']= event_m_get_param('event');
        $filter['status']= event_m_get_param('filter_status');
        $filter['filter_between']= event_m_get_param('filter_between');
        
        $this->data->paged= event_m_get_param('paged');
        $this->data->date_from= event_m_get_param('date_from');
        $this->data->date_to=  event_m_get_param('date_to');
        $this->data->filter_status=  event_m_get_param('filter_status');
        $args= $this->apply_filter($filter,$args);
        
        $this->data->selcted_bookings = event_m_get_param('selected_bookings');
      
        $bookings = get_posts($args);
        $this->data->post_query= $args;    
        // Preparing data for each booking 
        if (is_array($bookings)) {
            foreach ($bookings as $tmp) { 
                $booking = new stdClass();
                $booking->id = $tmp->ID;
               
                $user_id= em_get_post_meta($tmp->ID, 'user_id', true);
                $other_order_info= em_get_post_meta($tmp->ID, 'order_info', true);
                $booking->no_tickets= (int) $other_order_info['quantity'];
                $booking->status= $tmp->post_status;
                $booking->order_info= em_get_post_meta($booking->id, 'order_info', true);
                $user= get_user_by('id', $user_id);
                if(!empty($user)){
                    $booking->user_display_name = $user->display_name;
                    $booking->user_email = $user->user_email;
                }
                else
                {
                    $booking->user_email= $other_order_info['user_email'];
                }
                 $this->data->posts[] = $booking;
                
            }
        }
        
        $filter = array(  'numberposts' => -1,'post_status'=>'any', 'order' => 'ASC', 'post_type' => EM_EVENT_POST_TYPE);
        $event_dao = new EventM_Event_DAO();
        $events= $event_dao->get_events($filter);
        
        $this->data->filter_between= event_m_get_param('filter_between');
        
        $tmp_status= EventM_Constants::$status;
        $this->data->status= array();
        $this->data->status[]= array("key"=>"","label"=>"Select Status");
        
        foreach($tmp_status as $key=>$label)
        {
         $tmp= new stdClass();
         $tmp->key= $key;
         $tmp->label= $label;
         $this->data->status[]= $tmp;
        }
 
        $this->data->events= array();
        $this->data->events[]= array(id=>0,title=>"Select Event");
        if(!empty($events))
        {  
            
            foreach($events as $event)
            {   
                $tmp= new stdClass();
                $tmp->id= $event->ID;
                $tmp->title= $event->post_title;
                $this->data->events[]= $tmp;
            }
            
        }
        
        /**
         * Return post count 
         */ 
        // Calculating number of bookings
        $args['offset']=0;
        $args['posts_per_page']=99999;
      
        $tmp = get_posts($args);
        $this->data->total_bookings= range(1,count($tmp)) ;
        $this->data->pagination_limit= EM_PAGINATION_LIMIT;
        $this->data->event=  (int) event_m_get_param('event');

    }
    
    
    protected function loadTranslationStrings() {
        if($this->task=="edit"){
            //$this->data->trans= $this->stringsModel-singleBooking();
        }
        
        if($this->task=="list"){
            $this->data->trans= $this->stringsModel->listBookings();
        }
       
        
    }

      
    
    private function otherPageInfo(){
        if($this->task=="edit"){
            $this->data->links->cancel= admin_url('/admin.php?page=em_bookings');
        }
        
        if($this->task=="list"){
            $this->data->links->edit= admin_url('/admin.php?page=em_booking');
        }
    }
    
    protected function apply_filter($filter,$args)
    {  
        $date_query= array();
        
        switch($filter['filter_between'])
        {
            case 'today':  $today= getdate();
                            $date_query= array(array(
                            'year'  => $today['year'],
                            'month' => $today['mon'],
                            'day'=> $today['mday']
                        ));
                                        break;
            case 'week': $date_query= array(
                                        array(
                                'year' => date( 'Y' ),
                                'week' => strftime( '%U' )
                            )
            ); break;
            case 'month': 
                $today= getdate();
                $date_query= array(
                array(
                    'year' => $today['year'],
                    'month' => $today['mon'],
                ),
             ); break;
            case 'year': $date_query=  array(
                                    array(
                                        'year' => date( 'Y' ),
                                        ),
                                    );
                                    break;
            case 'range': $date_query = array(
                            array(
                                'after'     => $this->data->date_from,
                                'before'    => date('y-m-d',strtotime('+1 day',strtotime($this->data->date_to))),
                                'inclusive' => true
                            )); break;
            default:   $date_query= array();
        }
        
        
        
        $args['date_query']= $date_query;
        
        // Check for event type
        if(!empty($filter['event']))
        {
            $args['meta_query']= array(
                                    array(
                                        'key' => em_append_meta_key('event_id'), // Check the start date field
                                        'value' => $filter['event'], // Set today's date (note the similar format)
                                        'compare' => '=', // Return the ones less than today's date
                                        'type' => 'NUMERIC,'
                                    )
            );
        }
        
        if(!empty($filter['status']))
        {
            $args['post_status']= $filter['status'];
        }
         
        //print_r($args); die;
        return $args;
    }
    
   
    
}




