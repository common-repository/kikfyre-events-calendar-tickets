<?php

/**
 *
 * Service class for Events
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Booking Service class
 */
class EventM_Booking_Service {

    public function book_seat() {
        if (!is_user_logged_in())
            die("Cheating....");
        
      
            
        $response = new stdClass();
        $event_dao = new EventM_Event_DAO();
        $event_id = event_m_get_param('event_id',true);
        $venue = $event_dao->get_venue($event_id);
        $booked_seats= (int) em_get_post_meta($event_id, 'booked_seats', true);
        // check if seat is bookable
        if(!em_check_expired($event_id))
        {
            
            $error = new WP_Error('em_error_booking_expired', 'Booking expired.');
            echo  json_encode($error);
            die;
        }
        
        $available_seats= $event_dao->available_seats($event_id);
        if($available_seats<=0)
        {
            $error = new WP_Error('em_error_booking_finished', 'All the seats are booked.');
            echo  json_encode($error);
            die;
        }
        $event_service= new EventM_Service();
        $type = em_get_term_meta($venue->term_id,'type',true);
     
        $order_info= array('discount_per'=>0,'discount'=>0);
        
      //  $response['type']=$type;
        $order_info['quantity']= event_m_get_param('quantity',true);
        $order_info['item_price']= event_m_get_param('single_price',true);
         $global_settings = new EventM_Global_Settings_Model(); 
           $currency_symbol="";                    
        $currency_code= $global_settings->currency;
        if($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];                        
        else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
        endif;
        $order_info['currency']= $currency_symbol;
        $allow_discount = em_get_post_meta($event_id,'allow_discount',true);
        $discount_no_tickets = em_get_post_meta($event_id,'discount_no_tickets',true);
   
        if($order_info['quantity']>=$discount_no_tickets):
                if($allow_discount==1):
                    $order_info['discount_per']= event_m_get_param('discount_per',true);
                    $total_price = $order_info['quantity'] * $order_info['item_price'];
                    $discount = ($total_price*$order_info['discount_per'])/100;
                    $order_info['discount'] = $discount;
                endif;
            endif;
                $response->discount= $order_info['discount'];
       
        $user = wp_get_current_user();
        
        if ($type == "seats") {
            
            $order_info['seat_sequences'] = event_m_get_param("seat_sequences",true);
            $order_info['seat_pos'] = event_m_get_param("seat_pos",true);
           
            
            if (!$this->check_booking_availability($event_id,$order_info)) {
                $error = new WP_Error('error_seat_conflict', EventM_UI_Strings::get("ERROR_SEAT_CONFLICT"));
                echo json_encode($error);
                die;
            }
           
            if (is_user_logged_in()) {
                $seats = event_m_get_param("seats",true);
                $order_other_info['seats']= $seats;
                $event_dao = new EventM_Event_DAO();
                
                
                $order_title = "Order " . date("Y-m-d H:i:s");
                $order = array(
                    'post_title' => $order_title,
                    'post_status' => 'em-pending',
                    'post_type' => EM_BOOKING_POST_TYPE,
                );

                $order_id = wp_insert_post($order);
                
                if (!is_wp_error($order_id)) {
                    $event_service->update_booked_seats($event_id, $order_info['quantity'] + $booked_seats);
                    em_update_post_meta($order_id, 'user_id', $user->ID);
                    em_update_post_meta($order_id, 'booking_tmp_status', 1);
                    em_update_post_meta($order_id, 'booking_timestamp', current_time( 'timestamp' ));
                    em_update_post_meta($order_id, 'event_id', $event_id);                    
                    $order_info['user_email']= $user->user_email;
                    em_update_post_meta($order_id,'order_info',$order_info);
                    em_update_post_meta($event_id, 'seats', $seats);
                    $response->order_id = $order_id;
                    return $response;
                } else {
                    $error = new WP_Error('error_seat_conflict', EventM_UI_Strings::get("ERROR_SEAT_CONFLICT"));
                    return $error;
                }
            }
        }
        else
        {
            $event_dao = new EventM_Event_DAO();
            if (!$this->check_booking_availability($event_id,$order_info)) {
                $error = new WP_Error('error_capacity', EventM_UI_Strings::get("ERROR_CAPACITY"));
                echo json_encode($error);
                die;
            }

            $order_title = "Order " . date("Y-m-d H:i:s");
            $order = array(
               'post_title' => $order_title,
               'post_status' => 'em-pending',
               'post_type' => EM_BOOKING_POST_TYPE,
            );
            $order_id = wp_insert_post($order);
             $event_service->update_booked_seats($event_id, $order_info['quantity'] + $booked_seats);
            if (!is_wp_error($order_id)) {
                em_update_post_meta($order_id, 'user_id', $user->ID);
                em_update_post_meta($order_id, 'booking_tmp_status', 1);
                em_update_post_meta($order_id, 'booking_timestamp', current_time( 'timestamp' ));
                em_update_post_meta($order_id, 'event_id', $event_id);
                $order_info['user_email']= $user->user_email;
                em_update_post_meta($order_id,'order_info',$order_info);
                

                $user = wp_get_current_user();
                em_update_post_meta($order_id, 'user_id', $user->ID);
                $response->order_id = $order_id;
                return $response;
            } else {
                $error = new WP_Error('order_error', "Error in creating new order");
                return $error;
            }
            
        }

        die;
    }

