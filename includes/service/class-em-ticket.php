<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ticket Service class
 */
class EventM_Ticket_Service {
    
    private $dao;
    
    public function __construct() {
        $this->dao= new EventM_Event_Ticket_DAO();
    }
    
     /*
     * Load Add/Edit page for REST 
     */
    public function load_edit_page()
    {
        $response= new stdClass();
        $stringsModel= new EventM_Strings_Model();
        $id = event_m_get_param('post_id'); 
        $ticket= $this->load_model_from_db($id);
        if(empty($ticket))
        {
            $error= new WP_Error('NON_EXIST_TT', "Ticket Template does not exist.");
            echo wp_json_encode($error);
            wp_die();
        }
        $response->post= $ticket->to_array();
        $response->post['fonts']= $this->get_fonts_dropdown();
        $response->post['logo_image']= $this->get_logo_image($ticket->get_logo());
        $response->trans= $stringsModel->addTicketTemplate();
        $response->links->cancel= admin_url('/admin.php?page=em_ticket_templates');
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
         
        $args = array(
            'posts_per_page' => EM_PAGINATION_LIMIT,
            'offset' => ((int) event_m_get_param('paged')-1) * EM_PAGINATION_LIMIT,
            'post_type' => EM_TICKET_POST_TYPE,
            'post_status' => 'publish');
        $templates = get_posts($args);
        
        foreach($templates as $tmp){
           $template= $this->load_model_from_db($tmp->ID);
           $data= $template->to_array();
           $response->posts[]= $data;
        }
        
        $response->links->add_new= admin_url('/admin.php?page=em_ticket_template_add');
        $post_count= EventM_Utility::get_total_posts(EM_TICKET_POST_TYPE);
        $response->total_posts= range(1,$post_count);
        $response->trans= $stringsModel->listTicketTemplates();
        $response->pagination_limit= EM_PAGINATION_LIMIT;
        
        echo json_encode($response);
        wp_die();
    }
    
    public function save()
    {
        /*
         * Getting all the data from POST request
         */
        $attributes= EventM_Constants::get_ticket_cons();
        $model = datafromRequest(array_merge($attributes['core'],$attributes['meta']));
       
        // Response object 
        $response = new stdClass();
        $response->error_status = false;
        
        $id = event_m_get_param('id');
        $template= $this->map_request_to_model($id,$model);
        $template= $this->dao->save($template);
        
        // In case of any errors
        if ($template instanceof WP_Error) {
            $error->errors= wp_send_json_error($template);
            wp_die();
        }
        
        $response->redirect= admin_url('/admin.php?page=em_ticket_templates');
        echo json_encode($response);
        wp_die();
    }
    
    public function load_model_from_db($id)
    {
        return $this->dao->get($id);
    }
    
    public function map_request_to_model($id,$model=null)
    {  
        $ticket= new EventM_Event_Ticket_Model($id);
        $data= (array) $model;
        
        if(!empty($data) && is_array($data))
        {
            foreach($data as $key=>$val)
            {
                $method= "set_".$key;
                if(method_exists($ticket, $method))
                {
                    $ticket->$method($val);
                }
            }
        }
        return $ticket;
        
    }
    
    public function get_fonts_dropdown()
    {
        $fonts= EventM_Constants::get_fonts_cons();
        return $fonts;
    }
   
    public function get_logo_image($attach_id,$size='thumbnail')
    {
        $img= wp_get_attachment_image_src($attach_id,$size);
        if($img)
           return $img[0];
    }
}
