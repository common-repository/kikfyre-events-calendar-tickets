<?php  
        if(empty($user) || empty($booking))
            die("No such data exists");  
        $event= get_post(em_get_post_meta($booking->ID,'event_id',true));
        $terms = wp_get_post_terms($event->ID, EM_VENUE_TYPE_TAX);
        $venue = $terms[0];
        $type = em_get_term_meta($venue->term_id, 'type', true); 
        $order_info= em_get_post_meta($booking->ID, 'order_info', true); 
        $payment_info = em_get_post_meta($booking->ID, 'payment_log', true);
      
        $global_settings = new EventM_Global_Settings_Model(); 
       // $discount= ($order_info['quantity']*$order_info['item_price']*$order_info['discount'])/100;;
        $booking_service= new EventM_Booking_Service();
        $total_price=$booking_service->get_final_price($booking->ID); 
        $currency_symbol= $order_info['currency'];
                $payment_log= maybe_unserialize(em_get_post_meta($booking->ID, 'payment_log', true));   
                if(isset($currency_symbol) && !empty($currency_symbol)):
                           $currency_symbol= $currency_symbol;
                elseif(isset($payment_log['payment_gateway']) && ($payment_log['payment_gateway'] == 'paypal' )):
                            $currency_symbol = $payment_log['mc_currency'];
                else:   
                endif;
     //   $total_price= ($order_info['quantity']*$order_info['item_price'])-$order_info['discount'];
     
        if($total_price==0)
            $total_price = "Free";
        else 
            $total_price = $total_price.$currency_symbol;
        
        
         $status = EventM_Constants::$status[$booking->post_status];
        $offline_status = EventM_Constants::$offline_status[$payment_info['offline_status']];
  
