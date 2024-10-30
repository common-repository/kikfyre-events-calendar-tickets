<?php
/**
 *
 * General core functions available on both the front-end and admin.
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Esacaping request parameter
 * string paramete_key.
 *
 * @return string (Parameter Value)
 */
function event_m_get_param($param = null, $secure = false) {

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $null_return = null;
    
    if ($request !== null)
        $_POST = (array) $request;
    
    if ($param && isset($_POST[$param]) && is_array($_POST[$param])) {
        return $_POST[$param];
    }

    if ($param) {
        if ($secure)
            $value = (!empty($_POST[$param]) ? trim(esc_sql($_POST[$param])) : $null_return);
        else { 
            $value = (!empty($_POST[$param]) ? trim(esc_sql($_POST[$param])) : (!empty($_GET[$param]) ? trim(esc_sql($_GET[$param])) : $null_return ));
        }


        return stripslashes($value);
    } else { 
        $params = array();
        foreach ($_POST as $key => $param) {
            $params[trim(esc_sql($key))] = (!empty($_POST[$key]) ? trim(esc_sql($_POST[$key])) : $null_return );
        }
        if (!$secure) {
            foreach ($_GET as $key => $param) {
                $key = trim(esc_sql($key));
                if (!isset($params[$key])) { // if there is no key or it's a null value
                    $params[trim(esc_sql($key))] = (!empty($_GET[$key]) ? trim(esc_sql($_GET[$key])) : $null_return );
                }
            }
        }

        return stripslashes($params);
    }
}

/*
 * String Array with request keys values
 * @ return string (Paraneter Value)
 */

function datafromRequest($allowed = array()) {
    $data = new stdClass();
    foreach ($allowed as $value) {
//        if($value=='description'){
//        print_r(event_m_get_param($value)); die;}
        $data->$value = event_m_get_param($value) === null ? '' : event_m_get_param($value);
    }
    return $data;
}

/**
 * Format dates in a specific format
 * @param String $strdate String date
 * @param String $format Required date format for output
 */
function em_dateFormatter($strdate,$format=null) {
    if(empty($format))
        $format= get_option('date_format');
    
    if (empty($strdate))
        return;
    
    $time = strtotime($strdate);
    return date($format, $time);
}

function em_time($datetime) {
    if (empty($datetime))
        return;
    return strtotime($datetime);
}

/*
 * Return time in H:i format from timestamp
 */
function em_get_time($datetime) {
    return date('H:i', $datetime);
}

function em_showDateTime($datetime, $time = true,$format=null) {
    if(empty($format))
        $format = get_option('date_format');
    
    if (empty($datetime))
        return;
    if ($time)
        $format.= ' H:i';
    return date($format, $datetime);
}

/**
 * Formats Wordpress default date format for database
 */
function em_formatDateForSave($strdate, $t = false) {
    $time = strtotime($strdate);
    if ($t)
        return date('Y-m-d H:i:s', $time);
    else
        return date('Y-m-d', $time);
}

/**
 * Function to get global settings data
 * Possible meta options: gmap_api_key
 */
function em_global_settings($meta = null) {
    // Load global setting array from options table
    $global_options = get_option(EM_GLOBAL_SETTINGS);

    // Check if option exists 
    if ($global_options && is_array($global_options)):
        if ($meta !== null):
            if (array_key_exists($meta, $global_options)):
                return $global_options[$meta];
            else:
                // Option does not exists
                return false;
            endif;
        endif;
        return $global_options;
    endif;

    return false;
}

function em_recurringEventCheck() {
    $event_service = new EventM_Service();
    $event_service->recurringEventAdjustment();
}

function em_check_event_status() {
    $event_service = new EventM_Service();
    $event_service->update_past_event_status();
}

function em_delete_tmp_bookings() {
    $booking_service = new EventM_Booking_Service();
    $booking_service->remove_all_tmp_bookings();
}

function em_get_attached_posts($id, $tax_type) {
    $args = array(
        'post_type' => EM_EVENT_POST_TYPE,
        'numberposts' => -1,
        'post_status' => 'any',
        'tax_query' => array(
            array(
                'taxonomy' => $tax_type,
                'field' => 'term_id',
                'terms' => $id
            )
        )
    );

    $events = get_posts($args);
    console . log($events);
    return $events;
}

