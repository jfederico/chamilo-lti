<?php
/**
 * This script defines a class to interact with Chamilo API acting as an uintermediator for the LTI tool provider manager.
 * @package chamilo.plugin.lti
 */
/**
 * LTI-Chamilo connector class
 */

class LTI {
    const SCOPE_TOOL = 'tool';
    const SCOPE_SETTINGS = 'settings';

    const ACTION_ADD = 'add';
    const ACTION_EDIT = 'edit';
    const ACTION_SAVE = 'save';
    const ACTION_SHOW = 'show';
    const ACTION_DELETE = 'delete';
    
    var $plugin;
    var $table;

    /**
     * Constructor (generates a connection to the API and the Chamilo settings)
     */
    function __construct() {

        // initialize server settings from global settings
        $this->plugin = LTIPlugin::create();

        $this->table = Database::get_main_table('plugin_lti_tool');

    }

    function get_lang($key){
        return $this->plugin->get_lang($key);
    }

    function is_enabled(){
        return $this->plugin->is_enabled();
    }

    function is_teacher() {
        return api_is_course_admin() || api_is_coach() || api_is_platform_admin();
    }

    function build_actionbar($_scope, $_action){
        // Define action images.
        $button_images[$this::SCOPE_TOOL.'_'.$this::ACTION_ADD] = 'new_lti.png';
        $button_images[$this::SCOPE_SETTINGS.'_'.$this::ACTION_EDIT] = 'settings.png';
        // Define actions.
        $buttons = array(
                array( "scope" => $this::SCOPE_TOOL,
                       "action" => $this::ACTION_ADD,
                       "title" => get_lang('lti_actionbar_'.$this::SCOPE_TOOL.'_'.$this::ACTION_ADD)
                ),
                array( "scope" => $this::SCOPE_SETTINGS,
                       "action" => $this::ACTION_EDIT,
                       "title" => get_lang('lti_actionbar_'.$this::SCOPE_SETTINGS)
                ),
         );

        // Create the action array with the urls
        foreach ($buttons as $button) {
            $url = array();
            $url['url'] = api_get_self()."?scope=".$button['scope']."&action=".$button['action'];
            error_log($button_images[$button['scope'].'_'.$button['action']]);
            $url['content'] = Display::return_icon($button_images[$button['scope'].'_'.$button['action']], api_ucfirst($button['title']),'',ICON_SIZE_MEDIUM);
            if ($button['scope'] == $_scope && $button['action'] == $_action) {
                $url['active'] = true;
            }
            $action_array[] = $url;
        }
        // return html for actionbar
        return Display::actions($action_array);
        //$content .= Display::div($action_links, array('class'=> 'actions'));
    }

}
