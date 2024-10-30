<?php

/**
 *
 * Service class for Events
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Service class
 */
class EventM_Extensions {
    
      public function get_kf_extension_activate_url($path)
    {
        $plugin = $path;
        if (strpos($path, '/')) {
            $path = str_replace('/', '%2F', $path);
        }
        $activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s'), $path);    
        $activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);
        return $activateUrl;
    }
    
    public function kf_extention_plugin_path($plugin)
    {
        switch($plugin)
        {
            case 'STRIPE':
                $path = 'kikfyre-stripe-payments/kikfyre-stripe.php';
                break;
            
            case 'OFFLINE_PAYMENT':
                $path = 'kikfyre-offline-payments/kikfyre-offline.php';
                break;
            
        }
        return $path;
    }
    
    public function kf_is_extension_activated($plugin)
    {
        switch($plugin)
        {
            case 'STRIPE':
                $is_activate = class_exists("KF_Stripe");
                break;     
            
            case 'OFFLINE_PAYMENT':
                $is_activate = class_exists("KF_Offline");
                break;      
        }
       
        return $is_activate;
    }
    
    
    
     public function kf_get_extension_install_url($plugin)
    {
        switch($plugin)
        {
            case 'STRIPE':
                $url = "http://kikfyre.com/checkout/?download_id=293&edd_action=add_to_cart";
                break;
            
            case 'OFFLINE_PAYMENT':
                $url = "http://kikfyre.com/checkout/?download_id=297&edd_action=add_to_cart";
                break;
            
            default:
                $url = "http://kikfyre.com/";
                break;  
        }
        return $url;
    }
    
     public function kf_extension_install_button($plugin)
    {
        $data = new stdClass;
        $data->kf_extsion_install_url = $this->kf_get_extension_install_url($plugin);
        $data->kf_extsion_activate_url = '';
        $data->is_kf_extension_activate = $this->kf_is_extension_activated($plugin);
        
        $data->kf_extsion_page_url = '';
      
        if($data->is_kf_extension_activate) 
        {
            $data->is_kf_extension_installed = true;
        } 
        else 
        {
            $plugins = get_plugins();
       
            $path = $this->kf_extention_plugin_path($plugin);
            $data->is_kf_extension_installed = array_key_exists($path, $plugins);
           
            if($data->is_kf_extension_installed)
            {
                $data->kf_extsion_activate_url = $this->get_kf_extension_activate_url($path);
            }
            else
            {
                $data->kf_get_extension_install_url = $this->kf_get_extension_install_url($plugin);
            }

            
            
            
//            if(!isset($plugins[$kf_stripe]))
//            {
//               
//                $data->kf_get_extension_install_url = $this->kf_get_extension_install_url($plugin);
//            }
//            else if($data->is_kf_extension_installed)
//            {
//                
//                $data->kf_extsion_activate_url = $this->get_kf_extension_activate_url($path);
//            }
//            else if(isset($plugins[$kf_stripe]) && !$data->is_kf_extension_activate ){
                
            }
          
         

        return $data;
    }
    
    public function kf_get_extension_options_url($plugin)
    {
        switch($plugin)
        {
            case 'STRIPE':
                $redirect_url = admin_url('admin.php?page=em_global_settings');
                $url = add_query_arg( 'show_payment', '1', $redirect_url );
                break;
            
            case 'OFFLINE_PAYMENT':
                  $redirect_url = admin_url('admin.php?page=em_global_settings');
                $url = add_query_arg( 'show_payment', '1', $redirect_url );
                break;
             
        }
        return $url;
    }
    
     public function kf_get_extension_button($plugin) {
             
            $data = $this->kf_extension_install_button($plugin);
        //  echo'pre';  print_r($data);
            $optionurl = $this->kf_get_extension_options_url($plugin);
            
            if($data->is_kf_extension_activate)
        {
        ?>
            <li>
            <a class="install-now button kf-install-now-btn" href="<?php echo $optionurl;?>">OPTIONS</a>
            </li>
        <?php
        }
        elseif($data->is_kf_extension_installed)
        {
            ?>
            <li>
            <a class="install-now button kf-install-now-btn" href="<?php echo $data->kf_extsion_activate_url;?>">Activate Now</a>
            </li>
        <?php
        }
        else
        {   ?>
            <li>
            <a class="install-now button kf-install-now-btn" target="_blank" href="<?php echo $data->kf_get_extension_install_url;?>">Buy Now</a>
            </li>
        <?php
          
            
        }
     }
}
