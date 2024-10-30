eventMagicApp.controller('emBookingCtrl', function ($scope, $http, EMRequest, Seat) {
    $scope.data = {};
    $scope.show_cart= false;
    $scope.event= {};
    $scope.discount= 0;
    $scope.price= 0;
    $scope.order = {};
    $scope.selectedSeats = [];
    $scope.requestInProgress = false;
    $scope.orders= [];
    $scope.order_ids= [];
    $scope.order_id_arr =[];
    $scope.currency_symbol;
    $scope.payment_processors= [];
    $scope.venue_id;
    $scope.event_id;
    $scope.bookable= true;   
    $scope.parent={};
    $scope.update_cart= false;
    $scope.booking_notice;
    $scope.is_timer_on= false;
    $scope.minute=0;
    $scope.second=0;
    
    $scope.setSeatContainerWidth= function()
   {
       if( $scope.event.seats.length>0 )
       {
           var seat_container_width= ($scope.event.seats[0].length*35) + 40 + "px";
           $scope.seat_container_width={ "width" : seat_container_width };
       }
   }
   
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    // Called at initialization
    $scope.initialize = function (venue_id, event_id) {
        $scope.venue_id= venue_id;
        $scope.event_id= event_id;
        $scope.load_payment_configuration( $scope.event_id); 
    }

    /*
     * Loading children events
     * 
     */
    $scope.load_children= function()
    {
        $scope.request = {};
        $scope.request.venue_id = $scope.venue_id;
        $scope.request.event_id = $scope.event_id;
        
        // Loading Seats and other payment options related to Event
        $scope.progressStart();
        EMRequest.send('em_load_event_children', $scope.request).then(function (response) {
            $scope.progressStop();
            $scope.parent.children= response.data.children;
        });
    }
    
    // Loading Global payment configuration
    $scope.load_payment_configuration= function( event_id)
    {   
         $scope.request = {};
        $scope.request.event_id = event_id || $scope.event_id;
        $scope.event_id = event_id || $scope.event_id;
        EMRequest.send('em_load_payment_configuration', $scope.request).then(function (response) {
            $scope.progressStop();
            if(!response.data.is_payment_configured && response.data.ticket_price>0)
            {
                alert("Payment system is not configured.")
                return;
            }
            $scope.payment_processors= response.data.payment_prcoessor;
            $scope.currency_symbol= response.data.currency_symbol; 
            // As payment system is confiured loading Event's data for booking
            $scope.loadEventForBooking();
        });
    }
    
    $scope.loadEventForBooking= function(event_id)
    {
        $scope.request = {};
        $scope.request.event_id = event_id || $scope.event_id;
        $scope.event_id = event_id || $scope.event_id;
        
        // Loading Seats and other payment options related to Event
        $scope.progressStart();
        EMRequest.send('em_load_event_for_booking', $scope.request).then(function (response) {
            $scope.progressStop();
            if(response.data.child_events.length>0)
            {
                $scope.event.has_children= true;
                $scope.load_children();
            }
            else
            {
                $scope.event.children= [];
                // As there are no child events, Load event's data instantly
                $scope.event= response.data;
                $scope.setSeatContainerWidth(); 
            }
            if(response.data.venue.type=="seats")
                $scope.allowSeatSelectionUpdate();
            else
                $scope.allowSeatQuantityUpdate();
            
            $scope.display_cart(false);
        });
    }
    
    $scope.allowSeatQuantityUpdate= function()
    {
        $scope.selectedSeats= [];
        var order_exist= false;
        if($scope.orders.length>0)
        {  
            for(var i=0;i<$scope.orders.length;i++)
            {   
                if($scope.orders[i].event_id==$scope.event_id)
                {
                    document.getElementById("standing_order_quantity").value= $scope.orders[i].quantity;
                    $scope.update_cart= true;
                    order_exist= true;
                    break;
                }
                     
            }
        } 
        if(!order_exist)
        {
            $scope.update_cart= false;
            document.getElementById("standing_order_quantity").value= 1;
        }
            
        
    }
    
    // Allows selection and deselection of seats after order creation.
    $scope.allowSeatSelectionUpdate= function()
    {   $scope.selectedSeats= [];
       
        if($scope.orders.length>0)
        {  
            for(var i=0;i<$scope.orders.length;i++)
            {   
                if($scope.orders[i].event_id==$scope.event_id)
                {
                    var seatPositions= $scope.orders[i].seat_pos;
                    for(var j=0;j<seatPositions.length;j++)
                    {   
                         var seatIndexes= seatPositions[j].split("-");
                         var row= $scope.event.seats[seatIndexes[0]];
                         var seat = row[seatIndexes[1]];
                         seat.type='selected';
                         console.log(seat);
                         
                         $scope.update_cart= true;
                         $scope.selectedSeats.push(seat);
                    }

                }
            }
        } 
        
        if($scope.selectedSeats.length==0)
            $scope.update_cart= false;
    }
    
    // Delete current event's order and create new 
    $scope.updateOrder= function()
    {  
        $scope.progressStart();
        // Deleting previous order
        if($scope.orders.length>0)
        {
            for(var i=0;i<$scope.orders.length;i++)
            {   
                if($scope.orders[i].event_id==$scope.event_id)
                {
                    $scope.request= {
                        "order_id": $scope.orders[i].order_id
                    }
                      
                    //$scope.progressStart();
                    EMRequest.send('em_delete_order', $scope.request).then(function (response) {
                        $scope.progressStop();
                         // Removing old order
                         $scope.orders.splice(i,1);
                         // Check if any seats are selected
                        if($scope.event.venue.type=="seats" && $scope.selectedSeats.length > 0)
                            $scope.orderSeats();
                        else if($scope.event.venue.type=="standings")
                            $scope.orderStandings();
                        else
                           $scope.update_cart= false;
                         
                    });
                    break;
                }
            }
        }
        
      
    }
    
    $scope.orderStandings= function()
    {
         $scope.progressStart();
         
        $scope.order= {};
        $scope.request= {
            'event_id': $scope.event_id
        };
        // Order quantity related checks
        var quantity= document.getElementById("standing_order_quantity").value;
        if(!(quantity>0))
        {
            alert('Invalid Seats');
                  $scope.progressStop();
            return;
        }
        
        if($scope.event.max_tickets_per_person>0 && quantity>$scope.event.max_tickets_per_person)
        {
            alert("Maximum tickets allowed per booking - " + $scope.event.max_tickets_per_person);
                  $scope.progressStop();
            return;
        }
      
       
        
        // Check if event is bookable
        EMRequest.send('em_check_bookable', $scope.request).then(function (response) {
         
              $scope.progressStop();
            if ($scope.check_errors(response))
                return true;
            else
            {
              
                $scope.order.item_number = $scope.get_item_number($scope.event_id);
                $scope.order.quantity = quantity;
                $scope.no_seats = true;
                $scope.order.ticket_limit = $scope.event.max_tickets_per_person;
                $scope.order.seats = $scope.event.seats;
                $scope.order.allow_discount = $scope.event.allow_discount;
                $scope.order.discount_per = $scope.event.discount_per;
                $scope.order.discount_no_tickets = $scope.event.discount_no_tickets;           
                $scope.order.single_price = $scope.event.ticket_price;
                $scope.order.payment_gateway = "paypal";
                $scope.order.event_id = $scope.event_id;
                $scope.order.name=$scope.event.name;
                $scope.order.start_date=$scope.event.start_date;
             
                EMRequest.send('em_book_seat', $scope.order).then(function (response) {
                   
                    if ($scope.check_errors(response))
                    {
                        return true;
                    }
                    // Payment countdown timer
                    var timeInMinutes = 60*4 ,
                    display = document.querySelector('#em_payment_timer');
                    $scope.startTimer(timeInMinutes, display); 
                    $scope.order.order_id = response.data.order_id;
                   
                 //   console.log($scope.orders);
                    $scope.orders.push($scope.order);
                    $scope.calculate_discount();
                    $scope.calculate_price();
                    $scope.update_order_ids();
                    $scope.display_cart(true);
                });
            }


        });
        
    }
    
    /*
     * Setting Order object for seats
     */
    $scope.orderSeats = function () {
      $scope.progressStart();
        
         
        // Temporarily storing seating position and seat sequences
        var tmpSequences = [];
        var seatPos = [];
        $scope.order= {};
        $scope.request= {
            'event_id': $scope.event_id
        };
        
        // Check if any seats are selected
        if(!($scope.selectedSeats.length > 0))
        {
            alert("No seats are selected"); 
                  $scope.progressStop();
            return;
        }    
        
    
        // Check if event is bookable
        EMRequest.send('em_check_bookable', $scope.request).then(function (response) {
           $scope.progressStop();
            if ($scope.check_errors(response))
                return true;
            else
            {
                // If seats are selected for booking
                if ($scope.selectedSeats.length > 0) {

                    angular.forEach($scope.selectedSeats, function (seat, key) {
                        tmpSequences.push(seat.seatSequence);
                        seatPos.push(seat.row + "-" + seat.col);

                        // Updating seat type to temporarily block the seat from other users
                        seat.type = "tmp";
                    });

                    $scope.order.item_number = tmpSequences.join(', ');
                    $scope.order.seat_sequences = tmpSequences;
                    $scope.order.seat_pos = seatPos;
                    $scope.order.quantity = tmpSequences.length;
                    
                } else if ($scope.event.seats.length == 0)
                {
                    $scope.order.item_number = $scope.get_item_number($scope.event_id);
                    $scope.order.quantity = 1;
                    $scope.no_seats = true;
                } 
                // Show final checkout section
                $scope.order.ticket_limit = $scope.event.max_tickets_per_person;
                $scope.order.seats = $scope.event.seats;
                $scope.order.allow_discount = $scope.event.allow_discount;
                $scope.order.discount_per = $scope.event.discount_per;
                $scope.order.discount_no_tickets = $scope.event.discount_no_tickets;           
                $scope.order.single_price = $scope.event.ticket_price;
                $scope.order.payment_gateway = "paypal";
                $scope.order.event_id = $scope.event_id;
                $scope.order.name=$scope.event.name;
                
                $scope.order.start_date=$scope.event.start_date;
                $scope.progressStart();
                
                EMRequest.send('em_book_seat', $scope.order).then(function (response) {
                    $scope.progressStop();
                 
                    if ($scope.check_errors(response))
                    {
                        return true;
                    }
                    // Payment countdown timer
                    var timeInMinutes = 60*4 ,
                    display = document.querySelector('#em_payment_timer');
                   
                    $scope.startTimer(timeInMinutes, display); 

                    $scope.order.order_id = response.data.order_id;
                    $scope.orders.push($scope.order);
                    $scope.selectedSeats= [];
                    $scope.calculate_discount();
                    $scope.calculate_price();
                    $scope.update_order_ids();
                    $scope.display_cart(true);
                });
            }


        });
    //  var testDiv = document.getElementById("booking_summary");
  
    
          //jQuery('html, body').animate({ scrollTop: jQuery('#div_id').offset().top  }, 'fast');
    }
    
    $scope.add_update_quantity= function()
    {   newVal= $scope.order.quantity;
        
         if ($scope.order.seat_post == undefined) {
            $scope.bookable = false;
            if ($scope.order.ticket_limit != 0 && newVal >= $scope.order.ticket_limit) {
                $scope.order.quantity = $scope.order.ticket_limit;
            }

            if ($scope.order.quantity > 0)
            {
                $scope.calculate_discount();
                $scope.progressStart();
                EMRequest.send('em_update_booking', $scope.order).then(function (response) {
                    $scope.progressStop();
                    $scope.check_errors(response);
                });
            }

        }
    }
    
    $scope.get_item_number= function(event_id)
    {  
        if($scope.parent.children!== undefined && $scope.parent.children.length>0)
        {
            for(var i=0;i<$scope.parent.children.length;i++)
            {
                var child= $scope.parent.children[i];
                if(event_id==$scope.parent.children[i].event_id)
                {
                //    console.log($scope.parent.children[i].start_date);
                    return $scope.parent.children[i].start_date;
                }
            }
        }
        else
        {
            return $scope.event.name;
        }
        
    }
    
    $scope.display_cart= function(show)
    {
        if(show){
             $scope.show_cart= true; 
        }  
        else{
            $scope.show_cart= false;    } 
    }
    
    $scope.calculate_price= function()
    {
        $scope.price=0;
        $scope.item_numbers = 0
        for(var i=0;i<$scope.orders.length;i++)
        {
            
                $scope.price += $scope.orders[i].quantity * $scope.orders[i].single_price;
                $scope.item_numbers += $scope.orders[i].quantity;
        }
        
        if($scope.price>0)
        {  // console.log($scope.discount);
            $scope.price = $scope.price -$scope.discount;
        }       
    }
    
    $scope.update_order_ids= function()
    {   var order_ids= [];
        for(var i=0;i<$scope.orders.length;i++)
               order_ids.push($scope.orders[i].order_id);
           
        $scope.order_ids= order_ids.join(',');    
    }
    
    $scope.startTimer= function(duration, display) {
       
                if($scope.is_timer_on)                    
                    return true;
        
                var start = Date.now(),
                diff,
                minutes,
                seconds,
                stop= false,
                counter=1;
                function timer() {
                    if(!stop)
                    {

                    
                    
                                        // get the number of seconds that have elapsed since
                                        // startTimer() was called
                    diff = duration - (((Date.now() - start) / 1000) | 0);
                    
                                        // does the same job as parseInt truncates the float
                    
                    minutes = (diff / 60) | 0;
                    seconds = (diff % 60) | 0;
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    display.textContent = minutes + ":" + seconds;
                   
                   
                    if (diff <= 0){
                                        // add one second so that the count down starts at the full duration
                                        // example 04:00 not 03:59
                        start = Date.now() + 1000; 
                    }

                    if(diff == 0){
                        stop= true;
                        $scope.bookable= false;
                      
                        $scope.$apply();
                        jQuery(".kf-seat-table-popup").hide();
                        jQuery(".kf-standing-type-popup").hide();
//                         jQuery("#kf-reconfirm-popup").show();

                    }
                    else{
                        counter++;
                        jQuery("#em_payment_progress").width(counter*(100/240) + "%");
                    }
                    
                    
                    }
                };
                    // we don't want to wait a full second before the timer starts
                    
                    timer();
                    setInterval(timer, 1000);
                    $scope.is_timer_on= true;
            }

    // Submitting payment to Paypal
    $scope.proceedToPaypal = function () {
        $scope.progressStart();
        if ($scope.order.order_id > 0)
        {
            var booking = {};
            booking.booking_id = $scope.order.order_id;
           
            var total_price= $scope.calculate_price();
            EMRequest.send('em_verify_booking', booking).then(function (response) {
                $scope.progressStop();
                if (response.data)
                {
                    if($scope.price==0)
                    {
                       $scope.proceedWithoutPayment();
                    }
                    else
                    {
                       jQuery("[name=emPaypalForm]").submit();
                    }
                    
                }
                else
                    alert("There seems to be a problem. Please refresh the page and try again");
            });
        } else {
            alert("There seems to be a problem. Please refresh the page and try again");
        }
    }
    
    $scope.proceedWithoutPayment= function()
    {   
        var booking = {};
        var order_ids= [];
        var id=[];
   
            
        
        for(var i=0;i<$scope.orders.length;i++)
             order_ids.push($scope.orders[i].order_id);
             

  
  
        booking.booking_id = order_ids;
      
        $scope.progressStart();
        EMRequest.send('em_confirm_booking_without_payment', booking).then(function (res){
            $scope.progressStop();                
            if ($scope.check_errors(res))
            {
                return true;
            }
            location.href=res.data.redirect;
            $scope.progressStop();
            });
       
    
   
    }
    
    

    // Calculate discount if configured
    $scope.calculate_discount = function () {
        $scope.discount=0;
        if ($scope.order.allow_discount)
        {
            for(var i=0;i<$scope.orders.length;i++)
            {
                if ($scope.orders[i].quantity >= $scope.orders[i].discount_no_tickets)
                {
                    total_price = $scope.orders[i].quantity * $scope.orders[i].single_price;
                    $scope.discount += parseFloat(parseFloat(((total_price / 100) * $scope.orders[i].discount_per)).toFixed(2));

                } else
                {   // Making sure discount is 0
                    $scope.discount += 0;
                }   
            }
        }
    }
    
    

    $scope.check_errors = function (response)
    {
        if (response.data.errors)
        {
            if (response.data.errors.error_capacity) {
                alert(response.data.errors.error_capacity[0]);
                $scope.bookable = false;
                return true;
            }

            if (response.data.errors.em_error_booking_expired) {
                alert(response.data.errors.em_error_booking_expired[0]);
                $scope.bookable = false;
                return true;
            }
            
            if (response.data.errors.em_error_booking_finished) {
                alert(response.data.errors.em_error_booking_finished[0]);
                $scope.bookable = false;
                return true;
            }
            
            if (response.data.errors.error_seat_conflict) {
                alert(response.data.errors.error_seat_conflict[0]);
                $scope.bookable = false;
                return true;
            } 

        }

        $scope.bookable = true;
        return false;
    }


    /**************************** Seating selection code *************/
    $scope.selectSeat = function (seat, row, col) {
        // Preventing reserver,blocked and sold seats from selection
        if (seat.type == "reserve" || seat.type == "tmp" || seat.type == "sold") {
            return;
        }


        if (seat.type == 'selected' && seat.type != 'general')
        {   // If seat already selected then deselect it.
            var index = $scope.selectedSeats.indexOf(seat);
            if (index >= 0) {
                $scope.selectedSeats.splice(index, 1);
            }
            seat.type = 'general';
        } else
        {   
            // If number of tickets are  more than configured limit
            if (($scope.event.max_tickets_per_person != 0 && $scope.selectedSeats.length == $scope.event.max_tickets_per_person) || ($scope.selectedSeats.length==$scope.event.available_seats)) {
                angular.forEach($scope.selectedSeats, function (seat, key) {
                    seat.type = "general";
                });
                $scope.selectedSeats = [];
            }
            seat.type = 'selected';
            $scope.selectedSeats.push(seat);
        }
    }

    /********************** Generate Alphabet seat sequence ****************/
    $scope.getRowAlphabet = function (rowIndex) {
        var indexNumber = "";
        if (rowIndex > 25) {
            indexNumber = parseInt(rowIndex / 26);
            rowIndex = rowIndex % 26;
        }

        return String.fromCharCode(65 + parseInt(rowIndex)) + indexNumber;
    }

    $scope.adjustContainerWidth= function(columnMargin,index)
    {
        var width= parseInt($scope.seat_container_width.width);
        if(index==0 && columnMargin>0)
        {  
            width += columnMargin;
            $scope.seat_container_width.width = width + "px";
        }
        
    }
    
});

 
