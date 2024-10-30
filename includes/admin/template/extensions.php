<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$extension_service = new EventM_Extensions();
?>

<div class="kikfyre kf-full-width ">
    <div class="kf-scblock dbfl ">
        <div class="kf-scblock kf-scpagetitle dbfl"> <b>Event Kikfyre</b> <span class="kf-brand-color">Extensions</span> </div>
        <div class="kf-ext-list" id="the-list">
            <div class="plugin-card kf-ext-card">
                <div class="plugin-card-top">
                    <div class="name column-name">
                        <h3>
                            <a href="http://kikfyre.com/" class=" open-plugin-details-modal" target="_blank">
                               STRIPE PAYMENTS
            <img  class="plugin-icon" alt="" src="<?php echo plugin_dir_url(__DIR__) . 'template/images/'; ?>stripe-logo-ext.png" > 
                                
                            </a>
                        </h3> </div>
                    <div class="action-links">
                        <ul class="plugin-action-buttons ">
                            
                            <?php $extension_service->kf_get_extension_button('STRIPE');?> 
                               
                        </ul>
                    </div>
                    <div class="desc column-description">
                        <p>Start accepting credit cards on your site for Group memberships and registrations by integrating popular Stripe payment gateway.</p>
                        <p class="authors"> <cite>By <a target="_blank" href="http://kikfyre.com">KikFyre</a></cite></p>
                    </div>
                </div>
            </div>
                <div class="plugin-card kf-ext-card">
                <div class="plugin-card-top">
                    <div class="name column-name">
                        <h3>
                            <a href="http://kikfyre.com/" class=" open-plugin-details-modal" target="_blank">
                            AUTHORIZE.NET PAYMENTS
            <img  class="plugin-icon" alt="" src="<?php echo plugin_dir_url(__DIR__) . 'template/images/'; ?>authnet-payment-logo.png" > 
                                
                            </a>
                        </h3> </div>
                    <div class="action-links">
                        <ul class="plugin-action-buttons ">

                            <li>
                                <a class="install-now button kf-install-now-btn" href="#">Coming Soon</a>
                            </li>

                        </ul>
                    </div>
                    <div class="desc column-description">
                        <p>Start accepting credit cards on your site for Group memberships and registrations by integrating popular  Authorize.Net gateway.</p>
                        <p class="authors"> <cite>By <a target="_blank" href="http://kikfyre.com">KikFyre</a></cite></p>
                    </div>
                </div>
            </div>
                 <div class="plugin-card kf-ext-card">
                <div class="plugin-card-top">
                    <div class="name column-name">
                        <h3>
                            <a href="http://kikfyre.com/" class=" open-plugin-details-modal" target="_blank">
                                     OFFLINE PAYMENTS
            <img  class="plugin-icon" alt="" src="<?php echo plugin_dir_url(__DIR__) . 'template/images/'; ?>offline-payment.png" > 
                                
                            </a>
                        </h3> </div>
                    <div class="action-links">
                        <ul class="plugin-action-buttons ">

                          <?php $extension_service->kf_get_extension_button('OFFLINE_PAYMENT');?> 

                        </ul>
                    </div>
                    <div class="desc column-description">
                        <p>Collect payments offline and manually update booking status within Event Kikfyre's dashboard.</p>
                        <p class="authors"> <cite>By <a target="_blank" href="http://kikfyre.com">KikFyre</a></cite></p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

