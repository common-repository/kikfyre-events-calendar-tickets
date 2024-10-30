<?php

/**
 *
 * Service class for Events
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Service class
 */
class EventM_Service {

    private $dao;

    public function __construct() {
        $this->dao = new EventM_Event_DAO();
    }

    /*
     * Load Add/Edit page for REST 
     */

    public function load_edit_page() {

        $response = new stdClass();
        $stringsModel = new EventM_Strings_Model();
        $id = event_m_get_param('post_id');
        $event = $this->load_model_from_db($id);

        if (empty($event)) {
            $error = new WP_Error('NON_EXIST_EVENT', "Event does not exists");
            echo wp_json_encode($error);
            wp_die();
        }
        $response->post = $event->to_array();
       
        
        $response->post['venues'] = $this->get_venues_dropdown();
        $response->post['date_format'] = "mm/dd/yy";
        $response->post['cover_image_id'] = get_post_thumbnail_id($id);
        $response->post['cover_image_url'] = $this->get_event_cover_image($id);
        $response->post['images'] = $this->get_event_images($response->post['gallery_image_ids']);
        $response->post['sponser_images'] = $this->get_event_images($response->post['sponser_image_ids']);
        $response->post['recurring_options'] = $this->get_recurring_options($id);
        $response->post['recurrence_intervals'] = array(EventM_UI_Strings::get("LABEL_SELECT"), "Weekly", "Monthly", "Annually");


        // If it's a child event
        if (!empty($event->parent_event))
            $response->post['is_child'] = true;


        // Loading Currency symbol
        $global_settings = new EventM_Global_Settings_Model();
        $response->post['currency'] = $this->loadCurrency();
        $response->post['recurring_specific_dates'] = $event->recurring_specific_dates;
        $response->post['status_list'] = em_array_to_options(array("publish" => "Active", "em-expired" => "Unpublished"));
        $response->post['performers'] = $this->get_performers_dropdown();
        $response->post['event_types'] = $this->get_types_dropdown();
        $response->post['ticket_templates'] = $this->get_ticket_dropdown();
        $response->post['child_events'] = $this->get_child_event_list($id, true);
        $response->post['children'] = $this->get_child_event_list($id);

        foreach ($response->post['children'] as $data):
            if ($event->start_date > $data['start_date']):
                $response->start_date_error = 'Event' . $data['name'] . 'is starting from' . $data['start_date'];
            endif;
        endforeach;


        $response->rm_forms = $this->get_rm_forms();
        $response->trans = $stringsModel->addEvent();
        $response->links = new stdClass();
        $response->links->child_cancel = admin_url('/admin.php?page=em_add&post_id=');
        $response->links->cancel = admin_url('/admin.php?page=event_magic');
        $response->links->add_new_event = admin_url('/admin.php?page=em_add');
        $response->links->edit_child_event = admin_url('/admin.php?page=em_child_edit');
        //display seats when no child event is created
//        if(count($response->post['children']) >0){
//            $response->post['seats']= array();
//        }

        echo wp_json_encode($response);
        wp_die();
    }

    /*
     * Load List page for REST 
     */

