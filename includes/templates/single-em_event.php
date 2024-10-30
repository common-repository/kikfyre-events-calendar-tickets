<?php
      add_action('wp_head', 'em_load_twitter_meta');    //add Meta for Twitter
      em_localize_map_info('em-google-map');
      wp_enqueue_script('jquery-ui-tabs', array('jquery'));
      $event_id= (int) event_m_get_param('event');
      if(empty($event_id))
          return;
      
      global $post; 
      $post = get_post( $event_id, OBJECT );
      setup_postdata($post);
      if(empty($post))
          return;

      $event_service = new EventM_Service();
      $event_service->get_header();
      $global_settings = new EventM_Global_Settings_Model(); 
      $event_model= $event_service->load_model_from_db($event_id);
/**
 * The template for displaying all single events
 *
 */ 
        $organizer_info = em_get_post_meta($event_model->id, 'org_info', true);
        $ticket_price = em_get_post_meta($event_model->id, 'ticket_price', true, true);
        $currency_symbol="";                    
        $currency_code= $global_settings->currency;
        if($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];                        
            else:
                $currency_symbol = EM_DEFAULT_CURRENCY;
            endif;
        $terms = wp_get_post_terms($event_model->id, EM_VENUE_TYPE_TAX);
                if (!empty($terms) && count($terms) > 0):
                    $venue = $terms[0];
                    $venue_address = em_get_term_meta($venue->term_id, 'address', true);
                else:
                    $venue_address = 'Venue address is not specified.';                    
                endif;
               
        $sum = $event_service->booked_seats($event_model->id);
   
?>
<div class="emagic">
    <!-- .content-area starts-->
    <div id="em_primary" class="em_content_area">
       
        <div class="em_cover_image dbfl">
                        <?php
                         $thumbnail_id = get_post_meta($event_model->id, '_thumbnail_id', true);
                        if (!empty($thumbnail_id)) {
                         
                            echo wp_get_attachment_image($thumbnail_id, 'full');
                          
                        } ?>
                          
        </div>
        <div class="kf-event-header dbfl">
            <div class="kf-event-title dbfl">
                <div class="kf-event-date-large difl">
                    <div class="kf-date-large-icon em_bg dbfl">
                        <div class="kf-date-icon-top dbfl">
                            <?php echo date("M", em_time($event_model->start_date));?>
                        </div>
                        <div class="kf-date-icon-bottom dbfl">
                            <?php echo date("j", em_time($event_model->start_date));?>
                        </div>
                    </div>
                </div>
                <div class="kf-event-title-text difl">
                    <div class="kf-post-title dbfl">
                        <?php 
                        echo $event_model->name;
                        ?>
                    </div>
                    
                </div>
            </div>
            <div class="kf-event-buy-tickets dbfl">
                  <?php
                  
                
                  
                if ( $global_settings->social_sharing==1): ?>
                    <div class="kf-event-share difl">
                     <a href="https://twitter.com/share?url=<?php echo the_permalink(); ?>&amp;" target="_blank">  <!-- twitter share button -->
                                <i class="fa fa-twitter em_share" aria-hidden="true"></i>
                     </a>   

                   <i class="fa fa-facebook-official em_share_fb em_share" aria-hidden="true"></i>
                   <div id="em_fb_root"></div>
                    </div>
               <?php endif; ?>
                 
                <div class="kf_ticket_price">
                    <div class="kf-event-price difl">
                        <?php 
                            
                           
                             
                                  $data=$event_service->get_price_range($event_model);
                                 
                                    echo $data['ticket_price'];
                              