function em_check_paypal_ipn() {
    $paypal_service = new EventM_Paypal_Service();

    if (isset($_REQUEST['em_pp_notification']) && $_REQUEST['em_pp_notification'] == 'em_ipn') {
        if ($paypal_service->verify_ipn()) {
            $paypal_service->update_booking_info();
        }
    }
}

function em_get_post_meta($post_id, $key = '', $single = false, $numeric = false) {
    if (!empty($key))
        $key = "em_" . $key;

    $value = get_post_meta($post_id, $key, $single);
    if ($numeric) {
        if (empty($value))
            $value = 0;
    }

    return $value;
}

function em_update_post_meta($post_id, $meta_key, $meta_value) {
    $meta_key = "em_" . $meta_key;
    update_post_meta($post_id, $meta_key, $meta_value);
}

function em_get_term_meta($term_id, $key = '', $single = false) {
    if (!empty($key))
        $key = "em_" . $key;

    return get_term_meta($term_id, $key, $single);
}

function em_update_term_meta($term_id, $meta_key, $meta_value) {
    $meta_key = "em_" . $meta_key;
    update_term_meta($term_id, $meta_key, $meta_value);
}

function em_append_meta_key($key) {
    $keys= array();
    if(is_array($key))
    {
        foreach($key as $k)
        {
            $keys[]= "em_".$k;
        }
        return $keys;
    }
    else
        return "em_" . $key;
}

function em_event_seating_capcity($event_id) {
    $capacity = (int) em_get_post_meta($event_id, 'seating_capacity', true);
    
    if(!empty($capacity))
        return $capacity;
    
    $venues = wp_get_object_terms($event_id, EM_EVENT_VENUE_TAX);
  
    if(!empty($venues)):
        $type= em_get_term_meta($venues[0]->term_id, 'type', true);
        if($type=="standings")
            return 0;

        if (empty($venues))
            return 0;        

        $venue = $venues[0];
        $capacity = (int) em_get_term_meta($venue->term_id, 'seating_capacity', true);
    
        return $capacity;
    else:
            return 0;
    endif;
   
}

function is_registration_magic_active() {
    if (defined("REGMAGIC_GOLD") || defined("REGMAGIC_GOLD_i2") || defined("REGMAGIC_SILVER") || defined("REGMAGIC_BASIC"))
        return true;
    else
        return false;
}

function em_calculate_booking_price($booking) {
    if (!is_object($booking)) {
        $booking = get_post($booking);
    }
    $order_info = em_get_post_meta($booking->ID, 'order_info', true);
    $total_price = $order_info['quantity'] * $order_info['item_price'];
    $discount = ($total_price * $order_info['discount']) / 100;
    return $total_price - $discount;
}

// Returns true if both dates are equal
function em_compare_event_dates($event_id) {
    $start_date = em_showDateTime(em_get_post_meta($event_id, 'start_date', true), false);
    $end_date = em_showDateTime(em_get_post_meta($event_id, 'end_date', true), false);
 
    if (strtotime($start_date) == strtotime($end_date))
        return true;
    else
        return false;
}

function em_calculate_total_booking($booking) {
    if (!is_object($booking)) {
        $booking = get_post($booking);
    }
    $order_info = em_get_post_meta($booking->ID, 'order_info', true);
    $total_booking = $order_info['quantity'];
    return $total_booking;
}

function em_is_event_expired($event_id) {
    // Check event status
    $event = get_post($event_id);
    if ($event->post_status == "em-expired")
        return true;

    return false;
}

function em_is_event_bookable($event_id) {
 
    $last_booking_date = em_get_post_meta($event_id, 'last_booking_date', true);

    if(current_time('timestamp') >= $last_booking_date)     
        return false;
    
    $event_service = new EventM_Service();
    $available_seats = $event_service->available_seats($event_id);
    if ($available_seats <= 0)
        return false;


    return true;
}

function em_check_expired($event_id) {
    // Check event status
    $last_booking_date = em_get_post_meta($event_id, 'last_booking_date', true);

    if(current_time('timestamp') >= $last_booking_date){ 
        return false;
    }
     $event = get_post($event_id);
    if ($event->post_status == "em-expired"){ 
    return false;}
  
    return true;
}

function em_set_mail_content_type_html($content_type) {
    $content_type = 'text/html';
    return $content_type;
}

function em_set_mail_from($original_email_address) {
    return get_option('admin_email');
}

