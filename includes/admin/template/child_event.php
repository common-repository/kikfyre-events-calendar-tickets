<div class="kikfyre kf-container"  ng-controller="childEventCtrl" ng-app="eventMagicApp" ng-cloak ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-db-content">
            <div class="kf-db-title">
                {{data.trans.heading_child_event_page}}
            </div>
        <div class="form_errors">
            <ul>
                <li class="emfield_error" ng-repeat="error in  formErrors">
                    <span>{{error}}</span>
                </li>
            </ul>  
        </div>
        <!-- FORM -->
        <form  name="postForm" ng-submit="savePost(postForm.$valid)" novalidate >

            <div class="emrow">
                <div class="emfield">{{data.trans.label_name}}<sup>*</sup></div>
                <div class="eminput">
                    <input required type="text" name="name"  ng-model="data.post.name">
                    <div class="emfield_error">
                        <span ng-show="postForm.name.$error.required && (showFormErrors || !postForm.name.$pristine)">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of your Event. Should be unique.
                </div>
            </div>
            
            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">{{data.trans.label_description}}</div>
                <div class="eminput emeditor">
                    <?php 
                            include("editor.php"); 
                            $post_id= event_m_get_param('post_id');
                            $content='';
                            if($post_id!==null && (int)$post_id>0)
                            {
                                $post= get_post($post_id); 
                                if(!empty($post))
                                    $content= $post->post_content; 
                            }
                            em_add_editor('description',$content);
                    ?>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote emeditor">
                    Details about the Event that will be visible to the users on events page.
                </div>
            </div>
             
            
            <div class="emrow" ng-show="data.post.performer=='new_performer'">
                <div class="emfield">{{data.trans.label_new_performer}}</div>
                <div class="eminput">
                    <input disabled type="text" ng-model="data.post.custom_performer_name" /></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Person/Group who will be performing at the Event. You can choose multiple performers. For additional entries, select “Add New Performer”.
                </div>
            </div>


            <div class="emrow">
                <div class="emfield">{{data.trans.label_start_date}}<sup>*</sup></div>
                <div class="eminput">
                    <input  required id="event_start_date" readonly=""  type="text" name="start_date"  ng-model="data.post.start_date">
                    <div class="emfield_error">
                        <span ng-show="postForm.start_date.$error.required && !postForm.start_date.$pristine">{{data.trans.validation_required}}</span>
                        <span ng-show="postForm.start_date.$error.pattern && !postForm.start_date.$pristine">{{data.trans.validation_date_format}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Event start date and time.
                </div>
            </div>

            <div class="emrow">
                <div class="emfield">{{data.trans.label_end_date}}<sup>*</sup></div>
                <div class="eminput">
                    <input required id="event_end_date" readonly="readonly" type="text" name="end_date"  ng-model="data.post.end_date">
                    <div class="emfield_error">
                        <span ng-show="postForm.end_date.$error.required && !postForm.end_date.$pristine">{{data.trans.validation_required}}</span>
                        <span ng-show="postForm.end_date.$error.pattern && !postForm.end_date.$pristine">{{data.trans.validation_date_format}}</span>
                        <span ng-show="postForm.end_date.$error.invalidEndDate">{{data.trans.validation_invalid_end_date}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Event end date and time. Should always be later than the Start Date.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_last_booking_date}}<sup>*</sup></div>
                <div class="eminput">
                    <input required id="event_last_booking_date" readonly="readonly" type="text" name="last_booking_date"  ng-model="data.post.last_booking_date">
                    <div class="emfield_error">
                        <span ng-show="postForm.last_booking_date.$error.pattern && !postForm.last_booking_date.$pristine">{{data.trans.validation_date_format}}</span>
                        <span ng-show="postForm.last_booking_date.$error.invalidBookingDate">{{data.trans.validation_invalid_last_booking_date}}</span>
                  </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Last date for Event booking.
                </div>
            </div>
             <div class="emrow">
                 <div class="emfield"><a ng-click="showPrice = !showPrice">{{data.trans.label_override_ticket_price}}<span class="dashicons dashicons-arrow-down-alt"></span></a></div>
             </div>
          
            
            <div class="emrow" ng-show="showPrice" >
                    <div class="emfield">{{data.trans.label_ticket_price}}<sup>*</sup></div>
                    <div class="eminput">
                        <input  required id="event_ticket_price"  type="text" name="ticket_price"  ng-model="data.post.ticket_price">
                        <div class="emfield_error"></div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Single booking price. Currency can be changed through Global Settings.
                    </div>           
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_ticket_template}}</div>
                <div class="eminput">
                    <select name="ticket_template"  ng-model="data.post.ticket_template" ng-options="ticket_template.id as ticket_template.name for ticket_template in data.post.ticket_templates"></select>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket template to be used in emails and printing. You may add/customize it with Ticket Manager.
                </div>
            </div>
            
            <div class="emrow">
            <div class="emfield">{{data.trans.label_capacity}}<sup>*</sup></div>
              <div class="eminput">
                    <input  type="number" ng-min="0" name="seating_capacity"  ng-model="data.post.seating_capacity">
                    <div class="emfield_error">
                        <span ng-show="postForm.seats.$error.number && !postForm.seats.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="postForm.seating_capacity.$error.capacityExceeded">{{data.trans.validation_venue_capacity_exceeded}}</span>
                        <span ng-show="postForm.seating_capacity.$error.min">Invalid Value</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Maximum number of bookings allowed for this Event. Leave blank for unlimited bookings.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">Max Tickets Per Booking</div>
                <div class="eminput">
                    <input  type="number" ng-min="0"  name="max_tickets_per_person"  ng-model="data.post.max_tickets_per_person" >
                   
                    <div class="emfield_error">
                        <span ng-show="postForm.max_tickets_per_person.$error.number && !postForm.max_tickets_per_person.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="postForm.max_tickets_per_person.$error.min">Invalid Value</span>
                        <span ng-show="postForm.max_tickets_per_person.$error.exceededCapacity">Value can not exceed total capacity</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    No. of seats allowed per booking.
                </div>
            </div>
            
             <div class="emrow">
                <div class="emfield"> {{data.trans.label_allow_cancellations}}</div>
                <div class="eminput">
                    <input type="checkbox" name="allow_cancellations"  ng-model="data.post.allow_cancellations" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Allow users to request for booking cancellation from User Profile page. Seats will be revoked and freed up for selection just after cancellation request.
                </div>
            </div>
            
            
             <div class="emrow">
                <div class="emfield">{{data.trans.label_allow_volume_discount}}</div>
                <div class="eminput">
                    <input type="checkbox" name="allow_discount"  ng-model="data.post.allow_discount" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Enable volume discount for booking multiple tickets.
                </div>
            </div>
            
           <div id="em_volume_discount">
                
            <div class="emrow">
                <div class="emfield">{{data.trans.label_discount_no_tickets}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="post.allow_discount==1" ng-min="0" type="number" name="discount_no_tickets"  ng-model="data.post.discount_no_tickets">
                    <div class="emfield_error">
                        <span ng-show="postForm.discount_no_tickets.$error.number && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="postForm.discount_no_tickets.$error.max && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_discount_per_max}}</span>
                        <span ng-show="postForm.discount_no_tickets.$error.min && !postForm.discount_no_tickets.$pristine">Invalid Value</span>
                        <span ng-show="postForm.discount_no_tickets.$error.exceededCapacity && !postForm.discount_no_tickets.$pristine">Value can not exceed total capacity</span>
                        <span ng-show="postForm.discount_no_tickets.$error.required && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Minimum no. of tickets to be applicable for volume discount.
                </div>
            </div>
            
                <div class="emrow">
                    <div class="emfield">{{data.trans.label_discount_per}}<sup>*</sup></div>
                    <div class="eminput">
                        <input ng-required="post.allow_discount==1" max="100"  type="number" name="discount_per"  ng-model="data.post.discount_per">
                        <div class="emfield_error">
                            <span ng-show="postForm.discount_per.$error.number && !postForm.discount_per.$pristine">{{data.trans.validation_numeric}}</span>
                            <span ng-show="postForm.discount_per.$error.min && !postForm.discount_per.$pristine">Invalid Value</span>
                            <span ng-show="postForm.discount_per.$error.required && !postForm.discount_per.$pristine">{{data.trans.validation_required}}</span>
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Discount percentage allowed on volume discount.
                    </div>
                </div>
            </div>
            
            <div class="emrow hide_status" style='display:none;'>
                    <div class="emfield">{{data.trans.label_status}}</div>
                    <div class="eminput">
                        <select required name="status"  ng-model="data.post.status" ng-options="status.key as status.label for status in data.post.status_list"></select>
                        <div class="emfield_error">
                            <span ng-show="postForm.status.$error.required && !postForm.status.$pristine">{{data.trans.validation_required}}</span>
                        </div>
                    </div>
                    <div class="emnote">
                        Event status (Expired or Active). Booking is not allowed for expired Events. System automatically changes status to Expired based on Event dates.
                    </div>
             </div>
            
            
            
            
                       <div class="emrow">
                             <div class="emfield"><div ng-show="data.post.seats.length>0" ><a ng-click="showSeats = !showSeats" />{{data.trans.label_edit_seating_arrangement}}<span class="dashicons dashicons-arrow-down-alt"></span></a></div></div>
                            <div class="eminput"> &nbsp; </div>
                           <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Please make sure changes you made in seating arrangement correspond to value you set in individual day configurations. Otherwise, it can lead to unexpected results.
                    </div>
                       </div>
            <div ng-show="showSeats">
            <div class="emrow em_seat_table kf-bg-light" >
                <table class="em_venue_seating" ng-style="seat_container_width">

                    <tr ng-repeat="row in data.post.seats" class="row isles_row_spacer" id="row{{$index}}" ng-style="{'margin-top':row[0].rowMargin}"> 

                        <td class="row_selection_bar" ng-click="selectRow($index)">
                            <div class="em_seat_row_number">{{getRowAlphabet($index)}}</div>
                        </td>

                        
                        <td ng-repeat="seat in row" ng-init="adjustContainerWidth(seat.columnMargin,$parent.$index)" class="seat isles_col_spacer" ng-class="seat.type" id="ui{{$parent.$index}}-{{$index}}" 
                           ng-style="{'margin-left':seat.columnMargin}">
                             <div  ng-click="selectColumn($index)" ng-if="$parent.$index==0" class="em_seat_col_number">{{$index}}</div>
                            <div class="seat_avail seat_avail_number seat_status">{{seat.col + 1}}</div>
                              <div  id="pm_seat"  class="seat_avail seat_status" ng-click="selectSeat(seat, $parent.$index, $index)" ng-click="showSeatOptions(seat)">{{seat.uniqueIndex}} </div>
                        </td>
                    </tr>
                </table>
                 
            </div>    

            <div class="action_bar" ng-show="data.post.seats.length>0">
                <ul>
                    <li class="difl"><input type="button" value="Reserve" ng-click="reserveSeat()"/></li>
                    <li class="difl"><input type="button" value="Reset current Selection" ng-click="resetSelections()"/></li>
                    <li class="difl"><input type="button" value="Sync with Venue" ng-click="getCapacity()"/></li>
                         <li class="difl"><input type="button" value="Select Scheme" ng-click="em_call_scheme_popup('#pm-change-password1')"/></li>
                </ul>
            </div> 
        </div>
            
            <input type="text" class="hidden"  ng-model="data.post.sponser_image_ids" />
            <input type="text" class="hidden"  ng-model="data.post.cover_image_id" />
            <input type="text" class="hidden" ng-model="data.post.gallery_image_ids" />
            
            <div class="dbfl kf-buttonarea">
                <div class="em_cancel"><a class="kf-cancel" href="javascript: void(0)" onclick="javascript: self.parent.tb_remove()">{{data.trans.label_close}}</a></div>
                <button type="submit" class="btn btn-primary" ng-disabled="postForm.$invalid || requestInProgress">{{data.trans.label_save}}</button>
                <span class="kf-error" ng-show="postForm.$invalid && postForm.$dirty ">Please fill all required fields.</span>
                
                <div class="emnote">
                       Changes made to event days will reflect after you save Main Event page.
                </div>
            </div>
            
            
        </form>
        
        <div id="show_popup" ng-show = "scheme_popup">

                            <div class="pm-popup-mask"></div>    
                            <div id="pm-change-password1-dialog">
                                <div class="pm-popup-container">
                                    <div class="pm-popup-title pm-dbfl pm-bg-lt pm-pad10 pm-border-bt">
                                  
                                        
                                    

                                        <div class="pm-popup-action pm-dbfl pm-pad10 pm-bg">
                                            <div class="pm-login-box GCal-confirm-message">
                                                <div class="pm-login-box-error pm-pad10" style="display:none;" id="pm_reset_passerror">
                                                </div>
                                                <!-----Form Starts----->
                                                <div class="kf-seat_schemes dbfl">
                                                     <div class="kf-seat_schemes-titlebar">
                                                    <div class="kf-seat_schemes-title"> Scheme(s)</div>
                                                    <span  class='kf-popup-close' ng-click="scheme_popup=false">&times;</span>
                                                     </div>
                                                    <div class="emrow">
                                                        <div class="emfield"> Current scheme(s)</div>
                                                        <div class="eminput"> <div class="kf-seat_scheme difl" ng-repeat="row in selectedSeats" >
                                                    {{row.seatSequence}}
                                                    </div>
                                                        </div>
                                                    </div>
                                                       <div class="emrow">
                                                     <div class="emfield"> Change scheme(s)</div>
                                                     <div class="eminput">  <textarea id="custom_seat_sequences"></textarea></div>
                                                       </div>
                                                <div class="emrow kf-popup-button-area">
                                                    
                                                <input type="button" value="Update" ng-click="updateCurrentSeatScheme()" />
                                                </div> 
                                                </div> 
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

    </div>
</div> 


<style>
    #adminmenumain,#wpadminbar,#wpfooter{display: none;}
    #wpcontent{margin-left: 0%; padding-left: 0px;}
    .kikfyre {margin-left: 0px !important;}
</style>
