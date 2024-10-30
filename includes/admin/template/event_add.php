<div class="kikfyre kf-container"  ng-controller="eventCtrl" ng-app="eventMagicApp" ng-cloak ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-db-content">
            <div class="kf-db-title">
                {{data.trans.heading_new_event_page}}
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

            <div class="emrow" ng-show="post_edit">
                <div class="emfield">{{data.trans.label_slug}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="post_edit" type="text" name="slug"  ng-model="data.post.slug">
                    <div class="emfield_error">
                        <span ng-show="postForm.slug.$error.required && !postForm.slug.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Slug is the user friendly URL of an Event. Example: /popatbrooklyn, /jazznight,/monstertruckracing etc.
                </div>
            </div>


            <div class="emrow">
                <div class="emfield">{{data.trans.label_type}}<sup>*</sup></div>
                <div class="eminput">
                    <select required name="event_type"  ng-model="data.post.event_type" ng-options="event_type.id as event_type.name for event_type in data.post.event_types"></select>
                    <div class="emfield_error">
                        <span ng-show="(postForm.event_type.$error.required && !postForm.event_type.$pristine) || (data.post.event_type==0 && postForm.event_type.$setValidity('required', false))">{{data.trans.validation_required}}</span>
                    </div>
                
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Event category. Example: Musical Fest, Seminar, Sports etc.
                </div>
            </div>
            
            <div class="emrow" ng-show="data.post.event_type=='new_event_type'">
                <div class="emfield">{{data.trans.label_add_event_type}}<sup>*</sup></div>
                <div class="eminput">
                    <input type="text" name="new_event_type" ng-required="data.post.event_type=='new_event_type'" ng-model="data.post.new_event_type" /></select>
                </div>
                <div class="emfield_error">
                        <span ng-show="postForm.new_event_type.$error.required && !postForm.new_event_type.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of your Event Category.Should be unique.
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

            <div class="emrow">
                <div class="emfield">{{data.trans.label_venue}}<sup>*</sup></div>
                <div class="eminput">
                    <select id="em_venue" ng-change="getCapacity()" required name="venue"  ng-model="data.post.venue" ng-options="venue.id as venue.name for venue in data.post.venues"></select>
                    <div class="emfield_error">
                        <span ng-show="(postForm.venue.$error.required && !postForm.venue.$pristine) || (data.post.venue==0 && postForm.venue.$setValidity('required', false))">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Select the Venue. To quickly add a new one, choose “Add New Venue”.
                </div>
            </div>
            
            <div class="emrow" ng-show="data.post.venue=='new_venue'">
                <div class="emfield">{{data.trans.label_add_new_venue}}<sup>*</sup></div>
                <div class="eminput">
                    <input type="text" name="new_venue" ng-required="data.post.venue=='new_venue'" ng-model="data.post.new_venue" /></select>
                   <div class="emfield_error">
                        <span ng-show="postForm.new_venue.$error.required && !postForm.new_venue.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of the Venue where Event will be hosted.
                </div>
            </div>


            <div class="emrow">
                <div class="emfield"> Feature Image </div>
                <div class="eminput">
                    <input type="button" ng-click="mediaUploader(false)" class="button kf-upload" value="{{data.trans.label_upload}}" />
                    <div class="em_cover_image">
                        <img ng-src="{{data.post.cover_image_url}}" />
                    </div>
                </div>
                
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Large image for the Event. This will be displayed above Description prominently.
                </div>
            </div>
            
            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">Event Gallery</div>
                <div class="eminput emeditor">
                    <input  type="button" ng-click="mediaUploader(true)" class="button kf-upload" value="{{data.trans.label_upload}}" />
                    <div class="em_gallery_images">
                        <ul id="em_draggable" class="dbfl">
                            <li class="kf-db-image difl" ng-repeat="(key, value) in data.post.images" id="{{value.id}}">
                                <div><img ng-src="{{value.src[0]}}" />
                                    <span><input class="em-remove_button" type="button" ng-click="deleteGalleryImage(value.id, key, data.post.images, data.post.gallery_image_ids)" value="Remove" /></span> 
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="emnote emeditor">
                    Displays multiple images related to the event as gallery view on Event page.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_match}}</div>
                <div class="eminput">
                    <input type="checkbox" name="match"  ng-model="data.post.match"  ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Match between two performers.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_performer}}(s)</div>
                <div class="eminput">
               
                    <select name="performer"  id="em_performer" multiple ie-select-fix="data.post.performers"  ng-model="data.post.performer" ng-options="performer.id as performer.name for performer in data.post.performers"></select>
                    <div class="emfield_error" ng-show="data.post.match==1">
                        <span ng-show="postForm.performer.$error.invalidPerForMatch">Only two performers can be selected with Match configuration</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Person/Group who will be performing at the Event. You can choose multiple performers. For additional entries, select “Add New Performer”.
                </div>
            </div>
            
            <div class="emrow" ng-show="data.post.performer=='new_performer'">
                <div class="emfield">{{data.trans.label_new_performer}}</div>
                <div class="eminput">
                    <input type="text" ng-model="data.post.custom_performer_name" /></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of the Performer. Multiple Performers should be separated by comma.
                </div>
            </div>


            <div class="emrow">
                <div class="emfield">{{data.trans.label_start_date}}<sup>*</sup></div>
                <div class="eminput">
                    <input required id="event_start_date" readonly=""  type="text" name="start_date"  ng-model="data.post.start_date">
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
                    <input required id="event_end_date" readonly="readonly" type="text" name="end_date"  ng-model="data.post.end_date" ng-change="update_start_booking_date()">
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
            
            <div ng-show="multiDay && !data.post.child_events.length" id="multi_days">
                <div class="emrow">
                    <div class="emfield">Select Multi Days</div>
                    <div class="eminput">
                        <div id="m_dates"></div>
                        <input type="button" value="Reset" ng-click="resetMultiDates()" class="button" />
                       
                        <div class="emfield_error">
                            
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Name of your form. This is not visible on front-end
                    </div>
                </div>
            </div>
            
            <div ng-show="multiDay && data.post.child_events.length>0">
                <div class="emrow">
                    <div class="emfield">{{data.trans.label_daily_schedule}}</div>
                    <div class="eminput">
                        <div id="m_dates"></div>
                        <input type="checkbox" name="is_daily_event"  ng-model="data.post.is_daily_event"  ng-true-value="1" ng-false-value="0">
                        <div class="emfield_error">
                            
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                       Turn this on if different days in your event have different schedules.
                    </div>
                </div>
            </div>
            
             
           
            <div ng-show="data.post.child_events.length">
                <div class="emrow">
                    <div class="emfield">Event Days</div>
                    <div class="eminput">
                        <div ng-repeat="child in data.post.children track by $index"> 
                         
                            <a ng-if="child.child_events === false"  ng-href="{{data.links.edit_child_event}}&post_id={{child.id}}">Day {{$index+1}}, {{child.start_date}}</a>
                            <a class="thickbox" ng-if="child.child_events !== false"  ng-href="{{data.links.edit_child_event}}&post_id={{child.id}}&TB_iframe=true&width=600&height=550">{{child.name}}</a>

                            <div ng-show="child.seating_capacity>0">
                             Booked  {{child.booked_seats}}/{{child.seating_capacity}}
                            </div> 
                             
                             <div ng-show="child.seating_capacity==0">   
                                Booked {{child.booked_seats}}
                            </div>
                             <input type="button" ng-click="deleteChildren(child.id,child.start_date)" value="Remove" class="button" />
                             
                        </div>
                        <input type="checkbox" ng-model="data.post.deleteAllChildren" ng-click="deleteAllChildren()" ng-true-value="1" ng-false-value="0"> Remove All Days
                        <div class="emfield_error">
                            
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Manage properties of individual days of your events.
                    </div>
                </div>
            </div>
            
              <div class="emrow">
                <div class="emfield">{{data.trans.label_start_booking_date}}</div>
                <div class="eminput">
                    <input id="event_start_booking_date" readonly="readonly" type="text" name="start_booking_date"  ng-model="data.post.start_booking_date">
                    <div class="emfield_error">
                        <span ng-show="postForm.start_booking_date.$error.pattern && !postForm.start_booking_date.$pristine">{{data.trans.validation_date_format}}</span>
                        <span ng-show="postForm.start_booking_date.$error.invalidBookingDate">{{data.trans.validation_invalid_start_booking_date}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                   The date from which booking opens.
                </div>
            </div>
            
            
            
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_last_booking_date}}</div>
                <div class="eminput">
                    <input id="event_last_booking_date" readonly="readonly" type="text" name="last_booking_date"  ng-model="data.post.last_booking_date">
                    <div class="emfield_error">
                        <span ng-show="postForm.last_booking_date.$error.pattern && !postForm.last_booking_date.$pristine">{{data.trans.validation_date_format}}</span>
                        <span ng-show="postForm.last_booking_date.$error.invalidBookingDate">{{data.trans.validation_invalid_last_booking_date}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Last date for Event booking.
                </div>
            </div>
            
            
            <div ng-hide="multiDay">
            <div class="emrow">
                <div class="emfield">{{data.trans.label_recurrence}}</div>
                <div class="eminput">
                    <select name="recurring_option"  ng-model="data.post.recurring_option" ng-options="recurring_option.key as recurring_option.name for recurring_option in data.post.recurring_options"></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Make it a recurring Event.
                </div>
            </div>
            
            
            <div ng-show="data.post.recurring_option=='recurring'" id="recurring_section">
                <div class="emrow">
                    <div class="emfield">{{data.trans.label_recurrence_interval}}<sup>*</sup></div>
                    <div class="eminput">
                        <select ng-required="data.post.recurring_option=='recurring'" name="recurrence_interval"  ng-model="data.post.recurrence_interval" ng-options="recurrence_interval as recurrence_interval for recurrence_interval in data.post.recurrence_intervals"></select>
                        <div class="emfield_error">
                            <span ng-show="postForm.recurrence_interval.$error.required && !postForm.recurrence_interval.$pristine">{{data.trans.validation_required}}</span>
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Choose recurrence on monthly, weekly, or annual basis.
                    </div>
                </div>

                <div class="emrow" ng-show="data.post.recurrence_interval=='Weekly' || data.post.recurrence_interval=='Monthly' || data.post.recurrence_interval=='Annually'">
                    <div class="emfield">Recurrence</div>
                    <div class="eminput">
                        <input  name="recurrence"  type="number"  ng-model="data.post.recurrence" />
                        <div class="emfield_error">
                            <span ng-show="postForm.recurrence.$error.number && !postForm.recurrence.$pristine">{{data.trans.validation_numeric}}</span>
                            <span ng-show="postForm.recurrence.$error.min && !postForm.recurrence.$pristine">Minimum value should be 1</span>
                            <span ng-show="postForm.recurrence.$error.max && !postForm.recurrence.$pristine">Maximum allowed value is 12</span>
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Specify recurrence limit.
                    </div>
                </div>
                
                
                
            </div>

            <div class="emrow" ng-show="data.post.recurring_option=='specific_dates'" id="recurring_dates_section">
                <div class="emfield">{{data.trans.label_recurrence_dates}}<sup>*</sup></div>
                <div class="eminput">
                    <div id="r_dates"></div>
                    <input type="button" value="Reset" ng-click="resetRecurringDates()" class="button" />
                    <div class="emfield_error">
                        <span ng-show="postForm.recurring_specific_dates.$error.required && !postForm.recurring_specific_dates.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Set Recurrence Date(s) for an Event.
                </div>
            </div>
            </div>
            <div class="emrow" ng-show="data.rm_forms">
                <div class="emfield">Event Registration Form</div>
                <div class="eminput">
                    <select name="rm_form" ng-model="data.post.rm_form" ng-options="key as value for (key, value) in data.rm_forms" convert-to-number></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Form for user registration on Event page.
                </div>
            </div>
            
            <div class="emrow" >             
                <div class="emfield"><span class="kf_dragger">&#x2020;</span> Booking Capacity</div>
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
                <div class="emfield">{{data.trans.label_organizer_name}}</div>
                <div class="eminput">
                    <input type="text" name="organizer_name"  ng-model="data.post.organizer_name">
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of the Event organizer.
                </div>
            </div>


            <div class="emrow">
                <div class="emfield">{{data.trans.label_organizer_contact_details}}</div>
                <div class="eminput">
                    <input type="text" name="organizer_contact_details"  ng-model="data.post.organizer_contact_details">
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Contact details for organizer. Examples: Telephone, Address, Email,etc.
                </div>
            </div>

            <div class="emrow">
                <div class="emfield">{{data.trans.label_hide_organizer}}</div>
                <div class="eminput">
                    <input type="checkbox" name="hide_organizer"  ng-model="data.post.hide_organizer"  ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Hides organizer details from the viewers on Event page.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_hide_event_from_calendar}}</div>
                <div class="eminput">
                    <input  type="checkbox" name="hide_event_from_calendar"  ng-model="data.post.hide_event_from_calendar" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                     Hides Event from Kikfyre calendar widget.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_hide_event_from_events}}</div>
                <div class="eminput">
                    <input  type="checkbox" name="hide_event_from_events"  ng-model="data.post.hide_event_from_events" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Hides Event from the Events directory on front end.
                </div>
            </div>
            
              <div class="emrow">
                <div class="emfield">Hide Booking Status</div>
                <div class="eminput">
                    <input type="checkbox" name="hide_booking_status"  ng-model="data.post.hide_booking_status"  ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Hides booking status from the viewers on Event page.
                </div>
            </div>
            
 
            <div class="emrow" ng-show="data.post.child_events.length==0 || data.post.child_events==false" >
                <div class="emfield">{{data.trans.label_ticket_price}} </div>
                <div class="eminput em_price_input"><span class="em_price_symbol">{{data.post.currency}}</span>
                    <input type="number" ng-min="0" name="ticket_price" ng-min="1"  ng-model="data.post.ticket_price" >
                    <div class="emfield_error">
                        <span ng-show="postForm.ticket_price.$error.number && !postForm.ticket_price.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="postForm.ticket_price.$error.min && !postForm.ticket_price.$pristine">Invalid value</span>
                        <span ng-show="postForm.ticket_price.$error.required && !postForm.ticket_price.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Single booking price. Currency can be changed through Global Settings.
                </div>
            </div>
            
            <div class="emrow" ng-show="data.post.child_events.length==0 || data.post.child_events==false" >
                <div class="emfield">{{data.trans.label_ticket_template}}</div>
                <div class="eminput">
                    <select name="ticket_template"  ng-model="data.post.ticket_template" ng-options="ticket_template.id as ticket_template.name for ticket_template in data.post.ticket_templates"></select>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket template to be used in emails and printing. You may add/customize it with Ticket Manager.
                </div>
            </div>

            <div class="emrow" >            
                <div class="emfield"><span class="kf_dragger">&#x2020;</span> Max Tickets Per Booking</div>
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
            
             <div class="emrow" ng-show="data.post.child_events.length==0 || data.post.child_events==false">
                <div class="emfield"> {{data.trans.label_allow_cancellations}}</div>
                <div class="eminput">
                    <input type="checkbox" name="allow_cancellations"  ng-model="data.post.allow_cancellations" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Allow users to request for booking cancellation from User Profile page. Seats will be revoked and freed up for selection just after cancellation request.
                </div>
            </div>
            
            <div class="emrow kf-bg-light">
                <div class="emfield">Note for Attendees</div>
                <div class="eminput emeditor">
                    <textarea class="kf-note" name="audience_notice"  ng-model="data.post.audience_notice"></textarea>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote emeditor">
                    Notice for audience on Event page. Can be used for important instructions related to the event.
                </div>
            </div>
            
            <div class="emrow" >
               
                <div class="emfield"> <span class="kf_dragger">&#x2020;</span> {{data.trans.label_allow_volume_discount}}</div>
                <div class="eminput">
                    <input type="checkbox" name="allow_discount"  ng-model="data.post.allow_discount" ng-true-value="1" ng-false-value="0">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Enable volume discount for booking multiple tickets.
                </div>
            </div>
            
           <div id="em_volume_discount" >
                
            <div class="emrow">
                <div class="emfield">{{data.trans.label_discount_no_tickets}}<sup>*</sup></div>
                <div class="eminput">
                    <input ng-required="post.allow_discount==1" ng-min="2" type="number" name="discount_no_tickets"  ng-model="data.post.discount_no_tickets">
                    <div class="emfield_error">
                        <span ng-show="postForm.discount_no_tickets.$error.number && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_numeric}}</span>
                        <span ng-show="postForm.discount_no_tickets.$error.max && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_discount_per_max}}</span>
                        <span ng-show="postForm.discount_no_tickets.$error.min && !postForm.discount_no_tickets.$pristine">Invalid Value</span>
                        <span ng-show="postForm.discount_no_tickets.$error.exceededCapacity">Value can not exceed total capacity</span>
                        <span ng-show="postForm.discount_no_tickets.$error.required && !postForm.discount_no_tickets.$pristine">{{data.trans.validation_required}}</span>
                    
                    
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Minimum no. of tickets to be applicable for volume discount.
                </div>
            </div>
            
                <div class="emrow" >
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


            <div class="emrow kf-bg-light">
                <div class="emfield emeditor">{{data.trans.label_sponser_image}}</div>
                <div class="eminput emeditor">
                    <input  type="button" ng-click="sponserUploader(true)" class="button kf-upload" value="{{data.trans.label_upload}}" />
                    <div class="em_gallery_images">
                        <ul id="em_sponser_image_draggable" class="dbfl">
                            <li class="kf-db-image difl" ng-repeat="(key, value) in data.post.sponser_images" id="{{value.id}}">
                                <div><img ng-src="{{value.src[0]}}" />
                                    <span><input class="em-remove_button" type="button" ng-click="deleteGalleryImage(value.id, key,data.post.sponser_images, data.post.sponser_image_ids)" value="Remove" /></span> 
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="emnote emeditor">
                    Multiple sponsor images for gallery view on Event page under Venue information.
                </div>
            </div>
            
            
            <div class="emrow">
                    <div class="emfield">{{data.trans.label_facebook_page}}</div>
                    <div class="eminput">
                         <input class="kf-fb-field" ng-pattern="/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/?/"  type="url" name="facebook_page"  ng-model="data.post.facebook_page" />
                        <div class="emfield_error">
                            <span ng-show="!postForm.facebook_page.$valid && !postForm.facebook_page.$pristine">{{data.trans.validation_facebook_url}}</span>
                        </div>
                    </div>
                    <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                       Facebook page URL for the Event, if available. Eg.:https://www.facebook.com/XYZ/
                    </div>
             </div>

            <div class="emrow">
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
            
            
            <div>
               <span class="kf_dragger">&#x2020;</span> <a ng-click="showSeats = !showSeats" /><button class="kf-seatinng-arrangement">{{data.trans.label_edit_seating_arrangement}}</button></a>

            
            <div ng-show="showSeats" >
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

            <div class="action_bar" >
                <ul>
                    <li class="difl"><input type="button" value="Reserve" ng-click="reserveSeat()"/></li>
                    <li class="difl"><input type="button" value="Reset current Selection" ng-click="resetSelections()"/></li>
                    <li class="difl"><input type="button" value="Sync with Venue" ng-click="getCapacity()"/></li>
                <li class="difl"><input type="button" value="Select Scheme" ng-click="em_call_scheme_popup('#pm-change-password1')"/></li>
                </ul>
            </div> 
         </div>
        </div>
            
            <input type="text" class="hidden"  ng-model="data.post.sponser_image_ids" />
            <input type="text" class="hidden"  ng-model="data.post.cover_image_id" />
            <input type="text" class="hidden" ng-model="data.post.gallery_image_ids" />
            
            <div class="kf-notice-add-edit-event dbfl ">
               <span class="kf_dragger">&#x2020;</span> In Multiday events, value to these fields will be automatically assigned to individual event days. If you wish to override or change this value in future, please do it within individual event day's configuration.
            </div>
            
            <div class="dbfl kf-buttonarea">
                <div class="em_cancel"><a class="kf-cancel" ng-href="{{data.links.cancel}}">{{data.trans.label_cancel}}</a></div>
                <button type="submit" class="btn btn-primary" ng-disabled="postForm.$invalid || requestInProgress">{{data.trans.label_save}}</button>
              
            </div>
            
            
            <div class="dbfl kf-required-errors" ng-show="postForm.$dirty && postForm.$invalid">
                <h3>Looks like you missed out filling some required fields (*). You will not be able to save until all required fields are filled in. Here’s what's missing - 
                
                <span ng-show="postForm.name.$error.required">Name</span>
                
                <span ng-show="postForm.slug.$error.required">Slug</span>
                
                <span ng-show="postForm.start_date.$error.required">Start date</span>
                
                <span ng-show="postForm.end_date.$error.required">End date</span>
                
                <span ng-show="postForm.recurrence_interval.$error.required">Recurrence interval</span>
                
                <span ng-show="postForm.recurring_specific_dates.$error.required">Recurring date(s)</span>

                <span ng-show="postForm.discount_no_tickets.$error.required">Discount : No. of tickets</span>
            
                <span ng-show="postForm.discount_per.$error.required">Discount(%)</span>
                
                <span ng-show="postForm.new_venue.$error.required || postForm.venue.$error.required">Venue</span>
                
                <span ng-show="postForm.new_event_type.$error.required || postForm.event_type.$error.required">Event Type</span>
                    
                
                </h3>
            
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
                                                        <input type="button" value="Update" ng-click="updateCurrentSeatScheme()" /></div>

                                                </div> 
                                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        
        
    </div>
</div>
    
   
