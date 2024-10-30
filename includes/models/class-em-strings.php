<?php

class EventM_Strings_Model{
    
    private $trans;
    
    public function __construct() {
        $this->trans= new stdClass();
    }
    
    /* Event related translations */
    public function addEvent(){
       $labels= array('label_name','label_performer','label_slug','label_save','label_cover_image',
                       'label_gallery','label_performers','label_performer_role','label_performer_name',
                        'label_new_performer','label_start_date','label_end_date','label_recurrence',
                        'label_recurrence','label_recurrence_date','label_recurrence_interval','label_recurrence_dates',
                        'label_seating_capacity','label_organizer_name','label_organizer_contact_details',
                        'label_hide_organizer','label_hide_event_from_calendar','label_hide_event_from_events',
                        'label_ticket_price','label_override_ticket_price','label_edit_seating_arrangement','label_ticket_template','label_max_tickets_per_person','label_allow_cancellations',
                        'label_audience_notice','label_allow_volume_discount','label_discount_no_tickets',
                        'label_discount_per','label_facebook_page','label_sponser_image','label_status','label_type','label_add_event_type',
                        'label_description','label_venue','label_add_new_venue','label_upload','label_cancel','label_last_booking_date','label_start_booking_date','label_capacity','heading_new_event_page','label_match','label_event_duration','label_single_day','label_multi_days','label_close','heading_child_event_page','label_daily_schedule');
       
       $field_notes= array('note_event_name','note_event_performer','note_event_slug','note_event_cover_image',
                       'note_event_gallery','note_event_performers','note_event_performer_role','note_event_performer_name',
                        'note_event_new_performer','note_event_start_date','note_event_end_date','note_event_recurrence',
                        'note_event_recurrence','note_event_recurrence_date','note_event_recurrence_interval','note_event_recurrence_dates',
                        'note_event_seating_capacity','note_event_organizer_name','note_event_organizer_contact_details',
                        'note_event_hide_organizer','note_event_hide_event_from_calendar','note_event_hide_event_from_events',
                        'note_event_ticket_price','note_event_ticket_template','note_event_max_tickets_per_person','note_event_allow_cancellations',
                        'note_event_audience_notice','note_event_allow_volume_discount','note_event_discount_no_tickets',
                        'note_event_discount_per','note_event_facebook_page','note_event_sponser_image','note_event_status','note_event_type','note_event_add_event_type',
                        'note_event_description','note_event_venue','note_event_add_new_venue','note_event_upload');
       
       $validations= array('validation_venue_capacity_exceeded');
       $this->generalValidations();
       $this->createObject($labels);
       $this->createObject($validations);
       $this->createObject($field_notes);
       
       return $this->trans;
    }
    
    public function listEvents(){
        $labels= array('label_at','label_on','label_no_records','label_mark_all', 'heading_events_manager');
        $this->createObject($labels);
        $this->listActionBar();
        return $this->trans;
    }
    
    /* Event translations ends here */
    
    /* Venue translations starts */
    public function listVenues(){
        $labels= array('label_seats','label_standing','label_no_records','label_upcoming_events','heading_venue_manager','label_mark_all');
        
        $this->createObject($labels);
        $this->listActionBar();

        return $this->trans;
    }
    
    public function addVenue(){
        $labels= array('label_name','label_seating_capacity','label_address','label_gmap_control_all',
                       'label_gmap_control_est','label_gmap_control_geo','label_save','label_established',
                        'label_seating_organizer','label_gallery','label_slug','label_facebook_page','label_type',
                        'label_description','label_upload','label_rows','label_columns','label_cancel','heading_venue_page');
        
        $field_notes= array('note_venue_name','note_venue_seating_capacity','note_venue_address','note_venue_gmap_control_all',
                       'note_venue_gmap_control_est','note_venue_gmap_control_geo','note_venue_established',
                        'note_venue_seating_organizer','note_venue_gallery','note_venue_slug','note_venue_facebook_page','note_venue_type',
                        'note_venue_description','note_venue_upload','note_venue_rows','note_venue_columns');
        
        $this->createObject($labels);
        $this->generalValidations();
        $this->createObject($field_notes);
        
        return $this->trans;
    }
    
    /* Venue translations ends here */
    
    
    /* Event Type screen tranlations */
    public function addEventType(){ 
        $labels= array('label_name','label_color','label_save','label_age_group','label_custom_age',
                       'label_special_instructions','label_cancel','heading_new_event_type','heading_event_type_manager');
        
        $field_notes= array('note_event_type_name','note_event_type_color','note_event_type_age_group','note_event_type_custom_age',
                       'note_event_type_special_instructions');
        
        $this->createObject($labels);
        $this->generalValidations();
        $this->createObject($field_notes);
        
        return $this->trans;
    }
    
