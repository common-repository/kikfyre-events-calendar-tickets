<?php
em_localize_map_info('em-google-map');

 $global_settings = new EventM_Global_Settings_Model(); 
 $gmap_api_key= em_global_settings('gmap_api_key');
   $currency_symbol="";                    
    if($global_settings->currency):
        $all_currency_symbols = EventM_Constants::get_currency_symbol();
        $currency_symbol = $all_currency_symbols[$global_settings->currency];                        
    else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
    endif;
  $child_bookable=array();
 $is_children_booakable;
?>
<div class="emagic emagic_archive">
    <a></a>
    <!-- .content-area starts-->
    <div id="em_primary" class="em_content_area <?php if (!is_active_sidebar('em_right_sidebar-1')) echo 'em_single_fullwidth'; ?>">
        <?php
        $venue = get_term($venue_id);
        $venue_address = em_get_term_meta($venue->term_id, 'address', true);

        if (!empty($venue)):

            $venue_service = new EventM_Venue_Service();
            if (!empty($venue_service)):
                $upcoming_events = $venue_service->get_upcoming_events($venue->term_id);
            endif;
            ?>      <div class="em_venue_image em_block dbfl">                                                                                 <!-- Div Display First image  from Gallery of Venues -->
                <div class="em_cover_image">
                    <?php
                    $images = em_get_term_meta($venue->term_id, 'gallery_images', true);
                    if (!empty($images)) {
                        ?>

                        <?php echo wp_get_attachment_image($images[0], 'full'); ?>

                        <?php
                    } else {
                        ?>
                        <img src="<?php echo esc_url(plugins_url('/images/dummy_image.png', __FILE__)) ?>" alt="Dummy Image" class="em-venue-dummy-cover" >

                        <?php
                    }
                    ?> 
    <?php if (!empty($gmap_api_key) && !empty($venue_address)): ?>
                        <div class="em-venue-direction">


                            <div data-venue-id="<?php echo $venue->term_id; ?>" id="em_single_venue_map_canvas" style="max-height: 180px; height: 100%"</div>
                        </div>
                    </div>
                    <?php
                endif;
                ?>             

            </div>

        <?php endif; ?>

            <?php if (!empty($venue->name)): ?>
            <div class="kf-single-venue-post-title dbfl ">
    <?php echo $venue->name ?>

            </div> <!-- Name of venue -->
<?php endif; ?>


        <!---Venue Address----->

        <?php $venue_address = em_get_term_meta($venue->term_id, 'address', true); ?> 
<?php if (!empty($venue_address)): ?>
            <div class="em_block kf-venue-address dbfl">

    <?php echo $venue_address; ?>
                <span class="kf-vanue-directions">
                    <a target="blank" href='https://www.google.com/maps?saddr=My+Location&daddr=<?php echo urlencode($venue_address); ?>&dirflg=w'>
                        Directions
                    </a>  
            </div>  <!-- address of venue-->
<?php endif; ?>

        <!---End Venue Address----->

    </div>  

    <!-- Start side-bar -->

    <div class="kf-event-col2 difl">
        <div class="kf-event-single-venue-sidebar difl em_bg ">
            <div class="kf-event-col-title em_bg">Venue Information</div>

            <?php
            $established = em_showDateTime(em_get_term_meta($venue->term_id, 'established', true), false);
            $seating_organizer = em_get_term_meta($venue->term_id, 'seating_organizer', true);
            ?>
            <div class="kf-event-attr dbfl">
                <?php if ($established): ?>
                    <div class="organizer_det">
                        <?php echo '<div class="kf-event-attr-name em_color dbfl">Established: </div><div class="kf-event-attr-value dbfl">' . $established . '</div>'; ?>
                    </div>
                <?php endif; ?></div>


            <div class="kf-event-attr dbfl">
                <?php
                $seating_capacity = em_get_term_meta($venue->term_id, 'seating_capacity', true); //get seating capacity
                $type = em_get_term_meta($venue->term_id, 'type', true);

                if (!empty($type)) :           // here ve are checking about the type first because in Standing we dont have capacity and in Seat we have capacity                                 
                    if ($type == 'standings'):
                        ?>

                        <div class="kf-event-attr-name em_color dbfl">Type</div>
                        <div class="kf-event-attr-value dbfl"><?php echo 'Standing'; ?></div>

                    <?php else: ?>

                        <div class="kf-event-attr-name em_color dbfl">Capacity</div>
                        <div class="kf-event-attr-value dbfl"> <?php
                            echo $seating_capacity . ' People ';
                            //  EventM_UI_Strings::get("LABEL_SEATING_CAPACITY").' '.$seating_capacity.' '.$type; 
                            ?></div>

                    <?php
                    endif;
                endif;
                ?> </div>

            <div class="kf-event-attr dbfl">
                    <?php if ($seating_organizer): ?>
                    <div class="organizer_det">
                    <?php echo '<div class="kf-event-attr-name em_color dbfl">Coordinator: </div><div class="kf-event-attr-value dbfl"> ' . $seating_organizer . '</div>'; ?>
                    </div>
