<?php $global_settings = get_option('em_global_settings');

    if($global_settings['payment_test_mode']==1):       
        $URL= 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    else:
        $URL = 'https://www.paypal.com/cgi-bin/webscr';
    endif;
  $currency_symbol="";                    
        $currency_code= em_global_settings('currency');

        if($currency_code):
            $all_currency_symbols = EventM_Constants::get_currency_symbol();
            $currency_symbol = $all_currency_symbols[$currency_code];                        
        else:
            $currency_symbol = EM_DEFAULT_CURRENCY;
        endif;
?>

<FORM ng-show="data.pp.show_paypal" action= <?php echo $URL; ?> method="post" name="emPaypalForm">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo em_global_settings("paypal_email") ?>">
                 <input type="hidden" name="item_name" value="<?php echo $event->post_title; ?>">
                 <input type="hidden" name="item_number" value="{{item_numbers}}">
                 <input type="hidden" name="amount" value="{{price}}">
                 <input type="hidden" name="first_name" value="<?php echo $user->display_name; ?>">
                 <input type="hidden" name="email" value="<?php echo $user->user_email; ?>">
                 <input type="hidden" name="custom" value="{{order_ids}}">
                 <?php
                 
                        $return_url= add_query_arg( array('order_idss' => "{{order_ids}}" ), get_permalink(em_global_settings('profile_page')));
                 ?>
                 <INPUT TYPE="hidden" NAME="return" value="<?php echo $return_url; ?>">
                <INPUT TYPE="hidden" NAME="currency_code" value="<?php echo $currency_code;?>">
                 <input type="hidden" name="page_style" value="<?php echo em_global_settings('paypal_page_style'); ?>">

                 <input type="hidden" name="bn" value="CMSHelp_SP">
                     <?php
                        $notify_url= add_query_arg( array('em_pp_notification' => 'em_ipn'),get_permalink($event_id));
                 ?>
                 <INPUT TYPE="hidden" NAME="notify_url" value="<?php echo $notify_url; ?>">
</FORM>
