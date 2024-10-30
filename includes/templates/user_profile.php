<?php   
        wp_enqueue_script( 'em_ctabs_script',plugin_dir_url(__DIR__) . 'templates/js/em_custom_tabs.js',false );
        wp_enqueue_style( 'em_ctabs_style',plugin_dir_url(__DIR__) . 'templates/css/em_custom_tabs.css',false );
            em_localize_map_info('em-google-map');
          //  wp_enqueue_script('jquery-ui-tabs', array('jquery'));
            
            if(!is_user_logged_in()):
               include_once('user_registration.php');
            else:   
            // Get current user details
            $user= wp_get_current_user();
            
            // Get booking details
            $service= new EventM_Booking_Service();
           $bookings= $service->get_bookings_by_user($user->ID); 
            
            // Get Venue Location data
            $event_service= new EventM_Service();
            $upcoming_events=  $event_service->get_upcoming_events();
            $venue_service= new EventM_Venue_Service();
            $venues = $venue_service->get_venue_addresses_by_events($upcoming_events);
            
            // Add thickbox library for details pop up
            add_thickbox();
            
         
            
?>



<?php 
    $i=0;
$booking_service = new EventM_Booking_Service();
       $order_id = event_m_get_param('order_idss');
       $booking_id = explode(",",$order_id);
       $booking_status= array('publish'=>"Completed",
                                    'em-cancelled'=>"Cancelled",
                                    'em-pending'=>"Pending",
                                    'em-refunded'=>"Refunded");  
   
    if(!empty($order_id)): ?>
         <div class="emagic">
   <?php   $event_service->get_header();
       foreach($booking_id as $order_ids): 
         $i++;
          
         $data=$booking_service->get_event_by_booking($order_ids);
         if(!empty($data)):
         $booking = get_post($order_ids);
         //print_r($booking);
        $status =EventM_Constants::$status[$booking->post_status];
   
       ?>

                        <div class="kf-booking-confirmation dbfl">
                         
                          <?php   if($i == 1):?>
                            <div class="kf-booking-confirmation-notice">
                                   <?php if($status=='Pending'):
                                      echo 'Booking is Pending';
                                  endif; 
                                      ?>

                                   <?php if($status=='Completed'):
                                      echo 'Congratulations, your booking has been confirmed!';
                                  endif; 
                                      ?>
                                  <?php if($status=='Cancelled'):
                                      echo 'Your booking has been cancelled!';
                                  endif; 
                                      ?>

                              </div>
                            <?php endif; ?>
                        
 <?php
                $global_options = get_option(EM_GLOBAL_SETTINGS);
                if (isset($global_options['social_sharing'])):
                    $social_sharing = $global_options['social_sharing'];
                    if ($social_sharing == 1):
                        ?>
                   
                        <div class="kf-event-share-icon difl">
                            <img class="em_share_fb em_share" aria-hidden="true" height="24" width="153" src="<?php echo esc_url( plugins_url( '/images/kf-fb-share.png', __FILE__ ) ) ?>" alt="share on facebook" >
                            <div id="em_fb_root"></div>
                        </div>
                        <?php
                    endif;
                endif;
                ?>

                <div class="kf-booked-event-details em_block dbfl">
                    <div class="kf-booked-event-cover difl">
                        <?php $event_id = em_get_post_meta($order_ids,'event_id');?>
                              <?php
                         $thumbnail_id = get_post_meta($event_id[0], '_thumbnail_id', true);
                        if (!empty($thumbnail_id)) {
                         
                            echo wp_get_attachment_image($thumbnail_id, 'full');
                          
                        }
                        else{?>
                           <img height="150" width="150" src="<?php echo esc_url( plugins_url( '/images/dummy_image_thumbnail.png', __FILE__ ) ) ?>" alt="Dummy Image" >
                <?php        }
