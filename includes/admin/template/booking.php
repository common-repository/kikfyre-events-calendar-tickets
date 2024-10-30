<div class="kikfyre" ng-app="eventMagicApp" ng-controller="bookingCtrl" ng-cloak="" ng-init="initialize('edit')">
    <div class="kf_progress_screen" ng-show="requestInProgress"></div>
    <div class="kf-booking-header dbfl">
        From: {{data.user.email}}
    </div>
    
    
    
    <div class="em-booking-detail-wrap dbfl">
          <div class="em-booking-row" >
            <span class="em-booking-label">Booking ID:</span> <span class="em-booking-detail"> {{data.user.booking_id}}</span>
        </div>
        
        <div id="em_rm_custom_data" >
        </div>
        
        
        <div class="em-booking-row" >
            <span class="em-booking-label">Email:</span> <span class="em-booking-detail">{{data.post.order_info.user_email}}</span>
        </div>
        
        <div ng-show="data.user.phone.length>0" class="em-booking-row" >
            <span class="em-booking-label">Phone:</span> <span class="em-booking-detail">{{data.user.phone}}</span>
        </div>
        
        <div ng-show="data.post.event_name" class="em-booking-row" >
            <span class="em-booking-label">Event:</span> <span class="em-booking-detail">{{data.post.event_name}}</span>
        </div>
        
        <div ng-show="data.post.order_info.quantity>0" class="em-booking-row" >
            <span class="em-booking-label">No. of Persons:</span> <span class="em-booking-detail">{{data.post.order_info.quantity}}</span>
        </div>
        
        <div ng-show="data.post.order_info.discount>0" class="em-booking-row" >
            <span class="em-booking-label">Discount:</span> <span class="em-booking-detail">{{data.post.order_info.discount}} {{data.post.currency_symbol}}</span>
        </div>
        
        <div ng-show="data.post.order_info.seat_sequences" class="em-booking-row" >
            <span class="em-booking-label">Seat No.:</span> <span class="em-booking-detail">{{data.post.order_info.seat_sequences.join()}}</span>
        </div>
        
        <div ng-show="data.post.date" class="em-booking-row" >
            <span class="em-booking-label">Booked On:</span> <span class="em-booking-detail">{{data.post.date}}</span>
        </div>
        
        <div ng-show="data.post.status=='Completed'" class="em-booking-row" >
            <span class="em-booking-label">Amount Received:</span> <span class="em-booking-detail">{{(data.post.order_info.item_price*data.post.order_info.quantity)-data.post.order_info.discount}}{{data.post.currency_symbol}}</span>
        </div>
        
        <div ng-show="data.post.status!='Completed'" class="em-booking-row" >
            <span class="em-booking-label">Amount Due:</span> <span class="em-booking-detail">{{(data.post.order_info.item_price*data.post.order_info.quantity)-data.post.order_info.discount}}{{data.post.currency_symbol}}</span>
        </div>
        
        <div class="em-booking-row" >
            <span class="em-booking-label">Status:</span> <span class="em-booking-detail">{{data.post.status}}</span>
        </div>
         <?php do_action('em_admin_offline_handle');  ?>
        
        <div class="em-booking-row kf-bg-light" >
            <span class="em-booking-label">Notifications:</span>
            <span class="em-booking-detail">
                <ul>
                  <li><a href="javascript:void(0)" ng-click="reset_password_mail()">Reset User Password Mail</a></li>
                 <li ng-show="data.post.status=='Cancelled'"><a href="javascript:void(0)" ng-click="cancellation_mail()">Resend Cancellation Mail</a></li>
                 <li ng-show="data.post.status=='Completed'"><a href="javascript:void(0)" ng-click="confirm_mail()">Resend Booking Confirm Mail</a></li>
                 <li ng-show="data.post.status=='Refunded'"><a href="javascript:void(0)" ng-click="refund_mail()">Resend Booking Refund Mail</a></li>
                 <li ng-show="data.post.status=='Pending'"><a href="javascript:void(0)" ng-click="pending_mail()">Resend Booking Pending Mail</a></li>                                 
                </ul>
            </span>
        </div>

            <div ng-show="data.post.event_name" class="em-booking-row" ng-show="data.post.payment_log.length>0">
<!--             <span class="em-booking-label"><input type="button" class="btn-primary kf-upload" value="Last Transaction Log" ng-click="data.show_transaction_log=true" /></span>-->
                 <span class="em-booking-label"><input type="button" id="display_log" class="btn-primary kf-upload" value="Last Transaction Log" ng-click="showDiv = !showDiv" /></span>
                <span ng-show="!showDiv" class="em-booking-detail" ><pre>{{data.post.payment_log | json}}</pre></span>
          </div>
        <div>
            {{refund_status}}
            <div  ng-show="data.final_price>0 && (data.post.status=='Cancelled') && data.post.gateway!='offline'" class="em-booking-row" >
                <span class="em-booking-label"></span> <span class="em-booking-detail"><input type="button" value="Refund" ng-click="cancelBooking()" class="btn btn-primary kf-upload" /></span>
            </div>

        </div>
    </div>
    
    
   
    <div ng-if="data.post.type == 'seats' &&  data.post.event_status !='trash'" ng-show="data.post.status !='Pending' " class="em_booking_buttons em_seat_sequence">
        <span  ng-repeat="seat_sequence in data.post.order_info.seat_sequences" >
            <span><a id={{seat_sequence}} ng-click="printTicket(data.post.post_id,seat_sequence)" class="btn-primary">{{seat_sequence}}</a>
            <!--<span  ng-if="data.post.status !='Completed'" id={{seat_sequence}}  href="#" class="btn-primary em_cancelled_ticket">{{seat_sequence}}</span>-->
        </span>    
    </div>
    
    
     
    
    <div class="em_booking_buttons dbfl">
        <span  ng-if="data.post.type != 'seats'" ng-show="data.post.status !='Pending' && data.post.event_status !='trash'" >
            <a  ng-click="printTicketStand(data.post.post_id)" class="btn-primary">Print Ticket</a>
        </span>
       
       <span ng-show="data.post.status =='Pending' && data.post.event_status !='trash'" >
            <a>Print Ticket</a>
        </span>
        
        <span  ng-show="data.post.event_status !='trash'">
        <a href="admin-ajax.php?action=em_download_booking_details&booking_id={{data.post.post_id}}" class="btn-primary"> Download Booking Details</a>
        </span>
    </div>
    
    <div class="em-sticky-note-wrap dbfl">
        
        <div class="emheader">New Note</div>
    
     <div class="em-sticky-note dbfl">
        <div class="em-booking-row em-booking-textarea" >
            <div class="emrow">
                <textarea name="note" ng-model="data.post.note">
                
                </textarea>
            </div>
            <div class="emrow">
                <input type="button" ng-click="savePost()" value="Add"  class="btn btn-primary kf-upload"/>
                
            </div>
            <div class="emrow">    
                
                <ul  class="em-notes-row" >
                    <li ng-repeat="note in data.post.notes track by $index">
                        {{note}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
        <div>
           <a href='{{data.links.cancel}}'>Back</a>
        </div>
         <div id="em_printable">
            
        </div>