?>
<link rel="stylesheet" type="text/css" href="<?php echo EM_BASE_URL; ?>includes/templates/css/em_modal.css">
<script type="text/javascript" src="<?php echo EM_BASE_URL; ?>includes/templates/js/em-public.js"></script>
<div class="em_modal_wrapper" id="em_booking_details_modal">
    <div id="em_message_bar">
    <?php if(isset($offline_status))
        {?>
        
            <?php if($status=='Pending'):
                echo '**Ticket download link will be available after Confirmation.';
            endif; 
                ?>
             <?php if(($status=='Cancelled' && $offline_status == 'Received') || ($status=='Cancelled' && $payment_info['payment_gateway'] != 'offline')):

                echo '**Booking Cancelled Successfully.';
            endif; 
                ?>
                <?php if($status=='Completed' && ($offline_status == 'Pending' || $payment_info['payment_gateway'] != 'offline')):

                echo '**Ticket download link will be available after Confirmation.';
            endif; 
                ?>
                 <?php if($status=='Cancelled' && $offline_status == 'Cancel' ):

                echo '**Booking has been Cancelled by Admin.';
            endif; 
                ?>
                <?php if($status=='Cancelled' && $offline_status == 'Pending' ):

                echo  '**Booking Cancelled Successfully.';
            endif; 
                ?>
             <?php if($status=='Refunded'):
                echo '**We have issued a refund of '.$total_price .' for this booking';
            endif; 
                ?>
                 <?php if($status=='Completed' && $offline_status == 'Cancelled'):
               echo '**Booking has been Cancelled.';
            endif;
            ?>
    <?php }
    else{ ?>
        <div id="em_message_bar">
    <?php if($status=='Pending'):
        echo '**Ticket download link will be available after Confirmation.';
    endif; 
        ?>
     <?php if($status=='Cancelled'):
       
        echo '**Booking Cancelled Successfully. Refund of '.$total_price .' will be issued shortly.';
    endif; 
        ?>
     <?php if($status=='Refunded'):
        echo '**We have issued a refund of '.$total_price .' for this booking';
    endif; 
        ?>
    </div>
    <?php }
    ?>
    </div>
    <table class="em_modal_table">
        <tr>
              <td>Booking ID</td>
            <td><?php echo $booking->ID; ?></td>
        </tr>
        
        <tr> 
            <td>Tickets</td>
            <td><?php echo $order_info['quantity']; ?></td>
        </tr>
        
        <tr>
            <td>Total Price</td>
            <td><?php  echo $total_price; ?></td>
        </tr>
            
     <?php       
             if($type=='seats')
            {            
                    if(($order_info['seat_sequences'])>0)
                    { ?>                   
                         <tr> 
                            <td>Seats</td>
                            <td><?php echo implode(',',$order_info['seat_sequences']); ?></td>
                        </tr>
              <?php }
                 
            }
      ?>
            
        
        <tr> 
            <td>Status</td>
            <td id="em_booking_status"><?php echo EventM_Constants::$status[$booking->post_status]; ?></td>
        </tr>
        
        
        <?php 
        if(isset($offline_status) && $status !='Refunded' ){
            do_action('em_front_user_booking_details', $offline_status);
        }
        ?>  
        
        
          
        <?php
        $event_id = em_get_post_meta($booking->ID, 'event_id');           
        $allow_cancel = em_get_post_meta($event_id[0], 'allow_cancellations');
        
        if(($booking->post_status=="publish") &&  ($allow_cancel[0]==1)):
        ?>
        <tr id="em_action_bar"> 
           
            <td>Action</td>            
            <td><input type="button" id="em_cancelled_btn" value="Cancellation Request " onclick="em_cancel_booking(<?php echo $booking->ID; ?>)" /></td>
        </tr>
        <?php
        endif;
        ?>
        
        <tr id="em_print_bar">
             <?php 
              if(($status == 'Completed' && !isset($offline_status))||($status == 'Completed'  && $payment_info['offline_status'] === 'Received')):  
                      if(($order_info['seat_sequences'])>0):?>
                        <td>Select seat no. to download Ticket</td>
                        <td><select id="seat_no" onchange="em_show_ticket(this.value)">
                                <option selected="true"  disabled="disabled" value="">Select Ticket</option>
                            <?php   foreach($order_info['seat_sequences'] as $seats):?>
                                <option value="<?php echo $seats;?>"><?php echo $seats;?></option>
                            <?php endforeach;?>
                            </select>
                            <input id="em_print_btn" type="button" style="display:none" onclick="printTicket()" value="Print" />
                            </td>

                  <?php   else: ?>
                             <td>Click here to download Ticket</td>
                             
                      <td>
                          <button onclick="em_show_ticket_stand(<?php echo $booking->ID; ?>)">Ticket</button> 
                          
                          <input id="em_print_btn_stand" type="button" style="display:none" onclick="printTicketStand()" value="Print" />
                      </td>

                <?php    endif; ?>
         
          <?php  endif;
            ?>
    </tr>
   
    </table>
    <div id="em_printable">
    <?php      
        if($type=='seats'){
                foreach($order_info['seat_sequences'] as $seat):
                    $ticket_html = EventM_Print::front_ticket($booking,'',$seat);
                    echo '<div class="em_ticket" style="display:none" id=em_ticket_'.$seat.'>'.$ticket_html.'</div>';
                endforeach;
        }
        else{
            $ticket_html = EventM_Print::front_ticket($booking,'','');
            echo '<div class="em_ticket_stand" style="display:none" id="em_ticket">'.$ticket_html.'</div>';    	
        }
     ?>
    </div>    
     
    
    <!-- Getting font colors and font families -->
    
    <style>
        @media print {
            body{
            display:none;
            }
        }
    </style>
    <script>
        function em_show_ticket()
        {
            jQuery("#em_printable .em_ticket").hide();
            jQuery("#em_print_btn").show();
        }
        
    
    
        function printTicket()
        {   
            var seat = jQuery("#seat_no").val();
            if(jQuery("#em_print_section")!= undefined)
                jQuery("#em_print_section").remove();
            
            jQuery("#em_ticket_" + seat).show();
            jQuery('body').after("<div id='em_print_section'>" + jQuery("#em_printable").html() + "</div>");
            jQuery("#em_print_section").show();
            jQuery("#em_ticket_" + seat).hide();
            window.print();
            jQuery("#em_print_section").hide();
        }
        //Standing Tickets
        function em_show_ticket_stand(booking_id)
        {
            
            jQuery(".em_ticket_stand").hide(); 
            
            if(jQuery("#em_print_section")!= undefined)
                jQuery("#em_print_section").remove();
            
            jQuery("#em_ticket").show();
            jQuery('body').after("<div id='em_print_section'>" + jQuery("#em_printable").html() + "</div>");
            jQuery("#em_print_section").show();
            jQuery("#em_ticket").hide();
            window.print();
            jQuery("#em_print_section").hide();

        }
        

    </script>    
</div>
<?php die; ?>


        
        
        
        