<?php endif; ?>
            </div>

            <div class="kf-event-attr dbfl">
                <?php
                $venue_fb_link = em_get_term_meta($venue->term_id, 'facebook_page', true);
                if ($venue_fb_link):
                    ?>  

                    <div class="kf-event-attr-name em_color dbfl">Facebook</div>
                    <div class="kf-event-attr-value kf-fb-link dbfl dbfl"><?php echo $venue_fb_link; ?><a target='_blank' href="<?php echo $venue_fb_link; ?>"> <i class='fa fa-external-link' aria-hidden='true'></i></a></div>
<?php endif; ?>
            </div>


            <?php
            $events_page_id = em_global_settings("events_page");
            $form_action = !empty($events_page_id) ? get_permalink($events_page_id) : "";
            ?>

            <?php /* <div class="em_block dbfl">
              <form id="em_list_events" method="POST" name="em_list_events" action="<?php echo add_query_arg( array('em_s'=>'1', 'em_venues' => $venue->term_id),$form_action)?>">
              <input type="submit" class="em_venue_header_button em_header_button" id="searchsubmit" value="View Events" />
              </form>

              <!--<a href="<?php
              //echo add_query_arg( array('em_venues' => $venue->term_id),'http://127.0.0.1/NEW/events')?>" class="em_header_button" target="_blank">View Events</a>-->
              </div> */ ?>

        </div>

            <?php
            $event_gallery_id = em_get_term_meta($venue->term_id, 'gallery_images', true);
            if (is_array($event_gallery_id)):
                ?>
            <div class="em_photo_gallery em-single-venue-photo-gallery dbfl" >
                <?php
                foreach ($event_gallery_id as $id):
                    ?>
                    <a rel="gal" href="<?php echo wp_get_attachment_url($id); ?>"><?php echo wp_get_attachment_image($id, array(50, 50)); ?> </a>
                <?php
            endforeach;
            ?>  
            </div>
<?php endif; ?>

    </div>


    <div  class="kf-event-col1 difl">

<?php
$venue_description = em_get_term_meta($venue->term_id, 'description', true);
if (!empty($venue_description)):
    ?>                    
            <div class="em_venue_desc dbfl">

    <?php echo do_shortcode($venue_description); ?></div> <!-- description of venue-->
        <?php else: ?>
            <div class="em_no_venue_desc dbfl"> 
                Venue description is not available.
            </div>

<?php endif; ?>                            
    </div>


    <!----Upcoming events------>







    <div class="em_performer_events em_block dbfl">
        <div class="kf-row-heading">
            <span class="kf-row-title">Upcoming Events<span class="em_events_count-wrap em_bg"><?php echo '<span class="em_events_count_no em_color">' . count($upcoming_events) . '</span>'; ?></span>
        </div> 


