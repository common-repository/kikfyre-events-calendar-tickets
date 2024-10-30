<?php
   $event_service= new EventM_Service();
   $the_query= $event_service->get_events_the_query();
$global_settings = new EventM_Global_Settings_Model(); 

if(event_m_get_param('em_types')){ 
     $event_type_terms = get_term_meta(event_m_get_param('em_types'));
     if(isset( $event_type_terms['em_description'])):
     $special_instruction = $event_type_terms['em_description'][0];
     if(isset($special_instruction)){
          echo '<div class="em_event_type_note">'.do_shortcode($special_instruction).'</div><style>
                .em_event_type_note { padding: 20px 0px; margin-bottom : 20px;}
                .em_event_type_note p {font-size: 14px; line-height: 20px;}
                </style>';
     }    
     endif;
} 
  $currency_symbol="";                    
        if($global_settings->currency):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$global_settings->currency];                        
            else:
                $currency_symbol = EM_DEFAULT_CURRENCY;
            endif;
 ?>
    <div class="emagic">
        <?php if ($the_query->have_posts()) : ?>
        <div class="em_cards">
            <!-- the loop -->
            <?php while ($the_query->have_posts()) : $the_query->the_post(); 
        
            ?>
            <div class="em_card difl <?php if(em_is_event_expired(get_the_ID())) echo 'emcard-expired'; ?>">
                   <?php $thumbnail_id = get_post_meta(get_the_ID(), '_thumbnail_id', true); ?>
                	<div class="em_event_cover dbfl">
                	  <?php if(!empty($thumbnail_id))
                                {  ?>                               
                                    <a href="<?php echo add_query_arg("event",get_the_ID(),get_permalink($global_settings->events_page)); ?>"><?php echo the_post_thumbnail('large'); ?></a>
                          <?php }
                                else{?>
                                  <a href="<?php echo add_query_arg("event",get_the_ID(),get_permalink($global_settings->events_page)); ?>"><img src="<?php echo esc_url( plugins_url( '/images/dummy_image.png', __FILE__ ) ) ?>" alt="Dummy Image" ></a>
                              <?php  }                              
                            ?>
                        
                        </div>
                    <div class="dbfl em-card-description">
                        <div class="em_event_title em_block dbfl em_wrap"  title="<?php echo the_title(); ?>"><a href="<?php echo add_query_arg("event",get_the_ID(),get_permalink($global_settings->events_page)); ?>"><?php echo the_title(); ?></a></div>

	                    
                                             
	                  
                     <?php
                        $start_date= null;
                        $end_date= null;
                        $start_time = null;
                        $end_time= null;
                         $day= null;
                         
                        if(em_compare_event_dates(get_the_ID())): 
                            $day= em_showDateTime(em_get_post_meta(get_the_ID(), 'start_date', true),false);
                            $start_time = em_get_time(em_get_post_meta(get_the_ID(), 'start_date', true),true);
                            $end_time= em_get_time(em_get_post_meta(get_the_ID(), 'end_date', true),true); 
                        else:
                            $start_date= em_showDateTime(em_get_post_meta(get_the_ID(), 'start_date', true),false);
                            $end_date= em_showDateTime(em_get_post_meta(get_the_ID(), 'end_date', true),false);
                        endif;
                    
                    ?>
                         <?php  if(!empty($day)): ?>
                        <div class="em_event_start difl em_color em_wrap">
                         <?php   echo $day; ?>
                        </div>
                    <?php  echo '<div class="em_event_start difl em_color em_wrap">'.$start_time.'  to  '.$end_time.'</div>';
                    else: ?>
                             <div class="em_event_start difl em_color em_wrap">
                                <?php echo $start_date; ?> -    
                            </div>
                            <div class="em_event_start difl em_color em_wrap">
                                <?php echo $end_date; ?>  
                            </div>
                        
                    <?php endif; ?> 
                            
                            
                            
                            <?php
	                    // Venue Info (Assuming one event can have only one venue)
	                     $venue = wp_get_post_terms(get_the_ID(), EM_VENUE_TYPE_TAX);
                              if(!empty($venue)):
	                    $venue_address = em_get_term_meta($venue[0]->term_id, 'address', true); ?>
	                    <div class="em_event_address dbfl" title="<?php echo $venue_address; ?>"><?php echo $venue_address; ?></div>
	                    <?php endif; ?>
               
                   <?php 
                $hide_booking_status = em_get_post_meta(get_the_ID(),'hide_booking_status');
             
                if((!empty($hide_booking_status) && $hide_booking_status[0] !=1)|| (count($hide_booking_status)==0)):
                $child_events = em_get_post_meta(get_the_ID(),'child_events');
                if(empty($child_events[0])):
                 $sum= $event_service->booked_seats(get_the_ID());
                 $capacity= em_event_seating_capcity(get_the_ID());  ?>  
                   <div class="dbfl">
                    <div class="kf-event-attr-name dbfl">
                       
                        <?php echo EventM_UI_Strings::get('LABEL_EVENT_BOOKING_STATUS'); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">  
                        <?php
                          
                          
                    if ($capacity > 0):
                        ?>
                        <div class="dbfl">
                            <?php echo $sum; ?> / <?php echo $capacity; ?> 
                        </div>
                            <?php $width = ($sum / $capacity) * 100; ?>
                        <div class="dbfl">
                            <div id="progressbar" class="em_progressbar dbfl">
                                <div style="width:<?php echo $width . '%'; ?>" class="em_progressbar_fill em_bg" ></div>
                            </div>
                        </div>
                        <?php
                    else:
                        echo '<div class="dbfl">'.$sum.' Sold</div>';
                        ?>

                    <?php endif; ?>
                    </div>
                </div>  
                <?php 
                endif;
                endif; ?>
                </div>
                    <div class="em-cards-footer dbfl">
                         <div class="em_event_price  difl">
                               <?php
                                   
                                $event_service = new EventM_Service();
                                 $event_model= $event_service->load_model_from_db(get_the_ID());
                                
                             $ticket_price = em_get_post_meta($event_model->id, 'ticket_price', true,true);
                      
                                 $child_events = em_get_post_meta($event_model->id,'child_events',true); 
                               
                                  $child_ticket_price=array();
                                   if(is_string($child_events)):
                               			                        
                                
                                    elseif(!empty($child_events) && isset($child_events)):
                                      
                                  
                                        foreach($child_events as $key => $child_id ):
                                          $child_price  = em_get_post_meta($child_id,'ticket_price',true,true);
                                               $child_ticket_price[] = $child_price;
                                        endforeach;
                                    else:                                    
                                    endif; 
                                    
                                    
                                    
                                  
                                    if(!empty($child_ticket_price)):
                                        $min_ticket_price = min($child_ticket_price);
                                        $max_ticket_price = max($child_ticket_price);
                                        if($min_ticket_price==$max_ticket_price):
                                            echo $min_ticket_price.$currency_symbol;
                                        else:
                                            echo $min_ticket_price.$currency_symbol.' - '.$max_ticket_price.$currency_symbol;
                                        endif;
                                    else:
                                         echo $ticket_price  > 0 || $ticket_price =="" ?  $ticket_price.$currency_symbol : 'Free';
                                    endif;
