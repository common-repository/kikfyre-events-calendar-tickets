<?php

class EventM_Event_Ticket_Model extends EventM_Array_Model
{
    private $id;
    private $name;
    private $description;
    private $template='';
    private $background_color;
    private $font_color1;
    private $font_color2;
    private $border_color;
    private $font1='Times';
    private $font2='Times';
    private $logo;
    public  $fonts;
    private $attributes;
    private $slug;
    protected $status="publish";

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

    public function get_description() {
        return $this->description;
    }

    public function get_template() {
        return $this->template;
    }

    public function get_background_color() {
        return $this->background_color;
    }

    public function get_border_color() {
        return $this->border_color;
    }
    
    public function get_font_color1() {
        return $this->font_color1;
    }
    
    public function get_font_color2() {
        return $this->font_color2;
    }

    public function get_font1() {
        return $this->font1;
    }

    public function get_font2() {
        return $this->font2;
    }

    public function get_logo() {
        return $this->logo;
    }

    public function get_fonts() {
        return $this->fonts;
    }

    public function set_id($id) {
        $this->id = (int) $id;
    }

    public function set_name($name) {
        $this->name= $name;
    }

    public function set_description($description) {
        $this->description = $description;
    }

    public function set_template($template) {
        $this->template = $template;
    }

    public function set_background_color($background_color) {
        $this->background_color = $background_color;
    }

    public function set_border_color($border_color) {
        $this->border_color = $border_color;
    }
    
    public function set_font_color1($font_color1) {
        $this->font_color1 = $font_color1;
    }
    
    public function set_font_color2($font_color2) {
        $this->font_color2 = $font_color2;
    }

    public function set_font1($font1) {
        $this->font1 = $font1;
    }

    public function set_font2($font2) {
        $this->font2 = $font2;
    }

    public function set_logo($logo) {
        $this->logo =  $logo;
    }

    public function set_fonts($fonts) {
        $this->fonts = $fonts;
    }
    
    public function load_attributes()
    {
        $this->attributes= EventM_Constants::get_ticket_cons();
    }
    
    public function get_meta_attributes()
    {
        return $this->attributes['meta'];
    }
    
    public function get_core_attributes()
    {
        return $this->attributes['core'];
    }
    
    public function get_slug() {
        return $this->slug;
    }

    public function set_slug($slug) {
        $this->slug = $slug;
    }

    public function to_array($getters= true)
    {  
        $obj= array();
        $vars= get_object_vars($this);
        foreach($vars as $key=>$val)
        { 
           $method= "get_".$key; 
           if($getters && method_exists($this, $method)) 
               $obj[$key]=  $this->$method();
           else
               $obj[$key]= $val;
           
        }
        return $obj;
    }
    public function get_status() {
        return $this->status;
    }

    public function set_status($status) {
        $this->status = $status;
    }


}