?>
                        
                  
                    </div>
                    <div class="kf-tickets-button difl">
                    <!--<button class="kf-tickets" name="tickets"><i class="fa fa-ticket" aria-hidden="true"></i>Buy Tickets</button>-->
                    <?php
                    $sum = $event_service->booked_seats($event_model->id);
                    $capacity = em_event_seating_capcity($event_model->id);

                    $post = get_post($event_model->id);
                    $booking_page_id = $global_settings->booking_page;
                    
                    $user = wp_get_current_user();
                    $start_date = em_get_post_meta($event_model->id, 'start_date', true);
                    $start_booking_date = em_get_post_meta($event_model->id, 'start_booking_date', true);
                   $last_booking_date = em_get_post_meta($event_model->id, 'last_booking_date', true);
                    if (!empty($booking_page_id) && $booking_page_id > 0 && !em_is_event_expired($event_model->id)):
                        ?>                                    
                        <div class="em_event_attr_box em_eventpage_register difl">
                            <form action="<?php echo get_permalink($booking_page_id); ?>" method="post" name="em_booking">
                                     <?php if(is_user_logged_in()): 
                                        $today = current_time('timestamp');
                                        $child_events = $event_model->child_events;
                                         ?>  
                                
                                                        <?php if($today < $start_booking_date):?>
                                                               <div class="kf-single-event kf-booking-not-started"> 
                                                <?php echo EventM_UI_Strings::get('LABEL_BOOKING_NOT_STARTED'); ?>
                                        </div>
                                                    <?php    elseif($today >=$last_booking_date): ?>
                                                          <button class="em_header_button em_not_bookable kf-tickets"> <?php echo EventM_UI_Strings::get('LABEL_BOOKING_CLOSED');?></button>
                                                           
                                                     <?php elseif(isset($data['is_children_booakable']) && $data['is_children_booakable']!=1 ): ?>
                                                           <div class="em_header_button em_not_bookable"> <?php echo 'Sold Out' ; ?></div>       
 
                                                      <?php elseif(!em_check_expired($event_model->id)  && empty($child_events)): ?>  
                                                             <button class="em_header_button em_not_bookable kf-tickets"><?php echo EventM_UI_Strings::get('LABEL_BOOKING_EXPIRED'); ?></button>
                                                                   
                                                               
                                                                <?php elseif(em_check_expired($event_model->id) ): ?>
                                                            <button class="kf-tickets" name="tickets" onclick="em_event_booking(<?php echo $event_model->id; ?>)" class="em_header_button" id="em_booking"><i class="fa fa-ticket" aria-hidden="true"></i>Buy Tickets</button>
                                                        
                                                       
                                                         <?php
                                                          
                                                       else:
                                                           endif; ?>
                                                           

                                                             
                                                <?php else: ?>   
                                                             <a  class="em_header_button kf-tickets" target="_blank" href="<?php echo add_query_arg('event_id',get_the_ID(),get_permalink($global_settings->profile_page)) ?>"><?php echo EventM_UI_Strings::get('LABEL_REGISTER_NOW'); ?></a>
                                           


                                                    <?php endif;

                                $terms = wp_get_post_terms($event_model->id, EM_VENUE_TYPE_TAX);

                                if (!empty($terms) && count($terms) > 0):
                                    $venue = $terms[0];
                                    ?>
                                    <input type="hidden" name="event_id" value="<?php echo $event_model->id; ?>" />
                                    <input type="hidden" name="venue_id" value="<?php echo $venue->term_id; ?>" />
                                <?php endif; ?>
                            </form>
                        </div>
                
                    <?php else:
                        ?>
                        <div class="em_event_attr_box em_event_register">
                            <div  class="single-event-expired kf-booking-expired  "><?php echo EventM_UI_Strings::get('LABEL_EVENT_ENDED'); ?></div>
                        </div>
                    <?php
                    endif;?>
                    
                    </div>
                </div>
            </div>
       
            <div class="kf-event-attributes dbfl">
                <div class="dbfl">
                <div class="kf-event-attr difl">
                    <div class="kf-event-attr-name dbfl">
                       <?php echo EventM_UI_Strings::get('LABEL_DATE_TIME'); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">
                    
                        <?php echo em_showDateTime(em_time($event_model->start_date));?><br>
                        <?php echo em_showDateTime(em_time($event_model->end_date)); ?>
                        
                           <?php if($global_settings->gcal_sharing == 1) : ?>
                        <div id="add-to-google-calendar">

                            <p><label><input type="text" id="event_<?php echo $event_model->id ?>"  style="display: none" value="<?php echo the_title(); ?>"></label></p>
                            <p><label> <input type="text" id="s_date_<?php echo $event_model->id ?>"  style="display: none"value="<?php echo em_showDateTime(em_get_post_meta($event_model->id, 'start_date', true), true); ?>" ></label></p>
                            <p><label><input type="text" id="e_date_<?php echo $event_model->id ?>"  style="display: none" value="<?php echo em_showDateTime(em_get_post_meta($event_model->id, 'end_date', true), true); ?>" ></label></p>

                            <!--<p><button id="authorize-button" style="display: none; background-image: url(<?php echo esc_url(plugins_url('/images/google-calendar-icon-66527.png', __FILE__)); ?>)"></button></p>-->
                                  
                             <p><div onclick="em_gcal_handle_auth_click()"  id="authorize-button" class="kf-event-add-calendar em_color dbfl" style="display: none;">
                        <img class="kf-google-calendar-add" src="<?php echo esc_url(plugins_url('/images/gcal.png', __FILE__)); ?>">
                        <?php echo EventM_UI_Strings::get('LABEL_ADD_TO_CALENDAR'); ?>
                        </div></p>
                       
                            <div class="pm-edit-user pm-difl">
                                <a href="" class="pm_button pm-dbfl" id="pm-change-password" onclick="return false;">
                                           
                                            
                                    <p><div  onclick="em_add_to_calendar('<?php echo $event_model->id ?>')"  id="addToCalendar" style="display: none;"  class="kf-event-add-calendar em_color dbfl">
                        <img class="kf-google-calendar-add" src="<?php echo esc_url(plugins_url('/images/gcal.png', __FILE__)); ?>">
                       <?php echo EventM_UI_Strings::get('LABEL_ADD_TO_CALENDAR'); ?>
                        </div></p>
                        
                                  
                                </a>
                            </div>
                            <div class="pm-popup-mask"></div>    
                            <div id="pm-change-password-dialog">
                                <div class="pm-popup-container">
                                    <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
                                        <div class="title"><?php echo EventM_UI_Strings::get('LABEL_EVENT_ADDED'); ?>
                                            <div class="pm-popup-close pm-difr">
                                                <img src="<?php echo esc_url(plugins_url('/images/popup-close.png', __FILE__)); ?>"  height="24px" width="24px">
                                            </div>
                                        </div>
                                         <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
                                            <div class="pm-login-box GCal-confirm-message">
                                                <div class="pm-login-box-error pm-pad10" style="display:none;" id="pm_reset_passerror">
                                                </div>
                                                <!-----Form Starts----->
                                            </div>
                                        </div>

                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                       
                    <?php endif; ?>
                        
                        
                        
                        
                    </div>
                </div>
                 
                <div class="kf-event-attr difl">
                       <div class="kf-event-attr-name dbfl">
                   <?php echo EventM_UI_Strings::get('LABEL_BOOKING_STARTS'); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">  
                        <?php echo  em_showDateTime(em_time($event_model->start_booking_date)); ?>
                    </div>
                    
                    <div class="kf-event-attr-name dbfl">
                    <?php echo EventM_UI_Strings::get('LABEL_BOOKING_ENDS'); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">  
                        <?php echo  em_showDateTime(em_time($event_model->last_booking_date)); ?>
                    </div>
                    
                </div>
                    </div>
                <div class="dbfl">
                      <div class="kf-event-attr difl">
              <?php  $hide_booking_status = em_get_post_meta(get_the_ID(),'hide_booking_status');             
                if((!empty($hide_booking_status) && $hide_booking_status[0] !=1)|| (count($hide_booking_status)==0)): 
                $child_events = em_get_post_meta(get_the_ID(),'child_events');?>
               
            <?php    if(empty($child_events[0])):   ?> 
                     <div>
                        <div class="kf-event-attr-name dbfl">
                           <?php echo EventM_UI_Strings::get('LABEL_BOOKING_STATUS'); ?>
                        </div>
                        <div class="kf-event-attr-value dbfl">  
                            <?php
                        if ($capacity > 0):
                            ?>
                            <?php echo $sum; ?> / <?php echo $capacity; ?> 
                            <?php $width = ($sum / $capacity) * 100; ?>
                            <div id="progressbar" class="em_progressbar dbfl">
                                <div style="width:<?php echo $width . '%'; ?>" class="em_progressbar_fill em_bg" ></div>
                            </div>
                            <?php
                        else:
                            echo $sum.' '.EventM_UI_Strings::get('LABEL_SOLD');
                            ?>

                        <?php endif; ?>
                        </div>
                  </div>
                <?php
                endif;?>
                          
                <?php endif; ?>
                     </div>
                <div class="kf-event-attr difl">
                    <div class="kf-event-attr-name dbfl">
                        <?php echo EventM_UI_Strings::get('LABEL_LOCATION'); ?>
                    </div>
                        <a href="<?php echo add_query_arg('venue',$venue->term_id,get_permalink($global_settings->venues_page)); ?>"><?php echo $venue->name ?></a><br>
                    <?php if($venue_address):?>
                      
                    <div class="kf-event-attr-value dbfl">
                        <?php $venue_address; ?>
                    </div>
                    <?php else:
                       echo EventM_UI_Strings::get('LABEL_VENUE_DETAILS_NOT_AVAILABLE');
                    endif;
