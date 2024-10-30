<?php
/**
 * 
 * @summary Class to load all the Translation strings. 
 * 
 * Load Event data using "post_id" from GET request parameter.
 * @since 1.0
 * 
 */
class EventM_Event_Model extends EventM_Array_Model
{
    
    protected $name;
    protected $id;
    protected $status="publish";
    protected $slug;
    protected $event_type=0;
    protected $description;
    protected $venue=0;
    protected $performer=0;
    protected $start_date="";
    protected $end_date="";
    protected $recurring_option="";
    protected $recurrence_interval="";
    protected $recurring_specific_dates;
    protected $multi_dates= array();
    protected $seating_capacity;
    protected $organizer_name;
    protected $organizer_contact_details;
    protected $hide_event_from_calendar=0;
    protected $hide_event_from_events=0;
    protected $ticket_template=0;
    protected $max_tickets_per_person;
    protected $allow_cancellations=0;
    protected $audience_notice;
    protected $allow_discount=0;
    protected $discount_no_tickets=2;
    protected $discount_per;
    protected $facebook_page;
    protected $cover_image_id;
    protected $sponser_image_ids= array();
    protected $gallery_image_ids= array();
    protected $ticket_price;
    protected $hide_organizer= 0;
    protected $hide_booking_status=0;
    protected $last_booking_date="";
    protected $start_booking_date="";
    protected $rm_form=0;
    protected $seats= array();
    protected $recurrence;
    protected $org_info;
    protected $parent_event=0;
    protected $child_events=array();
    protected $booked_seats= 0;
    protected $attributes;
    protected $match=0;
    protected $is_daily_event= 0;
    
    function __construct($task) {
        $this->id= (int) $task;
        $this->load_attributes();
    }
    
    public function load_attributes()
    {
        $this->attributes= EventM_Constants::get_event_cons();
        
    }
    
    public function get_name() {
        
        return $this->name;
    }

    public function get_status() {
        return $this->status;
    }

    public function get_slug() {
        return $this->slug;
    }

