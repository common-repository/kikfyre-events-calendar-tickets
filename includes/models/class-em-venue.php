<?php

/**
 * 
 * @summary Class to load all the data for Model. 
 * 
 * @since 1.0
 * 
 */

class EventM_Venue_Model{
    private $id;
    private $name;
    private $slug;
    private $description;
    private $seats=array();
    private $facebook_page;
    private $address;
    private $type='';
    private $seating_capacity;
    private $seating_organizer;
    private $established;
    private $gallery_images= array();
    private $date_format= "mm/dd/yy";
    private $attributes;

    
    function __construct($id=0) {
        $this->id= (int) $id;
        $this->load_attributes();
    }

   public function get_id() {
       return $this->id;
   }


   public function get_slug() {
       return $this->slug;
   }

   public function get_seating_organizer() {
       return $this->seating_organizer;
   }

   public function get_description() {
       return $this->description;
   }

   public function get_seats() {
       return $this->seats;
   }

   public function get_facebook_page() {
       return $this->facebook_page;
   }

   public function get_address() {
       return $this->address;
   }

   public function get_type() {
       return $this->type;
   }

   public function get_seating_capacity() {
       return $this->seating_capacity;
   }

   public function get_established() {
       if(empty($this->established))
           return '';
       return em_showDateTime($this->established, false,'m/d/Y');
   }

   public function get_gallery_images() {
       return $this->gallery_images;
   }

   public function get_date_format() {
       return $this->date_format;
   }

   public function set_id($id) {
       $this->id = $id;
   }

   public function get_name() {
       return $this->name;
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

   public function set_seats($seats) {
        if($this->type=="standings")
            return $this->seats= array();
      
       $this->seats = $seats;
   }

   public function set_facebook_page($facebook_page) {
       $this->facebook_page = $facebook_page;
   }

   public function set_address($address) {
       $this->address = $address;
   }

   public function set_type($type) {
       $this->type = $type;
   }

   public function set_seating_capacity($seating_capacity) {
       if($this->type=="standings")
            $this->seating_capacity = 0;

       $this->seating_capacity = (int) $seating_capacity;
   }

   public function set_seating_organizer($seating_organizer) {
       $this->seating_organizer = $seating_organizer;
   }

   public function set_established($established) {
       $this->established = em_time($established);
   }

   public function set_gallery_images($gallery_images) {
       $this->gallery_images = $gallery_images;
   }

   public function set_date_format($date_format) {
       $this->date_format = $date_format;
   }

    private function load_attributes()
    {
        $this->attributes= EventM_Constants::get_venue_cons();
    }
    
    public function get_meta_attributes()
    {
        return $this->attributes['meta'];
    }
    
    public function get_core_attributes()
    {
        return $this->attributes['core'];
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
    
    public function __get($key) {
        $method= "get_".$key; 
        if(method_exists($this, $method)) 
        {
           return  $this->$method();
        }
        return false;
    }
    
    public function __set($name,$value)
    {
        $method= "set_".$name;
        if(method_exists($this, $method)) 
        {
           return  $this->$method($value);
        }
    }
}




