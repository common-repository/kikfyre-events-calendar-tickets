<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class EventM_Term_Dao
{
    protected $tax_type;
    
    public function __construct($tax_type) {
        $this->tax_type= $tax_type;
    }
    public function save($model)
    {   
      
        $core= $model->get_core_attributes();
        $args= array();
        if(is_array($core))
        {
            foreach($core as $attr)
            {              
                $method= 'get_'.$attr; 
                if(method_exists($model, $method))
                    $args[$attr]= $model->$method();
            }
                
        }

        if($model->get_id() > 0)
           $term= wp_update_term($model->get_id(), $this->tax_type,$args);
        else
           $term= wp_insert_term(wp_strip_all_tags($model->get_name()), $this->tax_type);
      
       
         // In case of any errors
        if ($term instanceof WP_Error) {
            return $term;
        }
        
        $meta= $model->get_meta_attributes();
        if(is_array($meta))
        {
            foreach($meta as $attr)
            {
                $method= 'get_'.$attr;
                if(method_exists($model, $method))
                    em_update_term_meta($term['term_id'], $attr, $model->$method()); 
            }
        }
    
        return $term;
    }
    
    public function get_all($args= array())
    {
        return get_terms($args);
    }
    
    public function get_single($term=0)
    {
        return get_term($term);
    }
    
    public function get_meta($term,$meta,$single= true)
    {   
        if(is_object($term) && isset($term->term_id))
            return  em_get_term_meta($term->term_id, $meta, $single);
        else 
            return  em_get_term_meta($term, $meta, $single);  
    }
    
    public function insert_or_update($term, $type, $id = 0)
    {
        if ($id > 0)
            return wp_update_term($id, $type, $term);
        else
            return wp_insert_term($term, $type);
    }
}