?>
                             <?php //echo em_get_post_meta(get_the_ID(), 'ticket_price', true,true) . $currency_symbol; ?>
                         </div>
                       
                          <?php      $post = get_post(get_the_ID());
                              $booking_page_id = $global_settings->booking_page;  
                              $profile_id   = $global_settings->profile_page;
                                $user = wp_get_current_user();
                                 $start_booking_date = em_get_post_meta(get_the_ID(), 'start_booking_date', true);
                                 $last_booking_date = em_get_post_meta(get_the_ID(), 'last_booking_date', true);
                                if (!empty($booking_page_id) && $booking_page_id > 0 && !em_is_event_expired(get_the_ID()) ):?>                                    
                                        <div class="em_event_register difr">
                                            <form action="<?php echo get_permalink($booking_page_id); ?>" method="post" name="em_booking<?php echo get_the_ID(); ?>">

                                                <?php if(is_user_logged_in()): 
                                                      $today = current_time('timestamp');
                                                
                                                    ?>   
                                                        <?php if($today < $start_booking_date):?>
                                         <div class="kf-booking-not-started"> 
                                                <?php echo EventM_UI_Strings::get('LABEL_BOOKING_NOT_STARTED'); ?>
                                        </div>
                                                
                                                        <?php elseif(em_check_expired(get_the_ID())): ?>
                                                            <a  onclick="em_event_booking(<?php echo get_the_ID(); ?>)" class="em_header_button " id="em_booking"><?php echo EventM_UI_Strings::get('LABEL_BOOK_NOW') ?></a>
                                                      
                                                            <?php elseif($today >=$last_booking_date): ?>
                                                           <div class="em_header_button  em_not_bookable"> <?php echo 'Booking Closed' ; ?></div>
                                                                
                                                                
                                                            <?php elseif(!em_check_expired(get_the_ID())  && empty($child_events)): ?>  
                                                             <div class="em_header_button em_not_bookable kf-tickets"><?php echo 'Booking Expired' ?></div>
                                                         <?php
                                                         
                                                       else:
                                                           endif; ?>
                                                                
                                                            
                                                      
                                                <?php else: ?>   
                                                            <a  class="em_header_button kf-tickets em_color" target="_blank"  href="<?php echo add_query_arg('event_id',get_the_ID(),get_permalink($global_settings->profile_page)) ?>"><?php echo EventM_UI_Strings::get('LABEL_REGISTER_NOW'); ?></a> 

                                                <?php endif;

                                                $terms = wp_get_post_terms(get_the_ID(), EM_VENUE_TYPE_TAX);

                                                if (!empty($terms) && count($terms) > 0):
                                                    $venue = $terms[0];
                                                    ?>
                                                    <input type="hidden" name="event_id" value="<?php echo get_the_ID(); ?>" />
                                                    <input type="hidden" name="venue_id" value="<?php echo $venue->term_id; ?>" />
                                                <?php endif; ?>
                                            </form>
                                        </div>

                                <?php 
                                else: ?>
                                    <div class="em_event_register  difr">  
                                    <div class="em_header_button kf-booking-expired "><?php echo EventM_UI_Strings::get('LABEL_EVENT_END'); ?></div>
                                    </div>
                                <?php
                                endif;
                           ?>
                    </div>
                   
                </div>
            <?php endwhile; ?>
        </div>   
      
</div>     

    <?php 

    if ($the_query->max_num_pages > 1) { // check if the max number of pages is greater than 1   ?>
        <nav class="prev-next-posts">
            <div class="prev-posts-link">
                <?php echo get_next_posts_link('Older Entries', $the_query->max_num_pages); // display older posts link  ?>
            </div>
            <div class="next-posts-link">
                <?php echo get_previous_posts_link('Newer Entries'); // display newer posts link  ?>
            </div>
        </nav>
    <?php } ?>




<?php else: ?>
    <article>
        
        <p><?php _e('There are no Events available right now.'); ?></p>
    </article>
</div>
<?php endif; ?>
<script>
     function em_user_register(user){
   
    window.open(user);
}
    </script>
