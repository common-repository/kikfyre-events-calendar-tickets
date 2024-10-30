<?php   em_localize_map_info('em-google-map');
        add_thickbox();
        // This page should be accessible only after login
        // and if Venue is given for selected Event
        $event_id= event_m_get_param('event_id'); 
        $event_service= new EventM_Service();
        $venue_id= $event_service->get_venue($event_id);
        
        
        // Get Event object
        $event= get_post($event_id);
        $single_price= em_get_post_meta($event_id);
        if(!empty($venue_id) && $venue_id>0 && is_user_logged_in()):
            add_thickbox();
?>
<div id="booking_dialog"   class="booking_dialog emagic" ng-app="eventMagicApp" ng-controller="emBookingCtrl"  ng-init="initialize(<?php echo $venue_id,','.$event_id; ?>)" ng-cloak>
    <div class="em_progress_screen" ng-show="requestInProgress"></div>
    
    <div class="emagic">
<!--        <div>Please select seats for the day(s) you wish to book. Once you have made the selection, you can
proceed to checkout</div>-->
        
    <div class="em_child_event_cards dbfl" ng-show="parent.children.length>0">

        <div ng-repeat="child in parent.children track by $index"  class="kf_day_card difl" ng-class="{kf_event_sold:child.available_seats==0}">
            <div class="kf-day-card-wrap dbfl"> 
                <div class="kf-day-event-head" >
                    <div class="kf-day-event-title">{{child.name}}</div>
                    <div class="kf-day-event-date" >{{child.start_date}}</div>

                </div>
                <div class="booking_seat_status">
                    <div class="kf-seats-left">
                        <span class="kf_seats_left" ng-if="child.available_seats>0 && child.available_seats<=10">{{child.available_seats}} seats left</span>


                    </div>
                </div>
                <div class="em_price_ticket"><!--<?php echo EventM_UI_Strings::get('LABEL_PER_SEAT'); ?> --> <span class="em_price_per_ticket ">{{child.currency_symbol}}{{child.price}}</span></div>
                <div ng-if="child.bookable == false " class="kf-event-expired">
                    <div class="kf-event-expired-label"><?php echo 'ENDED'; ?></div>
                </div> 
               
                <div class="kf-seats-soldout" ng-if="child.available_seats==0">
                    <div class="kf-event-expired-label" ><?php echo EventM_UI_Strings::get('LABEL_SOLD_OUT'); ?></div>
                </div>

                <!--<div class="kf_available_seat">
                    {{child.booked_seats}}/{{child.seating_capacity}}
                </div>-->

                <div ng-show="child.type=='seats'" class="kf-event-booking-mod em_block" > <a ng-click="loadEventForBooking(child.event_id)">Select Seats</a></div>
                <div ng-show="child.type=='standings'" class="kf-event-booking-mod em_block" > <a ng-click="loadEventForBooking(child.event_id)">Book</a></div>

            </div> 

        </div>
    </div>    
        
    <div ng-hide="show_cart" >
        <div class="kf-seat-table-popup kf_config_pop_wrap" ng-show="event.venue.type=='seats'" >
            <!-- Event Seat structure -->
            <div class="kf-seat-table-popup-overlay" ng-click="show_cart=true"></div>
            <div id="kf-seat-table-parent" class="em_block dbfl kf_config_pop " >

                <div class="kf-seat-table-popup-head dbfl">
                    <div ng-click="show_cart=true" class="kf-modal-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                        </svg></div>

                    <div class="proceed_button_container em_event_row difr">
                        <button  id="kf_seat_update" class="em_header_button kf-button em_color" ng-hide="update_cart" ng-disabled="requestInProgress"  ng-click="orderSeats()">Proceed</button>
                        <button ng-disabled="requestInProgress" id="kf_update_cart" class="em_header_button kf-button em_color" ng-show="update_cart" ng-click="updateOrder()"><?php echo EventM_UI_Strings::get('LABEL_UPDATE_CART'); ?></button>
                        <button class="em_header_button kf-button em_color" ng-click="display_cart(true)" ng-disabled="!(orders.length>0)"><?php echo EventM_UI_Strings::get('LABEL_SHOW_CART'); ?></button>
                    </div>

                    <div class="kf-popup-title">Please Select your seat(s) below</div>
                    <div  class="kf-popup-sub-title">You can only select from available seats. Once you are done, click proceed to checkout. </div>



                </div>




                <div class="kf-booking-seat-wrap dbfl">

                <table class="em_venue_seating" ng-style="seat_container_width">
                    <tr><td class="kf-booking-head">
                  <div class="kf-seat-selector">   Selected Seats: {{selectedSeats.length}} </div>
                                <div class="kf-single-ticket-price">  Price {{event.ticket_price}} {{currency_symbol}} per seat</div>
                                <div class="kf-max-ticket-booking-note"ng-show="event.max_tickets_per_person > 0"><?php echo EventM_UI_Strings::get('NOTICE_MAX_TICKET_PP'); ?>{{event.max_tickets_per_person}}</div>

                                <div class="kf-legends dbfl em_block "> 
                                    <div class="kf-legend difl"><span class="kf-available kf-legend-box"></span><?php echo EventM_UI_Strings::get('LABEL_AVAILABLE'); ?></div>
                                    <div class="kf-legend difl"><span class="kf-booked kf-legend-box"></span><?php echo EventM_UI_Strings::get('LABEL_BOOKED'); ?></div>   
                                    <div class="kf-legend difl"><span class="kf-reserved kf-legend-box"></span><?php echo EventM_UI_Strings::get('LABEL_RESERVED'); ?></div>  
                                    <div class="kf-legend difl"><span class="kf-selected kf-legend-box"></span><?php echo EventM_UI_Strings::get('LABEL_SELECTED'); ?></div>
                                </div></td></tr>

                        <tr ng-repeat="row in event.seats" class="row isles_row_spacer" id="row{{$index}}" ng-style="{'margin-top':row[0].rowMargin}">
                            <td class="row_selection_bar" ng-click="selectRow($index)">
                                <div class="em_seat_row_number">{{getRowAlphabet($index)}}</div>
                            </td>

    <!--                            <td ng-repeat="seat in row" ng-init="adjustContainerWidth(seat.columnMargin,$parent.$index)" class="seat isles_col_spacer" ng-class="seat.type"  id="ui{{$parent.$index}}-{{$index}}" 
                                    ng-style="{'margin-left':seat.columnMargin}"
                                    ng-click="selectSeat(seat, $parent.$index, $index)">

                                    <div class="seat_avail seat_status">{{seat.uniqueIndex}} </div>
                                </td>-->

                            <td ng-repeat="seat in row" ng-init="adjustContainerWidth(seat.columnMargin,$parent.$index)" class="seat isles_col_spacer" ng-class="seat.type" id="ui{{$parent.$index}}-{{$index}}" 
                                ng-style="{'margin-left':seat.columnMargin}">
                                <div  ng-dblclick="selectColumn($index)" ng-if="$parent.$index==0" class="em_seat_col_number">{{$index}}</div>
                                <div class="seat_avail seat_avail_number seat_status">{{seat.col + 1}}</div>
                                <div  id="pm_seat"  class="seat_avail seat_status" ng-click="selectSeat(seat, $parent.$index, $index)" >{{seat.uniqueIndex}} </div>
                            </td>


                        </tr>
                    </table>

                    <div class="em_booking_screen dbfl"><?php echo EventM_UI_Strings::get('LABEL_ALL_EYE'); ?></div>
                </div>

             


            </div>
        </div>

        <!-- Standing-->    
        <div class="kf-standing-type-popup kf-standing-type-popup-wrap" ng-show="event.venue.type=='standings' || event.venue.type==''">
                        <div class="kf-seat-table-popup-overlay" ng-click="show_cart=true"></div>
            <div id="kf-seat-table-parent" class="em_block dbfl kf_config_pop " >
                <div class="kf-seat-table-popup-head dbfl"> 
                    <div ng-click="show_cart=true" class="kf-modal-close"><svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                        </svg></div>

                    <div class="proceed_button_container em_event_row difr">                      
                        <button class="em_header_button kf-button em_color" id="kf_standing_update" ng-hide="update_cart" ng-disabled="requestInProgress" ng-click="orderStandings()"><?php echo "Proceed"; ?></button>
                        <button class="bg_gradient kf-button em_color" id="kf_update_cart" ng-show="update_cart" ng-disabled="requestInProgress" ng-click="updateOrder()"><?php echo EventM_UI_Strings::get('LABEL_UPDATE_CART'); ?></button>
                        <button class="bg_gradient kf-button em_color" ng-click="display_cart(true)" ng-disabled="!(orders.length>0)"><?php echo EventM_UI_Strings::get('LABEL_SHOW_CART'); ?></button>
                    </div>

                    <div ng-show="event.venue.type=='seats'" class="kf-popup-title">Please Select your seat(s) below</div>
                    <div ng-show="event.venue.type=='standings'" class="kf-popup-title">Please Enter number of tickets you wish to book.</div>

                    <div ng-show="event.venue.type=='seats'" class="kf-popup-sub-title">You can only select from available seats. Once you are done, click proceed to checkout.</div>
                    <div ng-show="event.venue.type=='standings'" class="kf-popup-sub-title">Once you are done, click proceed to checkout.</div>

                </div>
                <div class="kf-single-ticket-price">Price {{event.ticket_price}} {{currency_symbol}} per seat</div>
                <div class="kf-standing-order difl" ><input type="text" name="quantity" value="1" id="standing_order_quantity" /></div>
                <div id="show_popup_loader"></div>
            </div>
        </div>
    </div> 
        
    {{pos}}  
    
    <div id="booking_summary" class="em-before-payment dbfl" ng-show="show_cart && orders.length>0"> 
        <div class="kf-before-payment-wrap dbfl">
        
        <div ng-show="event.venue.type=='seats'">
            <div id="div_id" class="em_booking_heading em_event_attr_box em_align_center"><?php echo EventM_UI_Strings::get('LABEL_BOOKING_SUMMARY'); ?></div>

    <!--                <div class="em_booking_event_title em_event_attr_box em_align_center"><?php echo $event->post_title; ?></div>-->

            <div class="kf-payment-details em_floatfix dbfl">    

                <div ng-repeat="tmp in orders track by $index"  class="dbfl kf-checkout-info">
                    <div class="qty">{{tmp.quantity}} Tickets </div>
                    <strong>for</strong>
                    <div class="em_booking_event_title em_event_attr_box em_align_center">{{tmp.name}}</div> 
                    <div class="kf-booking-event-date">On {{tmp.start_date}} at <span class="kf-booking-event-vanue">{{event.venue.name}}</span></div>


                    <div class="kf-booked-event-seat-num">Seat Nos.- {{tmp.item_number}}<a ng-click="loadEventForBooking(tmp.event_id)" > Change </a></div>
                    <div class="em_total_price em_pay_detail em_align_right difl">
                        {{currency_symbol}}{{ (tmp.single_price * tmp.quantity) }}
                    </div>

                    <!--                        <div class="em_selected_seats em_pay_detail difl em_align_left">
                                                <div>{{tmp.item_number}}<span ng-click="loadEventForBooking(tmp.event_id)"> Edit </span></div>
                                            </div>
                                            <div class="em_total_price em_pay_detail em_align_right difl">
                                                {{currency_symbol}} {{ (tmp.single_price * tmp.quantity) }}
                                            </div>-->

                </div>  

            </div>

        </div>    

        <div ng-show="event.venue.type=='standings'">
            <div class="em_booking_heading em_event_attr_box em_align_center"><?php echo EventM_UI_Strings::get('LABEL_BOOKING_SUMMARY'); ?></div>

        <!--<div class="em_booking_event_title  em_event_attr_box em_align_center"><?php echo $event->post_title; ?></div>-->

            <div class="kf-payment-details em_floatfix dbfl">    

                <div ng-repeat="tmp in orders track by $index" class="dbfl kf-checkout-info" >
                    <div class="kf-booked-qty">{{tmp.quantity}} Tickets <a ng-click="loadEventForBooking(tmp.event_id)" > Change </a></div>
                    <strong>for</strong>
                    <div class="em_booking_event_title em_event_attr_box em_align_center">{{tmp.name}}</div> 
                    <div class="kf-booking-event-date">On {{tmp.start_date}} at <span class="kf-booking-event-vanue">{{event.venue.name}}</span></div>


                    <!--                        <div class="em_selected_seats em_pay_detail difl em_align_left">
                  {{tmp.item_number}} ({{tmp.quantity}})
                  <span ng-click="loadEventForBooking(tmp.event_id)">Change</span>
                  </div>-->

                    <div class="em_total_price em_pay_detail em_align_right difl">
                        {{currency_symbol}}{{ (tmp.single_price * tmp.quantity) }}
                    </div>

                </div>  

            </div>
            <!--                <div class="em_discount em_align_right dbfl" ng-show="discount>0">
                                <span class="em_subtotal difl"><?php echo EventM_UI_Strings::get('LABEL_DISCOUNT'); ?></span>
                                    -{{discount}}
                            </div>
                            <div class="em_final_price em_align_right dbfl">
                                    <span class="em_subtotal difl"><?php echo EventM_UI_Strings::get('LABEL_SUBTOTAL'); ?></span>
                                {{currency_symbol}}{{price}}
                            </div>-->
        </div>  
        <!--  0 Payment Proceed Button -->


        <div class="payment_notice dbfl" ng-show="price>0 && bookable">
            <div class="kf-payment-info dbfl"><?php echo EventM_UI_Strings::get('NOTICE_CHECKOUT_TIMER'); ?></div>
            <div class="em_payment_progress_wrap dbfl">
                <div class="em_payment_progress em_bg" id="em_payment_progress"></div>
            </div>       
        </div>  

       

        <div class="kf-checkout-footer dbfl">
            <div class="kf-final-payment difl"><div class="em_discount em_align_right dbfl" ng-show="discount>0">
                    <span class="em_subtotal difl"><?php echo EventM_UI_Strings::get('LABEL_DISCOUNT'); ?></span>
                    -{{discount}}
                </div>
                <div class="em_final_price em_align_right dbfl">
                    <span class="em_subtotal dbfl"><?php echo EventM_UI_Strings::get('LABEL_TOTAL_DUE'); ?></span>
                    <div class="dbfl">{{currency_symbol}}{{price}}</div>
                </div>
            </div>

            
               <!-- Paypal Proceed Button -->
                <div  class="em_checkout_btn difr " ng-show="data.payment_processor=='paypal' && price>0 && bookable">
                    <button ng-disabled="requestInProgress" class="bg_gradient em_color kf-button" ng-click="proceedToPaypal()"><?php echo EventM_UI_Strings::get('LABEL_PROCEED'); ?></button>
                </div>
               
            <div class="kf-checkout-button difr">
                    <div class="em_checkout_btn difl" ng-show="price==0 && bookable">
                        <button  class="bg_gradient kf-button em_color" ng-click="proceedWithoutPayment()"><?php echo EventM_UI_Strings::get('LABEL_PROCEED'); ?></button>
                    </div>

                    <div class="payment_prcoessors difl">
                        <div  class="difl kf-payment-mode-select" ng-show="price>0 && bookable && payment_processors.hasOwnProperty('paypal')">
                            <input type="radio" name="paypal" value="paypal" ng-model="data.payment_processor" /><i class="fa fa-paypal" aria-hidden="true"></i> <?php echo EventM_UI_Strings::get('LABEL_PAYPAL'); ?>
                        </div>
                        <?php do_action('em_front_prcessor_options'); ?>
                    </div> 
                </div>
             
             

                </div>
  
    </div>
                 <div class="dbfl em_block kf-notice-print-tickets"><?php echo EventM_UI_Strings::get('NOTICE_CHECKOUT_PRINT-TICKETS'); ?></div>

