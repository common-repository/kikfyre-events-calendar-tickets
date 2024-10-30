<?php 


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventM_Print {
    
     public static function front_ticket($booking,$temp,$seat_no= "")
    {
         $booking_service= new EventM_Booking_Service();
       /* $header_data = array('logo' => null, 'header_text' => null,'title' => '');
        $header_data = wp_parse_args( $header_data, array('logo' => null, 'header_text' => null,'title' => '') );        
        if(!class_exists('TCPDF'))
            require_once plugin_dir_path(dirname(__FILE__)) . 'lib/tcpdf_min/tcpdf.php';*/
        $tpl_location= plugin_dir_path( __DIR__ ).'print/ticket.html';  
      /*  // create new PDF document
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
        //$pdf->SetFont('courier', '', 10);

        // add a page
        $pdf->AddPage('L', 'A4');*/

        $data= array();
        $event= get_post(em_get_post_meta($booking->ID,'event_id',true));
        $event_id = $event->ID;
        
        $interval= em_datetime_diff(em_get_post_meta($event_id, 'start_date', true),em_get_post_meta($event_id, 'end_date', true));
                                if(em_compare_event_dates($event_id)): 
                                        $data['date_time'] =  em_showDateTime(em_get_post_meta($event_id, 'start_date', true), true);
                                            $hours=''; $minutes='';
                                            if($interval->h)
                                                $hours= '<span class="em_time">'.$interval->h.'</span> hours ';
                                            if($interval->i)
                                                $minutes= '<span class="em_time"> '.$interval->i.'</span> minutes';
                                        $data['duration'] =  '<span> Duration: '.$hours.$minutes.'</span> ';
                                else:
                                     $data['date_time'] =  em_showDateTime(em_get_post_meta($event_id, 'start_date', true),true);
                                     $data['duration'] = 'Duration: '.$interval->days.' day(s) '; 
                                endif;

        $data['booking_id'] = $booking->ID;
        $data['event_title']= $event->post_title;
        $ticket_id = em_get_post_meta($event->ID,'ticket_template');
       
        $organiser_details = em_get_post_meta($event->ID,'org_info');
        
        $data['organiser'] = $organiser_details[0]['organizer_name'];
        $data['organiser_contact'] = $organiser_details[0]['organizer_contact_details'];
        
        $event_type_terms = wp_get_post_terms($event->ID, EM_EVENT_TYPE_TAX);
                
        $ages_group = em_get_term_meta($event_type_terms[0]->term_id, 'age_group', true);
        $special_instruction = em_get_term_meta($event_type_terms[0]->term_id, 'description', true);
        
        
        if(!empty($ages_group)){
            if($ages_group == "parental_guidance"){
                $data['age_group'] =  "All ages but parental guidance";
            }
            else if($ages_group == 'custom_group'){
                 $custom_group = em_get_term_meta($event_type_terms[0]->term_id, 'custom_group', true);
                    if(!empty($custom_group)):
                        $data['age_group'] = $custom_group;
                    else:
                         $data['age_group'] = 'Not Specified. Contact organizer for details.';
                    
                    endif;
            }
            else {
                    $data['age_group'] =  $ages_group;
            }
        }
        else{
            $data['age_group'] = "";
        }
        if(!empty($special_instruction)){
          $data['audience_note'] =   $special_instruction;
        }
        else{
          $data['audience_note'] = "";  
        }
        
        //$audience_notice = em_get_post_meta($event->ID,'audience_notice');
        //$data['audience_note'] = $audience_notice[0];
        
        
        //$ticket_price = em_get_post_meta($event->ID,'ticket_price');
        
        if(em_get_post_meta($ticket_id[0],'font1')){
            $font1 = em_get_post_meta($ticket_id[0],'font1');
            $data['font1'] = $font1[0];}
        else{
            $data['font1'] = "Times";
        
        }

      //  print_r( $data['font1']); die;
        if(em_get_post_meta($ticket_id[0],'font2')){
            $font2 = em_get_post_meta($ticket_id[0],'font2');
        $data['font2'] = $font2[0];
        }
        else{
            $data['font2'] = "Times";
        }
        
        if(em_get_post_meta($ticket_id[0],'font_color1')){
            $font_color1 = em_get_post_meta($ticket_id[0],'font_color1');
            $data['font_color1'] = $font_color1[0];
        }
        else{
            $data['font_color1'] = "865C16";
        }
      //  print_r( $data['font1']); die;
        if(em_get_post_meta($ticket_id[0],'font_color2')){
            $font_color2 = em_get_post_meta($ticket_id[0],'font_color2');
            $data['font_color2'] = $font_color2[0];
        }
        else{
            $data['font_color2'] = "C8A366";
        }
        
//        if((($data['font1']=="Select Font") || ($data['font1']=="Monospace") || ($data['font2']=="Select Font") )):
//             $pdf->SetFont("Helvetica", '', 10);
//             $data['font1'] = "Helvetica";
//             $data['font2'] = "Helvetica";
//        else:
//             $pdf->SetFont($data['font1'], '', 10);
//        endif;
      
              
      
      
      
   
        
        $logo = em_get_post_meta($ticket_id[0],'logo');
        $logo_id = $logo[0];
        $data['ticket_logo1'] = wp_get_attachment_url( $logo_id);
        $background = em_get_post_meta($ticket_id[0],'background_color');
        if(isset($background[0])):
              $data['background_color']= $background[0];
        else:
            $data['background_color']='E2C699';
        endif;
        
        $border_color = em_get_post_meta($ticket_id[0],'border_color');
       if(isset($border_color[0])):
           $data['border_color']=$border_color[0];
       else:
           $data['border_color']='C8A366';
       endif;
        
      
        $thumbnail=get_the_post_thumbnail_url($event->ID,'post-thumbnail');
         $data['thumbnail']=$thumbnail;        
       
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
                $data['venue_address']= '';
            endif;
        endif;
        
        
        $order_info= em_get_post_meta($booking->ID,'order_info',true);
        $global_settings = new EventM_Global_Settings_Model(); 
       $payment_log= maybe_unserialize(em_get_post_meta($booking->ID, 'payment_log', true));
        $currency_symbol= $order_info['currency']; 
                     if( isset($currency_symbol) && !empty($currency_symbol)):
                         $currency_symbol = $currency_symbol;
                     elseif( $payment_log['payment_gateway'] == 'paypal'):
                              $currency_symbol = $payment_log['mc_currency'];
                     else:   
                     endif;   
            
        $ticket_price= $booking_service->get_price_for_print($booking->ID);
          
        if (empty($ticket_price)){
            $data['pay_status'] = "";
            $data['ticket_price'] = "Free";
            $data['currency_symbol'] = "";
            $data['ticket_price_dec'] = "";
        }else{
            $data['pay_status'] = "PAID";
            $data['currency_symbol'] = $currency_symbol;

            list($whole, $decimal) = explode('.', $ticket_price);
            $data['ticket_price'] = $whole;
            $data['ticket_price_dec'] = $decimal==0?'.00':'.'.$decimal;    
        }
        
        $terms = wp_get_post_terms($event->ID, EM_VENUE_TYPE_TAX);
        $venue = $terms[0];
            
        $type = em_get_term_meta($venue->term_id, 'type', true);
    
        if($type=='seats') 
        {   $data['seat_type'] = "Seat No.";
            $data['seat_no']= $seat_no;
        }
        else
        {   
            $data['seat_type'] = "No. of Person(s)";
            $data['seat_no']= (int) $order_info['quantity'];
        }
    
        
        if(file_exists($tpl_location))
        {
            ob_start();
            include($tpl_location);
            $html= ob_get_clean();
        }
            
        
        preg_match_all("/{{+(.*?)}}/",$html,$matches);
       
        if(isset($matches[1]) && count($matches[1])>0)
        {
            //Filling required data in template file
            foreach($matches[1] as $index=>$val)
            {  
                // Check if value exists in data
                if(isset($data[$val]))
                {  
                    $html = str_replace($matches[0][$index], $data[$val], $html);
                }
            } 
        }
        
        
//       if(isset($data['individual_seat'])){
//            $html =$html.'<p>Booking ID  #'.$data['booking_id'].'</p><br><p>Seat No.</p><h2>'.$data['individual_seat'].'</h2></td></tr></table></body></html>';
//       
//        }
//       
//        else
//            {
//             $html =$html.'<p>No. of Persons</p><h2>'.$data['seat_no'].'</h2></td></tr></table></body></html>';
//
//        }

  
        return $html;
       // $pdf->writeHTML($html, true, false, true, false, '');
//        if($temp=='mail')
//        {
//            $loc = get_temp_dir().'Event_Ticket'.'.pdf';         
//            $pdf->Output( $loc , 'F');
//            return true;
//        }
//        else{
          //  $pdf->Output("ticket.pdf", 'D');
            wp_die();          
       // }
    }
    
    
    
    
        
}
?>