    public function listEventTypes(){
        $labels= array('label_name','label_no_records','heading_event_types','label_mark_all','label_view');
        $this->createObject($labels);
        $this->listActionBar();

        return $this->trans;
    }
    
    /* Event type translation ends here */
    
    /* Performer translations */
    public function addPerformer(){
        $labels= array('label_cancel','label_name','label_slug','label_save','label_performer_type','label_role',
                       'label_image','label_display','label_performer_display','label_cover_image','label_upload','label_description','heading_new_performer');
        
        $field_notes= array('note_performer_name','note_performer_slug','note_performer_performer_type','note_performer_role',
                       'note_performer_image','note_performer_display','note_performer_performer_display','note_performer_cover_image','note_performer_upload','note_description');
        
        $this->createObject($labels);
        $this->generalValidations();
        $this->createObject($field_notes);

        return $this->trans;
    }
    
    public function listPerformers(){
        $labels= array('label_performers','label_no_records','label_mark_all');
        $this->createObject($labels);
        $this->listActionBar();

        return $this->trans;
    }
    
    public function listBookings(){
        $labels= array('label_no_booking_records','label_all','label_time','label_username',
                        'label_email','label_actions','label_no_tickets',
                       'label_today','label_this_week','label_this_month','label_this_year','heading_booking_manager',
                        'label_displaying_for','label_specific_period','label_from','label_to','label_search','label_view','label_booking_id','label_booking_status');
        $this->createObject($labels);
        $this->listActionBar();

        return $this->trans;
    }

    
    public function addTicketTemplate(){
        $labels= array('label_cancel','label_choose_template','label_name','label_font1','label_font2','label_background_color','label_border_color',
                       'label_cover_image','label_upload','label_save','label_background_pattern','label_choose_template','heading_new_ticket_template');
        
        $field_notes= array('note_ticket_template_choose_template','note_ticket_template_name','note_ticket_template_font1','note_ticket_template_font2','note_ticket_template_background_color','note_ticket_template_border_color',
                       'note_ticket_template_cover_image','note_ticket_template_upload','note_ticket_template_background_pattern','note_ticket_template_choose_template');
        
        $this->createObject($labels);
        $this->generalValidations();
        $this->createObject($field_notes);

        return $this->trans;
    }
    
    public function listTicketTemplates(){
        $labels= array('label_no_records','label_mark_all','heading_ticket_manager');
        $this->createObject($labels);
        $this->listActionBar();
        return $this->trans;
    }
    
    public function listActionBar(){
        $labels= array('label_add_new','label_delete','label_hide_expired','label_duplicate','label_sort_by','label_tour',
                        'label_export_all','label_mark_all');
        $this->createObject($labels);
    }
    
    public function createObject($items=array()){
        if(!is_array($items))
            return false;
        
        foreach($items as $item){
           $this->trans->$item= EventM_UI_Strings::get($item);
        }
    }
    
    public function generalValidations(){
        $validations= array('validation_numeric','validation_required','validation_email','validation_secret_key','validation_publishable_key',
                            'validation_date_format','validation_invalid_url','validation_discount_per_max',
                            'validation_invalid_date_value','validation_facebook_url','validation_invalid_end_date','validation_invalid_last_booking_date','validation_anet_login_key','validation_anet_trans_key','validation_anet_client_key');
        $this->createObject($validations);
    }
    
    public function global_settings(){
       $labels= array('label_google_cal_client_id','label_google_cal_api_key','label_facebook_api_key','label_google_map_api_key','label_cancel','label_save','label_payment_processor','label_paypal',
                      'label_test_mode','label_paypal_email','label_paypal_page_style','label_registration_email_content','label_registration_email_subject',
                        'label_booking_pending_content','label_booking_confirmed_content','label_booking_cancelation_content','label_booking_refund_content',
                      'label_paypal_api_username','label_paypal_api_password','label_paypal_api_sig','heading_global_settings','label_email_notifications','label_general_settings','label_default_pages',
                      'note_shortcode_page','note_exint_page','label_ex_int','label_payments','label_payments_sub');
       $validations= array();
       $this->generalValidations();
       $this->createObject($labels);
       $this->createObject($validations);
       return $this->trans;
    }
    public function view_analytics(){
        $labels= array('label_analytics');
        $this->createObject($labels);
        return $this->trans;
    }
}
