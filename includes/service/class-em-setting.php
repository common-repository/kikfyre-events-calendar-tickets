<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setting Service class
 */
class EventM_Setting_Service {
    
    private $dao;
    
    public function __construct() {
        $this->dao= new EventM_Global_Settings_DAO();
    }
    
    /*
     * Loading settings data on REST call
     */
    public function load_edit_page()
    {
    
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        
        $settings=  $this->load_model_from_db();
        $settings= $this->decrypt_paypal_info($settings);
        $response->options= $settings->to_array();
        $response->options= apply_filters('em_load_gs_ext_options',$response->options);
        $response->options['pages']= $this->load_global_pages();
        $response->links= new stdClass();
        $response->links->cancel= admin_url('admin.php?page=event_magic');
        $response->base_path= EM_BASE_URL.'includes/admin/template/';
        $response->trans= $stringsModel->global_settings();
        $response->currencies= em_array_to_options(EventM_Constants::$currencies);
     //  var_dump(json_encode($response)); die;
        echo json_encode($response);
        wp_die();
    }
    
    // Decrypts Paypal Info
    public function decrypt_paypal_info(EventM_Global_Settings_Model $model)
    {
           
        $paypal_api_username= $model->get_paypal_api_username();
        if(!empty($paypal_api_username))
            $model->set_paypal_api_username(em_decrypt($paypal_api_username));
        $paypal_api_password= $model->get_paypal_api_password();
        if(!empty($paypal_api_password))
            $model->set_paypal_api_password(em_decrypt($paypal_api_password));
        $paypal_api_sig= $model->get_paypal_api_sig();
        if(!empty($paypal_api_sig))
           $model->set_paypal_api_sig(em_decrypt($paypal_api_sig));
       
        return $model;
    }
    
    // Encrypts Paypal Info
    public function encrypt_paypal_info(EventM_Global_Settings_Model $model)
    {
        $paypal_api_username= $model->get_paypal_api_username();
        if(!empty($paypal_api_username))
            $model->set_paypal_api_username(em_encrypt($paypal_api_username));
        $paypal_api_password= $model->get_paypal_api_password();
        if(!empty($paypal_api_password))
            $model->set_paypal_api_password(em_encrypt($paypal_api_password));
        $paypal_api_sig= $model->get_paypal_api_sig();
        if(!empty($paypal_api_sig))
           $model->set_paypal_api_sig(em_encrypt($paypal_api_sig));
            
        return $model;    
    }
    
   public function save($model)
    {   
        if($model instanceof EventM_Array_Model)
            $this->dao->save($this->encrypt_paypal_info($model));   
        else if(is_array($model))
        {  
            $this->dao->save($model);
        }
    }
    
    
    public function load_global_pages(){
        $list= array();
        $pages = get_pages(array(
            'post_status'=>'publish',
             'numberposts'=>-1,
            ) );
         
         if (!empty($pages)) {
           foreach ($pages as $page) {
                $tmp = new stdClass();
                $tmp->id = $page->ID;
                $tmp->name = $page->post_title;
                $list[] = $tmp;
            }
       }
       return $list; 
    }
    
    public function load_model_from_db($type='EventM_Global_Settings_Model')
    {
         return $this->dao->get($type);
    }
    
    public function map_request_to_model($model=null)
    {  
        $settings= new EventM_Global_Settings_Model();
        $data= (array) $model;
        
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key=>$val)
            {
                $method= "set_".$key;
                if(method_exists($settings, $method))
                {
                    $settings->$method($val);
                }
            }
        }
        return $settings;
    }
        
}
