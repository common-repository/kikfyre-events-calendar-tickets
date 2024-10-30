<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Event Service class
 */
class EventTypeM_Service {
    
    private $dao;
    
    public function __construct() {
        $this->dao= new EventM_Event_Type_DAO();
    }
    
    /*
     * Load Add/Edit page for REST 
     */
    public function load_edit_page()
    {
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        $id = event_m_get_param('term_id');
        $event_type= $this->load_model_from_db($id);
        $response->term= $event_type->to_array();
        $response->term['age_groups']= $this->age_dropdown($event_type->get_age_group());
        $response->trans= $stringsModel->addEventType();
         $response->links= new stdClass();
        $response->links->cancel= admin_url('admin.php?page=em_event_types');
        echo json_encode($response);
        wp_die();
    }
    
    /*
     * Load List page for REST 
     */
    public function load_list_page()
    {
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();

        $response->terms= array();
         
        $types= get_terms(EM_EVENT_TYPE_TAX,
                 array( 'hide_empty' => false,
                        'offset'=> (int) (event_m_get_param('paged')-1) * EM_PAGINATION_LIMIT,
                        'number'=>EM_PAGINATION_LIMIT,'orderby'=>'term_id','order'=>'DESC'));
        $terms= array();
        foreach($types as $tmp){
           $type= $this->load_model_from_db($tmp->term_id);
           $data= $type->to_array();
           $terms[]= $data;
        }
        
        $response->terms= $terms;
        $terms_count= wp_count_terms( EM_EVENT_TYPE_TAX, array('hide_empty' => false) );
        if($terms_count>0)
            $response->total_count= range(1,$terms_count); 
        $response->pagination_limit= EM_PAGINATION_LIMIT;
        
        $response->tax_type= EM_EVENT_TYPE_TAX;
        $response->trans= $stringsModel->listEventTypes();
        $response->links= new stdClass();
        $response->links->add_new= admin_url('admin.php?page=em_event_type_add');
        
        echo json_encode($response);
        wp_die();
    }
    
    /*
     * Saving Event Type (Term) 
     */
    public function save($model)
    {
        // Check if user added any custom age group
        //print_r($model); 
        $custom_age_group= isset($model->custom_group) ? $model->custom_group : '';
        if(!empty($custom_age_group)){
            $model->custom_group= $custom_age_group;
        }
        // print_r($model); die;
        $id = isset($model->id) ? $model->id : 0;
        $type= $this->map_request_to_model($id,$model);
        $type= $this->dao->save($type);
        return $type;
    }
    
    public function get_all_Event_type(){           
             $event_type= $this->dao->getTypes();  
             return $event_type;
    }
    
    public function age_dropdown($age_group)
    {
        $age_groups=  array(
                     "all"=>EventM_UI_Strings::get("LABEL_ALL"),
                     "parental_guidance"=> EventM_UI_Strings::get("LABEL_ALL_PARENTAL_GUIDANCE"),
                     "custom_group"=> EventM_UI_Strings::get("LABEL_CUSTOM_AGE"),
        );
        if(!in_array($age_group,array("all","parental_guidance","custom_group")))
                $age_groups[$age_group]=$age_group; 
        
        return em_array_to_options($age_groups);
    }
    
    public function load_model_from_db($id)
    {
        return $this->dao->get($id);
    }
    
    public function map_request_to_model($id,$model=null)
    {  
        $type= new EventM_Event_Type_Model($id);
        $data= (array) $model;
      
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key=>$val)
            {
                $method= "set_".$key;
                if(method_exists($type, $method))
                {
                    $type->$method($val);
                }
            }
        }
        return $type;
        
    }
    
    public function validate($model, $response) {
        $venue_error= false;
        $response= new stdClass();
        $term= term_exists($model->name,EM_EVENT_TYPE_TAX);
        if(!empty($term) && isset($term['term_id']) && $term['term_id']!=$model->id)
        {
             $response->error_status = true;
             $response->errors[] = "Please use different name";
             $response->redirect = false;
        }
        return $response;
    }
    
}
?>
