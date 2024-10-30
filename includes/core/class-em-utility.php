<?php

class EventM_Utility {
    
    
    public static function em_is_ssl() {

     
       if ( isset( $_SERVER['HTTPS'] ) ) {
		if ( 'on' == strtolower( $_SERVER['HTTPS'] ) ) { 
			return true;
		}

		if ( '1' == $_SERVER['HTTPS'] ) {
			return true;
		}
	} elseif ( isset($_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
        return false;

    }

    public static function get_currency_symbol() {
        $gs_service = new EventM_Setting_Service();
        $gs = $gs_service->load_model_from_db();

        $currency_symbol = "";
        $currency_code = $gs->currency;

        if ($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];
        else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
        endif;

        return $currency_symbol;
    }

    /**
     * Removed posts 
     * @param Array $ids 
     */
    public static function delete_posts($ids) {
        if(!is_array($ids))
        $ids = explode(',', $ids);

            foreach ($ids as $id) {
                wp_delete_object_term_relationships($id, array(EM_EVENT_VENUE_TAX, EM_EVENT_TYPE_TAX));
                //wp_delete_post($id,true);
                $post = array(
                    'ID' => $id,
                    'post_status' => 'trash',
                );

                // Deleting child events if any
                $child_events = em_get_post_meta($id, 'child_events', true);
                if (!empty($child_events) && is_array($child_events)) {
                    foreach ($child_events as $c_id) {
                        wp_delete_object_term_relationships($c_id, array(EM_EVENT_VENUE_TAX, EM_EVENT_TYPE_TAX));
                        $child_post = array(
                            'ID' => $c_id,
                            'post_status' => 'trash',
                        );
                        wp_update_post($child_post);
                    }
                }
                wp_update_post($post);
            }
    }

    public static function duplicate_posts($ids) {
        global $wpdb;
        if(!is_array($ids))
        $ids = explode(',', $ids);
        $new_post_id = 0;
            foreach ($ids as $key =>  $id) {
                $post = get_post($id);

                /*
                 * if you don't want current user to be the new post author,
                 * then change next couple of lines to this: $new_post_author = $post->post_author;
                 */
                $current_user = wp_get_current_user();
                $new_post_author = $current_user->ID;
                if (isset($post) && $post != null) {
                    $args = array(
                        'comment_status' => $post->comment_status,
                        'ping_status' => $post->ping_status,
                        'post_author' => $new_post_author,
                        'post_content' => $post->post_content,
                        'post_excerpt' => $post->post_excerpt,
                        'post_name' => $post->post_name,
                        'post_parent' => $post->post_parent,
                        'post_password' => $post->post_password,
                        'post_status' => $post->post_status,
                        'post_title' => $post->post_title,
                        'post_type' => $post->post_type,
                        'to_ping' => $post->to_ping,
                        'menu_order' => $post->menu_order
                    );

                    /*
                     * insert the post by wp_insert_post() function
                     */
                    $new_post_id = wp_insert_post($args);
                    /*
                     * get all current post terms ad set them to the new post
                     */

                    // Copying Event Type taxonomy
                    $event_type_term = wp_get_object_terms($id, EM_EVENT_TYPE_TAX, array('fields' => 'slugs'));
                    if (!empty($event_type_term))
                        wp_set_object_terms($new_post_id, $event_type_term, EM_EVENT_TYPE_TAX, false);

                    // Copying Venue taxonomy
                    $venue_term = wp_get_object_terms($id, EM_EVENT_VENUE_TAX, array('fields' => 'slugs'));
                    if (!empty($venue_term))
                        wp_set_object_terms($new_post_id, $venue_term, EM_EVENT_VENUE_TAX, false);

                    /*
                     * duplicate all post meta just
                     */
                    $data = get_post_custom($id);
                    foreach ($data as $key => $values) {
                        if ($key == em_append_meta_key('seats')) {
                            $event_service = new EventM_Service();
                            $event_service->create_seats_from_venue($new_post_id, $event_service->get_venue($id));
                            continue;
                        }
                        if (in_array($key, em_append_meta_key(array('child_events', 'multi_dates', 'parent_event', 'booked_seats')))) {
                            continue;
                        }
                        foreach ($values as $value) {
                            add_post_meta($new_post_id, $key, maybe_unserialize($value));
                        }
                    }
                }
            }

        return $new_post_id;
    }

    public static function delete_terms($ids, $tax_type) {
        if(!is_array($ids))
        $ids = explode(',', $ids);

            foreach ($ids as $id) {
                $posts = em_get_attached_posts($id, $tax_type);

                foreach ($posts as $post):

                    $p = array(
                        'ID' => $post->ID,
                        'post_status' => 'trash'
                    );
                    wp_update_post($p);

                endforeach;
                wp_delete_term($id, $tax_type);
            }
    }

    /**
     * Function to get total number of posts
     */
    public static function get_total_posts($type = 'post') {
        $post_count = wp_count_posts($type);
        return $post_count->publish + $post_count->em_pending + $post_count->em_expired + $post_count->em_cancelled;
    }

    /*
     * Matches each symbol of PHP date format standard
     * with jQuery equivalent codeword
     * @author Tristan Jahier
     */

    public static function dateformat_PHP_to_jQueryUI($php_format) {
        $SYMBOLS_MATCHING = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => ''
        );
        $jqueryui_format = "";
        $escaping = false;
        for ($i = 0; $i < strlen($php_format); $i++) {
            $char = $php_format[$i];
            if ($char === '\\') { // PHP date format escaping character
                $i++;
                if ($escaping)
                    $jqueryui_format .= $php_format[$i];
                else
                    $jqueryui_format .= '\'' . $php_format[$i];
                $escaping = true;
            }
            else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($SYMBOLS_MATCHING[$char]))
                    $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                else
                    $jqueryui_format .= $char;
            }
        }
        return $jqueryui_format;
    }

    public static function create_date_range($strDateFrom, $strDateTo) {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }
    
    public static function dates_from_range($start, $end) 
    {
    $array= array();    
    $interval = new DateInterval('P1D');
    
    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(
         new DateTime($start),
         $interval,
         $realEnd
    );

    foreach($period as $date) { 
        $array[] = $date->format('Y-m-d'); 
    }

    return $array;
    }
    
    
    public static function em_get_converted_price_in_cent($price,$currency)
    {
        if(self::is_price_conversion_req_for_stripe($currency))
            return $price*100;
        return $price;
    }
    
    public static function convert_fr($currency,$price)
    {
        if(is_price_conversion_req_for_stripe($currency))
            return $price/100;
        return $price;
    }
    
    public static function is_price_conversion_req_for_stripe($currency)
    {$currency= strtoupper($currency);
        switch($currency)
        {
            case 'BIF':
            case 'DJF':
            case 'JPY':
            case 'KRW':
            case 'PYG':
            case 'VND':
            case 'XAF':
            case 'XPF':
            case 'CLP':
            case 'GNF':
            case 'KMF':
            case 'MGA':
            case 'RWF':
            case 'VUV':
            case 'XOF':
                return false;
            default:
                return true;
        }
        return false;
    }
}
