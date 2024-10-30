<?php 


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventM_DownloadBooking {
    
    
    public static function Booking_Details($booking)
    {
        
        $html= "";
         $header_data = array('logo' => null, 'header_text' => null,'title' => '');
        $header_data = wp_parse_args( $header_data, array('logo' => null, 'header_text' => null,'title' => '') );    
        if(!class_exists('TCPDF'))
            require_once plugin_dir_path(dirname(__FILE__)) . 'lib/tcpdf_min/tcpdf.php';

        // create new PDF document
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Event Magic');
                $pdf->SetTitle('Submission');
                $pdf->SetSubject('PDF for Submission');
                $pdf->SetKeywords('submission,pdf,print');
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
        $pdf->SetHeaderData($header_data['logo'], 10 ,$header_data['title'], $header_data['header_text']);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('courier', '', 10);

        // add a page
        $pdf->AddPage();

        $data= array();
      
        $event= get_post(em_get_post_meta($booking->ID,'event_id',true));
        $data['booking_id']=$booking->ID;
        $data['event_title']= $event->post_title;
        
            $status_list= array(
                       'publish'=>'Completed',
                       'em-pending'=>'Pending',
                       'em-refunded'=>'Refunded',
                       'em-cancelled'=>'Cancelled'
        );
           
        $payment_log= maybe_unserialize(em_get_post_meta($booking->ID, 'payment_log', true));
        $offline_status = EventM_Constants::$offline_status[$payment_log['offline_status']];

       
        if(isset($offline_status) && $status_list[get_post_status($booking->ID)]=='Refunded'){
            $data['status']=$status_list[get_post_status($booking->ID)];
        }  
        else if(isset($offline_status)){                                         
            $data['status']=$offline_status;                                            
        }
        else{
            $data['status']=$status_list[get_post_status($booking->ID)];
        }
        
        if(empty($event))
            die("Event does not exists in database.");
         
        $terms = wp_get_post_terms($event->ID, EM_VENUE_TYPE_TAX);
        if (!empty($terms) && count($terms) > 0):
            $venue = $terms[0];
            $venue_address = em_get_term_meta($venue->term_id, 'address', true);
            $data['venue_name']= $venue->name;
            if(!empty($venue_address)):
            $data['venue_address']= $venue_address;
            else:          
            $data['venue_address']= 'Venue address is not defined.';
        endif;    
        endif;
        $event_dao = new EventM_Event_DAO();
        $booking_service = new EventM_Booking_Service();
        $order_info= $event_dao->get_meta($booking->ID,'order_info');  
      
        $currency_symbol= $order_info['currency']; 
                     if( isset($currency_symbol) && !empty($currency_symbol)):
                         $currency_symbol = $currency_symbol;
                     elseif( $payment_log['payment_gateway'] == 'paypal'):
                              $currency_symbol = $payment_log['mc_currency'];
                     else:   
                     endif;   
              
        
        $type = em_get_term_meta($venue->term_id, 'type', true); 
        $data['type']=$type;
        if($order_info['item_price']=="" || empty($order_info['item_price']))
             $data['item_price'] = '0'.$currency_symbol;
        else
            $data['item_price']=$order_info['item_price'].$currency_symbol; 
      
        $data['final_price'] = $booking_service->get_final_price($booking->ID);        
        $data['quantity']= (int) $order_info['quantity']; 
        $data['discount'] = $order_info['discount'];
             
        if(($order_info['seat_sequences'])>0)
        {                   
            $data['seat_sequences']= $seat_sequences= implode (',', $order_info['seat_sequences']);  
            
        }
        else
        {                  
            $data['seat_no'] = (int) $order_info['quantity'];            
        }
    
          
        if(isset($data['seat_sequences'])){
              ob_start();
            $tpl_location= plugin_dir_path( __DIR__ ).'print/booking_detail_seats.html';           
            $my_var = ob_get_clean(); 
        
        }
        else
            {
            ob_start();
            $tpl_location= plugin_dir_path( __DIR__ ).'print/booking_detail_standing.html';           
            $my_var = ob_get_clean(); 
        }
        
           
    
       
       
       
        if(file_exists($tpl_location))
            $html= file_get_contents($tpl_location);
        preg_match_all("/{{+(.*?)}}/",$html,$matches);
      
        if(isset($matches[1]) && count($matches[1])>0)
        {
            //Filling required data in template file
            foreach($matches[1] as $index=>$val)
            {  
                // Check if value exists in data
                if(isset($data[$val]))
                {  
                    $html= str_replace($matches[0][$index], $data[$val], $html);
                }
            } 
        }
        
      
        if($data['discount']>0):
              $html =$html.'<tr><th style=" font-weight: bold; text-transform:capitalize; vertical-align:middle;border-bottom:1px solid #eceeef;"><br><br>Discount:<br></th><td style=" text-transform:capitalize; vertical-align:middle;border-bottom:1px solid #eceeef;"><br><br>'.$data['discount'].$currency_symbol.'<br></td></tr><tr><th style=" font-weight: bold; text-transform:capitalize; vertical-align:middle;border-bottom:1px solid #eceeef;"><br><br>Final Price<br></th><td style=" text-transform:capitalize; vertical-align:middle;border-bottom:1px solid #eceeef;"><br><br>'.$data['final_price'].$currency_symbol.'<br></td></tr></table></body></html>';
         else:
             $html =$html.'</table></body></html>';
        endif;
         
     

        $pdf->writeHTML($html, true, false, true, false, '');
        
           $pdf->Output("Booking'-'$booking->ID.pdf", 'D');
            die;          
         
    }
    
    
        
}
?>
