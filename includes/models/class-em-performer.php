<?php

class EventM_Performer_Model extends EventM_Array_Model
{
    
    protected $id;
    protected $name;
    protected $slug;
    protected $description;
    protected $type;
    protected $role;
    protected $display_front='true';
    protected $attributes;
    protected $status="publish";
    protected $feature_image_id;

    function __construct($id=0) {
        $this->id= (int) $id;
        $this->load_attributes();
    }
    
    public function load_attributes()
    {
        $this->attributes= EventM_Constants::get_performer_cons();
    }
    
    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_slug() {
        return $this->slug;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_role() {
        return $this->role;
    }

    public function get_display_front() {
        return $this->display_front;
    }

    public function get_attributes() {
        return $this->attributes;
    }
    
    public function set_id($id) {
        $this->id = absint($id);
    }

    public function set_name($name) {
        $this->name = $name;
    }

    public function set_slug($slug) {
        $this->slug = $slug;
    }

    public function set_description($description) {
        $this->description = $description;
    }

    public function set_type($type) {
        $this->type = $type;
    }

    public function set_role($role) {
        $this->role = $role;
    }

    public function set_display_front($display_front) {
        $this->display_front =  $display_front;
    }
    
    public function get_core_attributes()
    {
        return $this->attributes['core'];
    }
    
    
    public function get_meta_attributes()
    {
        return $this->attributes['meta'];
    }
    
    public function __set($name, $value) {
        $method= "set_".$name;
        if(method_exists($this, $method))
        {
            $this->$method($value);
        }
    }
    
    public function __get($name) {
        $method= "get_".$name;
        if(method_exists($this, $method))
        {
            $this->$method();
        }
    }
    
    public function get_status() {
        return $this->status;
    }

    public function set_status($status) {
        $this->status = $status;
    }

    public function get_feature_image_id() {
        return $this->feature_image_id;
    }

    public function set_feature_image_id($feature_image_id) {
        $this->feature_image_id = $feature_image_id;
    }


}




