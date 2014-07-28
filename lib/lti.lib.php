<?php
/**
 * This script defines a class to interact with Chamilo API acting as an intermediator for the LTI tool provider manager.
 * @package chamilo.plugin.lti
 */
/**
 * LTI-Chamilo connector class
 */

class LTI {

    /**
     * Constructor (generates a connection to the API and the Chamilo settings)
     */
    function __construct() {

        // initialize server settings from global settings
        $this->plugin = LTIPlugin::create();

        $this->table = Database::get_main_table(LTIPlugin::PLUGIN_TABLE_NAME);

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

    /*
    function build_add_external_tool_form($settings = array()){
        //return Display::page_header(get_lang('lti_actionbar_tool_add'));
        $add_external_tool_form = "\n";
        $add_external_tool_form .= '<form class="form-horizontal" method="post" action="'.api_get_self().'?scope=tool">'."\n";
        $add_external_tool_form .= '<legend>'.get_lang('lti_actionbar_tool_add').'</legend>'."\n";

        //public static function input($type, $name, $value, $extra_attributes = array()) {
        
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_title').':', Display::input('text', 'title', ''))."\n";
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_description').':', Display::input('text', 'description', ''))."\n";
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_endpoint').':', Display::input('text', 'endpoint', ''))."\n";
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_key').':', Display::input('text', 'key', ''))."\n";
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_secret').':', Display::input('password', 'secret', '').'&nbsp;&nbsp;'.Display::input('checkbox', 'secret', '').'&nbsp;'.get_lang('lti_course_secret_show'))."\n";
        $add_external_tool_form .= Display::form_row(get_lang('lti_course_custom').':', Display::input('text', 'custom', ''))."\n";

        //Display::button($name, $value, $extra_attributes = array())
        $add_external_tool_form .= Display::button('action', get_lang('lti_actionbar_tool_save'), array())."\n";
        $add_external_tool_form .= Display::button('action', get_lang('lti_actionbar_tool_cancel'), array())."\n";
        $add_external_tool_form .= '</form>';
        
        return $add_external_tool_form;
        //return Display::page_header(get_lang('lti_actionbar_tool_add'), null, 'legend');
    }
    
    function display_tool_options($uploadvisibledisabled, $origin) {
        global $gradebook;
        $is_allowed_to_edit = api_is_allowed_to_edit(null, true);
    
        if (!$is_allowed_to_edit) {
            return;
        }
        echo '<form class="form-horizontal" method="post" action="'.api_get_self().'?origin='.$origin.'&gradebook='.$gradebook.'&action=settings">';
        echo '<legend>'.get_lang('EditToolOptions').'</legend>';
        display_default_visibility_form($uploadvisibledisabled);
        display_studentsdelete_form();
        echo '<div class="row">
				<div class="formw">
					<button type="submit" class="save" name="changeProperties" value="'.get_lang('Ok').'">'.get_lang('Ok').'</button>
				</div>
			</div>';
        echo '</form>';
    }
    */
}
