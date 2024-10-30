<?php

class EventM_Paypal_Service extends EventM_Payment_Service
{
    function __construct() {
        parent::__construct("paypal");
    }
    
    public function verify_ipn() {
    
        $raw_post_data = file_get_contents('php://input');
        
        $raw_post_array = explode('&', $raw_post_data);
              
        $data = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $data[$keyval[0]] = urldecode($keyval[1]);
        }

        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }

        foreach ($data as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
 
        // Step 2: POST IPN data back to PayPal to validate

        $gs_service = new EventM_Setting_Service();
        $settings = $gs_service->load_model_from_db();
        if((int) $settings->payment_test_mode==1): 
            $ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
        else:
           $ch= curl_init('https://www.paypal.com/cgi-bin/webscr');
        endif;

       // $ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));


         
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        // inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0) {
            return true;
        } else if (strcmp($res, "INVALID") == 0) {
            
        }
        return false;
    }

    public function update_booking_info() {
        $ids =  event_m_get_param('custom');
        $order_ids= explode(',', $ids);
        foreach($order_ids as $order_id)
        {
            $order = get_post($order_id);
            if (empty($order))
                continue;

            $booking_service = new EventM_Booking_Service();
            $data= $_POST; 
            $data['payment_gateway']= 'paypal';
            if(strtolower($data['payment_status'])=="refunded")
            {
                $pp_log= (array) em_get_post_meta($order_id, 'payment_log', true);

                if(isset($pp_log['refund_log']))
                $pp_log['refund_log'][]= $data;
                else
                    $pp_log['refund_log']= array($data);
                
                em_update_post_meta($order_id, 'payment_log', $pp_log);
                continue;
            }
            else
            $booking_service->update_booking($order_id,$data); 
        }
       
    }
    
    /*
     * Refund transaction
     */
    public function refund($order_id,$info= array()) { 
        $order= get_post($order_id);
        $order_info= em_get_post_meta($order_id,'order_info',true);
        $pp_log= em_get_post_meta($order_id, 'payment_log', true);
        $booking_service = new EventM_Booking_Service();
        
        

        if($order->post_status=="em-refunded")
            return false;
        
        if(strtolower($pp_log['payment_status'])=="refunded")
            return false;
        
        if(empty($order) || empty($pp_log))
            return false;
        
        $methodName_=  'RefundTransaction';
       
        // Set request-specific fields.
        $transactionID = urlencode($pp_log['txn_id']);
        $refundType = urlencode('Full');  // or 'Partial'
        $amount= $booking_service->get_final_price($order_id);
        if($pp_log['mc_gross']>$amount)
        {
            $refundType = urlencode('Partial');
            $memo= "Partial Refund";
        }

        $gs_service = new EventM_Setting_Service();
        $settings = $gs_service->load_model_from_db();

        $currencyID = urlencode($settings->currency);   // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

        // Add request-specific fields to the request string.
        $nvpStr = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID";

        if(isset($memo)) {
                $nvpStr .= "&NOTE=$memo";
        }

        if(strcasecmp($refundType, 'Partial') == 0) {
                if(!isset($amount)) {
                        exit('Partial Refund Amount is not specified.');
                } else {
                        $nvpStr = $nvpStr."&AMT=$amount";
                }

                if(!isset($memo)) {
                        exit('Partial Refund Memo is not specified.');
                }
        }
        
        /**
         * Send HTTP POST Request
         *
         * @param     string     The API method name
         * @param     string     The POST Message fields in &name=value pair format
         * @return     array     Parsed HTTP Response body
         */
        // Set up your API credentials, PayPal end point, and API version.
        $settings= $gs_service->decrypt_paypal_info($settings);
        
        $API_UserName = urlencode($settings->paypal_api_username);
        $API_Password = urlencode($settings->paypal_api_password);
        $API_Signature = urlencode($settings->paypal_api_sig);
        
        if(empty($API_UserName) || empty($API_Password) || empty($API_Signature))
            return false;

        $sandbox = $settings->payment_test_mode;
        if ($sandbox == 1)
            $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
        else
            $API_Endpoint = "https://api-3t.paypal.com/nvp";

        $version = urlencode('119');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr";
        
        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);
        
        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }
        
        return $httpParsedResponseAr;
      
    }

    public function cancel($order_id) {
        
    }

    public function charge($info= array()) {
        // This is empty as currently we are using on express checkout.
        return null;
    }

}