function em_set_mail_from_name($original_from_address) {
    return get_option('blogname');
}

function em_rm_custom_data($user_id) {

   
    $current_user = get_user_by("ID",$user_id);
  
    $data = new stdClass();
    $data->is_user = true;
    $data->user = $current_user;
    $rm_Service = new RM_Services();
    $data->custom_fields = $rm_Service->get_custom_fields($current_user->user_email);


    if ($data->user->first_name) {
        ?>
        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_FNAME'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->first_name; ?></span>
        </div>
        <?php
    }
    if ($data->user->last_name) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_LNAME'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->last_name; ?></span>
        </div>
        <?php
    }
    if ($data->user->description) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('LABEL_BIO'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->description; ?></span>
        </div>
        <?php
    }
    if ($data->user->user_email) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('LABEL_EMAIL'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->user_email; ?></span>
        </div>
        <?php
    }
    if ($data->user->sec_email) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('LABEL_SECEMAIL'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->sec_email; ?></span>
        </div>
        <?php
    }
    if ($data->user->nickname) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_NICKNAME'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->nickname; ?></span>
        </div>
        <?php
    }
    if ($data->user->user_url) {
        ?>

        <div class="em-booking-row">
            <span class="em-booking-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_WEBSITE'); ?>:</span>
            <span class="em-booking-detail"><?php echo $data->user->user_url; ?></span>
        </div>
        <?php
    }

    if (is_array($data->custom_fields) || is_object($data->custom_fields))
        foreach ($data->custom_fields as $field_id => $sub) {
            $key = $sub->label;
            $meta = $sub->value;
            $sub_original = $sub;
            if (!isset($sub->type)) {
                $sub->type = '';
            }

            $meta = RM_Utilities::strip_slash_array(maybe_unserialize($meta));
            ?>
            <div class="em-booking-row">

                <span class="em-booking-label"><?php echo $key; ?></span>
                <span class="em-booking-detail">
                    <?php
                    if (is_array($meta) || is_object($meta)) {
                        if (isset($meta['rm_field_type']) && $meta['rm_field_type'] == 'File') {
                            unset($meta['rm_field_type']);

                            foreach ($meta as $sub) {

                                $att_path = get_attached_file($sub);
                                $att_url = wp_get_attachment_url($sub);
                                ?>
                                <div class="rm-submission-attachment">
                                    <?php echo wp_get_attachment_link($sub, 'thumbnail', false, true, false); ?>
                                    <div class="rm-submission-attachment-field"><?php echo basename($att_path); ?></div>
                                    <div class="rm-submission-attachment-field"><a href="<?php echo $att_url; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></a></div>
                                </div>

                                <?php
                            }
                        } elseif (isset($meta['rm_field_type']) && $meta['rm_field_type'] == 'Address') {
                            $sub = $meta['original'] . '<br/>';
                            if (count($meta) === 8) {
                                $sub .= '<b>Street Address</b> : ' . $meta['st_number'] . ', ' . $meta['st_route'] . '<br/>';
                                $sub .= '<b>City</b> : ' . $meta['city'] . '<br/>';
                                $sub .= '<b>State</b> : ' . $meta['state'] . '<br/>';
                                $sub .= '<b>Zip code</b> : ' . $meta['zip'] . '<br/>';
                                $sub .= '<b>Country</b> : ' . $meta['country'];
                            }
                            echo $sub;
                        } elseif ($sub->type == 'Time') {
                            echo $meta['time'] . ", Timezone: " . $meta['timezone'];
                        } else {
                            $sub = implode(', ', $meta);
                            echo $sub;
                        }
                    } else {
                        if ($sub->type == 'Rating') {
                            echo RM_Utilities::enqueue_external_scripts('script_rm_rating', RM_BASE_URL . 'public/js/rating3/jquery.rateit.js');
                            echo '<div class="rateit" id="rateit5" data-rateit-min="0" data-rateit-max="5" data-rateit-value="' . $meta . '" data-rateit-ispreset="true" data-rateit-readonly="true"></div>';
                        } else
                            echo $meta;
                    }
                    ?>
                </span>
            </div>
            <?php
        }
        
}

//function em_activation_notice() {
//    $has_been_shown_already = get_site_option('em_post_activation_notice_displayed', 'yes');
//
//    if ("yes" == $has_been_shown_already)
//        return;
//
//    echo "<div class='updated'><p>Please Save the Permalink </p></div>";
//    delete_site_option('em_post_activation_notice_displayed');
//}

