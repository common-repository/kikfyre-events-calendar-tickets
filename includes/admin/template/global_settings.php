<?php add_thickbox(); 

?>
<div class="kikfyre kf-container" ng-app="eventMagicApp" ng-controller="globalSettingsCtrl" ng-init="initialize()" ng-cloak>
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="content">
        <?php
        if(event_m_get_param('show_payment')):?>      
            <div  ng-init="show_payments(true)">  </div>
       <?php endif; ?>
        <div class="kf-db-title dbfl">{{data.trans.heading_global_settings}}</div>
        <div class="form_errors">
            {{formErrors}}   
        </div>
        <div class="kf-global-settings dbfl" ng-hide="showNotification || showPayments || showExternalIntegration || showPageIntegration || showPageGSettings">
            <div class="kf-settings-icon-area dbfl">
                <a href="javascript:void(0)" ng-click="showNotification = true">
                    <div class="em-settings-box">
                        <img class="em-settings-icon"  ng-src="{{data.base_path}}/images/rm-email-notifications.png">
                        <div class="em-settings-description">

                        </div>
                        <div class="em-settings-subtitle">{{data.trans.label_email_notifications}}</div>
                        <span>Notification contents</span>
                    </div>
                </a>        

                <a href="javascript:void(0)" ng-click="showPayments = true" >
                    <div class="em-settings-box">
                        <img class="em-settings-icon" ng-src="{{data.base_path}}/images/rm-payments.png">
                        <div class="em-settings-description">

                        </div>
                        <div class="em-settings-subtitle">{{data.trans.label_payments}}</div>
                        <span>{{data.trans.label_payments_sub}}</span>
                    </div></a>

                <a href="javascript:void(0)" ng-click="showExternalIntegration = true">
                    <div class="em-settings-box">
                        <img class="em-settings-icon"  ng-src="{{data.base_path}}/images/rm-third-party.png">
                        <div class="em-settings-description">

                        </div>
                        <div class="em-settings-subtitle">{{data.trans.label_ex_int}}</div>
                        <span>{{data.trans.note_exint_page}}</span>
                    </div>
                </a>


                <a href="javascript:void(0)" ng-click="showPageIntegration = true">
                    <div class="em-settings-box">
                        <img class="em-settings-icon"  ng-src="{{data.base_path}}/images/kf-pages.png">
                        <div class="em-settings-description">

                        </div>
                        <div class="em-settings-subtitle">{{data.trans.label_default_pages}}</div>
                        <span>{{data.trans.note_shortcode_page}}</span>
                    </div>
                </a>
                
                 <a href="javascript:void(0)" ng-click="showPageGSettings = true">
                    <div class="em-settings-box">
                        <img class="em-settings-icon"  ng-src="{{data.base_path}}/images/kf-general-setting-icon.png">
                        <div class="em-settings-description">

                        </div>
                        <div class="em-settings-subtitle">{{data.trans.label_general_settings}}</div>
                        <span>{{data.trans.label_general_settings}}</span>
                    </div>
                </a>

            </div>
        </div>    
        <form name="optionForm" ng-submit="saveSettings(optionForm.$valid)" novalidate >
            <div ng-show="showPageIntegration">    
                <div class="emrow">
                    <div class="emfield">Performers Page</div>
                    <div class="eminput">
                        <select id="em_page" name="performers_page" ng-model="data.options.performers_page"  ng-options="pages.id as pages.name for pages in data.options.pages"></select>
                    </div>                        
                    <div class="emfield_error">
                    </div>
                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Performers Page will navigate to selected page.
                    </div>
                </div>            


                <div class="emrow">
                    <div class="emfield">Venues Page</div>
                    <div class="eminput">
                        <select id="em_page" name="venues_page" ng-model="data.options.venues_page"  ng-options="pages.id as pages.name for pages in data.options.pages"></select>
                    </div>                        

                    <div class="emfield_error">
                    </div>
                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Venues Page will navigate to selected page.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield">Events Page</div>
                    <div class="eminput">
                        <select id="em_page"  name="events_page" ng-model="data.options.events_page"  ng-options="pages.id as pages.name for pages in data.options.pages"></select>
                    </div>                        

                    <div class="emfield_error">
                    </div>

                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Events Page will navigate to selected page.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield">Bookings Page</div>
                    <div class="eminput">
                        <select id="em_page"  name="booking_page" ng-model="data.options.booking_page"  ng-options="pages.id as pages.name for pages in data.options.pages"></select>
                    </div>                        

                    <div class="emfield_error">
                    </div>

                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Bookings Page will navigate to selected page.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield">Profile Page</div>
                    <div class="eminput">
                        <select id="em_page"  name="profile_page" ng-model="data.options.profile_page"  ng-options="pages.id as pages.name for pages in data.options.pages"></select>
                    </div>                        
                    <div class="emfield_error">
                    </div>
                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Profile Page will navigate to selected page.
                    </div>
                </div>

            </div>


            <div ng-show="showExternalIntegration">
                <div class="em-external-integration">
                    <div class="emrow">
                        <div class="emfield">{{data.trans.label_google_map_api_key}}</div>
                        <div class="eminput">
                            <input type="text" name="gmap_api_key"  ng-model="data.options.gmap_api_key">
                            <div class="emfield_error">

                            </div>
                        </div>
                        <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                            Enter Google Map API Key after registering your application for Google Maps.
                        </div>
                    </div>
                </div>   

                <div class="em-external-integration" >
                    <div class="emrow">
                        <div class="emfield">Allow Social Sharing</div>
                        <div class="eminput">
                            <input type="checkbox" ng-true-value="1" ng-fale-value="0" name="social_sharing"  ng-model="data.options.social_sharing">
                            <div class="emfield_error">

                            </div>
                        </div>
                        <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                            Allow visitors to share Events on Facebook and Twitter.
                        </div>
                    </div>
                </div>


                <div class="em-external-integration" ng-show="data.options.social_sharing == 1">
                    <div class="emrow">
                        <div class="emfield">{{data.trans.label_facebook_api_key}}<sup>*</sup></div>
                        <div class="eminput">
                            <input type="text" name="fb_api_key" ng-required="data.options.social_sharing==1"  ng-model="data.options.fb_api_key">
                            <div class="emfield_error">
                                <span ng-show="optionForm.fb_api_key.$error.required && !optionForm.fb_api_key.$pristine">{{data.trans.validation_required}}</span>
                            </div>
                        </div>
                        <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                            Enter your Facebook API key.
                        </div>
                    </div>
                </div>


                <div class="em-external-integration" >
                    <div class="emrow">
                        <div class="emfield">Google Calendar Sharing</div>
                        <div class="eminput">
                            <input type="checkbox" ng-true-value="1" ng-fale-value="0" name="gcal_sharing"  ng-model="data.options.gcal_sharing">
                            <div class="emfield_error">

                            </div>
                        </div>
                        <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                            Allow users to share events through Google Calendar. Calendar API lets you display, create and modify Events.
                        </div>
                    </div>
                </div>

                <div ng-show="data.options.gcal_sharing == 1">
                    <div class="em-external-integration">
                        <div class="emrow">
                            <div class="emfield">{{data.trans.label_google_cal_client_id}}<sup>*</sup></div>
                            <div class="eminput">
                                <input type="text" ng-required="data.options.gcal_sharing==1" name="google_cal_client_id"  ng-model="data.options.google_cal_client_id">
                                <div class="emfield_error">
                                    <span ng-show="optionForm.google_cal_client_id.$error.required && !optionForm.google_cal_client_id.$pristine">{{data.trans.validation_required}}</span>
                                </div>
                            </div>
                            <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                Enter your Google Calendar Client ID.
                            </div>
                        </div>
                    </div> 



                    <div class="em-external-integration">
                        <div class="emrow">
                            <div class="emfield">{{data.trans.label_google_cal_api_key}}<sup>*</sup></div>
                            <div class="eminput">
                                <input type="text" ng-required="data.options.gcal_sharing==1" name="google_cal_api_key"  ng-model="data.options.google_cal_api_key">
                                <div class="emfield_error">
                                    <span ng-show="optionForm.google_cal_api_key.$error.required && !optionForm.google_cal_api_key.$pristine">{{data.trans.validation_required}}</span>
                                </div>
                            </div>
                            <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                Enter your Google Calendar API Key.
                            </div>
                        </div>
                    </div>  
                </div>  
            </div>  



            <div ng-show="showPayments">

                <div class="emrow kf_pricefield_checkbox">
                    <div class="emfield">{{data.trans.label_payment_processor }}</div>
                    <div class="eminput em-payments">
                        
                        
                        
                        
                        <ul  class="payment_processor">
                            <li>
                                <input type="checkbox"   name="paypal_processor" id="paypal_processor" ng-true-value="1" ng-fale-value="0"  ng-model="data.options.paypal_processor" />
                                <span><img src="{{data.base_path}}images/payment-paypal.png" alt=""></span>
                                <div class="emrow"><div class="rminput" ><a ng-class="{'disable-Stripe-Config': !data.options.paypal_processor}"  ng-click="configure_paypal=true">Configure</a></div></div>
                                <div class="emfield_error" ng-show="optionForm.paypal_email.$invalid">
                                            <span>Paypal Email cannot be left blank</span>  
                                </div>
                                
                            </li>
                        </ul>
                        <div id="kf_pproc_config_parent_backdrop" class="pg_options kf_config_pop_wrap" ng-show="configure_paypal">
                            <div id="kf_pproc_config_parent" class="paypa_settings kf_config_pop" ng-show="data.options.paypal_processor == 1">
                                <div  class="kf_pproc_config_single" id="kf_pproc_config_paypal">
                                    <div class="kf_pproc_config_single_titlebar">
                                        <div class="kf_pproc_title">
                                           <img src="{{data.base_path}}images/payment-paypal.png" alt=""></div>
                                        <span ng-click="configure_paypal=false" class="kf-popup-close">×</span></div>
                                    
                                    
                                </div>
                                
                                
                        
                            
                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_test_mode}}</div>
                                    <div class="eminput">
                                        <input type="checkbox" name="payment_test_mode" ng-true-value="1" ng-false-value="0"  ng-model="data.options.payment_test_mode">
                                        <div class="emfield_error">

                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enable PayPal Sandbox. PayPal Sandbox can be used to test payments without initiating actual transactions.
                                    </div>
                                </div> 

                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_paypal_email}}</div>
                                    <div class="eminput">
                                        <input ng-required="data.options.paypal_processor==1" type="email" name="paypal_email"  ng-model="data.options.paypal_email">
                                        <div class="emfield_error" ng-show="optionForm.paypal_email.$invalid">
                                            <span> {{data.trans.validation_email}}</span>  
                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enter your email registered with PayPal.
                                    </div>
                                </div> 

                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_paypal_api_username}}</div>
                                    <div class="eminput">
                                        <input type="text" name="paypal_api_username"  ng-model="data.options.paypal_api_username">
                                        <div class="emfield_error">

                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enter your PayPal API Username.
                                    </div>
                                </div> 

                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_paypal_api_password}}</div>
                                    <div class="eminput">
                                        <input type="text" name="paypal_api_password"  ng-model="data.options.paypal_api_password">
                                        <div class="emfield_error">

                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enter your PayPal API Password.
                                    </div>
                                </div>  

                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_paypal_api_sig}}</div>
                                    <div class="eminput">
                                        <input type="text" name="paypal_api_sig"  ng-model="data.options.paypal_api_sig">
                                        <div class="emfield_error">

                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enter your PayPal API Signature.
                                    </div>
                                </div>         

                                <div class="emrow">
                                    <div class="emfield">{{data.trans.label_paypal_page_style}}</div>
                                    <div class="eminput">
                                        <input type="text" name="paypal_page_style"  ng-model="data.options.paypal_page_style">
                                        <div class="emfield_error">

                                        </div>
                                    </div>
                                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                                        Enter your PayPal Page Style.
                                    </div>
                                </div>
                                
                                 <div class="emrow">
                                     <div class="dbfl kf-buttonarea"><button class="btn btn-primary" ng-click="saveSettings(optionForm.$valid)" ng-disabled="postForm.$invalid" value="{{data.trans.label_save}}" >Save</button></div>
                                 </div>
                            </div>
                            
                         </div>     
                        <?php  do_action("em_admin_prcoessor_options"); ?>
                        <?php do_action("em_admin_prcoessor_options"); ?>
                   
                    </div>
                         <div class="emrow">
                                    <div class="emfield">Currency</div>
                                    <div class="eminput">
                                        <select id="currency" name="currency" ng-model="data.options.currency" ng-options="cur.key as cur.label for cur in data.currencies"></select>
                                        <div class="emfield_error">
                                        </div>
                                    </div>                        
                                    <div class="emnote "><i class="fa fa-info-circle" aria-hidden="true"></i>
                                       Default Currency for accepting payments. Usually, this will be default currency in your PayPal account.
                                    </div>
                        </div>
                    
                     <div class="dbfl kf-buttonarea">
                        <div class="em_cancel"><a class="kf-cancel" href="javascript:void(0)" ng-click="showSettingOptions()">Go to Settings Area</a></div>
                        <button class="btn btn-primary" ng-click="saveSettings(optionForm.$valid)" ng-disabled="postForm.$invalid" value="{{data.trans.label_save}}" > Save </button>
                        </div>
                    
                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Select your payment gateway.
                    </div>
                </div>   
                
            </div>



            <div ng-show="showNotification">
                <div class="emrow">
                    <div class="emfield emeditor">{{data.trans.label_registration_email_subject}}</div>
                    <div class="eminput emeditor">
                        <input type="text" name="registration_email_subject"  ng-model="data.options.registration_email_subject">
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Subject for email that will be sent to the user on registration.
                    </div>
                </div> 

                <div class="emrow">
                    <div class="emfield emeditor">{{data.trans.label_registration_email_content}}</div>
                    <div class="eminput emeditor">
                        <?php
                        include("editor.php");
                        $content = em_global_settings('registration_email_content');
                        em_add_editor('registration_email_content', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Message to be sent in email when user is registered.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield emeditor">{{data.trans.label_booking_pending_content}}</div>
                    <div class="eminput emeditor">
                        <?php
                        $content = em_global_settings('booking_pending_email');
                        em_add_editor('booking_pending_email', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Email with pending notification is sent to the user when either payment has not been made, or there is any issue related to it.
                    </div>
                </div>

                 <div class="emrow">   
                     <div class="emfield">Send Booking Confirmation Mail</div>
                     <div class="eminput">
                       <input type="checkbox" name="send_booking_confirm_email" id="send_booking_confirm_email"  ng-true-value="1" ng-false-value="0" ng-model="data.options.send_booking_confirm_email" >
                    </div>
                 </div>
                
                <div class="emrow" ng-show="data.options.send_booking_confirm_email==1">
                    <div class="emfield emeditor">{{data.trans.label_booking_confirmed_content}}</div>
                    <div class="eminput emeditor">
                        <?php
                        $content = em_global_settings('booking_confirmed_email');
                        em_add_editor('booking_confirmed_email', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Confirmation email is sent to the user once his/ her payment has been received and seats reserved.
                    </div>
                </div>

                
                 <div class="emrow">   
                     <div class="emfield">Send Booking Cancellation Mail</div>
                     <div class="eminput">
                       <input type="checkbox" name="send_booking_cancellation_email" id="send_booking_cancellation_email"  ng-true-value="1" ng-false-value="0"  ng-model="data.options.send_booking_cancellation_email" >
                    </div>
                 </div>
                
                <div class="emrow" ng-show="data.options.send_booking_cancellation_email==1">
                    <div class="emfield emeditor">{{data.trans.label_booking_cancelation_content}}</div>
                    <div class="eminput emeditor">
                        <?php
                        $content = em_global_settings('booking_cancelation_email');
                        em_add_editor('booking_cancelation_email', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        User will receive this message on requesting cancellation for a booking.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield emeditor">Reset  User Password </div>
                    <div class="eminput emeditor">
                        <?php
                        $content = em_global_settings('reset_password_mail');
                        em_add_editor('reset_password_mail', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        In case user requests password reset, admin may initiate it from Booking Manager Page, triggering this email with new password to the requesting user.
                    </div>
                </div>

                <div class="emrow">
                    <div class="emfield emeditor">{{data.trans.label_booking_refund_content}}</div>
                    <div class="eminput emeditor">
                        <?php
                        $content = em_global_settings('booking_refund_email');
                        em_add_editor('booking_refund_email', $content);
                        ?>    
                        <div class="emfield_error">

                        </div>
                    </div>
                    <div class="emnote emeditor GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Refund Mail is sent to the user when admin accepts cancellation request and issues a refund.
                    </div>
                </div>

            </div>
            
            <div ng-show="showPageGSettings">
                 <div class="emrow">
                    <div class="emfield">Hide Past Events from Events Directory</div>
                    <div class="eminput">
                        <input type="checkbox" ng-true-value="1" ng-false-value="0" name="hide_past_events" id="hide_past_events" ng-model="data.options.hide_past_events" /></li>
                    </div>                   
                    <div class="emfield_error">
                    </div>
                   <div class="emnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        This will hide past events from Events Directory page.
                    </div>
                </div>    
                
<!--                 <div class="emrow">
                    <div class="emfield">Show Recurrence Events</div>
                    <div class="eminput">
                        <input type="checkbox" ng-true-value="1" ng-false-value="0" name="show_recurrence_events" id="show_recurrence_events" ng-model="data.options.show_recurrence_events" /></li>
                    </div>                   
                    <div class="emfield_error">
                    </div>
                    <div class="emnote GSnote"><i class="fa fa-info-circle" aria-hidden="true"></i>
                        Hide Past Events from Events Directory
                    </div>
                </div> -->
                
            </div>    
            
            <div class="dbfl kf-buttonarea" ng-show="showNotification || showExternalIntegration || showPageIntegration || showPageGSettings">
                <div class="em_cancel"><a class="kf-cancel" href="javascript:void(0)" ng-click="showSettingOptions()">Go to Settings Area</a></div>
                <button type="submit" class="btn btn-primary" ng-disabled="postForm.$invalid">{{data.trans.label_save}}</button>
            </div>
        </form>    

    </div>
</div>
