<?php

class LTITool
{
    var $properties;
    
    /**
     * Constructor
     */
    function __construct($name = null, $description = null, $endpoint = null, $key = null, $secret = null, $custom = null) {
        $this->properties = array(
                'name' => $name,
                'description' => $description,
                'endpoint' => $endpoint,
                'key' => $key,
                'secret' => $secret,
                'custom' => $custom,
        );
    }
    
    function set($key, $value){
        $properties[$key] = $value;
    }

    function get($key){
        return $properties[$key];
    }
}