function em_encrypt($string) {
    $key = 'A Terrific tryst with tyranny';

    $iv = mcrypt_create_iv(
            mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM
    );

    $encrypted = base64_encode($iv . mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv
            )
    );
    return $encrypted;
}

function em_decrypt($string) {
    $key = 'A Terrific tryst with tyranny';

    $data = base64_decode($string);
    $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

    $decrypted = rtrim(
            mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv
            ), "\0"
    );

    return $decrypted;
}

function em_datetime_diff($start, $end) {
    $start = new DateTime('@' . $start);
    $end = new DateTime('@' . $end);
    $interval = $end->diff($start);
    return $interval;
}

function em_get_mail_confirm_content($order_id) {
    $booking_service = new EventM_Booking_Service();
    $event_detail = $booking_service->get_event_by_booking($order_id);
    $data = (array) $event_detail;
    return $data;
}

function em_admin_get_mail_confirm_content($mail_body,$order_id) {

    $mail_body = "";
    $booking_service = new EventM_Booking_Service();
    $event_detail = $booking_service->get_event_by_booking($order_id);

    $data = (array) $event_detail;
    ob_start();


    if (isset($data['seat_sequence'])):
        include('mail/customer.html');
        $mail_body = ob_get_clean();
        $mail_body = str_replace("(Seat No.)", $data['seat_sequence'], $mail_body);
    else:
        include('mail/customer_standing.html');
        $mail_body = ob_get_clean();
    endif;
    $mail_body = str_replace("#ID", $data['ID'], $mail_body);
    $mail_body = str_replace("Event Name", $data['event_name'], $mail_body);
    $mail_body = str_replace("Event Venue", $data['address'], $mail_body);
    $mail_body = str_replace("(Subtotal)", $data['total_price'], $mail_body);
    $mail_body = str_replace("(Quantity)", $data['order_info']['quantity'], $mail_body);
    $mail_body = str_replace("(Price)", $data['item_price'], $mail_body);
    return $mail_body;
}

function em_load_twitter_meta() {

    global $post;
     $post_id = $post->ID; 
     $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true); 
     $image = wp_get_attachment_image($thumbnail_id, 'full');
     if(empty($thumbnail_id))
     {
         $image= esc_url(plugins_url('/images/dummy_image _single_em_event.png', __FILE__)); 
     }
     else{
         $image = wp_get_attachment_image($thumbnail_id, 'full');
     }
    echo '<meta name="twitter:card" content="summary_large_image"/> ';
    echo "<meta name='twitter:title' content='$post->post_title'>";
    echo "<meta name='twitter:description' content='$post->post_content'>";
    echo "<meta name='twitter:image' content='$image'>";
}

function get_mail_body($mail_body,$order_id,$user_email=""){ 
    $data = em_get_mail_confirm_content($order_id);  
    if(isset($data['seat_sequence'])):
            $mail_body = str_replace("(Seat No.)",$data['seat_sequence'],$mail_body);
    else:
            $mail_body = str_replace("(Seat No.)","Standing Event",$mail_body);
     endif; 


    $mail_body = str_replace("#ID",$data['ID'],$mail_body);    
    $mail_body = str_replace("Event Name",$data['event_name'],$mail_body); 
    $mail_body = str_replace("Venue Name",$data['venue_name'],$mail_body);   
    $mail_body = str_replace("Event Venue",$data['address'],$mail_body);
    $mail_body = str_replace("$(Subtotal)",$data['total_price'],$mail_body); 
    $mail_body = str_replace("(Quantity)",$data['order_info']['quantity'],$mail_body);
    $mail_body = str_replace("$(Price)",$data['item_price'],$mail_body);
    $mail_body = str_replace("$(Discount)",$data['discount'],$mail_body);
    $mail_body = str_replace("(User Email)",$user_email,$mail_body);


     return $mail_body ;
    
}


/*
 * Widget Intialization 
 */
function em_widgets_init()
{
   
   register_sidebar( 
    array(
        'name' => 'KikFyre Header',
        'id' => 'em_header-1',
        'description' => 'Single Event Headerbar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget'  => '</li>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>'
        
    ));
}

/*
 * Localize Google Map API info 
 * Local objects name
 */