?>
                        
                        <!--<img src="http://localhost:8888/kikfyre/wp-content/uploads/2017/04/cover.jpg" width="194" height="194">-->
                    <div class="kf-booked-event-print em_bg dbfl em_block">
                                    <a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=em_show_booking_details&id=<?php echo $data->ID; ?>" class="thickbox details bg_gradient bg_grad_button">View Details</a>
                                </div>
                    
                    </div>
                    <div class="kf-booked-event-details-wrap difl">
                    <div class="kf-booked-event-name"> <?php echo $data->event_name; ?></div>
                    <div class="kf-booked-event-date em_color"> <?php echo $data->event_date; ?></div>
                    <!--         Add to Calendar-->
                    <?php
                    if (isset($global_options['gcal_sharing'])):
                        $gcal_sharing = $global_options['gcal_sharing'];
                        if ($gcal_sharing == 1):
                            ?>
                    <div id="add-to-google-calendar">

                            <span><label><input type="text" id="event_<?php echo $data->event_id; ?>"  style="display: none" value="<?php echo $data->event_name; ?>"></label></span>
                            <span><label> <input type="text" id="s_date_<?php echo $data->event_id; ?>"  style="display: none"value="<?php echo em_showDateTime(em_get_post_meta($data->event_id, 'start_date', true), true); ?>" ></label></span>
                            <span><label><input type="text" id="e_date_<?php echo $data->event_id; ?>"  style="display: none" value="<?php echo em_showDateTime(em_get_post_meta($data->event_id, 'end_date', true), true); ?>" ></label></span>

                            <!--<p><button id="authorize-button" style="display: none; background-image: url(<?php echo esc_url(plugins_url('/images/google-calendar-icon-66527.png', __FILE__)); ?>)"></button></p>-->
                              
                            <div onclick="em_gcal_handle_auth_click()" id="authorize-button" class="kf-event-add-calendar em_color dbfl" style="display: none;">
                        <img class="kf-google-calendar-add" src="<?php echo esc_url(plugins_url('/images/gcal.png', __FILE__)); ?>">
                        <a class="kf-add-calendar">Add To Calendar</a>
                        </div>
                      
                            <div class="pm-edit-user pm-difl">
                                <a href="" class="difl" id="pm-change-password" onclick="return false;">
                                                                 
                                            
                        <div onclick="em_add_to_calendar('<?php echo $data->event_id; ?>')" id="addToCalendar" style="display: none;" class="kf-event-add-calendar em_color dbfl">
                        <img class="kf-google-calendar-add" src="<?php echo esc_url(plugins_url('/images/gcal.png', __FILE__)); ?>">
                         <a class="kf-add-calendar">Add To Calendar</a>
                        </div>
                      
                                       
                         <!--<p><button disabled class="kf_cal_disabled" title="Add Event to Google Calendar" id="addToCalendar" style="display: none;  background-image: url(<?php echo esc_url(plugins_url('/images/google-calendar-icon-66527.png', __FILE__)); ?>)"></button></p>-->

                               
                                </a>
                            </div>
                            <div class="pm-popup-mask"></div>    
                            <div id="pm-change-password-dialog">
                                                        <div class="pm-popup-container">
                                                            <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
                                                                <div class="title">Event Added
                                                                    <div class="pm-popup-close pm-difr">
                                                                        <img src="<?php echo esc_url(plugins_url('/images/popup-close.png', __FILE__)); ?>"  height="24px" width="24px">
                                                                    </div>
                                                                </div>
                                                                <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
    <div class="pm-login-box GCal-confirm-message">
        <div class="pm-login-box-error pm-pad10" style="" id="pm_reset_passerror">
        </div>
    </div>
</div>





                                                            </div>
                                                        </div>
                                                    </div>
                        </div>
                            <?php
                        endif;
                    endif;
                    ?>

                    <div class="kf-booked-event-description"><?php echo do_shortcode($data->description); ?></div>
                </div>
                    </div>

              

</div>
                <?php 
                endif;
                endforeach; ?>
           <?php $gmap_api_key= em_global_settings('gmap_api_key');
          
            if (!empty($gmap_api_key) && !empty($data->address)): ?>
            <div class="kf-event-venue-info difr">
                <div class="kf-booked-event-venue-name"><?php echo $data->venue_name ?></div>
                <div class="kf-booked-event-venue-address"><?php echo $data->address; ?></div>
                <?php $direction_links = '<a target="blank" href="https://www.google.com/maps?saddr=My+Location&daddr=' . $data->address . '">'. $data->venue_name .'</a> '; ?>
                <div class="em_venue_dir">Directions : <?php echo $direction_links; ?></div>
                <div data-venue-id="<?php echo $data->venue_id; ?>" id="em_booking_map_canvas" style="height: 400px;"></div>

            </div>
            <?php endif;?>
                            
     <div class="kf-go-to-profile-page difl"> <a href="<?php echo get_permalink(em_global_settings('profile_page')); ?>">Go to User Profile Page </a></div>
          
        

<script>
     em_jQuery(document).ready(function () {
            em_load_map('booking', 'em_booking_map_canvas');
        });
</script>
  </div>
 <?php  

else:
?>