?>
                </div>
                    </div>
            </div>
        </div>
    <!--Event Header Ends--->
    <div class="kf-event-content kf-event-row dbfl">
        <?php $event_type_terms = wp_get_post_terms($event_model->id, EM_EVENT_TYPE_TAX);
                    if (!empty($event_type_terms)):
                        $event_type_terms = $event_type_terms[0];
                        if (!empty($event_type_terms) && count($event_type_terms) > 0):
                            $ages_group = em_get_term_meta($event_type_terms->term_id, 'age_group', true); 
                        endif;
                    endif;
                    
                    if (!empty($ages_group) || ($event_model->audience_notice) || $event_model->facebook_page || ((!empty($organizer_info['organizer_name'])) || (!empty($organizer_info['organizer_contact_details'])) && $organizer_info['hide_organizer'] != 1)):?>
        <!--start sidebar -->
        <div class="kf-event-col2 em_bg difl">
            <div class="kf-event-col-title em_bg">More Information</div>
             <?php if(!empty($ages_group)): ?>
        <div class="kf-event-attr dbfl">
           
                  
                    <div class="kf-event-attr-name em_color dbfl">
                        <?php echo EventM_UI_Strings::get("LABEL_AGE_GROUP"); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">  
                        <?php       if ($ages_group == "parental_guidance"):
                                        echo EventM_UI_Strings::get("LABEL_ALL_PARENTAL_GUIDANCE");
                                    else:
                                        echo $ages_group;  
                                    endif;
                          
                    ?>
                    </div>
        
                </div>
            <?php endif; ?>
            
            <?php  if($event_model->audience_notice):?>
            <div class="kf-event-attr dbfl">
                    <div class="kf-event-attr-name em_color dbfl">
                        <?php  echo EventM_UI_Strings::get("LABEL_NOTE"); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">

                        <?php    
                       echo $event_model->audience_notice; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($event_model->facebook_page):?>
            <div class="kf-event-attr dbfl">
                    <div class="kf-event-attr-name em_color dbfl">
                       <?php  echo EventM_UI_Strings::get("LABEL_FACEBOOK_PAGE"); ?>
                    </div>
                    <div class="kf-event-attr-value kf-fb-link dbfl">  
                        <?php
       
                echo $event_model->facebook_page . "&nbsp; &nbsp;" . "<a target='_blank' href = ". $event_model->facebook_page . "><i class='fa fa-external-link' aria-hidden='true'></i></a>";?>              

                    </div>
              </div>
            <?php endif; ?>
          
            <?php 
            if($organizer_info['hide_organizer'] != 1):
             if(!empty($organizer_info['organizer_name']) || (!empty($organizer_info['organizer_contact_details']))): ?>
            <div class="kf-event-attr dbfl">
                    <div class="kf-event-attr-name em_color dbfl">
                       <?php  echo EventM_UI_Strings::get("LABEL_ORGANIZER"); ?>
                    </div>
                    <div class="kf-event-attr-value dbfl">
                        <?php 
                     
                                    echo $organizer_info['organizer_name'] . "<br />";
                                    //  echo EventM_UI_Strings::get("LABEL_ORGANIZER_PHONE") . '&nbsp;&nbsp;';
                                    echo $organizer_info['organizer_contact_details'];
                                ?>
                    </div>
                </div>
            <?php endif;  
            endif; ?>
          
        </div>
         <?php endif; ?>
        <!--End of Sidebar !-->
        
        <div class="kf-event-col1 difl">
        <?php if(strlen(get_the_content())>0): ?>
            <div class="kf-event-attr-name dbfl"><?php  echo EventM_UI_Strings::get("LABEL_EVENT_DETAILS"); ?></div>
            <span><?php echo do_shortcode(get_the_content());?></span>
        <?php endif; ?>
            
       
            
            
             <?php
        // Event Gallery section
        $gallery_ids= $event_model->gallery_image_ids;     
        if (is_array($gallery_ids) && !empty($gallery_ids)):
            ?>       
            <div class="kf-event-attr-name dbfl"><?php  echo EventM_UI_Strings::get("LABEL_EVENT_PHOTOS"); ?></div>
            <div class="kf-event-gallery dbfl">
                                
            <div class="em_photo_gallery dbfl">
            <?php foreach ($gallery_ids as $id): ?>
                    <a class="difl" rel="gal" href="<?php echo wp_get_attachment_url($id); ?>"><?php echo wp_get_attachment_image($id, array(50, 50)); ?> </a>
            <?php endforeach; ?>
        </div> </div>                   
