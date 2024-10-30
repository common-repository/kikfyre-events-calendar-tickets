<?php
/**
 * The template for performer(s)
 *
 */
      $performer_id= (int) event_m_get_param('performer');
    
      
      if(empty($performer_id)): 
          return;
      endif;
          
        
      
      global $post; 
      $post = get_post( $performer_id, OBJECT );
      setup_postdata($post);
      $global_settings = new EventM_Global_Settings_Model(); 
     
?>
<div class="emagic">
    <!-- .content-area starts-->
    <div id="em_primary" class="em_content_area">

            <?php
            $event_type = wp_get_post_terms(get_the_ID(), EM_EVENT_TYPE_TAX);
            ?>
            <div id="post-<?php the_ID(); ?>">
              
            
                        
                          <div class="em_performer_profile dbfl">
                    

           
                              
                   <!---- Side-bar----->  
                   
                    <div class="kf-event-col2 difl">

                        <div class="kf-single-performer">
                    
                     <?php
                         $thumbnail_id = get_post_meta(get_the_ID(), '_thumbnail_id', true);
                        if (!empty($thumbnail_id)) {
                         
                            echo wp_get_attachment_image($thumbnail_id, 'thumbnail');
                          
                        } else {
                           
                            ?>
                            <img src="<?php echo esc_url(plugins_url('/images/dummy-performer.png', __FILE__)) ?>" alt="Dummy Image" >
                    <?php }
                    ?>
                             <?php   
                $performer = get_post($performer_id); ?>
                <div class="kf-performer-name dbfl em_block " title="<?php echo $performer->post_title; ?>"><?php echo $performer->post_title; ?></div>   
                                  <?php
                    $role = em_get_post_meta(get_the_ID(), 'role', true);
                    if (isset($role)) {
                        ?>                   
                        <div class="kf-performer-role em_color em_wrap dbfl">
                        
                        <?php echo $role; ?>
                            
                        </div>
                    
                      <?php } ?>   
                            
                        </div>
  </div>
                    <div class="kf-event-col1 difl">
                        <?php the_content();?>   
                    </div>

                </div>

