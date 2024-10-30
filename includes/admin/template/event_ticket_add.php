<div class="kikfyre kf-container"  ng-controller="eventTicketCtrl" ng-app="eventMagicApp" ng-cloak ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    
    <div class="kf-db-content">
            <div class="kf-db-title">
                {{data.trans.page_heading}}
            </div>
        <div class="form_errors">
            <ul>
                {{formErrors}} 
            </ul>
                   
            </div>

        <!-- FORM -->
        <form name="postForm" ng-submit="saveEventTicket(postForm.$valid)" novalidate class="em_ticket_form">
            
            <div class="em_ticket_template">
                  
                    <div class="kf-ticket-wrapper"> 
                        <div class="kf-event-details-wrap">
                            <div class="kf-logo-details dbfl">
                                <div class="kf-event-logo difl">
                                    <img  ng-src="{{data.post.logo_image}}" >&nbsp;
                                </div>
                                <div class="event-details difl">
                                    <div class="dbfl">
                                        <div class="kf-ticket-row dbfl">
                                            <div class="kf-font-color1 kf-event-title"  ng-style="data.ticket_font_family1">Event Name</div>
                                            <p class="kf-font-color2 kf-font1"  ng-style="data.ticket_font_family2" >21st December, 2017 4:30 PM-7:00 PM</p>
                                        </div>
                                        <div class="kf-spacer dbfl"></div>
                                        <div class="kf-ticket-row dbfl">
                                            <p class="kf-font-color2 kf-font1"  ng-style="data.ticket_font_family2" >VENUE NAME</p>
                                            <p class="kf-font-color1 kf-font2" ng-style="data.ticket_font_family2" >Address Line 1, Address Line 2, City  ZipCode</p>
                                        </div>
                                        <div class="kf-ticket-row dbfl">
                                            <p class="kf-font-color2 kf-font1"  ng-style="data.ticket_font_family2" >BOOKING COORDINATOR</p>
                                            <p class="kf-font-color1 kf-font2" ng-style="data.ticket_font_family2" >Contact Details</p>
                                        </div>
                                        <div class="kf-ticket-row dbfl">
                                            <p class="kf-font-color1 kf-font1"  ng-style="data.ticket_font_family2">Age group: 18 years and above</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="kf-ticket-blank">&nbsp;</div>
                            </div>
                            <div class="kf-note-price-wrap dbfl">
                                <div class="kf-note-price">
                                    <div class="kf-special-note kf-font-color1"  ng-style="data.ticket_font_family2">
                                        Special Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus sagittis eget
                                        ex sit amet tempor. Maecenas mi nunc, pellentesque quis eleifend eget, fermentum vel nulla.
                                    </div>
                                    <div class="kf-ticket-price">
                                        <span class="kf-font-color2 kf-font1 kf-price-tag dbfl"  ng-style="data.ticket_font_family2">Price</span>
                                        <span class="kf-price dbfl kf-font-color1"  ng-style="data.ticket_font_family2">$10<sup class="kf-font-color1">.00</sup></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Seat Number -->
                        <div class="kf-seat-wrap">
                            <div class="dbfl">
                                <p class="kf-font-color2 kf-seat-tag"  ng-style="data.ticket_font_family2">SEAT NO.</p>
                                <div class="kf-font-color1 kf-seat-no"  ng-style="data.ticket_font_family1">A-21</div>
                                <p class=" kf-font-color1 kf-seat-id">ID # 1003459234</p>
                            </div>
                        </div>
                    </div>
            
           <!-- <div class="emrow">
                <div class="emfield">{{data.trans.label_choose_template}}<sup>*</sup></div>
                <div class="eminput">
                    <select required  name="template" ng-model="data.post.template" ng-options="template.key as template.label for template in data.post.templates"></select>
                    <div class="emfield_error">
                        <span ng-show="postForm.template.$error.required && !postForm.template.$pristine">{{data.trans.validation_required}}</span>
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Name of your form. This is not visible on front-end
                </div>
            </div> -->
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_name}}<sup>*</sup></div>
                <div class="eminput">
                    <input placeholder="" required  type="text" name="name"  ng-model="data.post.name">
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Unique template name for identification.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_font1}}</div>
                <div class="eminput">
                    <select name="font1" ng-change="changeStyle()" ng-model="data.post.font1"  ng-options="font as font for font in data.post.fonts"></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Font to be used in ticket template.
                </div>
            </div>
        
            <div class="emrow">
                <div class="emfield">{{data.trans.label_font2}}</div>
                <div class="eminput">
                    <select name="font2" ng-change="changeStyle()" ng-model="data.post.font2"  ng-options="font as font for font in data.post.fonts"></select>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Alternative font, in case Font1 is not available.
                </div>
            </div>
            <!-- -->
            <div class="emrow">
                <div class="emfield">Font Color 1</div>
                <div class="eminput">
                    <input id="em_font1_color_picker" ng-change="changeStyle()" class="jscolor"  type="text" name="font_color1"  ng-model="data.post.font_color1" >
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket font's color. Will be visible in PDF format.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">Font Color 2</div>
                <div class="eminput">
                    <input id="em_font2_color_picker" ng-change="changeStyle()" class="jscolor" type="text" name="font_color2"  ng-model="data.post.font_color2" >
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket font's color. Will be visible in PDF format.
                </div>
            </div>
            
            <!-- -->
            <div class="emrow">
                <div class="emfield">{{data.trans.label_background_color}}</div>
                <div class="eminput">
                    <input id="em_background_color_picker" ng-change="changeStyle()" class="jscolor"  type="text" name="background_color"  ng-model="data.post.background_color" >
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket background color. Will be visible in PDF format.
                </div>
            </div>
            
            <div class="emrow">
                <div class="emfield">{{data.trans.label_border_color}}</div>
                <div class="eminput">
                    <input id="em_border_color_picker" ng-change="changeStyle()" class="jscolor" type="text" name="border_color"  ng-model="data.post.border_color" >
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Ticket border color. Will be visible in PDF format.
                </div>
            </div>
            
           <div class="emrow">
                <div class="emfield">Logo</div>
                <div class="eminput">
                     <input type="button" class="kf-upload" value="{{data.trans.label_upload}}" ng-click="mediaUploader(false)" />
                     <div class="em_cover_image">
                        <image ng-src="{{data.post.logo_image}}" />
                     </div>
                    <div class="emfield_error">
                    </div>
                </div>
                <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    Logo for the Event or Organizer. This will be visible on ticket printouts.
                </div>
            </div>
            
            
            
            
            <input type="text" name="logo" ng-model="data.post.logo" class="hidden" />
            <div class="dbfl kf-buttonarea">
            <div class="em_cancel"><a class="kf-cancel" ng-href="{{data.links.cancel}}">{{data.trans.label_cancel}}</a></div>
            <button type="submit" class="btn btn-primary" ng-disabled="postForm.$invalid">{{data.trans.label_save}}</button>
            
            
            </div>
            
            <div class="dbfl kf-required-errors" ng-show="postForm.$invalid && postForm.$dirty">
                <h3>Looks like you missed out filling some required fields (*). You will not be able to save until all required fields are filled in. Here’s what's missing - 
                
                <span ng-show="postForm.name.$error.required">Name</span>
                </h3>
            <div>
            
            
</div>
    </div>









