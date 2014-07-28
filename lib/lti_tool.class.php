<?php

class LTITool
{
    public $properties;
    
    /**
     * Constructor
     */
    function __construct($id = null, $name = null, $description = null, $endpoint = null, $key = null, $secret = null, $custom = null) {
        $this->properties = array(
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'endpoint' => $endpoint,
                'key' => $key,
                'secret' => $secret,
                'custom' => $custom,
        );
    }
    
    function set($key, $value){
        $this->properties[$key] = $value;
    }

    function get($key){
        if( isset($this->properties[$key]) )
            return $this->properties[$key];
        else
            return null;
    }
}