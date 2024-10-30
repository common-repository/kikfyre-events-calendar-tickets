<?php

class EventM_Global_Settings_Model extends EventM_Array_Model
{
    protected $google_cal_client_id;
    protected $google_cal_api_key;
    protected $fb_api_key;
    protected $gmap_api_key;
    protected $paypal_processor;
    protected $payment_test_mode;
    protected $paypal_email;
    protected $paypal_page_style;
    protected $registration_email_content;
    protected $registration_email_subject;
    protected $booking_pending_email;
    protected $send_booking_confirm_email;
    protected $send_booking_cancellation_email;
    protected $booking_confirmed_email;
    protected $booking_cancelation_email;
    protected $booking_refund_email;
    protected $reset_password_mail;
    protected $paypal_api_username;
    protected $paypal_api_password;
    protected $paypal_api_sig;
    protected $social_sharing;
    protected $gcal_sharing;
    protected $hide_past_events;
    protected $show_recurrence_events;    
    protected $performers_page;
    protected $venues_page;
    protected $events_page;
    protected $booking_page;
    protected $profile_page;
    protected $attributes;
    protected $currency= 'USD';
    protected $hide_expired_from_admin= 0;
            
    
    function __construct() { 
       $this->initialize();
    }
    
    public function initialize()
    {  
        $global_options= get_option(EM_GLOBAL_SETTINGS);
        foreach($global_options as $key=>$value)
        {
            $method= "set_".$key;
            if(method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
    
    public function load_attributes()
    {
    }
    
    public function get_google_cal_client_id() {
        return $this->google_cal_client_id;
    }

    public function get_google_cal_api_key() {
        return $this->google_cal_api_key;
    }

    public function get_fb_api_key() {
        return $this->fb_api_key;
    }

    public function get_gmap_api_key() {
        return $this->gmap_api_key;
    }
    
    public function get_paypal_processor() {
        return $this->paypal_processor;
    }

    public function get_payment_test_mode() {
        return $this->payment_test_mode;
    }

    public function get_paypal_email() {
        return $this->paypal_email;
    }

    public function get_paypal_page_style() {
        return $this->paypal_page_style;
    }

    public function get_registration_email_content() {
        return $this->registration_email_content;
    }

    public function get_registration_email_subject() {
        return $this->registration_email_subject;
    }

    public function get_booking_pending_email() {
        return $this->booking_pending_email;
    }

    public function get_send_booking_confirm_email(){
          return $this->send_booking_confirm_email;
    }
    
    public function get_send_booking_cancellation_email(){
        return $this->send_booking_cancellation_email;
    }
    
    public function get_booking_confirmed_email() {
        return $this->booking_confirmed_email;
    }

    public function get_booking_cancelation_email() {
        return $this->booking_cancelation_email;
    }

    public function get_booking_refund_email() {
        return $this->booking_refund_email;
    }

    public function get_reset_password_mail() {
        return $this->reset_password_mail;
    }

    public function get_paypal_api_username() {
        return $this->paypal_api_username;
    }

    public function get_paypal_api_password() {
        return $this->paypal_api_password;
    }

    public function get_paypal_api_sig() {
        return $this->paypal_api_sig;
    }

    public function get_social_sharing() {
        return $this->social_sharing;
    }

    public function get_gcal_sharing() {
        return $this->gcal_sharing;
    }

     public function get_hide_past_events() {
        return (int) $this->hide_past_events;
    }
    
    public function get_show_recurrence_events() {
        return (int) $this->show_recurrence_events;
    }
    
    public function get_performers_page() {
        return $this->performers_page;
    }

    public function get_venues_page() {
        return $this->venues_page;
    }

    public function get_events_page() {
        return $this->events_page;
    }
    
     public function get_event_type_page() {
        return $this->event_types;
    }

    public function get_booking_page() {
        return $this->booking_page;
    }

    public function get_profile_page() {
        return $this->profile_page;
    }

    public function set_google_cal_client_id($google_cal_client_id) {
        $this->google_cal_client_id = $google_cal_client_id;
    }

    public function set_google_cal_api_key($google_cal_api_key) {
        $this->google_cal_api_key = $google_cal_api_key;
    }

    public function set_fb_api_key($fb_api_key) {
        $this->fb_api_key = $fb_api_key;
    }

    public function set_gmap_api_key($gmap_api_key) {
        $this->gmap_api_key = $gmap_api_key;
    }

    public function set_paypal_processor($paypal_processor) {
        $this->paypal_processor = (int) $paypal_processor;
    }

    public function set_payment_test_mode($payment_test_mode) {
        $this->payment_test_mode = (int) $payment_test_mode;
    }

    public function set_paypal_email($paypal_email) {
        $this->paypal_email = $paypal_email;
    }

    public function set_paypal_page_style($paypal_page_style) {
        $this->paypal_page_style = $paypal_page_style;
    }

    public function set_registration_email_content($registration_email_content) {
        $this->registration_email_content = $registration_email_content;
    }

    public function set_registration_email_subject($registration_email_subject) {
        $this->registration_email_subject = $registration_email_subject;
    }

    public function set_booking_pending_email($booking_pending_email) {
        $this->booking_pending_email = $booking_pending_email;
    }

   public function set_send_booking_confirm_email($send_booking_confirm_email){
      $this->send_booking_confirm_email = (int) $send_booking_confirm_email;
   }
   public function set_send_booking_cancellation_email($send_booking_cancellation_email){
       $this->send_booking_cancellation_email = (int) $send_booking_cancellation_email;
   }
    public function set_booking_confirmed_email($booking_confirmed_email) {
        $this->booking_confirmed_email = $booking_confirmed_email;
    }

    public function set_booking_cancelation_email($booking_cancelation_email) {
        $this->booking_cancelation_email = $booking_cancelation_email;
    }

    public function set_booking_refund_email($booking_refund_email) {
        $this->booking_refund_email = $booking_refund_email;
    }

    public function set_reset_password_mail($reset_password_mail) {
        $this->reset_password_mail = $reset_password_mail;
    }

    public function set_paypal_api_username($paypal_api_username) {
            $this->paypal_api_username = $paypal_api_username;
    }

    public function set_paypal_api_password($paypal_api_password) {
            $this->paypal_api_password = $paypal_api_password;
    }

    public function set_paypal_api_sig($paypal_api_sig) {
        $this->paypal_api_sig = $paypal_api_sig;
    }

    public function set_social_sharing($social_sharing) {
        $this->social_sharing = $social_sharing;
    }

    public function set_gcal_sharing($gcal_sharing) {
        $this->gcal_sharing = $gcal_sharing;
    }

    public function set_hide_past_events($hide_past_events) {
        $this->hide_past_events = (int) $hide_past_events;
    }
    
    public function set_show_recurrence_events($show_recurrence_events) {
        $this->show_recurrence_events = (int) $show_recurrence_events;
    }
    
    public function set_performers_page($performers_page) {
        $this->performers_page = (int) $performers_page;
    }

    public function set_venues_page($venues_page) {
        $this->venues_page = (int) $venues_page;
    }

    public function set_events_page($events_page) {
        $this->events_page = (int) $events_page;
    }
    
    public function set_event_type_page($event_types) {
        $this->event_types = (int) $event_types;
    }

    public function set_booking_page($booking_page) {
        $this->booking_page = (int) $booking_page;
    }

    public function set_profile_page($profile_page) {
        $this->profile_page = (int) $profile_page;
    }

    public function get_currency() {
        return $this->currency;
    }
    public function set_currency($currency) {
        $this->currency= $currency;
    }
    
    public function __get($name)
    {
        $method= "get_".$name;
        if(method_exists($this,$method))
        {
           return $this->$method();
        }
        
        return false;
    }
    
    public function get_hide_expired_from_admin() {
        return $this->hide_expired_from_admin;
    }

    public function set_hide_expired_from_admin($hide_expired_from_admin) {
        $this->hide_expired_from_admin = $hide_expired_from_admin;
    }


}