<div class="dbfl em_block">     <a href="<?php  echo get_permalink($event_id); ?>">Go to Event</a></div>

       
            <?php 
                    $user= wp_get_current_user();
                    include('paypal.php');
             ?> 
    </div>
    
    
     <div id="kf-reconfirm-popup" class="dbfl" ng-show="!bookable">
            
           <div class="dbfl em_block kf-reconfirm-popup-content">
<!--               <img class="kf-reconfirm-pc" src="<?php echo esc_url(plugins_url('/images/popup-close.png', __FILE__)) ?>">-->
                <?php 
                echo '<p> Sorry, the time window for checkout has expired. Selected seats have been released for bookings. You can click here to go back to the Event page and try booking again.</p>';
                ?>
               <a href="<?php  echo get_permalink($event_id); ?>">OK</a>
           </div>
        </div> 


    </div>
   

<?php   endif;?>
<script>
   em_jQuery(document).ready(function () {
            em_load_map('booking', 'em_booking_map_canvas');
           
      
        });
        
        em_jQuery(".kf-reconfirm-pc").click(function(){
            em_jQuery("#kf-reconfirm-popup").hide();
        });
        
//      em_jQuery(window).load(function() {         
//          
//     console.log(jQuery('#div_id').offset().top );
//     console.log(jQuery('#div_id'));
//    jQuery('html, body').animate({ scrollTop: jQuery('#div_id').offset().top}, 'fast');
//     });
</script>
