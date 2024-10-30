<div class="emagic">
    <div class="em_register_form em_block dbfl" ng-app="eventMagicApp" ng-controller="emRegisterCtrl" ng-cloak="" ng-init="initialize()" id="em_register_section">
        <div class="em_reg_form em_block dbfl em_bg_lt" ng-show="!data.showLogin">
            <?php
            // Check if Registration Magic Form is configured
            $rm_form_id = em_get_post_meta($_GET['event_id'], 'rm_form', true);     
            if($rm_form_id>0 && is_registration_magic_active()):
          
                $form = new RM_Forms;
                $form->load_from_db($rm_form_id);
            endif;
            
            if ($rm_form_id > 0 && is_registration_magic_active() && $form->form_type==1):
                echo do_shortcode("[RM_Form id='$rm_form_id']");
            else:
                ?>
            
                <form name="emRegisterForm" novalidate ng-show="!data.showLogin">
                    <div class="em_input_row dbfl">
                        <h3 class="em_form_heading"> <i class="fa fa-user-plus" aria-hidden="true"></i>REGISTER</h3>
                    </div>
                    <div class="em_input_row dbfl">
                        <div class="em_form_errors"> {{data.registerError}} </div>
                        <div class="em_input_form_field">
                            <label class="em_input_label">First Name<sup>*</sup></label>
                            <input required type="text" class="em_input_field" name="first_name" ng-model="data.first_name" />
                        </div>
                    </div>
                    <div class="em_input_row dbfl">
                        <div class="em_input_form_field">    
                            <label class="em_input_label">Last Name<sup>*</sup></label>
                            <input type="text" required="" class="em_input_field" name="last_name" ng-model="data.last_name" />
                        </div>
                    </div> 

                    <div class="em_input_row dbfl">
                        <div class="em_input_form_field">
                            <label class="em_input_label">Email/Username<sup>*</sup></label>
                            <input type="email" required=""  class="em_input_field" name="email" ng-model="data.email" />
                        </div>
                    </div>
                    <div class="em_input_row dbfl">
                        <div class="em_input_form_field">
                            <label class="em_input_label">Phone</label>
                            <input type="text"  class="em_input_field" name="phone" ng-model="data.phone" />
                        </div>

                    </div>

                    <div class="em_input_row dbfl">
                        <div class="em_input_submit_field">
                            <input  type="button" ng-disabled="emRegisterForm.$invalid" ng-click="register(emRegisterForm.$valid)" value="Register" />
                        </div>
                    </div>
                    
                    
                </form> 
        <?php endif; ?>
                <div class="em_login_notice dbfl">Already have an Account? <a href="javascript:void(0)" ng-click="data.showLogin = true" class="em_login">Please Login.</a></div>
           </div>    
        

        <div ng-show="data.showLogin" class="em_login em_block em_bg_lt dbfl">
            <form name="emLoginForm" novalidate>

                <div class="em_notice dbfl">
                    <div class="em_new_registration" ng-show="data.newRegistration">
                        You have successfully registered. Please check your email and login.
                    </div>
                </div>

                <div class="em_input_row dbfl">
                    <h3 class="em_form_heading"><i class="fa fa-sign-in" aria-hidden="true"></i>LOGIN</h3>
                    <div class="em_form_errors"> {{data.loginError}} </div>

                    <div class="em_input_row dbfl">
                        <div class="em_input_form_field">
                            <label class="em_input_label">Email/Username<sup>*</sup></label>
                            <input type="text" required=""  class="em_input_field" name="user_name" ng-model="data.user_name" />
                        </div>
                    </div>
                    <div class="em_input_row dbfl">
                        <div class="em_input_form_field">
                            <label class="em_input_label">Password</label>
                            <input type="password"  class="em_input_field" name="password" ng-model="data.password" />
                        </div>
                    </div>
                    <div class="em_input_row dbfl">
                        <div class="em_input_submit_field">
                            <label class="em_input_label">&nbsp;</label>
                            <input  type="button" ng-click="login(emLoginForm.$valid)" value="Login" />
                        </div>
                        <div class="em_input_row dbfl">
                            <div class="em_login_notice">Forgot Password? <a href='<?php echo wp_login_url() . "?action=lostpassword"; ?>' ng-click="" >Click Here</a></div>

                            <div class="em_login_notice">Don't have an Account? <a href="javascript:void(0)" ng-click="data.showLogin = false" class="em_login">Please Register.</a></div>
                        </div>     
                        </div>

                    
                </div>

            </form>
        </div>
    </div>
</div>