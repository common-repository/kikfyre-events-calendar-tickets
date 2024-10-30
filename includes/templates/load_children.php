<?php
            $is_user = is_user_logged_in();
            $event_service = new EventM_Service();
            $available_seats = $event_service->available_seats($event_id);
            $booking_page_id = em_global_settings('booking_page');
            $venue_service= new EventM_Venue_Service();
            foreach ($child_events as $index => $child):
                $available_seats = $event_service->available_seats($child);
                $venue_model= $venue_service->load_model_from_db($venue_id);
                $start_date= em_get_post_meta($child, 'start_date', true);
    ?>
        <div ng-click="loadEventSeats(<?php echo $venue_id.','.$child; ?>)" class="kf_day_card <?php em_is_event_bookable($child)?'kf_bookable':'kf_not_bookable'; ?>">
            Day <?php echo ($index + 1).', '.  em_showDateTime($start_date, true); ?>
            

        <form action="<?php echo get_permalink($booking_page_id); ?>" method="post" name="em_booking<?php echo $child; ?>">
            

            <?php
            $terms = wp_get_post_terms($child, EM_VENUE_TYPE_TAX);

            if (!empty($terms) && count($terms) > 0):
                $venue = $terms[0];
                ?>
                <input type="hidden" name="event_id" value="<?php echo $child; ?>" />
                <input type="hidden" name="venue_id" value="<?php echo $venue->term_id; ?>" />
        </form>  
        
        <?php if($venue_model->type=="seats"): ?>
        <div>
            <?php echo $available_seats; ?> seats left
        </div>
        <?php
              endif;  
        ?>
            <?php endif; ?>   
            </div>    
            <?php
        endforeach;
        ?>