<?php $event_service = new EventM_Performer_Service();
                        $performer_events = $event_service->get_upcoming_events(get_the_ID()); ?>
                <div class="em_performer_events em_block dbfl">
                             <div class="kf-row-heading">
                    <span class="kf-row-title"><?php echo EventM_UI_Strings::get('LABEL_UPCOMING_EVENTS'); ?><span class="em_events_count-wrap em_bg"><?php echo '<span class="em_events_count_no em_color">' . count($performer_events) . '</span>'; ?></span>
                </div> 
                     <?php  
                        $existing_event_ids = array();
                        if(!empty($performer_events)){
                        ?>
                    <div class="em_event_list">
                     

                        <?php
                     
                        if (count($performer_events) > 0):
                            foreach ($performer_events as $event):
                                $existing_event_ids[] = $event->ID;
                                $event_url= add_query_arg("event",$event->ID,get_permalink(em_global_settings("events_page")));
                                ?>
                                <div class="kf-upcoming-event-row em_block dbfl">
                                    <div class="kf-upcoming-event-thumb em-col-2 difl"><a href="<?php echo $event_url; ?>"><?php $thumbnail_id = wp_get_attachment_image(get_post_thumbnail_id($event->ID)); if(!empty($thumbnail_id)): echo $thumbnail_id; else:?>  <img src="<?php echo esc_url(plugins_url('/images/dummy_image_thumbnail.png', __FILE__)) ?>" alt="Dummy Image" > <?php endif; ?></a></div>
                                    <div class="kf-upcoming-event-title em-col-5 em-col-pad20 difl"> <a href="<?php echo $event_url; ?>"><?php echo $event->post_title ?></a>
                                    <?php    $event_details = em_get_post_meta($event->ID);
                                        $start_date = $event_details['em_start_date'];
                                        $end_date = $event_details['em_end_date']; 
                                        $start_time = em_get_time(em_get_post_meta($event->ID, 'start_date', true),true);
                                        $end_time= em_get_time(em_get_post_meta($event->ID, 'end_date', true),true); 
                                        $today  = current_time( 'timestamp' ); 
                                      if($today > $start_date[0]):
                                                echo '<span class="kf-live">Live</span>';
                                       endif;
                                            ?>
                                         <?php
                       
                     
                         $day= null;
                         
                        if(em_compare_event_dates($event->ID)): 
                            $day= em_showDateTime(em_get_post_meta($event->ID, 'start_date', true),false);
                            $start_time = em_get_time(em_get_post_meta($event->ID, 'start_date', true),true);
                            $end_time= em_get_time(em_get_post_meta($event->ID, 'end_date', true),true); 
                        else:
                            $start_date= em_showDateTime(em_get_post_meta($event->ID, 'start_date', true),false);
                            $end_date= em_showDateTime(em_get_post_meta($event->ID, 'end_date', true),false);
                        endif;
                    
                    ?>
                                        
                                        
                                          <div class="kf-upcoming-event-post-date">
                                                <?php  if(!empty($day)): ?>
                                                 <div class="em_event_start difl em_wrap">
                                                  <?php   echo $day; ?>
                                                 </div>
                                             <?php  echo '<div class="em_event_start difl em_wrap">'.$start_time.'  to  '.$end_time.'</div>';
                                             else: ?>
                                                      <div class="em_event_start difl em_wrap">
                                                         <?php echo $start_date; ?> -    
                                                     </div>
                                                     <div class="em_event_start difl em_wrap">
                                                         <?php echo $end_date; ?>  
                                                     </div>

                                             <?php endif; ?> 
                                          </div>
                                  
                                    
                                    </div>
                               
                                    <div class="kf-upcoming-event-price em-col-2 em-col-pad20 difl">
                                        <?php //echo em_get_post_meta($event->ID, 'ticket_price', true,true).' '. $currency;  ?>
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
                                        $profile   = em_global_settings('profile_page');
                                        $start_date = em_get_post_meta($event->ID, 'start_date', true);
                                        $start_booking_date = em_get_post_meta($event->ID, 'start_booking_date', true);
                                           $last_booking_date = em_get_post_meta($event->ID, 'last_booking_date', true);
                                        if (!empty($booking_page_id) && $booking_page_id > 0 && !em_is_event_expired($event->ID)):
                                            ?>                                    
                                            <div class="em_event_head em_event_register">
                                                <form action="<?php echo get_permalink($booking_page_id); ?>" method="post" name="em_booking<?php echo get_the_ID(); ?>">

                                                    
                                                   <?php if(is_user_logged_in()): 
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

                                                    $terms = wp_get_post_terms(get_the_ID(), EM_VENUE_TYPE_TAX);

                                                    if (!empty($terms) && count($terms) > 0):
                                                        $venue = $terms[0];
                                                        ?>
                                                        <input type="hidden" name="event_id" value="<?php echo get_the_ID(); ?>" />
                                                        <input type="hidden" name="venue_id" value="<?php echo $venue->term_id; ?>" />
                <?php endif; ?>
                                                </form>



                                            </div>

                <?php else:
                ?>
                                            <div class="em_event_head em_event_register">
                                                <a href="javascript:void(0)" class="em_header_button">Expired</a>
                                            </div>
            <?php
            endif;
            ?>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>    
                      <?php  }
                        else
                                        {
                                           ?>
<div class="kf-no-upcoming-event"><?php echo EventM_UI_Strings::get('NOTICE_NO_UPCOMING_EVENTS'); ?>
 </div>
<?php } ?>
                </div>
            </div>

    </div>
</div>
    <!-- .content-area ends-->
<?php wp_reset_postdata(); ?>    
<script>
    hide_entry_title();
    </script>