    public function check_booking_availability($event_id,$order_info) { 
        $event_service= new EventM_Service();
      
        if(!empty($order_info['seat_pos']))
        {  
           $seats = em_get_post_meta($event_id, 'seats', true);
             
            // Venue does not have any seats
            if (empty($seats) || empty($seats[0])) {
                return false;
            }

            if (!empty($order_info['seat_pos'])) {
                foreach ($order_info['seat_pos'] as $pos) {
                    $positions = explode('-', $pos);
                    $row = $positions[0];
                    $col = $positions[1];
                    
                    if (isset($seats[$row][$col])) {
                        $seat = $seats[$row][$col];
                        if ($seat->type != 'general') {
                            return false;
                        }
                    }
                }
            } 
        }
        else
        {  
           $total_capacity= (int) em_get_post_meta($event_id,'seating_capacity',true);
           
           // Check if capcity is not given
           if($total_capacity==0)
               return true;
          
           $available= $event_service->available_seats($event_id);  
          
           if($available< (int) $order_info['quantity'])
           {
               return false;
           } 
        }
        
        return true;
    }
    
    private function get_child_id($id){
        
            $list= em_get_post_meta($id, 'child_events', true);          
            return $list;            
        }
    public function remove_tmp_bookings($user_id) {
        
        $booking_dao = new EventM_Booking_DAO();
        $event_service= new EventM_Service();
        $bookings = $booking_dao->get_tmp_bookings($user_id);
        if (!empty($bookings)) {
            foreach ($bookings as $booking) {
                $order_info = em_get_post_meta($booking->ID, 'order_info', true);
                $booking= $this->get_event_by_booking($booking->ID);
                if(!empty($order_info['seat_pos']))
                {
                    $seat_pos= $order_info['seat_pos'];
                    // Reverting Seat status as it was before booking
                    $this->update_seat_status($booking->event_id, $seat_pos, 'tmp', 'general');
                    // Updating booked seats
                    $booked_seats= (int) $event_service->booked_seats($booking->event_id);
                    if($booked_seats>0)
                        $event_service->update_booked_seats($booking->event_id, $booked_seats- (int)$order_info['quantity']);

                }else{
                    // Standing event order
                     $booked_seats= (int) $event_service->booked_seats($booking->event_id);
                     if($booked_seats>0)
                        $event_service->update_booked_seats($booking->event_id, $booked_seats- (int)$order_info['quantity']);
                }
                
                wp_delete_post($booking->ID);
            }
        }
    }

       
    private function update_seat_status($event_id, $seat_pos, $type_from, $type_to) {
        $seats = em_get_post_meta($event_id, 'seats', true);
        if (!empty($seats) && !empty($seats[0])) {
            if(!empty($seat_pos)){
            foreach ($seat_pos as $pos) {
                $positions = explode('-', $pos);
                $row = $positions[0];
                $col = $positions[1];

                if (isset($seats[$row][$col])) {
                    $seat = $seats[$row][$col];
                    if ($seat->type == $type_from) {
                        $seat->type = $type_to;
                    }
                }
            }
            em_update_post_meta($event_id, 'seats', $seats);
            }
        }
    }

