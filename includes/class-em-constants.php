<?php

/**
 * This class works as a repository of all the string resources used in  UI
 * for easy translation and management. 
 *
 */
class EventM_Constants
{
    static private $eventTicket= array(
          'core' => array('name','description'),
          'meta' => array('template','background_color','border_color','font_color1','font_color2','font1','font2','logo')
    ); 
    
    static private $eventType= array(
          'core' => array('name'),
          'meta' => array('color','age_group','description','custom_group')
    ); 
    
    static private $event= array(
            'core'=> array('name','slug','description','status'),
            'meta'=>array('event_type','new_event_type', 'venue','new_venue', 'performer',
            'custom_performer_name', 'start_date','end_date', 'recurring_option', 'recurrence_interval',
            'recurring_specific_dates','multi_dates', 'seating_capacity', 'organizer_name', 'organizer_contact_details',
            'hide_event_from_calendar', 'hide_event_from_events','hide_booking_status', 'ticket_template',
            'max_tickets_per_person', 'allow_cancellations', 'audience_notice', 'allow_discount',
            'discount_no_tickets', 'discount_per', 'facebook_page', 'cover_image_id', 'sponser_image_ids',
            'gallery_image_ids', 'ticket_price', 'hide_organizer','last_booking_date','start_booking_date','rm_form','seats','recurrence','parent_event','child_events','booked_seats','match','is_daily_event'),
    );
    
    static private $performer= array(
          'core' => array('name', 'slug','description'),
          'meta' => array('type','role', 'display_front','feature_image_id')
    );
    
    static private $booking= array(
          'allowed' => array('note','offline_status'),
          'meta' => array('note','offline_status')
    );
    
    static private $venue= array(
          'core' => array( 'name','slug'),
          'meta' => array('seating_organizer','description','seats','facebook_page','address', 'type', 'seating_capacity', 'seating_organizer','gallery_images','established')
    );
    
    static public $status= array('publish'=>"Completed",
                                    'em-cancelled'=>"Cancelled",
                                    'em-pending'=>"Pending",
                                    'em-refunded'=>"Refunded"
    );
    
    static public $offline_status= array('Pending'=>"Pending",
                                    'Received'=>"Received",
                                    'Cancelled'=>"Cancelled"

    );
    
    static public $currencies= array(
            'USD' => 'United States Dollars',
            'EUR' => 'Euros',
            'GBP' => 'Pounds Sterling',
            'AUD' => 'Australian Dollars',
            'BRL' => 'Brazilian Real',
            'CAD' => 'Canadian Dollars',
            'CZK' => 'Czech Koruna',
            'DKK' => 'Danish Krone',
            'HKD' => 'Hong Kong Dollar',
            'HUF' => 'Hungarian Forint',
            'ILS' => 'Israeli Shekel',
            'JPY' => 'Japanese Yen',
            'MYR' => 'Malaysian Ringgits',
            'MXN' => 'Mexican Peso',
            'NZD' => 'New Zealand Dollar',
            'NOK' => 'Norwegian Krone',
            'PHP' => 'Philippine Pesos',
            'PLN' => 'Polish Zloty',
            'SGD' => 'Singapore Dollar',
            'SEK' => 'Swedish Krona',
            'CHF' => 'Swiss Franc',
            'TWD' => 'Taiwan New Dollars',
            'THB' => 'Thai Baht',
            'INR' => 'Indian Rupee',
            'TRY' => 'Turkish Lira',
            'RIAL' => 'Iranian Rial',
            'RUB' => 'Russian Rubles',
            'BIF' => 'Burundi Franc'
        );
   
    static public $currency_symbol = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AUD' => '$',
            'BRL' => 'R$',
            'CAD' => '$',
            'CZK' => 'Kč',
            'DKK' => 'kr',
            'HKD' => '$',
            'HUF' => 'Ft',
            'ILS' => '₪',
            'JPY' => '¥',
            'MYR' => 'RM',
            'MXN' => '$',
            'NZD' => '$',
            'NOK' => 'kr',
            'PHP' => '₱',
            'PLN' => 'zł',
            'SGD' => '$',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'TWD' => 'NT$',
            'THB' => '฿',
            'INR' => '₹',
            'TRY' => 'TRY',
            'RIAL' => 'RIAL',
            'RUB' => 'руб',
            'BIF' => '₣'
        
        );
    
    static private $fonts= array("Helvetica","Monospace","Courier","Arial","Times");
    static public function get_currencies_cons(){
        return self::$currencies;
    }
    
     static public function get_currency_symbol(){
        return self::$currency_symbol;
    }

    static public function get_ticket_cons(){
        return self::$eventTicket;
    }
    
    static public function get_event_cons(){
    
        return self::$event;
    }
   
    
    static public function get_performer_cons(){
        return self::$performer;
    }
    
    static public function get_venue_cons(){
        return self::$venue;
    }
    
    static public function get_type_cons(){
        return self::$eventType;
    }
    
    static public function get_booking_cons()
    {
        return self::$booking;
    }
    
    static public function get_fonts_cons()
    {
        return self::$fonts;
    }
    
}
