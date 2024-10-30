<?php
/**
 *
 * Service class for Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Event Service class
 */
class EventM_Performer_Service {
    
    private $dao;
    
    public function __construct() {
        $this->dao= new EventM_Performer_DAO();
    }
    
    /*
     * Load Add/Edit page for REST 
    */
    public function load_edit_page()
    {
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        $id = event_m_get_param('post_id');
        $performer= $this->load_model_from_db($id);
        if(empty($performer))
        {
            $error= new WP_Error('NON_EXIST_PERFORMER', "Performer does not exists");
            echo wp_json_encode($error);
            wp_die();
        }
        $response->post= $performer->to_array();
        $response->post['types']=  $this->get_types();
        $response->post['feature_image']= $this->get_image($id);
        $response->trans= $stringsModel->addPerformer();
        $response->links->cancel= admin_url('/admin.php?page=em_performers');
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

        $response->posts= array();
         
        // Get all the performers (posts)
        $args = array(
            'posts_per_page' => EM_PAGINATION_LIMIT,
            'offset' => ((int) event_m_get_param('paged')-1) * EM_PAGINATION_LIMIT,
            'post_type' => EM_PERFORMER_POST_TYPE,
            'post_status' => 'publish');
        $performers = get_posts($args);
       
        foreach($performers as $tmp){
           $performer= $this->load_model_from_db($tmp->ID);
           $data= $performer->to_array();
           $data['cover_image_url']= $this->get_image($tmp->ID,'large');
           $response->posts[]= $data;
        }
        
        $response->links->add_new= admin_url('/admin.php?page=em_performer_add');
        $post_count= EventM_Utility::get_total_posts(EM_PERFORMER_POST_TYPE);
        $response->total_posts= range(1,$post_count);
        $response->trans= $stringsModel->listPerformers();
        $response->pagination_limit= EM_PAGINATION_LIMIT;
        
        echo json_encode($response);
        wp_die();
    }
    
    public function save($model)
    {
        $id = isset($model->id) ? $model->id : 0;
        $performer= $this->map_request_to_model($id,$model);
        $performer= $this->dao->save($performer);
        
         // In case of any errors
        if ($performer instanceof WP_Error) {
            return $performer;
        }
        $attach_id= (int) $model->feature_image_id;
        $this->set_image($performer,$attach_id);

       return $performer;
        
    }

    public function get_upcoming_events($performer_id){
        $event_dao= new EventM_Event_DAO();
        $events= $event_dao->get_upcoming_events();
        $settings_service= new EventM_Setting_Service();
        $gs= $settings_service->load_model_from_db();
        $performer_dao= new EventM_Performer_DAO();
        $performer_events= $performer_dao->get_upcoming_events($performer_id, $events);
    
        return $performer_events;
    }
    
    public function get_upcoming_recurring_events($performer_id,$exclude=array()){
        $event_dao= new EventM_Event_DAO();
        $data= $event_dao->get_upcoming_recurring_events();
      
        if(count($exclude)>0):
            foreach($data->ids as $key=>$id):
                if(in_array($id, $exclude)):
                    unset($data->ids[$key]);
                endif;
            endforeach;
        endif;
        
        $performer_dao= new EventM_Performer_DAO();
        $performer_events= $performer_dao->get_upcoming_events($performer_id, $data->ids);
        return $performer_events;
    }
    
    public function load_model_from_db($id)
    {
        return $this->dao->get($id);
    }
    
    public function map_request_to_model($id,$model=null)
    {  
        $performer= new EventM_Performer_Model($id);
        $data= (array) $model;
        
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key=>$val)
            {
                $method= "set_".$key;
                if(method_exists($performer, $method))
                {
                    $performer->$method($val);
                }
            }
        }
    
        return $performer;
        
    }
    
    public function get_types()
    {
        $types= array('person'=>EventM_UI_Strings::get('LABEL_PERSON'),
                      'group'=>EventM_UI_Strings::get('LABEL_GROUP'));
        return em_array_to_options($types);
    }
    
    public function get_image($id,$size='thumbnail')
    {
       $img= wp_get_attachment_image_src(get_post_thumbnail_id($id),$size);
       if(!empty($img))
         return $img[0];
    }
    
    public function set_image($id,$attach_id)
    {  
        if(!empty($attach_id)){
            $this->dao->set_thumbnail($id, $attach_id);
        }
    }
}
