<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class EventM_Booking_DAO {

    public function get_tmp_bookings($user_id){ 
        
         $filter = array(
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status'=> 'em-pending',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => em_append_meta_key('user_id'), 
                    'value' => $user_id, 
                    'compare' => '=', 
                    'type' => 'NUMERIC,'
                ),
                array(
                    'key' => em_append_meta_key('booking_tmp_status'), 
                    'value' => 1, 
                    'compare' => '=', 
                    'type' => 'NUMERIC,' 
                ),
            ),
            'post_type' => EM_BOOKING_POST_TYPE);
         
        $bookings= get_posts($filter);
        
        return $bookings;
        
    }
    
    public function get_all_tmp_bookings(){
         $filter = array(
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status'=> 'em-pending',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => em_append_meta_key('booking_timestamp'), 
                    'value' => current_time( 'timestamp' )-240, 
                    'compare' => '<=', 
                    'type' => 'NUMERIC,'
                ),
                array(
                    'key' => em_append_meta_key('booking_tmp_status'), 
                    'value' => 1, 
                    'compare' => '=', 
                    'type' => 'NUMERIC,' 
                )
            ),
            'post_type' => EM_BOOKING_POST_TYPE);
         
        $bookings= get_posts($filter);
       
        return $bookings;
        
    }
    
    public function edit_booking_info()
    {
        /*
         * Getting all the data from POST request
         */
        $booking_cons= EventM_Constants::get_booking_cons();
       
        $model = datafromRequest($booking_cons['allowed']);
       
        $meta = $booking_cons['meta'];
        $post_id= event_m_get_param('post_id');
        
        // Adding Order notes
        $notes= em_get_post_meta($post_id, 'notes', true);
        $order_info= em_get_post_meta($post_id,'order_info',true);
        if(empty($notes))
        {
            $notes= array();
        }
        if(!empty($model->note)){
            $notes[]= $model->note;
            em_update_post_meta($post_id, 'notes', $notes);
        }
        
        // If Offline payment
        if($order_info['payment_gateway']=="offline"){
           
            $charge_status = $model->offline_status;
            $data = array('post_id'=> $post_id ,'offline_status' => $charge_status );
            do_action('em_edit_offline_status', $data);            
        }

        $response = new stdClass();
        $response->error_status = false;
        echo json_encode($response);
        wp_die();
    }
    
    public function get_bookings_by_user($user_id)
    {
        $filter = array(
            'numberposts'=>-1,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status'=> 'any',
            'meta_query' => array(
                array(
                    'key' => em_append_meta_key('user_id'), 
                    'value' => $user_id, 
                    'compare' => '=', 
                    'type' => 'NUMERIC,'
                )
            ),
        'post_type' => EM_BOOKING_POST_TYPE);
        
        $bookings= get_posts($filter);
        return $bookings;
    }
    
    public function get_completed_bookings($filter_query= array())
    {
        $date_query= array();
        $meta_query= array();
        if(!empty($filter_query['date_query']))
        {
            $date_query= $filter_query['date_query'];
        }
        
        if(!empty($filter_query['meta_query']))
        {
            $meta_query= $filter_query['meta_query'];
        }
        
        if(!empty($filter_query['tax_query']))
        {
            $tax_query= $filter_query['tax_query'];
        }
        
        $filter = array(
            'orderby' => 'date',
            'order' => 'ASC',
            'numberposts'=>-1,
            'post_status'=> 'publish',
            'date_query'=>$date_query,
            'meta_query'=>$meta_query,
            'tax_query'=>$tax_query,
        'post_type' => EM_BOOKING_POST_TYPE);
        
        $bookings= get_posts($filter);
        return $bookings;
    }
    
  public function get_event_booking($booking_id){  
        $data = new stdClass();
        $global_settings = new EventM_Global_Settings_Model(); 
       
        
        $booking= get_post($booking_id);
            if(empty($booking)){
                echo "No such booking exists for Order ID #".$booking_id;
             return;   
            }
   
        $event= em_get_post_meta($booking_id,'event_id',true);   
        $event_service= new EventM_Service();
        $venue_id= $event_service->get_venue($event);
        if(!empty($event)):
             
                $terms = wp_get_post_terms($event, EM_VENUE_TYPE_TAX);
           
           	$data->ID = $booking_id;
 		$posts = get_post($event, EM_EVENT_POST_TYPE);
                $start_date =  em_showDateTime(em_get_post_meta($event, 'start_date', true), true);
                $data->event_id=$event;
                $data->event_date=$start_date;
                $data->event_name = $posts->post_title;
                $data->description=$posts->post_content;
               	  
               	   
                $venue = $terms[0];       
                  
                $data->venue_id=$venue->term_id;
      
                $data->address = em_get_term_meta($venue->term_id, 'address', true); 
                $data->venue_name=$venue->name;
                if(!empty($data->address)):
                  $data->address=  $data->address;
                else:
                     $data->address= '';
                endif;
                
                $data->type = em_get_term_meta($venue->term_id, 'type', true); 
                $event_dao = new EventM_Event_DAO();
                $booking_service = new EventM_Booking_Service();
                $order_info= $event_dao->get_meta($booking_id,'order_info');      

                       
                $currency_symbol= $order_info['currency'];
                $payment_log= maybe_unserialize(em_get_post_meta($booking_id, 'payment_log', true));   
                if(isset($currency_symbol) && !empty($currency_symbol)):
                          $currency_symbol= $currency_symbol;
                elseif(isset($payment_log['payment_gateway']) && ($payment_log['payment_gateway'] == 'paypal' )):
                             $currency_symbol = $payment_log['mc_currency'];
                else:   
                endif;   
                
                
                
                
                $data->item_price = $order_info['item_price'];
                $data->order_info=  $order_info;
                $data->total_price=   $booking_service->get_final_price($booking->ID);  
                if(!$data->item_price){
                      $data->item_price = "Free";
                      $data->total_price= "Free";
                }
                else{
                     $data->item_price = $order_info['item_price'].$currency_symbol;   
                     $data->total_price= $data->total_price.$currency_symbol;
 
                }
             endif;
            
             $data->discount = $order_info['discount'].$currency_symbol;
             
         if($data->type=='seats')
        {            
            if(($order_info['seat_sequences'])>0)
            {                  

                $data->seat_sequence = implode(',',$order_info['seat_sequences']); 

            }

        } 
        
      
     return $data;
    }
    
}