<?php
if (!empty($upcoming_events)) {
    ?>


            <div class="event_list">

                <?php
                if (is_array($upcoming_events)):
                    $existing_event_ids = array();
                    foreach ($upcoming_events as $event):
                        $event_details = em_get_post_meta($event->ID);
                        $start_date = $event_details['em_start_date'];
                        $end_date = $event_details['em_end_date'];
                        $today = current_time('timestamp');

                        $existing_event_ids[] = $event->ID;
                        $event_url = add_query_arg("event", $event->ID, get_permalink(em_global_settings("events_page")));
                        
                        
                        ?>
                        <div class="kf-upcoming-event-row em_block dbfl">

                            <div class="kf-upcoming-event-thumb em-col-2 difl"> <a href="<?php echo get_permalink($event->ID); ?>"><?php
                                $thumbnail_id = get_the_post_thumbnail($event->ID, 'thumbnail');
                                if (!empty($thumbnail_id)): echo $thumbnail_id;
                                else:
                                    ?>  <img src="<?php echo esc_url(plugins_url('/images/dummy_image_thumbnail.png', __FILE__)) ?>" alt="Dummy Image" > <?php endif; ?></a></div>
                            <div class="kf-upcoming-event-title em-col-5 em-col-pad20 difl"><a href="<?php echo $event_url; ?>"><?php echo $event->post_title; ?></a>
                                    <?php
                                     if ($today > $start_date[0]):
                                    echo '<span class="kf-live">Live</span>';
                                     endif;
                                    ?>
                                <div class="kf-upcoming-event-post-date">
                                    <?php
                                    $start_date = null;
                                    $end_date = null;
                                    $start_time = null;
                                    $end_time = null;
                                    $day = null;

                                    if (em_compare_event_dates($event->ID)):
                                        $day = em_showDateTime(em_get_post_meta($event->ID, 'start_date', true), false);
                                        $start_time = em_get_time(em_get_post_meta($event->ID, 'start_date', true), true);
                                        $end_time = em_get_time(em_get_post_meta($event->ID, 'end_date', true), true);
                                    else:
                                        $start_date = em_showDateTime(em_get_post_meta($event->ID, 'start_date', true), false);
                                        $end_date = em_showDateTime(em_get_post_meta($event->ID, 'end_date', true), false);
                                    endif;
                                    ?>
                                    <?php if (!empty($day)): ?>
                                        <div class="em_event_start difl em_color em_wrap">
                                            <?php echo $day; ?>
                                        </div>
                                            <?php echo '<div class="em_event_start difl em_color em_wrap">' . $start_time . '  to  ' . $end_time . '</div>';
                                        else:
                                            ?>
                                        <div class="em_event_start difl em_color em_wrap">
                                        <?php echo $start_date; ?> -    
                                        </div>
                                        <div class="em_event_start difl em_color em_wrap">
                <?php echo $end_date; ?>  
                                        </div>

            <?php endif; ?> 
            <?php //echo em_showDateTime(em_get_post_meta($event->ID, 'start_date', true));  ?>

                                </div>
                            </div>

                            <div class="kf-upcoming-event-price em-col-2 em-col-pad20 difl">
                                <?php// echo em_get_post_meta($event->ID, 'ticket_price', true, true) . ' ' . $currency; ?>
                            <?php 
                             
                                $event_service = new EventM_Service();
                                $event_model= $event_service->load_model_from_db($event->ID);
                                  $data=$event_service->get_price_range($event_model);
                                 
                                    echo $data['ticket_price'];
                                
                           
                                    ?>
                            </div>

                            <div class="kf-upcoming-event-booking em-col-3 em-col-pad20 difl">
                                <?php
                                $post = get_post($event->ID);
                                $booking_page_id = em_global_settings('booking_page');
                                $user = wp_get_current_user();
                                $profile_id = em_global_settings('profile_page');
                                  $start_date = em_get_post_meta($event->ID, 'start_date', true);
                                   $start_booking_date = em_get_post_meta($event->ID, 'start_booking_date', true);
                                  $last_booking_date = em_get_post_meta($event->ID, 'last_booking_date', true);
                                  
                                if (!empty($booking_page_id) && $booking_page_id > 0 && !em_is_event_expired($event->ID)):
                                    ?>                                    
                                    <div class="em_event_head em_event_register">
                                        <form action="<?php echo get_permalink($booking_page_id); ?>" method="post" name="em_booking<?php echo $event->ID; ?>">

                                            <?php if (is_user_logged_in()):
                                                  $today = current_time('timestamp');
                                       $child_events = em_get_post_meta($event->ID, 'child_events', true);
                                                ?> 
                                             <?php if($today < $start_booking_date):?>
                                                 <div class="em_header_button  em_not_bookable"> <?php  echo 'Booking is not started'; ?></div>
                                                <?php
                                                           elseif($today >=$last_booking_date): ?>
                                                           <div class="em_header_button  em_not_bookable"> <?php echo 'Booking Closed' ; ?></div>
                                                                
                                                            <?php elseif(isset($data['is_children_booakable']) && $data['is_children_booakable']!=1 ): ?>
                                                           <div class="em_header_button em_not_bookable"> <?php echo 'Sold Out' ; ?></div>
                                                                
                                                                <?php  elseif(!em_check_expired($event->id)  && empty($child_events)):?>  
                                                             <div class="em_header_button  em_not_bookable"><?php echo 'Booking Expired' ?></div>
                                                <?php elseif(em_check_expired($event->ID)): ?>
                                                            <button  onclick="em_event_booking(<?php echo $event->ID; ?>)" class="em_header_button kf-button em_color" id="em_booking"><?php echo EventM_UI_Strings::get('LABEL_BOOK_NOW') ?></button>
                                                
                                                                 <?php
                                                          
                                                       else:
                                                           endif; ?>
                                                <?php else: ?>             
                                                  <a  class="em_header_button kf-tickets" target="_blank" href="<?php echo add_query_arg('event_id',$event->ID,get_permalink($global_settings->profile_page)) ?>">Register Now</a>
                                                <?php endif;

                                            $terms = wp_get_post_terms($event->ID, EM_VENUE_TYPE_TAX);

                                            if (!empty($terms) && count($terms) > 0):
                                                $venue = $terms[0];
                                                ?>
                                                <input type="hidden" name="event_id" value="<?php echo $event->ID; ?>" />
                                                <input type="hidden" name="venue_id" value="<?php echo $venue->term_id; ?>" />
                <?php endif; ?>
                                        </form>
                                    </div>

            <?php else:
                ?>
                                    <div class="em_event_head em_event_register">
                                        <a href="javascript:void(0)" class="em_header_button">Expired</a>
                                    </div>
                        <?php endif; ?>
                            </div>

                        </div>

                    <?php
                endforeach;
            endif;
            ?>
            </div>   
            <?php
        }

        else {
            ?>
            <div class="kf-no-upcoming-event"><?php echo "No upcoming events at this venue."; ?></div>
    <?php
}
?>
    </div>
</div>


<?php ?>
</div>


<script>
    em_jQuery(document).ready(function () {
        em_jQuery(".em_photo_gallery a").colorbox({width: "75%", height: "75%"});

        em_load_map('single_venue', 'em_single_venue_map_canvas');

    });

    hide_entry_title();

</script>
