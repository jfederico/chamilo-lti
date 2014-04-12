<?php

class lti {

    var $endpoint;
    var $key;
    var $secret;
    var $protocol = 'http://';
    var $debug = false;
    var $plugin_enabled = false;

    /**
     * Constructor (generates a connection to the API and the Chamilo settings)
     */
    function __construct() {

        // initialize video server settings from global settings
        $plugin = LTIPlugin::create();

        $lti_plugin = $plugin->get('tool_enable');
        $lti_endpoint = $plugin->get('endpoint');
        $lti_key = $plugin->get('key');
        $lti_secret = $plugin->get('secret');

        //$course_code = api_get_course_id();

        $this->table = Database::get_main_table('plugin_lti_tool');

        if ($lti_plugin == true) {
            $this->endpoint = $endpoint;
            $this->key = $key;
            $this->secret = $secret;

            // Setting BBB api
            define('CONFIG_LTI_ENDPOINT', $this->endpoint);
            define('CONFIG_LTI_KEY', $this->key);
            define('CONFIG_LTI_SECRET', $this->secret);
            
            $this->plugin_enabled = true;
        }
    }
}
