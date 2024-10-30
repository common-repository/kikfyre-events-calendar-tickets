<?php
            
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 *
 * AJAX Event Handler.
 *
 */
class EventM_AJAX {
    
    protected $request;
    
    public function __construct() {
        $this->request= new EventM_Raw_Request();
        $this->hider_errors();
        // Hook in ajax handlers
        $this->add_ajax_events();
    }

    /**
     * Turning off error configuration
     */
    public function hider_errors() {
        // Turn off display_errors during AJAX events to prevent malformed JSON
       // if (!WP_DEBUG || ( WP_DEBUG && !WP_DEBUG_DISPLAY )) {
            @ini_set( 'display_errors', 0 );
       // }
    }
    
    
    
    /**
     * Hook in methods - uses WordPress ajax handlers (admin-ajax).
     */
    public function add_ajax_events() {

        add_action('wp_ajax_em_load_strings', array($this, 'load_data'));

        $ajax_events = array('save_venue' => false,
                        'save_performer' => false,
                        'save_event_type'=> false,
                        'save_event_ticket'=> false,
                        'save_event'=>false,
                        'delete_posts'=>false,
                        'duplicate_posts'=>false,
                        'delete_terms'=>false,
                        'duplicate_terms'=>false,
                        'save_global_settings'=>false,
                        'save_booking'=>false,
                        'load_chart'=>false,
                        'cancel_booking'=>false,
                        'edit_child_event'=> false,
                        'load_event_dates'=>true,
                        'load_venue_addresses'=>true,
                        'register_user'=>true,
                        'check_bookable'=>false,
                        'login_user'=>true,
                        'book_seat'=>true,
                        'load_event_seats'=>true,
                        'load_payment_configuration'=>true,
                        'update_booking'=>true,
                        'print_ticket'=>true,
                        'export_bookings'=>false,
                        'event_details'=>true,
                        'show_booking_details'=>true,
                        'cancel_booking_by_user'=>true,
                        'get_venue_capcity'=>true,
                        'verify_booking'=>true,
                        'download_booking_details'=>true,
                        'confirm_booking_without_payment'=> true,
                        'rm_custom_datas'=>true,
                        'delete_all_child_events'=>false,
                        'resend_mail' =>true,
                        'booking_cancellation_mail'=>true,
                        'booking_confirm_mail' => true,
                        'booking_refund_mail' =>true,
                        'booking_pending_mail' =>true,
                        'load_event_for_booking'=>true,
                        'delete_child_events'=> true,
                        'load_event_children'=>true,
                        'delete_order'=> true,
                        'add_event_tour_completed' => false,
                        'event_tour_completed' => false
            );

        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_em_' . $ajax_event, array($this, $ajax_event));
            if ($nopriv) {
                add_action('wp_ajax_nopriv_em_' . $ajax_event, array($this, $ajax_event));
            }
        }
    }

    public function load_data() {
       $context = event_m_get_param('em_request_context');
       $model=null; 
        switch ($context) {
            case 'admin_venue': $service= new EventM_Venue_Service(); 
                                $service->load_edit_page();
                                break;
            case 'admin_venues': $service= new EventM_Venue_Service();
                                 $service->load_list_page();
                                 break;
            case 'admin_event': $service= new EventM_Service();
                                $service->load_edit_page();
                                break;
             case 'admin_events': $service= new EventM_Service();
                                  $service->load_list_page();
                                  break;
             case 'admin_performer': $service= new EventM_Performer_Service(); 
                                     $service->load_edit_page();
                                     break;
             case 'admin_performers': $service= new EventM_Performer_Service();
                                      $service->load_list_page(); 
                                      break;
            case 'admin_event_type':   $service= new EventTypeM_Service(); 
                                       $service->load_edit_page();
                                       break;
             case 'admin_event_types': $service= new EventTypeM_Service();
                                       $service->load_list_page(); 
                                       break;   
             case 'admin_ticket_template': $service= new EventM_Ticket_Service();
                                           $service->load_edit_page(); 
                                           break;
             case 'admin_ticket_templates': $service= new EventM_Ticket_Service();
                                            $service->load_list_page(); 
                                            break;
             case 'admin_global_settings': $service= new EventM_Setting_Service();
                                           $service->load_edit_page(); 
                                           break;  
                
             case 'admin_bookings': $model= new EventM_Booking_Model("list");
                    break;  
            case 'admin_booking': $model= new EventM_Booking_Model("edit"); 
                break;
            case 'admin_analytics': $service= new EventM_Analytics_Service();
                                    $service->load_options();
                                    break;
        }
        if($model!=null):
            echo json_encode($model->get_data());
        else:
           // echo 'No Model found';
        endif;
        
        wp_die();
    }

    function save_venue() {
        $service=  new EventM_Venue_Service(); 
        $venue_consts= EventM_Constants::get_venue_cons();
        $model = datafromRequest(array_merge($venue_consts['meta'],$venue_consts['core']));
        $model->id= event_m_get_param('id');

         // Validate data
        $response= $service->validate($model, $response);
        if($response->error_status)
        {
            echo json_encode($response);
            wp_die();
        }
        
         
        $venue= $service->save($model);
       
        $response= new stdClass();
        $response->error_status = false;
        if($venue instanceof WP_Error){
                echo wp_send_json_error($venue);
                wp_die();
        } 
        $response->redirect= admin_url('admin.php/?page=em_venues');
        echo json_encode($response);
        wp_die();
    }
    
    function save_performer() {
          $performer_consts= EventM_Constants::get_performer_cons();
          $model = datafromRequest(array_merge($performer_consts['core'],$performer_consts['meta']));
          $model->id= event_m_get_param('id');  
          $service=  new EventM_Performer_Service();    
          $performer= $service->save($model); 
          $response= new stdClass();
          
          if($performer instanceof WP_Error){
                echo wp_send_json_error($performer);
                wp_die();
          } 
          $response->redirect= admin_url('/admin.php?page=em_performers');
          echo json_encode($response);
          wp_die(); 
    }
    
    function save_event_type(){
        $event_type_consts = EventM_Constants::get_type_cons();
        $model = datafromRequest(array_merge($event_type_consts['core'], $event_type_consts['meta']));
        $model->id= event_m_get_param('id');
        $service=  new EventTypeM_Service(); 
        
         // Validate data
        $response= $service->validate($model, $response);
        if($response->error_status)
        {
            echo json_encode($response);
            wp_die();
        }
        
        
        $event_type= $service->save($model);
         if($event_type instanceof WP_Error){
                echo wp_send_json_error($event_type);
                wp_die();
          } 
        $response->redirect= admin_url('admin.php/?page=em_event_types');
        echo json_encode($response);
        wp_die();   
    }
    
    function save_event_ticket(){
         $service=  new EventM_Ticket_Service();
         $service->save();
    }
    
    function save_event(){
      
        $service=  new EventM_Service();
          // Response object 
        $response = new stdClass();
        $response->error_status = false;
        $response->errors= array();
        $response->redirect = admin_url('/admin.php?page=event_magic');
        
        $event_consts = EventM_Constants::get_event_cons();
        $model = datafromRequest(array_merge($event_consts['core'], $event_consts['meta']));
        $model->id= event_m_get_param('id');
      
         // Validate data
        $response= $service->validate($model, $response);
        if($response->error_status)
        {
            echo json_encode($response);
            wp_die();
        }
        
        $event= $service->save($model);
          
        if($event instanceof WP_Error){
            echo wp_send_json_error($event);
            wp_die();
        } 
        $response->redirect= admin_url('/admin.php?page=event_magic');
        echo json_encode($response);
        wp_die();
    
    }

    function delete_posts(){
        EventM_Utility::delete_posts($this->request->get_param('ids'));
    }
    
    function delete_terms(){
        EventM_Utility::delete_terms($this->request->get_param('ids'),$this->request->get_param('tax_type'));
        $response = new stdClass();
        $response->reload = true;
        echo json_encode($response);
        wp_die();
    }
    
    function duplicate_posts(){
        EventM_Utility::duplicate_posts($this->request->get_param('ids'));
    }
    
    function duplicate_terms(){
        em_duplicate_terms(event_m_get_param('ids'),event_m_get_param('tax_type'));
    }
    
    function save_global_settings(){
        // Response object 
        $response = new stdClass();
        $response->error_status = false;
        $response->redirect= admin_url('/admin.php?page=event_magic');
        
        $request= new EventM_Raw_Request();
        $model= $request->map_request_to_model('EventM_Global_Settings_Model');
        $service=  new EventM_Setting_Service();
        $service->save($model);
        
        do_action('em_after_gs_save');
        echo json_encode($response);
        wp_die();
    }
    
    function save_booking()
    {
        $dao=  new EventM_Booking_DAO();
        $dao->edit_booking_info();
    }
    
    function cancel_booking()
    {
        $service= new EventM_Booking_Service();
        $post_id= event_m_get_param('post_id', true); 
        
        $response= $service->refund_booking($post_id);        
        echo json_encode($response);
        die;
    }
        
    
    function load_event_dates(){ 
      // Get all the events dates
      $data= new stdClass();
      $data->start_dates= array();
      $data->event_ids= array();
      
      $event_dao= new EventM_Event_DAO();
      $events= $event_dao->get_upcoming_events_calendar();
    
      $event_service= new EventM_Service();
      
      if(is_array($events)):
          foreach($events as $event):
              $start_date=date('Y-m-d',em_get_post_meta($event->ID, 'start_date', true));
              $end_date= date('Y-m-d',em_get_post_meta($event->ID, 'end_date', true)); 
             
              if(!empty($start_date)): 
                  preg_match( '/[0-9]{4}\-[0-9]{1,2}-[0-9]{1,2}/', $start_date, $matches);
                  if(count($matches)>0 && !empty($matches[0])){
                          
                          if(strtotime($matches[0])<= strtotime(date('Y-m-d')) &&
                                  strtotime($end_date)>= strtotime(date('Y-m-d'))):
                              $data->start_dates[]= date("Y-m-d");
                              
                          else:
                              $data->start_dates[]= $matches[0];
                          endif;
                          
                          $data->event_ids[]= $event->ID;
                  }
                    
              endif;
          endforeach;
      endif;
      
      $recurring_events= $event_service->get_recurring_event_dates_for_calendar();
      foreach($recurring_events->event_dates as $index=>$value)
      {
          $data->start_dates[]= $recurring_events->event_dates[$index];
          $data->event_ids[]= $recurring_events->ids[$index];        
      }
     // Removing duplicate enteries
     //  $data->start_dates= array_values(array_unique($data->start_dates));
     //$data->event_ids= array_values(array_unique($data->event_ids));  

      echo json_encode($data);
      die;
    }
    
    function load_venue_addresses(){
               
        $venue_service= new EventM_Venue_Service();
        $venues= $venue_service->venue_addresses_for_marker();
        echo json_encode($venues);
        die;
    }
    
    function register_user(){ 
        $user_service= new EventM_User_Service();
        $user_service->register_user();
    }
    
    function login_user(){ 
        $user_service= new EventM_User_Service();
        $user_service->login_user();
            
    }
    
    function book_seat()
    {
        $book_service= new EventM_Booking_Service();
        $data= $book_service->book_seat();
        echo json_encode($data);
        die;
    }
    
    public function load_payment_configuration()
    {        
        $response= new stdClass();
        $response->payment_prcoessor= array();
        $request= new EventM_Raw_Request();
        $response->is_payment_configured= true;
        
        $setting_service = new EventM_Setting_Service();
        $setting= $setting_service->load_model_from_db();
              
        $event_id = $request->get_param('event_id');
        $response->event_id=$event_id;
        $ticket_price = (int) em_get_post_meta($event_id, 'ticket_price', true);
        $response->ticket_price = $ticket_price;
        $currency_code= $setting->currency;

        if($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];                        
        else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
        endif;
        $response->currency_symbol= $currency_symbol;
        
        $paypal_processor=  $setting->paypal_processor;
        if($paypal_processor==1)
        {
            $paypal_email= $setting->paypal_email;
            if(!empty($paypal_email)){
                  $response->payment_prcoessor['paypal']= array();
             } 
        }
        
        $response = apply_filters( 'em_load_payment_configuration', $response );
        if(!count($response->payment_prcoessor))
            $response->is_payment_configured= false;
        echo json_encode($response);
        die;
    }
    
    public function update_booking()
    {  
        $response= new stdClass();
        $booking_service= new EventM_Booking_Service();
        $event_service= new EventM_Service();
        $order_id= event_m_get_param("order_id",true);
        $event_id= event_m_get_param("event_id",true);
        if(empty($order_id)){ 
            $response->updated= false;
        }
        
        $order_info= em_get_post_meta($order_id, 'order_info', true);

        if(!empty($order_info))
        { 
           $order_info['quantity']= event_m_get_param('quantity');
           $order_info['discount']= event_m_get_param('discount');
       
           // Removing previous quantity data
            $prev_order_info= em_get_post_meta($order_id,'order_info',true);
            $pre_booked_seats= (int) em_get_post_meta($event_id, 'booked_seats', true);
            $event_service->update_booked_seats($event_id, $pre_booked_seats-$prev_order_info['quantity']);
            
          
           if(!$booking_service->check_booking_availability($event_id,$order_info))
           {
               // Removing quantity from order info as well
               $order_info['quantity']=0;
               em_update_post_meta($order_id,'order_info',$order_info);
               
               $error = new WP_Error('error_capacity', EventM_UI_Strings::get("ERROR_CAPACITY"));
               echo json_encode($error);
               die;
           }
           $current_booked_seats= $pre_booked_seats-$prev_order_info['quantity'];
           $event_service->update_booked_seats($event_id, $current_booked_seats+$order_info['quantity']);
            
            
           em_update_post_meta($order_id,'order_info',$order_info);
             $response->updated= true;
        }
    
        echo json_encode($response);
        die;  
        
    }
    
    public function print_ticket()
    {   
        $booking_id= event_m_get_param('booking_id'); 
        $seat_no = event_m_get_param('seat_no');
        $booking= get_post($booking_id);     
//        if(empty($booking))
//            die("No Booking with such ID");
//        
        if(isset($seat_no)){
            $ticket_html = EventM_Print::front_ticket($booking,'',$seat_no);
        }else{
            $ticket_html = EventM_Print::front_ticket($booking,'','');
        }
        echo $ticket_html;
        //EventM_Print::ticket($booking,'',$seat_no);
        die;
    }
    
    public function download_booking_details(){
        $booking_id= event_m_get_param('booking_id');
        $booking= get_post($booking_id);    
       
        EventM_DownloadBooking::Booking_Details($booking);
        die;
    }
    
    public function event_details(){
         $response= new stdClass();
        $event_id= event_m_get_param('element_id');
       // echo $event_id;
        $id= get_post($event_id);
        $response->title=$id->post_title;
//        print_r($id->ID);
//          print_r($id->post_title);

        $terms = wp_get_post_terms($id->ID, EM_VENUE_TYPE_TAX);
        if (!empty($terms) && count($terms) > 0):
                $venue = $terms[0];       
                $venue_address = em_get_term_meta($venue->term_id, 'address', true); 
                $response->address = $venue_address;
        endif;
        
            $booking_seats = new EventM_Booking_Service();
            $my_querys=$booking_seats->get_seats($event_id);                    
                  
                    foreach($my_querys as $data):
                        $id=$data->ID;
                        $seat=get_post_meta($id,'em_order_info');                    
                        foreach($seat as $data):                          
                        $seat_sequence=$data['seat_sequences'];
                        $s=implode(',',$seat_sequence);
                        $response->seats=$s;
                        endforeach;
                      
                        $dates=get_post($id,'post_date');
                     $response->booking_date=$dates->post_date;
            endforeach;
        
         echo json_encode($response);
        die;
    }
    
    public function show_booking_details()
    {
        if(is_user_logged_in())
        {
            $user= wp_get_current_user();
            $booking_id= event_m_get_param('id');
            $booking= get_post($booking_id);
            if(empty($booking))
                die("No such booking exists");
            
            // Check if this booking belongs to the same user
            $user_id= em_get_post_meta($booking_id, 'user_id','true');
            if($user_id!=$user->ID)
                die("User not authorized");
            
            // Load view file
            include_once('templates/booking_details.php');
        }
        die;
        
    }
    
    public function cancel_booking_by_user()
    {
        $response= new stdClass();
        $response->error= true;
        
        if(is_user_logged_in())
        {
            $user= wp_get_current_user();
            $booking_id= event_m_get_param('post_id',true);
            $booking= get_post($booking_id);
            
            
            if(empty($booking))
                die("No such booking exists");
            
            // Booking can not be refunded.
            if($booking->post_status=='em-cancelled'  || $booking->post_status=='em-refunded')
            {
                 echo json_encode(array());
                 return;
            }
              
            
            // Check if this booking belongs to the same user
            $user_id= em_get_post_meta($booking_id, 'user_id', 'true');
            if($user_id!=$user->ID)
                die("User not authorized");
            
            $service= new EventM_Booking_Service();
            $service->revoke_seats($booking_id);
            
            
            // Changing booking status
            $booking = array(
              'ID'           => $booking_id,  
              'post_status' => 'em-cancelled',
            );
            $booking_id = wp_update_post($booking);
            
            $order_info= em_get_post_meta($booking_id,'order_info',true);
            
            if($order_info['payment_gateway']== "offline"){
            $payment_log= em_get_post_meta($booking_id, 'payment_log', true);
            $payment_log['offline_status'] = 'Cancelled';
            em_update_post_meta($booking_id, 'payment_log', $payment_log);
            }
            
            
            if(!is_wp_error($booking_id))
            {  
                $booking= get_post($booking_id);
                $response->error= false;
                $response->status= EventM_Constants::$status[$booking->post_status];
            }
            
            EventM_Notification_Service::booking_cancel($booking_id);
        }
        
        echo json_encode($response);
        die;
    }
    
    public function get_venue_capcity()
    {
        $response= new stdClass();
        $venue_id= event_m_get_param('venue_id');
        $event_id = event_m_get_param('event_id');
       
        $service= new EventM_Venue_Service();
        $response->capacity= (int) $service->capacity($venue_id); 
        $response->seats= $service->get_seats($venue_id,$event_id);
        
        echo json_encode($response);
        die;
    }
    
    public function verify_booking()
    {
        $booking_id= event_m_get_param('booking_id',true);
        $booking= get_post($booking_id);
        if(empty($booking))
            echo false;
        else
            echo true;
        die;
    }
    
    public function load_chart()
    {
       
        $service= new EventM_Analytics_Service();
        $response= $service->get_chart_data();
        echo json_encode($response);
        die;
    }
    
    public function export_bookings(){ 
      
        $em_booking_id = event_m_get_param('selected_bookings'); 
        $selected_bookings= array();
        if(!empty($em_booking_id))
            $selected_bookings = explode(",",$em_booking_id);
        
         $status_list= array(
                       'publish'=>'Completed',
                       'em-pending'=>'Pending',
                       'em-refunded'=>'Refunded',
                       'em-cancelled'=>'Cancelled'
        );
        $args= (array) json_decode(urldecode(event_m_get_param('post_query')));
       
        if(isset($args['meta_query']))
        {
            $args['meta_query']= array((array) $args['meta_query'][0]);
        }
        
        if(isset($args['date_query']))
        {
            $args['date_query']= array((array) $args['date_query'][0]);
        }

        unset($args['posts_per_page']);

           
        if(!empty($selected_bookings) ){          
            $em_booking_data = array();
            foreach($selected_bookings as $ID):
                 $em_booking_data[] = get_post($ID);       
            endforeach;
            $posts = $em_booking_data;      
        }
        else{
             $posts = get_posts($args);       
        }
        
        
         if (is_array($posts)) {
            
            foreach ($posts as $p) { 
               
                $post = new stdClass();
                $post->id = $p->ID;
               
                $user_id= em_get_post_meta($p->ID, 'user_id', true);
                $user= get_user_by('id', $user_id);
                $post->user_display_name = $user->display_name;
                $post->user_email = $user->user_email;
                $other_order_info= em_get_post_meta($p->ID, 'order_info', true);               
                $post->seat_no =  implode(",",$other_order_info['seat_sequences']);
                $post->price= (int) $other_order_info['item_price'];
                $post->no_tickets= (int) $other_order_info['quantity']; 
                $post->amount_total = (int) $other_order_info['quantity'] * (int) $other_order_info['item_price'];
                $event_id = em_get_post_meta($p->ID, 'event_id', true);
                $event_post = get_post($event_id);           
             $post->event_name= $event_post->post_title;         
             $post->status= $status_list[get_post_status($p->ID)];
            
             if($post->status == 'Completed')
                $post->amount_recieved = (int) $other_order_info['quantity'] * (int) $other_order_info['item_price'];
             else
                 $post->amount_due = (int) $other_order_info['quantity'] * (int) $other_order_info['item_price'];
               $data->posts[] = $post;
            
            }
        }
        
        
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment;filename="export.csv"');
        header('Cache-Control: max-age=0');
        $csv_name = 'em_Bookings' . time() . mt_rand(10, 1000000);
        $csv_path = get_temp_dir() . $csv_name . '.csv';   
        $csv = fopen('php://output', "w");
        
       if (!$csv) {
          return false;
        }
         
        //Add UTF-8 header for proper encoding of the file
       
        fputs($csv, chr(0xEF).chr(0xBB).chr(0xBF) );
        
        $csv_fields=array();

        $csv_fields[] = 'ID';
        $csv_fields[] = 'User Name';
        $csv_fields[] = 'Email';
        $csv_fields[] = 'Seat No.';
        $csv_fields[] = 'Price';
        $csv_fields[] = 'No of Ticket';
        $csv_fields[] = 'Total Amount';
        $csv_fields[] = 'Event Name';
        $csv_fields[] = 'Status';
        $csv_fields[] = 'Amount Recieved';
        $csv_fields[] = 'Amount Due';

         
        fputcsv($csv, $csv_fields);

           foreach ($data->posts as $a) {
              //print_r(array_values((array)$a));
              if (!fputcsv($csv, array_values((array)$a)))
                  return false;
           }

        fclose($csv);
        wp_die();
    }
    
    public function check_bookable()
    { 
        $event_id = event_m_get_param('event_id',true);
        
        if(!em_check_expired($event_id) )
        {
            $error = new WP_Error('em_error_booking_expired', 'Booking expired.');
            echo  json_encode($error);
          
        }
        
        $event_service= new EventM_Service();
        $available_seats= $event_service->available_seats($event_id);
        if($available_seats<=0)
        {
            $error = new WP_Error('em_error_booking_finished', 'All the seats are booked.');
            echo  json_encode($error);
        }
       
        wp_die();
        
    }
    
    public function confirm_booking_without_payment()
    {
        $order_id = event_m_get_param('booking_id',true);
        $order_ids= implode(",",$order_id);
    foreach($order_id as $ids):
        $event_id= em_get_post_meta($ids, 'event_id', true,true);
        $order_info= em_get_post_meta($ids, 'order_info', true);
        $venues = wp_get_object_terms($event_id, EM_EVENT_VENUE_TAX);
        $type= em_get_term_meta($venues[0]->term_id, 'type', true);
        
        $response= new stdClass();
        $order = get_post($ids);
        if (empty($order))
            die("Order does not exists");
        
        // Check availability only when venue is standing
        $booking_service = new EventM_Booking_Service();
        if($type=="standings" && !$booking_service->check_booking_availability($event_id, $order_info))
        {
            $error = new WP_Error('error_capacity', EventM_UI_Strings::get("ERROR_CAPACITY"));
            echo json_encode($error);
            wp_die();
        }
        
        $data= array();
        $data['payment_gateway']= 'none';
        $data['payment_status']= 'completed';

        $booking_service->update_booking($ids,$data);
        $global_options= get_option(EM_GLOBAL_SETTINGS);
           endforeach;
            $response->redirect=   add_query_arg( array('order_idss' => $order_ids ), get_permalink(em_global_settings('profile_page')));


        echo json_encode($response);
        die;
    }
    public function rm_custom_datas(){      
       $post_id = event_m_get_param('post_id');       
       $user_id = em_get_post_meta($post_id, 'user_id', true);      

        if(is_registration_magic_active())
        {    
           $html="";
        
            echo em_rm_custom_data($user_id); 
        }
        else{ 
            $current_user =get_user_by('ID',$user_id )
            ?>
<div class="em-booking-row"><span class="em-booking-label">Name:</span><span class="em-booking-detail"><?php echo $current_user->display_name; ?></span></div>
<div class="em-booking-row"><span class="em-booking-label">Email:</span><span class="em-booking-detail"><?php echo $current_user->user_email; ?></span></div>
<div class="em-booking-row"><span class="em-booking-label">Registered On:</span><span class="em-booking-detail"><?php echo $current_user->user_registered; ?></span></div>
        <?php }
        wp_die();
 
    }
    
    public function resend_mail(){
       $booking_id = event_m_get_param('post_id'); 
         EventM_Notification_Service::reset_password_mail($booking_id);
       

    }
    public function booking_cancellation_mail(){
         $booking_id = event_m_get_param('post_id'); 
           EventM_Notification_Service::booking_cancel($booking_id);
    }
    
    public function booking_confirm_mail(){
        $booking_id = event_m_get_param('post_id'); 
           EventM_Notification_Service::booking_confirmed($booking_id);
    }
    
    public function booking_refund_mail(){
        $booking_id = event_m_get_param('post_id'); 
           EventM_Notification_Service::booking_refund($booking_id);
    }
    
    public function booking_pending_mail(){
        $booking_id = event_m_get_param('post_id'); 
           EventM_Notification_Service::booking_pending($booking_id);
    }
    
    public function load_event_for_booking()
    {
        if(!is_user_logged_in())
            wp_die("User not logged in");
       
        // Event's data
        $event_id= $this->request->get_param('event_id', true);
        $event_service= new EventM_Service();
        $event= $event_service->load_model_from_db($event_id);
        $event_array= $event->to_array();
        $event_array['available_seats']= $event_service->available_seats($event_id);
        $children= $event_service->load_children($event_id);
        
        // Remove seats data in case it's a parent event
        if(!empty($children))
          $event_array['seats']= array();
            
        // Venue's data
        $venue_service= new EventM_Venue_Service();
        $venue= $venue_service->load_model_from_db($event->venue);
        $venue_arr= $venue->to_array();
        $venue_arr['available_seats']= $event_service->available_seats($event_id);
        // Unset seats info as it is already included in event's data
        unset($venue_arr['seats']);
        // Mergin both event and venue data
        $event_array['venue']= $venue_arr;
       
        echo json_encode($event_array);
        wp_die();     
    }

    public function load_event_children()
    {
      if(!is_user_logged_in())
            wp_die("User not logged in");
      
       $currency_symbol="";                    
            $currency_code= em_global_settings('currency');
    
            if($currency_code):
                $all_currency_symbols = EventM_Constants::get_currency_symbol();
                $currency_symbol = $all_currency_symbols[$currency_code];                        
            else:
                $currency_symbol = EM_DEFAULT_CURRENCY;
            endif;
      $response= new stdClass();
      $response->children= array();
        
      $event_id= event_m_get_param('event_id');
      $venue_id= event_m_get_param('venue_id');
      
      $venue_service= new EventM_Venue_Service();
      $event_service= new EventM_Service();
      $events= $event_service->load_children($event_id);
      
      foreach ($events as $event){ 
          $event_model= $event_service->load_model_from_db($event->id);
                $child= new stdClass();
                $child->available_seats= $event_service->available_seats($event->id);
                
                $child->max_per_booking= $event_model->max_tickets_per_person;
                $child->allow_cancellations= $event_model->allow_cancellations;
                if($child->allow_cancellations):
                    $child->allow_discount= $event_model->allow_discount;
                    $child->discount_no_tickets= $event_model->discount_no_tickets;
                endif;
                $child->discount_per= $event_model->discount_per;
                $child->event_id= $event->id;
                $child->name = $event_service->get_child_name($event->id);
                $child->venue_id= $venue_id;
                $venue_model= $venue_service->load_model_from_db($venue_id);
                $child->seating_capacity = (int) em_get_post_meta($event->id, 'seating_capacity', true);
                $child->booked_seats = $event_service->booked_seats($event->id);
                $child->type= $venue_model->type;
                $child->start_date= em_showDateTime(strtotime($event->start_date));
                $child->last_booking_date= strtotime($event->last_booking_date);
                $child->current_date= current_time('timestamp');
                $child->bookable= em_check_expired($event->id);
                $child->price= $event->ticket_price;
                $child->currency_symbol = $currency_symbol;
                
                $response->children[]= $child;
        }
        
        echo json_encode($response);
        wp_die();
    }
    
     function delete_child_events()
    {  
        $event_service= new EventM_Service();
        $id= event_m_get_param('id');    
        $child_id= event_m_get_param('child_id');     
        $event_service->delete_child_events($id,$child_id);
        wp_die(); 
    }
     function event_tour_completed(){    
        $global_options= get_option(EM_GLOBAL_SETTINGS);
        $global_options['event_tour'] = 1;
        update_option(EM_GLOBAL_SETTINGS, $global_options);
        wp_die();
    }
    function add_event_tour_completed(){
        $global_options= get_option(EM_GLOBAL_SETTINGS);
        $global_options['add_event_tour'] = 1;
        update_option(EM_GLOBAL_SETTINGS, $global_options);
        wp_die();
    }
    
    function delete_order()
    {
        $order_id= $this->request->get_param('order_id', true);
        $booking_service= new EventM_Booking_Service();
        $booking_service->revoke_seats($order_id,'tmp','general');
        wp_delete_post($order_id);
        exit(0);
    }
    
    function delete_all_child_events()
    {
        $event_service= new EventM_Service();
        $id= $this->request->get_param('id',true);  
        $event_service= new EventM_Service();
        $event= $event_service->load_model_from_db($id);
        $child_events= $event->get_child_events();
        if(!empty($child_events))
        {
           foreach($child_events as $child)
           {
               $event_service->delete_child_events($id,$child);
           }
        }
        
        wp_die(); 
    }
    
}   


new EventM_AJAX();