<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="emagic">
    <a id='em_dummy_link_for_primary_color_extraction' style='display:none' href='#'></a>
    
    <div class="em_block dbfl">
        <div class="em_username_profile em_block dbfl">
            <?php echo EventM_UI_Strings::get("WELCOME").', <span class="profile_title">'.ucwords($user->display_name).'</span>'; ?> &nbsp; 
        </div>
    </div>
    
    <div class="em-tabmenu-container em_block dbfl">
        
        <div class="tabs">
            
            <div class="tab-links em-tab-vertical-menu ">
                <div class="em-tab-nav">
                <div class="emtabs_head" data-emt-tabcontent="#tab1"><i class="material-icons">receipt</i><?php echo EventM_UI_Strings::get("MY_BOOKINGS"); ?></div>
                <div class="emtabs_head" data-emt-tabcontent="#tab2"><i class="material-icons">directions</i>MAP</div>
                <div class="emtabs_head" data-emt-tabcontent="#tab3"><i class="material-icons">credit_card</i><?php echo EventM_UI_Strings::get("TRANSACTIONS"); ?></div>
                <div class="emtabs_head" data-emt-tabcontent="#tab4"><i class="material-icons">account_box</i><?php echo EventM_UI_Strings::get("ACCOUNT"); ?></div>
                </div>
            </div>

            <div class="em-tab-content-main">
                <div id="tab1" class="tab active em_block dbfl">
                    <table class="em_profile_table em_block">
                        <tbody>
                            <tr>
                                <th class="em_profile_serial"></th>                              
                                <th><?php echo EventM_UI_Strings::get("EVENT_NAME"); ?></th>                                
                                <th><?php echo EventM_UI_Strings::get("EVENT_DATE"); ?></th>
                                <th></th>
                            </tr>
                            
                            <?php
                            if(!empty($bookings)):
                                $i=0;
                               foreach ($bookings as $booking):    
                                 
                                $event_id= em_get_post_meta($booking->ID, 'event_id', true);
                                $event= get_post($event_id);
                                //print_r($event);
                                 if(!empty($event)):
                                      $i++;
                                    $order_info= em_get_post_meta($booking->ID, 'order_info', true);
                                    $discount= ($order_info['quantity']*$order_info['item_price']*$order_info['discount'])/100;;
                                    $total_price= ($order_info['quantity']*$order_info['item_price'])-$discount;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php if($event->post_title): echo $event->post_title;  endif; ?></td>                              
                                    <td><?php if(em_showDateTime(em_get_post_meta($event_id, 'end_date', true),false)): echo em_showDateTime(em_get_post_meta($event_id, 'start_date', true),false);  endif;?></td>
                                    <td><a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=em_show_booking_details&id=<?php echo $booking->ID; ?>" class="thickbox details bg_gradient bg_grad_button">Details</a></td>

                                        <?php $status =EventM_Constants::$status[$booking->post_status]; 
//                                        if($status != 'Pending'):?>                                      
                                            <!--<td><a href="//<?php //echo site_url(); ?>/wp-admin/admin-ajax.php?action=em_print_ticket&booking_id=<?php echo $booking->ID; ?>" class= "bg_gradient bg_grad_button" >Ticket</a></td>-->
                                      <?php //else:?>
<!--                                            <td><a class="bg_gradient bg_grad_button em_not_bookable">Ticket</a></td>-->
                                      <?php //endif; ?>

                                </tr>
                            <?php 
                                endif;
                                 endforeach;
                             endif;
                            ?>

                        </tbody></table>
                </div>
                
                <div id="tab2" class="tab em_block dbfl">
                    <?php
                    $venue_ids= array();
                    $v_ids='';
                    $venue_addresses= array();
                    $datas= array();
                    $direction_links= '';
                    foreach($venues as $key => $venue):                      
                        $venue_ids[]= $venue->venue_id;
                        $venue_addresses[$key]['address'] = $venue->address;
                        $venue_addresses[$key]['name']=$venue->name;                     
                    endforeach;  
                    
                    $address= array_unique($venue_addresses, SORT_REGULAR);
                 
                    foreach($address as $key => $values):                       
                         $direction_links .= '<a target="blank" href="https://www.google.com/maps?saddr=My+Location&daddr='.$values['address'].'">'.$values['name'].'</a> ';
                    endforeach;
                    
                    
                    if(count($venue_ids)>0)
                        $v_ids=implode(',', $venue_ids);  
                    ?>
                   
                    <div class="em_venue_dir">Directions : <?php echo $direction_links; ?></div>
                    <div data-venue-ids="<?php echo $v_ids; ?>" id="em_user_event_venue_canvas" style="height: 400px;"></div>             
                </div>

                <div id="tab3" class="tab">
                    <table class="em_profile_table">
                        <tbody>
                            <tr>
                                <th class="em_profile_serial"></th>
                                <th><?php echo EventM_UI_Strings::get("EVENT_NAME"); ?></th>
                                <th><?php echo EventM_UI_Strings::get("LABEL_AMOUNT"); ?></th>
                                <th><?php echo EventM_UI_Strings::get("LABEL_STATUS"); ?></th>
                                <!--<th><?php// echo EventM_UI_Strings::get("LABEL_DATE"); ?></th>-->
                                <th><?php echo 'BOOKED ON'; ?></th>

                            </tr>
                            
                            <?php
                            if(!empty($bookings)):
                                
                               foreach ($bookings as $booking):
                                  
                                $event_id= em_get_post_meta($booking->ID, 'event_id', true);    
                               $event_dao = new EventM_Event_DAO();
                $booking_service = new EventM_Booking_Service();
                    
                                    $event= get_post($event_id);
                                    if(!empty($event)):
                                   
                                    $order_info= $event_dao->get_meta($booking->ID,'order_info');  