<?php endif;?>
        </div>
    </div>
    
    <!--- KF Multiday Event---->

    
        <?php
        $child_events = $event_model->child_events;
      
        if (isset($child_events) && !empty($child_events)):
             
            foreach ($child_events as $data):
                $child_name = $event_service->get_child_name($data);
                $start_date = em_showDateTime(em_get_post_meta($data,'start_date',true));
                ?> 
    <div class="kf-event-row kf-child-event-wrap dbfl">  
                <div class="kf-event-col2 difl"> 
                    <div class="kf_child_name"><span class="em_color"> <?php echo $child_name; ?></span></div>
                    <div><?php echo $start_date; ?></div>
                
                </div>
                <?php 
                $is_daily_event = em_get_post_meta($event_model->id,'is_daily_event',true);
              
                if($is_daily_event==1):
                    $child_post = get_post($data); ?>
                    <div class="kf-event-col1 difl"> <div class="kf_child_description"><?php echo $child_post->post_content; ?> </div></div>
                <?php endif; ?>
            </div> 

            <?php
            endforeach;
        endif;
        ?>
     <!--- KF Multiday Event Ends---->
    
    <!---Event Content Ends--->
        <?php
        $performers = $event_model->performer;
 
        if (!empty($performers[0])): ?>
    <?php
    $match = em_get_post_meta($event_model->id,'match');    
    ?> 
    
    
        <div class="kf-event-performers kf-event-row dbfl">
            <div class="kf-row-heading">
            <span class="kf-row-title em_color"><i class="fa fa-star-o" aria-hidden="true"></i> <?php  echo EventM_UI_Strings::get("LABEL_FEATURING"); ?></span>
            </div>
    
                <?php
                 if(!empty($match[0]) && $match[0]==1):?>
            <div class="kf-match-performers"> <span class="em_bg">VS</span></div>
        <?php  
        endif;
                foreach ($performers as $id):  
                
                    $status = get_post_status($id);
                    if ($status != 'trash'):
                        ?>                           
                        <?php
                        $performer = get_post($id);
                        $show_performer = get_post_meta($id, 'em_display_front', true);
                     
                        if (!empty($performer) && ($show_performer == 'true' || $show_performer == 1) ):?> 
                <?php $thumbnail_id = get_post_meta($id, '_thumbnail_id', true); ?>
                        <div class="kf-performer-card difl">
                                <div class="kf-performer-img difl">
                                        <?php if (!empty($thumbnail_id)) {
                                            ?>                               
                                            <a href="<?php echo get_permalink($id); ?>"><?php echo get_the_post_thumbnail($id, 'full'); ?></a>
                                            <?php
                                        } else {
                                            ?>
                                            <a href="<?php echo get_permalink($id); ?>"><img src="<?php echo esc_url(plugins_url('/images/dummy-performer.png', __FILE__)) ?>" alt="Dummy Image" ></a>
                                        <?php }
                                        ?>
                                </div>

               
                                    <div class="kf-performer-details difl">
                                        <div class="kf-performer-name dbfl em_wrap" title="<?php echo $performer->post_title; ?>">
                <?php echo $performer->post_title; ?><a href="<?php echo add_query_arg('performer',$performer->ID,get_permalink($global_settings->performers_page)); ?>" target="_blank"><?php echo '<i class="fa fa-external-link" aria-hidden="true"></i>'.'<br>'?></a>         
                                    </div>
                                    <?php
                                        $role = em_get_post_meta($id, 'role', true);
                                        if (!empty($role)):
                                            ?>
                                    <div class="kf-performer-role dbfl em_wrap" title="<?php echo em_get_post_meta($id, 'role', true); ?>">
                                      
                    <?php echo em_get_post_meta($id, 'role', true); ?>
                                        </div>
                <?php endif; ?>
                                    
                                    <?php if (!empty($performer->post_content)) { ?>
                                        <div class="kf-performer-description dbfl"><?php echo wp_trim_words($performer->post_content, 4); ?> <a href="<?php echo get_permalink($id); ?>" target="_blank"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a></div>
                                  
                                    <?php
                                } else {
                                    echo '<span class="kf-performer-nodetails">Performer details are not available</span>';
                                }
                                ?>
                                          </div>
                            </div>                            
                        <?php
                        endif;
                        //   endforeach;
                        ?>
                <?php endif; // Performer section ends here         
             endforeach;
            ?>       
        </div>
     <?php endif;
        ?>