function em_localize_map_info($handler)
{
    
    $gmap_api_key = em_global_settings('gmap_api_key');
    $local_objects= array();
    
    if ($gmap_api_key):
        $local_objects['gmap_uri']= 'https://maps.googleapis.com/maps/api/js?key=' . $gmap_api_key . '&libraries=places';
    else:
        $local_objects['gmap_uri']= false;
    endif;
    wp_enqueue_script( 'em-google-map',plugin_dir_url(__DIR__) . '/includes/js/em-map.js',false );
    wp_localize_script($handler, "em_map_info", $local_objects);
   
}

/*
 * Filter for ordering posts by status
 */


/*
 * Convert an associative array into key,label objects for dropdown fields
 */
function em_array_to_options($data= array())
{
    $options= array();
    foreach($data as $key=>$value)
    {
        $option= new stdClass();
        if(is_numeric($key))
            $option->key= $value;
        else
            $option->key= $key;
        
        $option->label= $value;
        $options[]= $option;
    }
    
    return $options;
}

function em_array_sort_by_date($a,$b)
{
    return strtotime($a)>strtotime($b);
}

function get_payment_log_info($booking_id){

     $payment_log= maybe_unserialize(em_get_post_meta($booking_id, 'payment_log', true));     
     $currency_symbol="";              
                    $currency_code= em_global_settings('currency');
                   
                       if( isset($payment_log['payment_gateway']) && ($payment_log['payment_gateway'] == 'paypal' )):
                            $currency_code=$payment_log['mc_currency'];
                       elseif(isset($payment_log['payment_gateway']) &&  $payment_log['payment_gateway'] == 'stripe'):                           
                        $currency_code=$payment_log['currency'];
                       else:
                       endif;   
                    
                    if($currency_code):
                        $all_currency_symbols = EventM_Constants::get_currency_symbol();
                        $currency_symbol = $all_currency_symbols[$currency_code];                        
                    else:
                        $currency_symbol = EM_DEFAULT_CURRENCY;
                    endif;
                    
                    return $currency_symbol;
                    
}

function em_upload_into_media($file,$parent_post_id=0){  
    $sample_image;
    $filename = basename($file);
    $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
            
            
    if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_parent' => $parent_post_id,
        'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content' => '',
        'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
            wp_update_attachment_metadata( $attachment_id,  $attachment_data );
            return $attachment_id;
        }else
            return false;
        
    }
}

function em_redirect_event_posts()
{  
   $postID = url_to_postid( $_SERVER['REQUEST_URI']); 
   $post= get_post($postID);
   $redirect_url='';
   if(!empty($post) && $post->post_status!="trash")
   {   
       $post_type= get_post_type($postID);
       if($post_type=='em_event')
       {
           $page_url= get_permalink(em_global_settings("events_page"));
           $redirect_url= add_query_arg("event",$postID,$page_url);
       }
       elseif($post_type=='em_performer')
       {
           $page_url= get_permalink(em_global_settings("performers_page"));
           $redirect_url= add_query_arg("performer",$postID,$page_url);
       }   
       else
          return; 
       
       wp_redirect($redirect_url);
       exit;
   }
}

function em_check_required_pages()
{ 
    $notices= '';
    $pages= array(
            "events_page"=> array("Event List","[em_events]"),
            "venues_page"=> array("Venues List","[em_venues]"),
            "booking_page"=>array("Booking","[em_booking]"),
            "profile_page"=> array("User Profile","[em_profile]"),
            "performers_page"=>array("Performer List","[em_performers]")
            );
    foreach($pages as $key=>$value)
    {
        $page_id= em_global_settings($key);
        $post= get_post($page_id);
        $short_code_exists= strpos($post->post_content,$value[1]);
        if(empty($post) || $post->post_status=="trash" || $short_code_exists===false)
        {
            $notices .= '<p> For '.$value[0].' use '.$value[1].' shortcode</p>';
        }
    }
    
    if(!empty($notices))
    {
        echo '<div class="notice notice-success is-dismissible">Event Kikfyre: It seems all the required pages are not configured.'.$notices.
           '<b>Note*: Once you have pasted all the shortcodes inside corresponding pages, you can configure the default pages in Kikfyre Global Settings->Default Pages. </b>'.
             '</div>';
    }
        
    
}

 function em_posts_order_by($orderby_statement)
 {  
     return 'post_status DESC,'.$orderby_statement;
 }
