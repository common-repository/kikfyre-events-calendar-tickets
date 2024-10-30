<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class EventM_Post_Dao
{
    protected $post_type;
    
    public function __construct($post_type) {
        $this->post_type= $post_type;
    }

    public function save($model)
    { 
        
        $title= method_exists($model, 'get_title')?$model->get_title() :$model->get_name();
        $args = array(
            'post_title' => wp_strip_all_tags($title),
            'post_content' => $model->get_description(),
            'post_status' => $model->get_status(),
            'post_name'=> $model->get_slug(),
            'post_type' => $this->post_type);
         
        if($model->get_id() > 0)
        {
            $args['ID']= $model->get_id();
            $post= wp_update_post($args);
        } 
        else    
            $post= wp_insert_post($args);
        
         // In case of any errors
        if ($post instanceof WP_Error) {
            return $post;
        }
        
        $meta= $model->get_meta_attributes();
        $model_array= $model->to_array(false);
        if(is_array($meta))
        {
            foreach($meta as $attr)
            {
                if(isset($model_array[$attr]))
                    em_update_post_meta($post, $attr, $model_array[$attr]); 
            }
        }
        
        return $post;
    }
    
    public function get_meta($post,$meta,$single= true)
    {   
        if(is_object($post) && isset($post->ID))
            return em_get_post_meta($post->ID, $meta, $single);
        else 
            return  em_get_post_meta($post, $meta, $single);  
    }
    
    public function set_meta($post,$meta,$meta_value)
    {   
        if(is_object($post) && isset($post->ID))
            return em_update_post_meta($post->ID, $meta, $meta_value);
        else 
            return em_update_post_meta($post, $meta, $meta_value);  
    }
}
