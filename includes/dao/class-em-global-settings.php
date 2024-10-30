<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @summary Dao class for Global Settings. 
 * 
 */
class EventM_Global_Settings_DAO 
{

    public function get($type='EventM_Global_Settings_Model')
    {
        $options= get_option(EM_GLOBAL_SETTINGS);
        $settings= new $type();
       
        if(is_array($options))
         {   
             foreach($options as $key=>$value)
             {
                 $method= "set_".$key;
                 if(method_exists($settings, $method))
                 {   
                     $settings->$method($value);
                 }
             }
         }
         
         return $settings;
    }
    
    public function save($model)
    {   
        $options_from_db= get_option(EM_GLOBAL_SETTINGS);
        if($model instanceof EventM_Array_Model)
            $options= $model->to_array();
        else if(is_array($model))
            $options= $model;
        $options= array_merge($options_from_db, $options);
        update_option(EM_GLOBAL_SETTINGS, $options);
    }
}
