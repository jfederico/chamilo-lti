<?php

require_once 'oauth/OAuth.php';

class lti {

    //var $endpoint;
    //var $key;
    //var $secret;
    //var $protocol = 'http://';
    //var $debug = false;
    var $plugin_enabled = false;

    /**
     * Constructor (generates a connection to the API and the Chamilo settings)
     */
    function __construct() {

        // initialize video server settings from global settings
        $plugin = LTIPlugin::create();

        $course_code = api_get_course_id();
        //$lti_course_endpoint = api_get_course_setting('lti_course_endpoint', $course_code);
        //$lti_course_key = api_get_course_setting('lti_course_key', $course_code);
        //$lti_course_secret = api_get_course_setting('lti_course_secret', $course_code);
        
        $lti_plugin = $plugin->get('tool_enable');
        //$lti_endpoint = $plugin->get('endpoint');
        //$lti_key = $plugin->get('key');
        //$lti_secret = $plugin->get('secret');


        $this->table = Database::get_main_table('plugin_lti_tool');

        if ($lti_plugin == 'true') {
            //$this->endpoint = isset($lti_course_endpoint)? $lti_course_endpoint: $lti_endpoint;
            //$this->key = isset($lti_course_key)? $lti_course_key: $lti_key;
            //$this->secret = isset($lti_course_secret)? $lti_course_secret: $lti_secret;

            // Setting LTI api
            //define('CONFIG_LTI_ENDPOINT', $this->endpoint);
            //define('CONFIG_LTI_KEY', $this->key);
            //define('CONFIG_LTI_SECRET', $this->secret);
            
            $this->plugin_enabled = true;
        }
    }
}
