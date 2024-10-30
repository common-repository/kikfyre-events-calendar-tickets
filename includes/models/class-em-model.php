<?php

abstract class EventM_Model{
    
    protected $data;
    protected $stringsModel;
    
    public function __construct($data_elements= array()) {
       $this->data= new stdClass();
       
       foreach($data_elements as $element){
           $this->data->$element= new stdClass();
       }
       
       $this->stringsModel= new EventM_Strings_Model();
    }
    
    protected abstract function loadTranslationStrings();
    protected abstract function loadData();
    
    public function get_data(){
        return $this->data;
    }
}