<div class="kf-event-venue-area dbfl">
     <div class="kf-event-performers kf-event-row dbfl">
            <div class="kf-row-heading">
            <span class="kf-row-title em_color"><i class="fa fa-map-o" aria-hidden="true"></i><?php  echo EventM_UI_Strings::get("LABEL_LOCATION"); ?></span>
            </div>
    
   <?php $gmap_api_key= em_global_settings('gmap_api_key');
                        if (!empty($gmap_api_key) && !empty($venue_address)): ?>
         <div class="kf-event-venue-map dbfl">
                        <div>
                            <div data-venue-id="<?php echo $venue->term_id; ?>" id="em_event_map_canvas" style="height: 400px;"</div>
                            </div>
                        </div>
                            
                                 </div>
                     <?php   endif;
?>
  
</div>
    <div class="kf-event-venue-details dbfl">
        <div class="kf-event-venue-name dbfl">
                <?php
                        // Venue images
                        $gallery_ids = em_get_term_meta($venue->term_id, 'gallery_images', true);
                        if (!empty($gallery_ids) && is_array($gallery_ids)):
                            ?>                                            
                            <div class="em_venue_images em_block dbfl">
                                <?php
                                for ($i = 0; $i < 3; $i++):
                                    if (isset($gallery_ids[$i])):
                                        echo wp_get_attachment_image($gallery_ids[$i], array(150, 150));
                                    else:
                                        break;
                                    endif;
                                endfor;
                                ?>   
                            </div>
                                <?php endif; // Venue images ends here ?>
        <?php if (!empty($venue->name)): 
     ?>  
        <i class="fa fa-map-marker" aria-hidden="true"></i>
        <?php echo $venue->name;?>  <a href="<?php echo add_query_arg('venue',$venue->term_id,get_permalink($global_settings->venues_page)); ?>" target="_blank"><?php echo '<i class="fa fa-external-link" aria-hidden="true"></i>'.'<br>'?></a>                  

        <?php endif;?>
        </div>
                             
    <?php
                if (!empty($terms) && count($terms) > 0):
                    $venue = $terms[0];
                    $venue_address = em_get_term_meta($venue->term_id, 'address', true);
                endif;

                if (!empty($venue_address)):
                    ?>
                    <div class="kf-event-venue-address dbfl">
                        <?php echo $venue_address; ?>
                    </div>
                    <?php
                    else:
                        echo '<span class="kf_no_info">'.EventM_UI_Strings::get('LABEL_VENUE_DETAILS_NOT_AVAILABLE').'</span>';
                endif;
                ?> 
         <?php
                $seating_capacity = em_get_term_meta($venue->term_id, 'seating_capacity', true);
                if ($seating_capacity > 0):?>
                    <div class="kf-event-venue-capacity em_color dbfl">   
                    <?php echo EventM_UI_Strings::get('LABEL_CAN_HOLD').' '.$seating_capacity .' '.EventM_UI_Strings::get('LABEL_PEOPLE'); ?>
                    </div> 
                <?php endif;?>
        <?php 
            if(!empty($venue_address)):
        ?>
        <div class="kf-event-venue-markers dbfl em_color">
            <a target="blank" href='https://www.google.com/maps?saddr=My+Location&daddr=<?php echo urlencode($venue_address); ?>&dirflg=w'>
                <i class="fa fa-male" aria-hidden="true"></i>
            </a>
            <a target="blank" href='https://www.google.com/maps?saddr=My+Location&daddr=<?php echo urlencode($venue_address); ?>&dirflg=d'>
                <i class="fa fa-car" aria-hidden="true"></i>
            </a>
            
            <a target="blank" href='https://www.google.com/maps?saddr=My+Location&daddr=<?php echo urlencode($venue_address); ?>&dirflg=r'>
                <i class="fa fa-bus" aria-hidden="true"></i>
            </a>
            
        </div>
        <?php endif; ?>
        </div>
          <?php 
                $description = em_get_term_meta($venue->term_id, 'description', true); //description of venue  
                if (!empty($description)):?>
                    <div class="kf-event-venue-description difl">
                    <?php echo do_shortcode( wp_trim_words($description, 50, '...'));
                        //   echo wp_trim_words($description, 50, '...');
                    ?>                             
                    <a href="<?php echo add_query_arg('venue',$venue->term_id,get_permalink($global_settings->venues_page)); ?>" target="_blank"><?php echo '<i class="fa fa-arrow-circle-right" aria-hidden="true"></i>'.'<br>'?></a>                  
 
                    </div> 
                <?php endif;?>
    <div class="kf-event-venue-events em_bg difl">
        <div class="kf-event-attr-name em_bg dbfl">
            <i class="fa fa-clock-o" aria-hidden="true"></i>
             <?php echo EventM_UI_Strings::get('LABEL_UPCOMING_EVENTS_HERE');?>
                    </div>
        <div class="kf-upcoming-events dbfl">
                            <?php
                            $venue_service = new EventM_Venue_Service();
                            $upcoming_venues = $venue_service->get_upcoming_events($venue->term_id);
                            if(!empty($upcoming_venues)){
                            foreach ($upcoming_venues as $key => $event_name):
                                // print_r($event_name->ID);
                                $event_details = em_get_post_meta($event_name->ID);
                                $start_date = $event_details['em_start_date'];
                                $end_date = $event_details['em_end_date'];
                                $today = current_time('timestamp');
//                                 print_r($start_date[0]);
//                                 print_r($today);
                                ?>

                                <div class="dbfl">
                                    <a href="<?php echo get_permalink($event_name->ID); ?>">
                                        <?php echo $event_name->post_title; ?>
                                    </a>
                                    <?php if ($today > $start_date[0]):
                                        ?>
                                        <span class="kf-live em_color"><?php echo EventM_UI_Strings::get('LABEL_LIVE');?></span>
                                <?php endif;
                                ?>
                                </div>
                            <?php endforeach;
                            }else{
                               echo EventM_UI_Strings::get('LABEL_NO_UPCOMING_EVENT_AT_VENUE');
                            }
                            ?>
        </div> 
        </div>
    </div>
        <div class="kf-event-sponsors dbfl">
        <?php
            $sponser_gallery_ids = em_get_post_meta($event_model->id, 'sponser_image_ids', true);
            if (is_array($sponser_gallery_ids) && count($sponser_gallery_ids) > 0):
        ?>    
            <div class="kf-row-heading">
            <span class="kf-row-title em_color"><i class="fa fa-hashtag" aria-hidden="true"></i><?php  echo EventM_UI_Strings::get('LABEL_SPONSOR'); ?></span></div>
    <?php
    foreach ($sponser_gallery_ids as $id):
        echo '<div class="kf-event-sponsor-logo difl">' . wp_get_attachment_image($id, 'full') . '</div>';
    endforeach;
    ?>
      
        
              <?php
        endif;
        ?>
   </div>
</div>
</div>

<script>
    widthAdjust(".em_performer");
   
 em_jQuery('#addToCalendar').click(function () {
        em_jQuery(this).prop("disabled", true);
    });
    
    hide_entry_title();
 
</script>

    
<?php wp_reset_postdata(); ?>