    public function remove_all_tmp_bookings() {
        $booking_dao = new EventM_Booking_DAO();
        $event_dao= new EventM_Event_DAO();
        $bookings = $booking_dao->get_all_tmp_bookings();
        $event_service= new EventM_Service();
        
        if (!empty($bookings)) {
            foreach ($bookings as $booking) {
                $order_info = em_get_post_meta($booking->ID, 'order_info', true); 
                $event_id = em_get_post_meta($booking->ID, 'event_id', true);
                if(isset($order_info['seat_pos']))
                {
                    $seat_pos= $order_info['seat_pos'];                  
                    $this->update_seat_status($event_id, $seat_pos, 'tmp', 'general'); // Reverting Seat status as it was before booking
                }
                                  
                $booking_tmp_status= (int) em_get_post_meta($booking->ID, 'booking_tmp_status',true);
                // Updating booked seats
                $event_service->update_booked_seats($event_id, $event_dao->booked_seats($event_id)-$order_info['quantity']);
                
                if($booking_tmp_status==1)
                    wp_delete_post($booking->ID);
            }
        }
    }
    
    
    /*
     * Called after booking confirmation from payment gateway.
     */
    public function update_booking($order_id,$data= array()){
        $order= get_post($order_id);
        if(empty($order))
            return;
        
        $user= wp_get_current_user();
        if(empty($user))
            die("User does not exists");
        
        em_update_post_meta($order_id, 'booking_tmp_status', 0);
        $event_id= em_get_post_meta($order_id, 'event_id',true);
        $order_info= em_get_post_meta($order_id,'order_info',true);

        $em_booked_seats= (int) em_get_post_meta($event_id, 'booked_seats', true);

        if(!empty($event_id) && !empty($order_info['seat_pos']))
        {
           $this->update_seat_status($event_id, $order_info['seat_pos'], 'tmp', 'sold');
        }

        $order_info= em_get_post_meta($order_id,'order_info',true);
        $order_info['payment_gateway']= $data['payment_gateway'];
        if(!empty($user->user_email))
          $order_info['user_email']= $user->user_email;
        em_update_post_meta($order_id, 'order_info', $order_info);
        
        // Update payment log
        em_update_post_meta($order_id, 'payment_log', $data);

        $order = array('ID' => $order_id);
        if (strtolower($data['payment_status']) == "completed") {
            $order['post_status'] = 'publish';
            EventM_Notification_Service::booking_confirmed($order_id);
        } else if (strtolower($data['payment_status']) == "refunded") {
            $order['post_status'] = 'em-refunded';
            EventM_Notification_Service::booking_refund($order_id);
        } else {
            EventM_Notification_Service::booking_pending($order_id);
        }

        wp_update_post($order);
        
        
    }
    
    public function refund_booking($order_id)
    {
        $response= new stdClass();
        $response->msg= EventM_UI_Strings::get("MSG_REFUND_ERROR");
        $refund_status= false;
        
        $order= get_post($order_id);
        $order_info= em_get_post_meta($order_id,'order_info',true);
        if($order_info['payment_gateway']=="paypal")
        {
            $payment_service= new EventM_Paypal_Service();
            $payment_response= $payment_service->refund($order_id);
            if(isset($payment_response['ACK']) && $payment_response['ACK']=="Success")
            {
                $refund_status= true;
            }
        }
        elseif($order_info['payment_gateway']== "offline")
        {
            
            $data= array('order_id'=>$order_id,'refund_status'=>$refund_status);
            $data= apply_filters('em_refund_booking_offline',$data);
        }
         else{
            
            $data= array('order_id'=>$order_id,'refund_status'=>$refund_status);
            $data= apply_filters('em_refund_booking',$data); 
        }
            
      
        
        if($data['refund_status'])
        {
            // Changing booking status
            $order = array(
              'ID'           => $order_id,  
              'post_status' => 'em-refunded',
            );
            $order_id = wp_update_post($order);
            $response->msg= EventM_UI_Strings::get("MSG_REFUND_SUCCESS");
            
           // $this->revoke_seats($order_id);
            EventM_Notification_Service::booking_refund($order_id);
        }
       
        
        return $response;
        
    }
    
    public function revoke_seats($order_id,$from='sold',$target='general')
    {     
         $event_service= new EventM_Service(); 
         $order_info= em_get_post_meta($order_id,'order_info',true);
         $event_id= em_get_post_meta($order_id, 'event_id',true);
         $em_booked_seats= (int) em_get_post_meta($event_id, 'booked_seats', true);
         $event_service->update_booked_seats($event_id, $em_booked_seats-$order_info['quantity']);
          
        if(!empty($event_id) && !empty($order_info['seat_pos']))
        { 
           $this->update_seat_status($event_id, $order_info['seat_pos'], $from, $target);
        }
    }
    public function get_seats($event_ID){
        
        $args = array(
                    'post_type'=> EM_BOOKING_POST_TYPE,
                    'meta_query'	=>	array(
                            array(
                            'key'     => 'em_event_id',
                            'value'   => $event_ID,
                            'compare' => '='
                            )
                    ));        
                    $my_query =  get_posts( $args );                  
                    return $my_query;
    }
    
    public function get_bookings_by_user($user_id)
    {
        $dao= new EventM_Booking_DAO();
        $bookings= $dao->get_bookings_by_user($user_id);
        return $bookings;
    }
    
     public function get_event_by_booking($order_id)
     {
        
        $dao= new EventM_Booking_DAO();
        $events= $dao->get_event_booking($order_id);
        return $events;
         
    }
    
    public function get_final_price($order_id)
    {
        $event_dao = new EventM_Event_DAO();
        $ticket_price=0;
        $order_info= $event_dao->get_meta($order_id,'order_info');        
        $after_discount_price= ($order_info['item_price']*$order_info['quantity'])- $order_info['discount'];
        return  $after_discount_price;
    }
    
    public function get_single_price($order_id)
    {
        $event_dao = new EventM_Event_DAO();
        $order_info= $event_dao->get_meta($order_id,'order_info');
        return  $order_info['item_price'];
    }
    
    public function get_price_for_print($order_id)
    {  
        return $this->get_single_price($order_id);
    }
    
    
    
    
    

}