    public function get_event_type() {
        return (int) $this->event_type;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_venue() {
        return (int) $this->venue;
    }

    public function get_performer() {
        return $this->performer;
    }

    public function get_start_date() {
        if(!empty($this->start_date))
            return em_showDateTime($this->start_date,true,"m/d/Y");
        else
            return $this->start_date;
    }

    public function get_end_date() {
        if(!empty($this->end_date))
         return em_showDateTime($this->end_date,true,"m/d/Y");
        else 
         return $this->end_date;   

    }

    public function get_recurring_option() {
        return $this->recurring_option;
    }

    public function get_recurrence_interval() {
        return $this->recurrence_interval;
    }

    public function get_recurring_specific_dates() {
        $dates= maybe_unserialize($this->recurring_specific_dates);
        if(!empty($dates))
            return array_unique($dates);
        return array();
        
    }

    public function get_multi_dates() {
        return empty($this->multi_dates)?array():$this->multi_dates;
    }

    public function get_seating_capacity() {
        return (int) $this->seating_capacity;
    }

    public function get_organizer_name() {
        return $this->organizer_name;
    }

    public function get_organizer_contact_details() {
        return $this->organizer_contact_details;
    }

    public function get_hide_event_from_calendar() {
        return (int) $this->hide_event_from_calendar;
    }
    
    public function get_hide_booking_status(){
          return (int) $this->hide_booking_status;
    }
     

    public function get_hide_event_from_events() {
        return (int) $this->hide_event_from_events;
    }

    public function get_ticket_template() {
        return (int) $this->ticket_template;
    }

    public function get_max_tickets_per_person() {
        return  (int)$this->max_tickets_per_person;
    }

    public function get_allow_cancellations() {
        return (int) $this->allow_cancellations;
    }

    public function get_audience_notice() {
        return $this->audience_notice;
    }

    public function get_allow_discount() {
        return  (int) $this->allow_discount;
    }

    public function get_discount_no_tickets() {
        return (int) $this->discount_no_tickets;
    }

    public function get_discount_per() {
        return (int) $this->discount_per;
    }

    public function get_facebook_page() {
        return $this->facebook_page;
    }

    public function get_cover_image_id() {
        return (int) $this->cover_image_id;
    }

    public function get_sponser_image_ids() {
        return maybe_unserialize($this->sponser_image_ids);
    }

    public function get_gallery_image_ids() {
        return maybe_unserialize($this->gallery_image_ids);
    }

    public function get_ticket_price() {
        return (float) $this->ticket_price;
    }

    public function get_hide_organizer() {
        return (int)$this->hide_organizer;
    }
    
     public function get_start_booking_date() {
         if(!empty($this->start_booking_date))
            return em_showDateTime($this->start_booking_date,true,"m/d/Y");
         else
             return $this->start_booking_date;
    }

    public function get_last_booking_date() {
         if(!empty($this->last_booking_date))
            return em_showDateTime($this->last_booking_date,true,"m/d/Y");
         else
             return $this->last_booking_date;
    }

    public function get_rm_form() {
        return $this->rm_form;
    }

    public function get_seats() {
        return $this->seats;
    }

    public function get_recurrence() {
        return (int)$this->recurrence;
    }

    public function set_name($name) {
     
        $this->name = $name;
    }

    public function set_status($status) {
        $this->status = $status;
    }

    public function set_slug($slug) {
        $this->slug = $slug;
    }

    public function set_event_type($event_type) {
        if(empty($event_type))
        {
            $term = wp_get_object_terms($this->id, EM_EVENT_TYPE_TAX);
            if(!empty($term))
                 return $this->event_type= $term[0]->term_id; 
        }
        return $this->event_type= (int) $event_type;
        
    }

    public function set_description($description) {
       
        $this->description = $description;
    }

    public function set_venue($venue) {
      
        if(empty($venue))
        {
            $terms = wp_get_post_terms($this->id, EM_VENUE_TYPE_TAX);
            if (!empty($terms) && count($terms) > 0):
                $venue = $terms[0];
            
                return $this->venue= $venue->term_id;
            endif;
        }
        return $this->venue= (int) $venue;
        
    }

    public function set_performer($performer) {
       /*  if(empty($performer))
        {
            $performer_posts = get_post($this->id, EM_PERFORMER_POST_TYPE);
            if (!empty($performer_posts) && count($performer_posts) > 0):
                $performer = $performer_posts[0];
                return $this->$performer= $performer_posts->ID;
            endif;
        }*/
       // return $this->$performer_posts=  maybe_unserialize($performer);
        
        
        $this->performer = maybe_unserialize($performer);
    }

    public function set_start_date($start_date) {
            $this->start_date = em_time($start_date);
    }

    public function set_end_date($end_date) {
            $this->end_date = em_time($end_date);
    }

    public function set_recurring_option($recurring_option) {
        $this->recurring_option = $recurring_option;
    }

    public function set_recurrence_interval($recurrence_interval) {
        $this->recurrence_interval =  $recurrence_interval;
    }

    public function set_recurring_specific_dates($recurring_specific_dates) {
        $this->recurring_specific_dates = $recurring_specific_dates;
    }

    public function set_multi_dates($multi_dates) {
        $this->multi_dates = $multi_dates;
    }

    public function set_seating_capacity($seating_capacity) {
        $this->seating_capacity = (int) $seating_capacity;
    }

    public function set_organizer_name($organizer_name) {
        $this->organizer_name = $organizer_name;
    }

    public function set_organizer_contact_details($organizer_contact_details) {
        $this->organizer_contact_details = $organizer_contact_details;
    }

    public function set_hide_event_from_calendar($hide_event_from_calendar) {
        $this->hide_event_from_calendar = (int) $hide_event_from_calendar;
    }

    public function set_hide_event_from_events($hide_event_from_events) {
        $this->hide_event_from_events = (int) $hide_event_from_events;
    }
    
     public function set_hide_booking_status($hide_booking_status){
           $this->hide_booking_status =(int) $hide_booking_status;
    }
     
    

    public function set_ticket_template($ticket_template) {
        $this->ticket_template =  $ticket_template;
    }

    public function set_max_tickets_per_person($max_tickets_per_person) {
        $this->max_tickets_per_person =  (int)$max_tickets_per_person;
    }

    public function set_allow_cancellations($allow_cancellations) {
        $this->allow_cancellations = (int) $allow_cancellations;
    }

    public function set_audience_notice($audience_notice) {
        $this->audience_notice = $audience_notice;
    }

    public function set_allow_discount($allow_discount) {
        $this->allow_discount = (int) $allow_discount;
    }

    public function set_discount_no_tickets($discount_no_tickets) {
        $this->discount_no_tickets = (int) $discount_no_tickets;
    }

    public function set_discount_per($discount_per) {
        $this->discount_per = (int) $discount_per;
    }

    public function set_facebook_page($facebook_page) {
        $this->facebook_page = $facebook_page;
    }

    public function set_cover_image_id($cover_image_id) {
        $this->cover_image_id = $cover_image_id;
    }

    public function set_sponser_image_ids($sponser_image_ids) {
        $this->sponser_image_ids = $sponser_image_ids;
    }

    public function set_gallery_image_ids($gallery_image_ids) {
        $this->gallery_image_ids = $gallery_image_ids;
    }

    public function set_ticket_price($ticket_price) {
        $this->ticket_price = (float) $ticket_price;
    }

    public function set_hide_organizer($hide_organizer) {
        $this->hide_organizer = (int) $hide_organizer;
    }

     public function set_start_booking_date($start_booking_date) {
            $this->start_booking_date = em_time($start_booking_date);
    }
    
    public function set_last_booking_date($last_booking_date) {
            $this->last_booking_date = em_time($last_booking_date);
    }

    public function set_rm_form($rm_form) {
        $this->rm_form = $rm_form;
    }

    public function set_seats($seats) {
        $this->seats = $seats;
    }

    public function set_recurrence($recurrence) {
        $this->recurrence = (int)$recurrence;
    }

    public function get_org_info() {
        return $this->org_info;
    }
    
    public function get_parent_event() {
        return (int) $this->parent_event;
    }

    public function set_parent_event($parent_event) {
        $this->parent_event = (int) $parent_event;
    }

        public function set_org_info($org_info) {
        $this->org_info = $org_info;
        isset($org_info['organizer_name']) && $this->set_organizer_name($org_info['organizer_name']);
        isset($org_info['organizer_contact_details']) && $this->set_organizer_contact_details($org_info['organizer_contact_details']);
        isset($org_info['hide_organizer']) && $this->set_hide_organizer($org_info['hide_organizer']);
    }
    
    
    public function __get($name)
    {
        $method= "get_".$name;
        if(method_exists($this, $method))
        {
            return $this->$method();
        }
    }
    
    public function __set($name,$value)
    {
        $method= "set_".$name;
        if(method_exists($this, $method))
        {
            return $this->$method($value);
        }
    }

    public function get_child_events() {
        return $this->child_events;
    }

    public function set_child_events($child_events) {
        $this->child_events = $child_events;
    }

    public function get_booked_seats() {
        return (int)$this->booked_seats;
    }

    public function set_booked_seats($booked_seats) {
        $this->booked_seats = (int) $booked_seats;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function get_core_attributes()
    {
        return $this->attributes['core'];
    }
    
    
    public function get_meta_attributes()
    {
        return $this->attributes['meta'];
    }
    
    public function from_array($data= array())
    {  
        $obj= array();
        foreach($data as $key=>$value)
        {
           $method= "set_".$key;  
           if(method_exists($this, $method))
           {
               $this->$key= $value;
           }
        }
    }
    
    public function get_match() {
        return (int) $this->match;
    }

    public function set_match($match) {
        $this->match = (int) $match;
    }

    public function get_is_daily_event() {
        return (int) $this->is_daily_event;
    }

    public function set_is_daily_event($daily_event) {
        $this->is_daily_event = (int) $daily_event;
    }


}