//                                   
                                    
               // $seat_pos = $order_info['seat_pos'];
               
                $total_price=$booking_service->get_final_price($booking->ID);  
               
               
                $payment_log= maybe_unserialize(em_get_post_meta($booking->ID, 'payment_log', true));   
                if(isset($order_info['currency']) && !empty($order_info['currency'])):
                           $currency_symbol= $order_info['currency'];
                elseif(isset($payment_log['payment_gateway']) && ($payment_log['payment_gateway'] == 'paypal' )):
                            $currency_symbol = $payment_log['mc_currency'];
                else:   
                endif;            
               
                                        
                                ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $event->post_title; ?></td>
                                    <td><?php echo $total_price.$currency_symbol; ?></td>
                                    <td>
                                        <?php 
                                            $offline_status = EventM_Constants::$offline_status[$payment_log['offline_status']];
                                             if(isset($offline_status) &&  EventM_Constants::$status[$booking->post_status]=='Refunded'){
                                                echo EventM_Constants::$status[$booking->post_status]; 
                                            }  
                                            else if(isset($offline_status)){                                         
                                                echo $offline_status;                                            
                                            }
                                            else{
                                                echo EventM_Constants::$status[$booking->post_status]; 
                                            }                                   
                                            
                                          
                                        ?>
                                    </td>
                                    <td><?php echo  $booking->post_date;?></td>
                                   
                                </tr>
                            <?php 
                                endif;
                                 endforeach;
                             endif;
                            ?>

                        </tbody></table>
                </div>

             
                    <div id="tab4" class="tab em_block dbfl">
                    <?php $current_user = wp_get_current_user(); 
                   // echo'<pre>';print_r($current_user);
                    
                    if(!$current_user instanceof WP_User) 
                        return false;
               
                    if(is_registration_magic_active())
                    {                            
                     em_rm_custom_data($current_user->ID);                          
                    }
                    else{?>
                        <table>
                            <tr><th>Name:</th><td><?php echo $current_user->display_name; ?></td></tr>
                            <tr><th>Email:</th><td><?php echo $current_user->user_email; ?></td></tr>
                            <tr><th>Registered On:</th><td><?php echo $current_user->user_registered; ?></td></tr>
                        </table>
                   <?php }
?>
                </div>
            </div>
        </div>

    </div>


    
    
    
    
    
    
    
    
    <script>
         /* setTimeout(function () {
                jQuery(".tabs").tabs();
            }, 2000);*/
            var g_em_customtab, g_em_acc_color;
        em_jQuery(document).ready(function(){
    
        
        //get accent color from theme
        g_em_acc_color = jQuery('#em_dummy_link_for_primary_color_extraction').css('color');
        if(typeof g_em_acc_color == 'undefined')
            g_em_acc_color = '#000';
        
        var emagic_jq = jQuery(".emagic");
        emagic_jq.find("[data-em_apply_acc_color='true']").css('color',g_em_acc_color);
        emagic_jq.find("[data-em_apply_acc_bgcolor='true']").css('background-color',g_em_acc_color);
        g_em_customtab = new EMCustomTabs({
                                        container: '.tabs',
                                        animation: 'fade',
                                        accentColor: g_em_acc_color,
                                        activeTabIndex: 0,
                                        onTabChange: function(i) { 
                                                        if(i==1) {
                                                             em_load_map('user_profile','em_user_event_venue_canvas');
                                                        }
                                                    }
                                    });
    });       
    </script>
    
   
</div>


<?php
    endif;
    endif;
?>




