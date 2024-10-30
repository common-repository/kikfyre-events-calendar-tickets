<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EventM_Analytics_Service {
    
    /*
     * Load analytics options on REST
     */
    public function load_options()
    {
        $response= new stdClass();
        $response->venues= array();
        
        /* Load Venues data */
        $venue_service= new EventM_Venue_Service();
        $venues= $venue_service->get_venues();
        
        
        /*Load String*/
        $stringsModel= new EventM_Strings_Model();
        $response->trans= $stringsModel->view_analytics();
        
        $response->venues[]= array(id=>"",name=>"Select Venue");
        $response->venue= "";
        
        
        if(!empty($venues))
        {
          foreach($venues as $venue)
          {
              $tmp= new stdClass();
              $tmp->id= $venue->term_id;
              $tmp->name= $venue->name;
              $response->venues[]= $tmp;
          }
        }
        
        $response->report_by="";
        /* Load Events data */
        $event_Dao= new EventM_Event_DAO();
        $events= $event_Dao->get_events($filter = array('numberposts' => -1,'post_status'=>'any', 'order' => 'ASC', 'post_type' => EM_EVENT_POST_TYPE));
        $response->venues->events= array();
        $response->venues->events[]= array(id=>"",name=>"Select Event");
        $response->venues->event= "";
        
        if(!empty($events))
        {
          foreach($events as $event)
          {
              $tmp= new stdClass();
              $tmp->id= $event->ID;
              $tmp->name= $event->post_title;
              $response->events[]= $tmp;
          }
        }
        
        echo json_encode($response);
        wp_die();
    }
    
    public function get_chart_data()
    {
        $filter= array();
        $filter_type= event_m_get_param("filter_type");
        $report_by= event_m_get_param("report_by");
        $venue_id= (int) event_m_get_param('venue');
        $event_id= (int) event_m_get_param('event');
        $start_date= em_formatDateForSave(event_m_get_param('start_date'));
        $end_date= em_formatDateForSave(event_m_get_param('end_date'));
        
        $search_params= array('filter_type'=>$filter_type,
                        'report_by'=>$report_by,'venue_id'=>$venue_id,'event_id'=>$event_id,
                         'start_date'=>$start_date,'end_date'=>$end_date,'filter_type'=>$filter_type);
        
        $event_service= new EventM_Service();
        $data= array();
        
                
        if($report_by=="today" || $report_by=="yesterday")
        {
            return $this->get_data_by_day($search_params,$filter); 
        }

        if($report_by=="last_week" || $report_by=="this_week")
        {
           return $this->get_data_by_week($search_params,$filter); 
        }
        
        if($report_by=="this_year" || $report_by=="last_year")
        {
            return $this->get_data_by_year($search_params,$filter);
        }

        
        if($report_by=="this_month" || $report_by=="last_month")
        { 
            return $this->get_data_by_month($search_params,$filter);  
        }


        if($report_by=="custom")
        {   
            return $this->get_data_by_custom_dates($search_params,$filter);
        }
        
    }
    
    private function get_data_by_month($search_params,$filter= array())
    {
        $dates= array();
        $booking_data= array();
        $data= array();
        $tmp_array= array();
        $event_dao = new EventM_Event_DAO();
        
        // Fetching last month data
        if($search_params['report_by']=="last_month")
        {
            $today= getdate();
            $filter['date_query'] = array(
                 array(
                   'year' => date('Y', strtotime('first day of last month')),
                   'month' => date('m', strtotime('first day of last month'))
                     
                )
             );
            // for each day in the month
            $last_month_first_day=strtotime('first day of last month');
            $no_of_days=date('t',$last_month_first_day);
            $date_value=$last_month_first_day;
            for($i=0;$i<$no_of_days;$i++)
            {
                $dates[]= date('Y-m-d',$date_value);
                $date_value=strtotime("+1 day",$date_value);
            }
        }
        
        // Fetching this month data
        if($search_params['report_by']=="this_month")
        {
            $today= getdate();
            $filter['date_query'] = array(
                array(
                    'year' => $today['year'],
                    'month' => $today['mon']
                )
             );
            
            // for each day in the month
            for($i = 1; $i <=  date('t'); $i++)
            {
               // add the date to the dates array
               $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
            }
        }
       
        $booking_dao= new EventM_Booking_DAO();
        $bookings= $booking_dao->get_completed_bookings($filter);
        
        // Preparing bookings data
        if(!empty($bookings))
        {
            foreach($bookings as $booking)
            {
                if(!$this->is_booking_belongs_to_venue($search_params['venue_id'],$booking->ID))
                        continue;
                
                if(!$this->is_booking_belongs_to_event($search_params['event_id'],$booking->ID))
                   continue;      
                
                $booking_date= em_formatDateForSave($booking->post_date);
                if(isset($booking_data[$booking_date]))
                {   
                    $booking_data[$booking_date] += $this->calculate_field_value($search_params['filter_type'],$booking);
                }
                else
                {
                    $booking_data[$booking_date] = $this->calculate_field_value($search_params['filter_type'],$booking);
                }
            }
        }
        
        // Preparing array for complete month
        foreach($dates as $date)
        {
            $tmp_array[]= $date;
            if(!array_key_exists($date, $booking_data))
            {
                $tmp_array[]= 0;
            }
            else{
                 $tmp_array[]= $booking_data[$date];
            }
            $data[]= $tmp_array;
            $tmp_array= array();
        }
        
        
        return $data;
        
    }
    
    private function get_data_by_day($search_params,$filter= array())
    {
        $booking_data= array();
        $hours= range(0,23);
        $data= array();
        
        if($search_params['report_by']=="today")
        {   
            $today= getdate();
            $filter['date_query'] = array(
            array(
                'year'  => date('Y',current_time('timestamp')),
                'month' => date('m',current_time('timestamp')),
                'day'=> date('d',current_time('timestamp'))
            ));
        }
        
        if($search_params['report_by']=="yesterday")
        {
            $yesterday= getdate(strtotime("yesterday"));
            $filter['date_query'] = array(
            array(
                'year'  => $yesterday['year'],
                'month' => $yesterday['mon'],
                'day'=> $yesterday['mday']
            ));
        }
        
        $booking_dao= new EventM_Booking_DAO();
        $bookings= $booking_dao->get_completed_bookings($filter);
        
        if(!empty($bookings))
        {
            foreach($bookings as $booking)
            {
                if(!$this->is_booking_belongs_to_venue($search_params['venue_id'],$booking->ID))
                        continue;
                
                if(!$this->is_booking_belongs_to_event($search_params['event_id'],$booking->ID))
                   continue; 

                $hour= (int) date("H",strtotime($booking->post_date));
                if(isset($booking_data[$hour]))
                {
                    $booking_data[$hour] += $this->calculate_field_value($search_params['filter_type'],$booking);
                }
                else
                {
                    $booking_data[$hour] = $this->calculate_field_value($search_params['filter_type'],$booking);
                }    
            }
        }
        
        // Preparing array for complete month
        foreach($hours as $hour)
        {
            $time= array($hour,00,00);
            $tmp_array[]= $time;
            if(!array_key_exists($hour, $booking_data))
            {
                $tmp_array[]= 0;
            }
            else{
                 $tmp_array[]= $booking_data[$hour];
            }
            $data[]= $tmp_array;
            $tmp_array= array();
        }
        
        return $data;
    }
    
    private function get_data_by_week($search_params,$filter= array())
    {
        $dates= array();
        $booking_data= array();
        $data= array();
        $tmp_array= array();
        
        if($search_params['report_by']=="last_week")
        {
           $filter['date_query'] = array(array('after' => date('Y-m-d', strtotime('last week')),'before' => date('Y-m-d', strtotime('this week')), 'inclusive' => true));

           for($i=0; $i<7; $i++){
                $dates[] = date('Y-m-d', strtotime('last week + '.$i.' day'));
            }
        }
      
        if($search_params['report_by']=="this_week")
        {
            $filter['date_query'] = array(
                array(
                    'year' => date( 'Y' ),
                    'week' => strftime( '%U' )
                )
             );
            
            for($i=0; $i<7; $i++){
                $dates[] = date('Y-m-d', strtotime('this week + '.$i.' day'));
            }
        }
        
        $booking_dao= new EventM_Booking_DAO();
        $bookings= $booking_dao->get_completed_bookings($filter);
        
        // Preparing bookings data
        if(!empty($bookings))
        {
            foreach($bookings as $booking)
            {
                if(!$this->is_booking_belongs_to_venue($search_params['venue_id'],$booking->ID))
                        continue;
                
                if(!$this->is_booking_belongs_to_event($search_params['event_id'],$booking->ID))
                   continue; 
                
                $booking_date= em_formatDateForSave($booking->post_date);
                if(isset($booking_data[$booking_date]))
                {
                    $booking_data[$booking_date] += $this->calculate_field_value($search_params['filter_type'],$booking);
                }
                else
                {
                    $booking_data[$booking_date] = $this->calculate_field_value($search_params['filter_type'],$booking);
                }
            }
        }
        
        // Preparing array for complete month
        foreach($dates as $date)
        {
            $tmp_array[]= $date;
            if(!array_key_exists($date, $booking_data))
            {
                $tmp_array[]= 0;
            }
            else{
                 $tmp_array[]= $booking_data[$date];
            }
            $data[]= $tmp_array;
            $tmp_array= array();
        }
        
        return $data;
    }
    
    private function get_data_by_year($search_params,$filter)
    {
        $months= array('Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec');
        $booking_data= array();
        $data= array();
        $tmp_array= array();
        
        if($search_params['report_by']=="this_year")
        {
            $filter['date_query'] = array(
                array(
                    'year' => date( 'Y' )
                )
             );
        }
        
        
        if($search_params['report_by']=="last_year")
        {
            $filter['date_query'] = array(
                array(
                    'year' => date( 'Y' )-1
                )
             );
        }
        
        $booking_dao= new EventM_Booking_DAO();
        $bookings= $booking_dao->get_completed_bookings($filter);
        
        // Preparing bookings data
        if(!empty($bookings))
        {
            foreach($bookings as $booking)
            {
                if(!$this->is_booking_belongs_to_venue($search_params['venue_id'],$booking->ID))
                        continue;
                
                if(!$this->is_booking_belongs_to_event($search_params['event_id'],$booking->ID))
                   continue; 
                
                $booking_month= date('M',strtotime($booking->post_date));
                if(isset($booking_data[$booking_month]))
                {
                    $booking_data[$booking_month] += $this->calculate_field_value($search_params['filter_type'],$booking);
                }
                else
                {
                    $booking_data[$booking_month] = $this->calculate_field_value($search_params['filter_type'],$booking);
                }
            }
        }
        
        // Preparing array for complete month
        foreach($months as $month)
        {
            $tmp_array[]= $month;
            if(!array_key_exists($month, $booking_data))
            {
                $tmp_array[]= 0;
            }
            else{
                 $tmp_array[]= $booking_data[$month];
            }
            $data[]= $tmp_array;
            $tmp_array= array();
        }
        
        return $data;
    }
    
    private function get_data_by_custom_dates($search_params,$filter)
    {
       if(!empty($search_params['start_date']) && !empty($search_params['end_date'])){
            $filter['date_query'] = array(
                     array(
                       'after' => $search_params['start_date'],
                       'before' => date('y-m-d',strtotime('+1 day',strtotime($search_params['end_date']))),
                       'inclusive' => true
                    )
            );
        
        $booking_dao= new EventM_Booking_DAO();
        $bookings= $booking_dao->get_completed_bookings($filter);
        
        if(!empty($bookings))
        {
            foreach($bookings as $booking)
            {
                if(!$this->is_booking_belongs_to_venue($search_params['venue_id'],$booking->ID))
                        continue;
                
                if(!$this->is_booking_belongs_to_event($search_params['event_id'],$booking->ID))
                   continue; 
                
                $booking_date= em_formatDateForSave($booking->post_date);
                if(isset($booking_data[$booking_date]))
                {
                    $booking_data[$booking_date] += $this->calculate_field_value($search_params['filter_type'],$booking);
                }
                else
                {
                    $booking_data[$booking_date] = $this->calculate_field_value($search_params['filter_type'],$booking);
                }    
            }
        }
        
        // Preparing array for complete month
        $dates= EventM_Utility::dates_from_range($search_params['start_date'],$search_params['end_date']);
        foreach($dates as $date)
        {
            $tmp_array[]= $date;
            if(!array_key_exists($date, $booking_data))
            {
                $tmp_array[]= 0;
            }
            else{
                 $tmp_array[]= $booking_data[$date];
            }
            $data[]= $tmp_array;
            $tmp_array= array();
        }
        }
        
        return $data;
    }
    
    private function is_booking_belongs_to_venue($venue_id,$booking_id)
    {   
        $event_dao = new EventM_Event_DAO();
        if(empty($venue_id))
            return true;
       
        $venue = $event_dao->get_venue(em_get_post_meta($booking_id, 'event_id', true));
        if(!empty($venue) && $venue->term_id==$venue_id)
           return true;

        return false;      
    }
    
    private function is_booking_belongs_to_event($event_id,$booking_id)
    {   
       $event_dao = new EventM_Event_DAO();
       if(empty($event_id))
            return true;

       if($event_id== (int) em_get_post_meta($booking_id, 'event_id', true))
            return true;
       
       return false;
        
    }
    
    private function calculate_field_value($field,$booking)
    {
        if($field=="revenue")
            return (float) em_calculate_booking_price($booking);
        
         if($field=="booking")
            return (int) em_calculate_total_booking($booking);
    }
}

