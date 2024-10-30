<?php
class EventM_Event_Type_Model{
    private $id;
    private $name;
    private $color;
    private $age_group='all';
    private $description;
    private $attributes;
    private $custom_group;
    
    function __construct($id=0) {
        $this->id= (int) $id;
        $this->load_attributes();
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_color() {
        return $this->color;
    }

    public function get_age_group() {
        return $this->age_group;
    }

    public function get_custom_group() {
        return $this->custom_group;
    }
    
    public function get_description() {
        return $this->description;
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function set_color($color) {
        $this->color = $color;
    }

    public function set_age_group($age_group) {
        $this->age_group = $age_group;
    }

    public function set_description($description) {
        $this->description = $description;
    }

    public function set_custom_group($custom_group) {
         $this->custom_group = $custom_group;
    }
        public function get_meta_attributes()
    {
        return $this->attributes['meta'];
    }
    
    public function get_core_attributes()
    {
        return $this->attributes['core'];
    }
    
    private function load_attributes()
    {
        $this->attributes= EventM_Constants::get_type_cons();
    }
    
    public function to_array()
    {  
        $obj= array();
        $vars= get_object_vars($this);
        foreach($vars as $key=>$val)
        { 
           $method= "get_".$key; 
           if(method_exists($this, $method)) 
           {
               $obj[$key]=  $this->$method();
           }
           
        }
        return $obj;
    }
}