    public function load_list_page() {
        $gs= new EventM_Setting_Service();
        $gs_model= $gs->load_model_from_db();
        
        $response = new stdClass();
        $stringsModel = new EventM_Strings_Model();
        $response->posts = array();
        $hideExpired = event_m_get_param('hideExpired');
     
        if($hideExpired==true){
            $gs_model->set_hide_expired_from_admin(1);
        } else{
            
            $full_load= event_m_get_param('full_load');
            if(!$full_load){
                $gs_model->set_hide_expired_from_admin(0);
            }
                
        }
        
        $gs_array= $gs_model->to_array();
        $gs->save($gs_array);
        
        $sort_option = event_m_get_param('sort_option');
        $response->sort_option = $sort_option;
        $response->hideExpired =  $gs_model->get_hide_expired_from_admin();

        $post_status= array('publish', 'em-expired');
        if ($gs_model->get_hide_expired_from_admin()==1){
            $post_status= array('publish');
        }
            
        
        $args = array(
            'posts_per_page' => EM_PAGINATION_LIMIT,
            'offset' => ((int) event_m_get_param('paged') - 1) * EM_PAGINATION_LIMIT,
            'order' => event_m_get_param('order'),
            'post_type' => 'em_event',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => em_append_meta_key('parent_event'),
                    'value' => 0,
                    'compare' => '=',
                    'type' => 'NUMERIC,'
                ),
                array(
                    'key' => em_append_meta_key('parent_event'),
                    'compare' => 'NOT EXISTS',
                )
            ),
            'post_status' => $post_status);
      
        
        

        if ($sort_option == "title") {
            $args['orderby'] = $sort_option;
        }

        if ($sort_option == "date") {
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = em_append_meta_key("start_date");
        }

        if ($sort_option == "filled") {
            $args['orderby'] = array('meta_value_num' => 'DESC');
            $args['meta_key'] = em_append_meta_key("booked_seats");
        }
        $events = get_posts($args);

        
        add_filter('posts_orderby', 'em_posts_order_by'); 
        $wp_query= new WP_Query($args);
        $events = $wp_query->posts;
        remove_filter('posts_orderby','em_posts_order_by');

        if (is_array($events)) {
            foreach ($events as $p) {
                $post = new stdClass();
                $event = $this->load_model_from_db($p->ID);
                $post->sum = $this->booked_seats($event->id);
                $post->id = $event->id;
                $post->name = $event->name;
                $post->is_expired = em_is_event_expired($event->id);
                $venue_terms = wp_get_object_terms($event->id, EM_EVENT_VENUE_TAX);

                if (!empty($venue_terms))
                    $post->venue_name = $venue_terms[0]->name;

                $post->capacity = em_event_seating_capcity($event->id);
                $post->between = $event->start_date . " to " . $event->end_date;
                $post->child_events= $this->dao->get_meta($event->id, 'child_events');
                // Cover Image 
                $cover_image_id = get_post_thumbnail_id($event->id);
                if (!empty($cover_image_id) && $cover_image_id > 0) {
                    $cover_image = wp_get_attachment_image_src($cover_image_id, 'large');
                    $post->cover_image_url = $cover_image[0];
                }

                $response->posts[] = $post;
            }
        }


        $response->trans = $stringsModel->listEvents();
        $response->trans->sort_options = $this->event_sorting_dropdown();
        $response->links = new stdClass();
        $response->links->add_new_event = admin_url('/admin.php?page=em_add');
        $response->total_posts = range(1, $this->get_total_events($args));


        $response->event_ids = array();
        
       
        $arg = array(
            'numberposts' => -1,
            'post_type' => 'em_event',
            
            );
       

        $post_data = get_posts($arg);
        if (is_array($post_data)) {
            foreach ($post_data as $p) {
                $response->event_ids[] = $p->ID;
            }
        }

        $response->pagination_limit = EM_PAGINATION_LIMIT;
        echo json_encode($response);
        wp_die();
    }

    public function get_child_name($id) {
        $child_event = get_post($id);
        //  $child_event_name = $child_event->post_title;
        return $child_event->post_title;
        //  return $child_event_name;
    }

    public function get_total_events($args = array()) {
        if (isset($args['posts_per_page']))
            unset($args['posts_per_page']);
        $args['numberposts'] = -1;
        $posts = get_posts($args);
        $total_count = count($posts);
        return $total_count;
    }

    public function get_event_cover_image($id, $size = 'thumbnail') {
        $cover_image_url = get_the_post_thumbnail_url($id, $size);
        if ($cover_image_url === false)
            return "";
        else
            return $cover_image_url;
    }

    public function get_event_images($image_ids) {
        //  Gallery Image Ids
        $gallery_image_ids = maybe_unserialize($image_ids);
        $images = array();

        if (!empty($gallery_image_ids) && $gallery_image_ids != "") {
            $image_ids = array_unique($gallery_image_ids);
            foreach ($image_ids as $image_id) {
                $tmp = new stdClass();
                $tmp->src = wp_get_attachment_image_src($image_id);
                $tmp->id = $image_id;
                $images[] = $tmp;
            }
        }

        return $images;
    }

    public function get_rm_forms() {
        $rm_forms = array();
        // Registration Magic Integration
        if (is_registration_magic_active()) {
            $where = array("form_type" => 1);
            $data_specifier = array('%d');
            $forms = RM_DBManager::get('FORMS', $where, $data_specifier, 'results', 0, 99999, '*', $sort_by = 'created_on', $descending = true);
            $form_dropdown_array = array();
            $form_dropdown_array[0] = "Select Form";
            if ($forms)
                foreach ($forms as $form)
                    $form_dropdown_array[$form->form_id] = $form->form_name;
            $rm_forms = $form_dropdown_array;
        }
        return $rm_forms;
    }

    public function save($model) {
        
        $id = isset($model->id) ? $model->id : 0;
        $model= $this->map_request_to_model($id,$model);

        // If admin chosen to add new Performer(s)
        $performer_dao= new EventM_Performer_DAO();
        $custom_performer_name = event_m_get_param('custom_performer_name');
        if ($custom_performer_name != null && !empty($custom_performer_name)) {
          
            // Check if multiple performers given seperated by comma
            $performers = explode(',', $custom_performer_name);
            $performer_ids = array();
            foreach ($performers as $performer_name):
                if (!empty($performer_name)):
                
                    $performer= new EventM_Performer_Model();
                    $performer->name= $performer_name;
                    $performer->description= "";
                    $performer_id = $performer_dao->save($performer);
                
                    // In case of any errors
                    if (!($performer_id instanceof WP_Error)) {
                        $performer_ids[] = $performer_id;
                        
                        // Inserting mandatory fields 
                        em_update_post_meta($performer_id, 'type', 'person');
                    }
                endif;
            endforeach;

            $model->set_performer($performer_ids);
        }
        
        $event_id= $this->dao->save($model);
        // In case of any errors
        if ($event_id instanceof WP_Error) {
            return $event_id;
        }
        $event = $this->load_model_from_db($event_id);

        // If admin chosen to add new Event Type
        $type_dao = new EventM_Event_Type_DAO();
        $new_event_type = event_m_get_param('new_event_type');
        if (!empty($new_event_type)) {
            $event_type_service = new EventTypeM_Service();
            $event_type = $event_type_service->map_request_to_model(0, array('name' => $new_event_type));
            $type = $type_dao->save($event_type);
            $this->dao->set_type($event->id, $type['term_id']);
        } else {
            $type = $model->event_type;
            if ($type > 0)
                $this->dao->set_type($event->id, $type);
        }

        // If admin chosen to add new Venue


        $new_venue = event_m_get_param('new_venue');
        if (!empty($new_venue)) {

            $venue_service = new EventM_Venue_Service();
            $venue = $venue_service->map_request_to_model(0, array('name' => $new_venue,'type'=>'standings'));
            $venue_dao = new EventM_Venue_DAO();
            $venue_id = $venue_dao->save($venue);
            $this->dao->set_venue($event->id, $venue_id['term_id']);
        } else {
            // Save Venue info
            $venue = $model->venue;
            if ($venue > 0)
                $this->dao->set_venue($event->id, $venue);
        }

        // Copying seat structure from Venue
        $this->create_seats_from_venue($event->id, $venue);
        $this->save_organizer($event->id, $model);



        // Set Feature image
        $cover_image_id = $event->cover_image_id;
        if ($cover_image_id != null && (int) $cover_image_id > 0) {
            $this->dao->set_thumbnail($event->id, $cover_image_id);
        }

        // If cover image not uploaded then set first image from gallery
        if ($cover_image_id == null || (int) $cover_image_id == 0) {
            $gallery_image_ids = explode(',', $event->gallery_image_ids);
            if (is_array($gallery_image_ids)) {
                $this->dao->set_thumbnail($event->id, $gallery_image_ids[0]);
            }
        }       


        // Check if this is a multiday event
        $this->save_multiday_events($event->id, $model);

        // Update info in child events


        $children = $this->get_child_event_list($event->id, true);

        $venue_service = new EventM_Venue_Service();
        $child_events_data = em_get_post_meta($event->id, 'child_events', true);

        foreach ($children as $key => $ch_id) {

            $child = $this->load_model_from_db($ch_id);
           
            if (!$child_events_data) {
             
                $child->set_description($event->description);
                $child->set_allow_cancellations($event->allow_cancellations);
                $child->set_ticket_price($event->ticket_price);
                $child->set_ticket_template($event->ticket_template);
                $child->set_seating_capacity($event->seating_capacity);
                $child->set_max_tickets_per_person($event->max_tickets_per_person);
                $child->set_allow_discount($event->allow_discount);
                $child->set_discount_no_tickets($event->discount_no_tickets);
                $child->set_discount_per($event->discount_per);
                
            }
         
               
           

            // $child->set_available_seats($event->available_seats);

            $child->set_hide_event_from_calendar($event->hide_event_from_calendar);
            $child->set_hide_event_from_events($event->hide_event_from_events);
            $child_seats = em_get_post_meta($ch_id, 'seats');

            // Check for venue changes
            // if(!$child_events_data || empty($child_seats[0])){

            $child_event_venue_id = $this->get_venue($ch_id);
            $parent_event_venue_id = $this->get_venue($event->id);
          
            if ($child_event_venue_id != $parent_event_venue_id) {
                $this->dao->set_venue($ch_id, $parent_event_venue_id);
                $child->set_seats($venue_service->get_seats($parent_event_venue_id));
            }
            else if(empty($child_seats)){
            
                  $child->set_seats($event->get_seats());
            }
            //}

            $this->dao->save($child);
            
           
        }
        return $event;
    }

    /*
     * Create child events in case event last for multiple days
     */

    private function save_multiday_events($event, $model) {
        // Check if child events exists in database

        $child_events_data = em_get_post_meta($event, 'child_events', true);
        if ($child_events_data)
            return;

        $multi_dates = em_get_post_meta($event, 'multi_dates', true);
        usort($multi_dates, "em_array_sort_by_date");
        $last_booking_date = em_get_post_meta($event, 'last_booking_date', true);
        $child_events = array();

        if (!empty($multi_dates)) {
            // Removing recurring option if any
            em_update_post_meta($event, 'recurring_option', "");

            // Create child events
            foreach ($multi_dates  as $key => $date) {

                $child = EventM_Utility::duplicate_posts($event);

                // Updating data for  child
            $child_post = array(
                 'ID'           => $child,
                 'post_title'   => 'Day ' . ++$key . ' - ' . $model->name

             );
            wp_update_post( $child_post );
                $end_time = em_get_time(em_time($model->end_date));

                $start_time = em_get_time(em_time($model->start_date));
                $ticket_price = $model->ticket_price;
                //  $description = $model->description;
//                    $allow_discount = $model->allow_discount;
//                    $discount_no_tickets = $model->discount_no_tickets;
//                    $discount_per = $model->discount_per;




                em_update_post_meta($child, 'start_date', em_time($date . " $start_time"));
                em_update_post_meta($child, 'end_date', em_time($date . $end_time));
                em_update_post_meta($child, 'last_booking_date', em_time($date . "$start_time"));
                em_update_post_meta($child, 'parent_event', $event);
                em_update_post_meta($child, 'ticket_price', $ticket_price);
                // em_update_post_meta($child,'description',$description);
//                    em_update_post_meta($child, 'ticket_price_discount', $allow_discount);
//                    em_update_post_meta($child, 'discount_no_tickets', $discount_no_tickets);
//                    em_update_post_meta($child, 'discount_per', $discount_per);

                if ($last_booking_date >= em_time($date . " $end_time")) {
                    em_update_post_meta($child, 'last_booking_date', em_time($date . " $start_time"));
                }

                $child_events[] = $child;
                //    endif;
            }
        }

        if (!empty($child_events))
            em_update_post_meta($event, 'child_events', $child_events);
    }

    private function save_organizer($event, $data) {
     
        $organizer_info = array();
        $organizer_info['organizer_name'] = $data->organizer_name;
        $organizer_info['organizer_contact_details'] = $data->organizer_contact_details;
        $organizer_info['hide_organizer'] = $data->hide_organizer;
        em_update_post_meta($event, 'org_info', $organizer_info);
    }

    private function update_meta_before_save($meta, $model) {

        if (is_array($meta)) {
            foreach ($meta as $value) {
                if ($value == "recurring_option" && $model->$value == "recurring") {
                    $index = array_search('recurring_specific_dates', $meta);
                    unset($meta[$index]);
                }

                if ($value == "recurring_option" && $model->$value == "specific_dates") {

                    $index = array_search('recurring_date', $meta);
                    unset($meta[$index]);

                    $index = array_search('recurrence_interval', $meta);
                    unset($meta[$index]);
                }

                if ($value == "allow_discount" && 0 == (int) $model->$value) {
                    $index = array_search('discount_no_tickets', $meta);
                    unset($meta[$index]);

                    $index = array_search('discount_per', $meta);
                    unset($meta[$index]);
                }
            }
        }

        return $meta;
    }
    
    public function get_venue_capcity($venue_id,$event_id)
    {
        $response= array();
//        $venue_id= event_m_get_param('venue_id');
//        $event_id = event_m_get_param('event_id');
       
        $service= new EventM_Venue_Service();
        $response['capacity']= (int) $service->capacity($venue_id); 
        $response['seats']= $service->get_seats($venue_id,$event_id);
        
        return $response;
    }
    
    public function validate($model, $response) {
        // Validating dates
        $seating_capacity = $model->seating_capacity;
        
        $venue_capacity = $this->get_venue_capcity($model->venue,$model->id);
        if($venue_capacity['capacity']< $seating_capacity){
             $response->error_status = true;
             $response->errors[] = "Capacity exceeded more than the Venue capacity";
             $response->redirect = false;
        }
       
        
        $start_date = em_time($model->start_date);
        $end_date = em_time($model->end_date);
        $last_booking_date = em_time($model->last_booking_date);
        $start_booking_date = em_time($model->start_booking_date);
        
        $venue_error= false;
        if($model->venue=='new_venue' && term_exists($model->new_venue,EM_EVENT_VENUE_TAX))
               $venue_error= true;
        else if(term_exists($model->venue,EM_EVENT_VENUE_TAX))
               $venue_error= true;  
        
        if($venue_error)
        {
             $response->error_status = true;
             $response->errors[] = "Please use different Venue name";
             $response->redirect = false;
        }
        
        $event_type_error= false;
        if($model->event_type=='new_event_type' && term_exists($model->new_event_type,EM_EVENT_TYPE_TAX))
               $event_type_error= true;
        else if(term_exists($model->event_type,EM_EVENT_TYPE_TAX))
               $event_type_error= true;  
        
        if($event_type_error)
        {
             $response->error_status = true;
             $response->errors[] = "Please use different Event Type";
             $response->redirect = false;
        }
        if ($start_date > $end_date) {
            $response->error_status = true;
            $response->errors[] = "Event Start date should be prior to Event End date";
            $response->redirect = false;
        }

        if ($last_booking_date > $end_date) {
            $response->error_status = true;
            $response->errors[] = "Last booking date can not be greater than End date";
            $response->redirect = false;
        }

        if ($start_booking_date > $last_booking_date) {
            $response->error_status = true;
            $response->errors[] = "Start booking date should be earlier than the Last Booking date";
            $response->redirect = false;
        }

        if ($start_booking_date > $start_date) {
            $response->error_status = true;
            $response->errors[] = "Start booking date must be earlier than Event Start date";
            $response->redirect = false;
        }

        $child_events = $model->child_events;
        if (is_array($child_events)) {
            foreach ($child_events as $child_event):
                $child_start_date = em_get_post_meta($child_event, 'start_date');
                $child_end_date = em_get_post_meta($child_event, 'end_date');


                if (strtotime($model->start_date) > $child_start_date[0]):
                    $response->error_status = true;
                    $response->errors[] = "You cannot set event day to begin before the event start time";
                    $response->redirect = false;
                    break;
                endif;

                if (strtotime($model->end_date) < $child_end_date[0]):
                    $response->error_status = true;
                    $response->errors[] = "You cannot set event day to end after the event end time.";
                    $response->redirect = false;
                    break;
                endif;

            endforeach;
        }
        // Check if specific dates option selected
        $recurring_option = $model->recurring_option;

        if (strtolower($model->recurring_option) == 'specific_dates' && empty($model->recurring_specific_dates)) {
            $response->error_status = true;
            $response->errors[] = "Recurrence dates are not given.";
            $response->redirect = false;
        }

        return $response;
    }

    public function get_events_the_query() {
        $setting_service = new EventM_Setting_Service();
        $gs = $setting_service->load_model_from_db();
        $hide_past_events = $gs->hide_past_events;

        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        // if($hide_past_events!=1):
        $args = array(
            'meta_key' => em_append_meta_key('start_date'),
            'numberposts' => -1,
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'posts_per_page' => 10,
            'paged' => $paged,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => em_append_meta_key('hide_event_from_events'),
                    'value' => '1',
                    'compare' => '!='
                ), $this->dao->exclude_child_query()
            ),
            'post_type' => EM_EVENT_POST_TYPE);
        $args['post_status'] = $hide_past_events == 1 ? 'publish' : array();
        $args['post__in']= array();
        // Check if any event IDs in search parameter
        $search = event_m_get_param('em_s');
        $filter_results = array();

        if ($search == "1"):
            $start_date = event_m_get_param('em_sd');
            if (!empty($start_date)):
                $event_dao = new EventM_Event_DAO();
               
              $filter_results[]= $event_dao->get_events_by_start_date($start_date,true);

            endif;
           
            $search = event_m_get_param('em_search');
            if (!empty($search)):
                $events = $this->searchByText($search);
                $filter_results[] = $events;
            endif;

            $types = event_m_get_param('em_types');
            $types_results = array();
            if (!empty($types)):
                if (is_array($types)):
                    foreach ($types as $type):
                        $events = $this->searchByType($type, $args['post__in']);
                        $types_results = array_merge($types_results, $events);
                    endforeach;
                else:
                    $types_results = $this->searchByType($types, $args['post__in']);
                endif;

                $filter_results[] = $types_results;
            endif;

            $venues = event_m_get_param('em_venues');

            if (!empty($venues)):
                // foreach($venues as $venue):
                $events = $this->searchByVenue($venues, $args['post__in']);
                $filter_results[] = $events;
            //  endforeach; 
            endif;

        
            foreach ($filter_results as $index => $rs) {
                if (empty($rs))
                    break;

                if (!empty($args['post__in']))
                    $args['post__in'] = array_merge($args['post__in'], $rs);
                else
                    $args['post__in'] = $rs;
            }


            if (empty($args['post__in'])):
                $args = array();
            endif;
        endif;


        add_filter('posts_orderby', 'em_posts_order_by');
        $wp_query = new WP_Query($args);
        remove_filter('posts_orderby', 'em_posts_order_by');
        return $wp_query;
    }

    public function get_upcoming_events($exclude = array()) {
        $event_dao = new EventM_Event_DAO();
        $data = $event_dao->get_upcoming_events();
        if (!empty($data)):
            return $data;
        else:
            return array();
        endif;
    }

    // Changes expired event dates for recurring events
    public function recurringEventAdjustment() {
        $event_dao = new EventM_Event_DAO();
        $events = $event_dao->get_past_events();

        if (!empty($events)):
            foreach ($events as $event):
                $event_dates = $event_dao->get_recurring_event_dates($event);
                $start_date = $this->get_upcoming_date($event_dates);

                if (!empty($start_date)):
                    $start_time = date("H:i", em_get_post_meta($event->ID, 'start_date', true));

                    $start_end_diff = em_get_post_meta($event->ID, 'end_date', true) - em_get_post_meta($event->ID, 'start_date', true);
                    $start_last_diff = em_get_post_meta($event->ID, 'last_booking_date', true) - em_get_post_meta($event->ID, 'start_date', true);

                    em_update_post_meta($event->ID, 'start_date', em_time($start_date . " $start_time"));
                    em_update_post_meta($event->ID, 'end_date', em_time($start_date . " $start_time") + $start_end_diff);
                    em_update_post_meta($event->ID, 'last_booking_date', em_time($start_date . " $start_time") + $start_last_diff);

                    wp_update_post(array('ID' => $event->ID, 'post_status' => 'publish'));
                endif;
            endforeach;
        endif;
    }

    private function get_upcoming_date($dates = array()) {
        $upcoming_date = null;

        if (!empty($dates)):
            foreach ($dates as $date):
                if (strtotime($date) >= strtotime(date('Y-m-d'))):
                    $upcoming_date = $date;
                    break;
                endif;
            endforeach;
        endif;

        return $upcoming_date;
    }

    public function get_upcoming_recurring_events($exclude = array()) {
        $event_dao = new EventM_Event_DAO();
        $data = $event_dao->get_upcoming_recurring_events();

        if (count($exclude) > 0):
            foreach ($data->ids as $key => $id):
                if (in_array($id, $exclude)):
                    unset($data->ids[$key]);
                endif;
            endforeach;
        endif;

        if (!empty($data->ids)):
            $filter = array('orderby' => 'date', 'order' => 'DESC',
                'include' => $data->ids,
                'post_type' => EM_EVENT_POST_TYPE);
            return $event_dao->get_events($filter);
        else:
            return array();
        endif;
    }

    public function get_data_for_slider() {
        $data = new stdClass();
        $data->image_ids = array();
        $data->links = array();
        $data->ids = array();
        $args = array(
            
         
            'numberposts'=>-1,
            'order' => 'ASC',  
            'post_status'=> 'publish',          
            'meta_query' => array( 'relation'=> 'AND',// WordPress has all the results, now, return only the events after today's date
               array(
               'relation'=> 'AND',
                array(
                                        'key' => em_append_meta_key('hide_event_from_events'), 
                                        'value' => '1', //
                                        'compare' => '!='
                 ),    array(
                'key'     => em_append_meta_key('hide_event_from_calendar'),
		'value'   => '1',
                'compare' => '!='            
               ),   
                   
                array(   
               'relation' => 'OR',
                array(
                    'key' => em_append_meta_key('start_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                ),
                array(
                    'key' => em_append_meta_key('end_date'), // Check the start date field
                    'value' => current_time( 'timestamp' ), // Set today's date (note the similar format)
                    'compare' => '>=', // Return the ones greater than today's date
                     // Let WordPress know we're working with numbers
                ))),
        
                    array(
                   'key' => em_append_meta_key('parent_event'), 
                   'value' => 0, 
                   'compare' => '>'

                    )
                    ),   
            'post_type' => EM_EVENT_POST_TYPE);

        $events = get_posts($args);
      

        if (!empty($events)):
            foreach ($events as $event):
        
                $image_id = get_post_thumbnail_id($event->ID);
                if (!empty($image_id)) {
                    $data->image_ids[] = $image_id;
                }
                else{
                    $data->image_ids[]='';
             }
                    $parent_event_id = em_get_post_meta($event->ID,'parent_event',true);
                    $parent_posts = get_post($parent_event_id);
                    $data->parent_event[] =  $parent_posts->post_title;
                    $data->links[] = get_permalink($parent_event_id);
                   
                    $data->ids[] = $event->ID;
                

            endforeach;
           
        endif;

        return $data;
    }

    public function searchByText($text, $exclude = array()) {
        $search_query = new WP_Query();
        $events = $search_query->query('s=' . $text);
        $event_ids = array();
        // echo'<pre>'; var_dump($events); 

        foreach ($events as $key => $event):
            if (!in_array($event->ID, $exclude)):
                $event_ids[] = $event->ID;
            endif;
        endforeach;

        return $event_ids;
    }

    public function searchByType($type, $exclude = array()) {
        $event_ids = array();
        $type_dao = new EventM_Event_Type_DAO();
        $events = $type_dao->getAttachedEvent($type);

        foreach ($events as $key => $event):
            if (!in_array($event->ID, $exclude)):
                $event_ids[] = $event->ID;
            endif;
        endforeach;


        return ($event_ids);
    }

    public function searchByVenue($venue, $exclude = array()) {
        $venue_ids = array();
        $event_ids = array();

        $event_dao = new EventM_Event_DAO();
        $events = $event_dao->getAttachedEvents($venue);

        foreach ($events as $key => $event):
            if (!in_array($event->ID, $exclude)):
                $event_ids[] = $event->ID;
            endif;
        endforeach;

        return ($event_ids);
    }

    public function update_past_event_status() {
        $event_dao = new EventM_Event_DAO();
        $events = $event_dao->get_past_events();

        if (!empty($events)):
            foreach ($events as $event):
                wp_update_post(array('ID' => $event->ID, 'post_status' => 'em-expired'));
            endforeach;
        endif;
    }

    /*
     * Copy Seating structure from Venue
     */

    public function create_seats_from_venue($event_id, $venue_id) {
        $data = new stdClass();
        $event_seats = array();
        $venue_seats = array();

        $event_seats = em_get_post_meta($event_id, 'seats', true);
        $venue_seats = em_get_term_meta($venue_id, 'seats', true);
     
        $data->event_id = $event_id;

        if (empty($venue_seats)) { 
            $data->seats = array();
            em_update_post_meta($event_id, 'seats', array());
            return $data;
        }
        // Copy seat structure in case it not copied already
        if (empty($event_seats)) {  
            em_update_post_meta($event_id, 'seats', $venue_seats);
          
            $data->seats = $venue_seats;
            return $data;
        } else {
            $event_seats = em_get_post_meta($event_id, 'seats', true);
            $data->seats = $event_seats;
            return $data;
        }
        
         
    }

    public function get_all_events() {
        $event_dao = new EventM_Event_DAO();
        return $event_dao->get_events();
    }

    public function get_post($ID) {
        $args = array(
            'author' => $ID,
            'orderby' => 'post_date',
            'order' => 'ASC',
            'post_type' => EM_EVENT_POST_TYPE
        );
        $details = get_posts($args);
        return $details;
    }

    public function get_venue($event_id) {
        $venue = $this->dao->get_venue($event_id);
        if (!empty($venue))
            return $venue->term_id;
        return null;
    }

    public function get_performer($event_id) {

        $performer = em_get_post_meta($event_id, 'performer', true);
        return $performer;
        /*
          if($performer[0]=="0"){
          $post_performer = get_posts($event_id, EM_PERFORMER_POST_TYPE);
          if(empty($post_performer))
          return 0;
          else
          return $post_performer[0]->ID;
          }

          return (int)$performer[0];

         */
    }

    public function get_type($event_id) {
        $type = $this->dao->get_type($event_id);
        if (!empty($type))
            return $type->term_id;
        return null;
    }

    public function get_events($filter) {
        return $this->dao->get_events($filter);
    }

    public function available_seats($event_id) {
        return $this->dao->available_seats($event_id);
    }

    public function booked_seats($event_id) {
        return (int) $this->dao->booked_seats($event_id);
    }

    public function get_recurring_event_dates_for_calendar() {
        $data = $this->dao->get_upcoming_recurring_events();
        if (count($data) > 0) {
            foreach ($data->event_dates as $key => $event_start_date) {
                $hide_from_calendar = (int) em_get_post_meta($data->ids[$key], 'hide_event_from_calendar', true);
                if ($hide_from_calendar == 1) {
                    unset($data->event_dates[$key]);
                    unset($data->ids[$key]);
                }
            }
        }

        return $data;
    }

    public function get_header() {
        global $post;
        $local_objects = array();
        $google_cal_client_id = em_global_settings('google_cal_client_id');
        $google_cal_api_key = em_global_settings('google_cal_api_key');


        wp_enqueue_script('jquery-ui-tabs', array('jquery'));

        get_header();

        wp_enqueue_script("em-single-event", plugin_dir_url(__DIR__) . 'templates/js/em-single-event.js');

        if ((int) em_global_settings('gcal_sharing') > 0):
            wp_enqueue_script("em-gcal", plugin_dir_url(__DIR__) . 'templates/js/em-gcal.js');
            wp_enqueue_script("em-google-client", "https://apis.google.com/js/client.js?onload=em_gcal_handle");
            wp_localize_script("em-gcal", "em_local_gcal_objects", array("gc_id" => $google_cal_client_id, "g_api_key" => $google_cal_api_key));
        endif;

        if ((int) em_global_settings('social_sharing') > 0):
            $fb_api_key = em_global_settings('fb_api_key');
            if (!empty($fb_api_key)):
                $local_objects["social_sharing"] = 1;
                $local_objects["fb_api"] = $fb_api_key;
            endif;

        endif;

        $img_path = get_the_post_thumbnail_url(em_get_post_meta($post->ID, 'thumbnail_id', true));
        if (empty($img_path))
            $img_path = esc_url(EM_BASE_URL . 'includes/templates/images/dummy_image _single_em_event.png');

        $local_objects["fb_event_img"] = $img_path;
        $local_objects["fb_event_href"] = get_permalink();

        wp_localize_script("em-single-event", "em_local_event_objects", $local_objects);
        em_localize_map_info("em-google-map");
    }

    public function load_model_from_db($id) {
        return $this->dao->get($id);
    }

    public function get_venues_dropdown() {
        $venue_dao = new EventM_Venue_DAO();
        $dropdown = array();
        $venues = $venue_dao->get_all();
        // Insert default value 
        $tmp = new stdClass();
        $tmp->id = 0;
        $tmp->name = EventM_UI_Strings::get("LABEL_SELECT");
        $dropdown[] = $tmp;

        if ($venues != null) {
            foreach ($venues as $venue) {
                $tmp = new stdClass();
                $tmp->id = $venue->term_id;
                $tmp->name = $venue->name;
                $tmp->seating_capacity = (int) em_get_term_meta($venue->term_id, "seating_capacity", true);
                $dropdown[] = $tmp;
            }
        }

        $tmp = new stdClass();
        $tmp->name = EventM_UI_Strings::get("label_add_new_venue");
        $tmp->id = "new_venue";
        $dropdown[] = $tmp;

        return $dropdown;
    }

    public function get_performers_dropdown() {
        $performer_dao = new EventM_Performer_DAO();
        $dropdown = array();
        $performers = $performer_dao->getPerformers();

        $tmp = new stdClass();
        $tmp->id = 0;
        $tmp->name = EventM_UI_Strings::get("LABEL_SELECT");
        $dropdown[] = $tmp;

        if ($performers != null) {
            foreach ($performers as $performer) {
                $tmp = new stdClass();
                $tmp->name = $performer->post_title;
                $tmp->id = $performer->ID;
                $dropdown[] = $tmp;
            }
        }

        $tmp = new stdClass();
        $tmp->name = EventM_UI_Strings::get("label_new_performer");
        $tmp->id = "new_performer";
        $dropdown[] = $tmp;

        return $dropdown;
    }

    public function get_types_dropdown() {
        $type_dao = new EventM_Event_Type_DAO();
        $event_types = array();
        $types = $type_dao->getTypes();

        // Insert default value 
        $tmp = new stdClass();
        $tmp->id = 0;
        $tmp->name = EventM_UI_Strings::get("LABEL_SELECT");
        $event_types[] = $tmp;

        if ($types != null) {
            foreach ($types as $type) {
                $tmp = new stdClass();
                $tmp->name = $type->name;
                $tmp->id = $type->term_id;
                $event_types[] = $tmp;
            }
        }

        $tmp = new stdClass();
        $tmp->name = EventM_UI_Strings::get("label_add_event_type");
        $tmp->id = "new_event_type";
        $event_types[] = $tmp;

        return $event_types;
    }

    public function get_ticket_dropdown() {
        $dropdown = array();
        $ticket_dao = new EventM_Event_Ticket_DAO();
        $templates = $ticket_dao->getTemplates();

        $tmp = new stdClass();
        $tmp->id = 0;
        $tmp->name = EventM_UI_Strings::get("LABEL_SELECT");
        $dropdown[] = $tmp;

        if ($templates != null) {
            foreach ($templates as $template) {
                $tmp = new stdClass();
                $tmp->name = $template->post_title;
                $tmp->id = $template->ID;
                $dropdown[] = $tmp;
            }
        }

        return $dropdown;
    }

    /*
     * Loads child event's data
     */

    public function get_child_event_list($id, $ids_only = false) {
        $child_events = array();
        $list = em_get_post_meta($id, 'child_events', true);
        if ($ids_only) {
            return $list;
        }

        if (!empty($list) && is_array($list)) {
            foreach ($list as $id) {
                $event = $this->load_model_from_db($id);
                $child_events[] = $event->to_array();
            }
        }

        return $child_events;
    }

    public function get_recurring_options($post_id) {
        $recurring_options = array();
        $options = array("recurring" => EventM_UI_Strings::get('LABEL_RECURRING'), "specific_dates" => EventM_UI_Strings::get('LABEL_SPECIFIC_DATES'));

        $tmp = new stdClass();
        $tmp->key = "";
        $tmp->name = EventM_UI_Strings::get("LABEL_SELECT");
        $recurring_options[] = $tmp;

        foreach ($options as $key => $name) {
            $tmp = new stdClass();
            $tmp->key = $key;
            $tmp->name = $name;
            $recurring_options[] = $tmp;
        }

        return $recurring_options;
    }

    public function map_request_to_model($id, $model = null) {
        $event = new EventM_Event_Model($id);
        $data = (array) $model;

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {

                $method = "set_" . $key;

                if (method_exists($event, $method)) {
                    $event->$method($val);
                }
            }
        }
        return $event;
    }

    public function event_sorting_dropdown() {
        $sort_options = array();
        // Loading default sorting options
        $sort_option = new stdClass();
        $sort_option->key = 'filled';
        $sort_option->label = 'Filled';
        $sort_options[] = $sort_option;

        $sort_option = new stdClass();
        $sort_option->key = 'title';
        $sort_option->label = 'Alphabetically';
        $sort_options[] = $sort_option;

        $sort_option = new stdClass();
        $sort_option->key = 'date';
        $sort_option->label = 'Date';
        $sort_options[] = $sort_option;

        return $sort_options;
    }

    public function delete_child_events($id, $child_id) {

        $event = $this->load_model_from_db($id);
        $child_event = $this->load_model_from_db($child_id);
        $ids = $event->get_child_events();

        $index = array_search($child_id, $ids);
        if (isset($ids[$index])) {
            unset($ids[$index]);
            $ids = array_values($ids);
        }


        $event->set_child_events($ids);

        $multi_dates = $event->get_multi_dates();
        if (isset($multi_dates[$index])) {
            unset($multi_dates[$index]);
            $multi_dates = array_values($multi_dates);
        }

        $event->set_multi_dates($multi_dates);

        $this->dao->save($event);
        $this->dao->delete_child_events(array($child_id));
    }

    public function load_children($event_id) {
        $event = $this->load_model_from_db($event_id);
        $children = array();
        if (is_array($event->child_events)) {
            foreach ($event->child_events as $child_id)
                $children[] = $this->load_model_from_db($child_id);
        }

        return $children;
    }

    public function loadCurrency() {
        $currency_symbol = "";
        $currency_code = em_global_settings('currency');

        if ($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];
        else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
        endif;
        return $currency_symbol;
    }
    
    public function update_booked_seats($event,$no_seats)
    {
        // Get parent event
        $event_status = get_post_status($event);
        if($event_status == 'publish')
        {
            $parent=  $this->dao->get_meta($event, 'parent_event');
            if(!empty($parent)){
                $prev_parent_booked_seats= (int) $this->dao->get_meta($parent, 'booked_seats'); 
                $prev_child_booked_seats= (int) $this->dao->get_meta($event, 'booked_seats'); 
                if($prev_child_booked_seats > $no_seats)
                {
                    $diff_seats= $prev_child_booked_seats-$no_seats;
                    $this->dao->set_meta($parent, 'booked_seats', $prev_parent_booked_seats-$diff_seats);
                } else if($no_seats>$prev_child_booked_seats)
                {
                    $diff_seats= $no_seats-$prev_child_booked_seats;
                    $this->dao->set_meta($parent, 'booked_seats', $prev_parent_booked_seats+$diff_seats);
                }
            }
            $this->dao->set_meta($event, 'booked_seats', $no_seats);
        }
    }
    
    public function get_price_range($event_model){
        $currency_symbol=EventM_Utility::get_currency_symbol();
        $ticket_price = em_get_post_meta($event_model->id, 'ticket_price', true,true);
        $child_bookable=array();
        $data=array();
        $is_children_booakable;           
                                 $child_events = em_get_post_meta($event_model->id,'child_events',true); 
                               
                                  $child_ticket_price=array();
                                 
                                   if(is_string($child_events)):
                               			                        
                                
                                    elseif(!empty($child_events) && isset($child_events)):
                                      
                                  
                                        foreach($child_events as $key => $child_id ):
                                            $child_price  = em_get_post_meta($child_id,'ticket_price',true,true);
                                            $child_ticket_price[] = $child_price;
                                            $child_bookable[] =  em_check_expired($child_id);                                      
                                        endforeach;
                                    else:                                    
                                    endif; 
                                    
                                    foreach($child_bookable as $check_child):
                                        if($check_child!=''):                                           
                                            $data['is_children_booakable']=1;
                                            break;
                                        endif;
                                    endforeach;
                                    
                                  
                                    if(!empty($child_ticket_price)):
                                        $min_ticket_price = min($child_ticket_price);
                                        $max_ticket_price = max($child_ticket_price);
                                        if($min_ticket_price==$max_ticket_price):
                                            $data['ticket_price']=$min_ticket_price.$currency_symbol;
                                        else:
                                            $data['ticket_price']=$min_ticket_price.$currency_symbol.' - '.$max_ticket_price.$currency_symbol;
                                        endif;
                                    else:
                                         $data['ticket_price']=$ticket_price  > 0 || $ticket_price =="" ?  $ticket_price.$currency_symbol : 'Free';
                                    endif;
                                    return $data;
    }

}
