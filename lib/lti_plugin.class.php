<?php

class LTIPlugin extends Plugin
{
    const PLUGIN_LTI_TOOL = 'plugin_lti_tool';

    protected $table;
    public $is_course_plugin = true;
    
    //When creating a new course this settings are added to the course
    //Course tool providers should be set up here
    //public $course_settings = array(
    //                array('name' => 'lti_course_title',  'type' => 'text'),
    //                array('name' => 'lti_course_description',  'type' => 'text'),
    //                array('name' => 'lti_course_endpoint',  'type' => 'text'),
    //                array('name' => 'lti_course_key',  'type' => 'text'),
    //                array('name' => 'lti_course_secret',  'type' => 'text'),
    //                array('name' => 'lti_course_custom',  'type' => 'textarea'),
    //                array('name' => 'lti_course_iframe_height',  'type' => 'text'),
    //                array('name' => 'lti_course_open_new_window', 'type' => 'checkbox'),
    //                array('name' => 'lti_course_debug_launch', 'type' => 'checkbox')
    //);

    protected function __construct() {
        $this->table = Database::get_main_table(self::PLUGIN_LTI_TOOL);

        //parent::__construct('1.0', 'Jesus Federico', array('tool_enable' => 'boolean', 'lti_global_tool_producer' => 'meta-form'));
        //parent::__construct('1.0', 'Jesus Federico', array('lti_global_tool_enable' => 'boolean', 'lti_global_endpoint' => 'text', 'lti_global_key' => 'text', 'lti_global_secret' => 'text', 'lti_global_custom' => 'textarea'));
        //parent::__construct('1.0', 'Jesus Federico', array('tool_enable' => 'boolean', 'lti_global_endpoint' => 'text', 'lti_global_key' => 'text', 'lti_global_secret' => 'text', 'lti_global_custom' => 'text'));
        parent::__construct('1.0', 'Jesus Federico', array('tool_enable' => 'boolean'));
        //Global tool providers should be set up here
    }

    static function create() {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    function install() {
        $sql = "CREATE TABLE IF NOT EXISTS $this->table (".
               " id INT unsigned NOT NULL auto_increment PRIMARY KEY,".
               " c_id INT unsigned NOT NULL DEFAULT 0,".
               " tool_name VARCHAR(255) NOT NULL DEFAULT '',".
               " tool_description TEXT NOT NULL DEFAULT '',".
               " tool_endpoint VARCHAR(255) NOT NULL DEFAULT '',".
               " tool_key VARCHAR(255) NOT NULL DEFAULT '',".
               " tool_secret VARCHAR(255) NOT NULL DEFAULT '',".
               " tool_custom TEXT NOT NULL DEFAULT '');";
        
        //Installing course settings
        $this->install_course_fields_in_all_courses();
    }

    function uninstall() {
        $t_settings = Database::get_main_table(TABLE_MAIN_SETTINGS_CURRENT);
        $t_options = Database::get_main_table(TABLE_MAIN_SETTINGS_OPTIONS);
        $t_tool = Database::get_course_table(TABLE_TOOL_LIST);

        //New settings
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_tool_enable'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_endpoint'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_key'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_secret'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_custom'";
        Database::query($sql);

        //Old settings deleting just in case
        $sql = "DELETE FROM $t_options WHERE variable  = 'lti_plugin'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_plugin'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_plugin_endpoint'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_plugin_key'";
        Database::query($sql);
        $sql = "DELETE FROM $t_settings WHERE variable = 'lti_plugin_secret'";
        Database::query($sql);

        //hack to get rid of Database::query warning (please add c_id...)
        $sql = "DELETE FROM $t_tool WHERE name = 'lti' AND c_id = c_id";
        Database::query($sql);

        $sql = "DROP TABLE IF EXISTS plugin_lti_tool";
        Database::query($sql);

        //Deleting course settings
        $this->uninstall_course_fields_in_all_courses();
    }

    function course_settings_updated($values = array()) {
        if (!is_array($values) or count($values)==0) {
            return false;
        }
        //error_log(json_encode($values), 0);
    }

    function is_enabled(){
        return $this->get('tool_enable') == "true"? true: false;
    }

    function is_teacher() {
        return api_is_course_admin() || api_is_coach() || api_is_platform_admin();
    }

    //////////////////////////////////
    /// UX definitions and methods ///
    //////////////////////////////////
    const SCOPE_MAIN = 'main';
    const SCOPE_TOOL = 'tool';
    const SCOPE_SETTINGS = 'settings';

    const ACTION_ADD = 'add';
    const ACTION_EDIT = 'edit';
    const ACTION_SAVE = 'save';
    const ACTION_SHOW = 'show';
    const ACTION_DELETE = 'delete';
    const ACTION_CANCEL = 'cancel';

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
            //error_log($button_images[$button['scope'].'_'.$button['action']]);
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

    function build_external_tool_form($action = LTIPlugin::ACTION_ADD, $settings = array()){
        if ( $action == LTIPlugin::ACTION_EDIT ) {
            ///Lookup for settings
        }
        //return Display::page_header(get_lang('lti_actionbar_tool_add'));
        $external_tool_form = "\n";
        $external_tool_form .= '<form class="form-horizontal" method="post" action="'.api_get_self().'?scope=tool">'."\n";
        $external_tool_form .= '<legend>'.get_lang('lti_actionbar_tool_add').'</legend>'."\n";

        //public static function input($type, $name, $value, $extra_attributes = array()) {

        $external_tool_form .= Display::form_row(get_lang('lti_course_name').':', Display::input('text', 'name', ''))."\n";
        $external_tool_form .= Display::form_row(get_lang('lti_course_description').':', Display::input('text', 'description', ''))."\n";
        $external_tool_form .= Display::form_row(get_lang('lti_course_endpoint').':', Display::input('text', 'endpoint', ''))."\n";
        $external_tool_form .= Display::form_row(get_lang('lti_course_key').':', Display::input('text', 'key', ''))."\n";
        $external_tool_form .= Display::form_row(get_lang('lti_course_secret').':', Display::input('password', 'secret', '').'&nbsp;&nbsp;'.Display::input('checkbox', 'secret', '').'&nbsp;'.get_lang('lti_course_secret_show'))."\n";
        $external_tool_form .= Display::form_row(get_lang('lti_course_custom').':', Display::input('text', 'custom', ''))."\n";

        //Display::button($name, $value, $extra_attributes = array())
        //$external_tool_form .= Display::button('action', get_lang('lti_actionbar_tool_save'), array( 'type' => 'submit' ))."\n";
        //$external_tool_form .= Display::button('action', get_lang('lti_actionbar_tool_cancel'), array())."\n";
        $external_tool_form .= Display::input('submit', 'action', get_lang('lti_actionbar_tool_save'), array( 'id' => LTIPlugin::ACTION_SAVE ))."\n";
        $external_tool_form .= Display::input('submit', 'action', get_lang('lti_actionbar_tool_cancel'), array( 'id' => LTIPlugin::ACTION_CANCEL ))."\n";
        $external_tool_form .= '</form>';

        return $external_tool_form;
        //return Display::page_header(get_lang('lti_actionbar_tool_add'), null, 'legend');
    }

    function build_external_tool_list() {
        $external_tool_list = "\n";

        $headers = array( get_lang('lti_list_header_type'), get_lang('lti_list_header_name'), get_lang('lti_list_header_description'), get_lang('lti_list_header_actions') );
        $rows = array();

        $tool_list = Database::select('*', $this->table, array('where' => array('c_id = ? ' => api_get_course_int_id())));
        //$sql = "SELECT * FROM $file_tbl WHERE c_id = $course_id AND session_id = $session_id";
        //$result = Database::query($sql);
        foreach ($tool_list as $tool) {
            $rows[] = array(
                    Display::return_icon('lti.png', get_lang('lti_actionbar_tool_launch'), array('class' => 'link', 'align' => 'center'), ICON_SIZE_MEDIUM),
                    $tool['tool_name'],
                    $tool['tool_description'],
                    Display::url(get_lang('lti_actionbar_tool_edit'), api_get_self().'?scope=tool&action=edit&id='.$tool['id'], array('class' => 'icon')).
                    "&nbsp;".
                    Display::url(get_lang('lti_actionbar_tool_delete'), api_get_self().'?scope=tool&action=delete&id='.$tool['id'], array())
            );
        }
        //foreach ($tool_list as $tool) {
        //    $external_tool_list .= '<h4>'.$tool['tool_name'].'</h4>'."\n";
        //}
        error_log($rows);
        $external_tool_list .= Display::table($headers, $rows);
        error_log($external_tool_list);

        return $external_tool_list;
    }

    function build_settings_form() {
        $settings_form = "\n";
        $settings_form .= '<h4>Everything OK, lets config the global settings</h4>'."\n";
        return $settings_form;
    }
    
    function save_external_tool(LTITool $lti_tool){
        try {
            //$table = Database::get_main_table(self::PLUGIN_LTI_TOOL);

            if( $id = $lti_tool->get('id') ) {
                $sql = "SELECT * FROM $table WHERE id = $id;";
                //Database::query($sql);
                
            } else {
                $params['c_id'] = api_get_course_int_id();
                foreach ( $lti_tool->properties as $key => $value ) {
                    if( $key != 'id' && $value != null ) {
                        $params['tool_'.$key] = $lti_tool->get($key);
                        error_log('tool_'.$key.'='.$params['tool_'.$key]);
                    }
                }
                
                // Validates if all the required parameters are set
                if( $params['tool_name'] == null || $params['tool_description'] == null || $params['tool_endpoint'] == null || $params['tool_key'] == null || $params['tool_secret'] == null){
                    error_log('Not all the parameters required are set');
                    return FALSE;
                }
                
                error_log(json_encode($params));
                $id = Database::insert($this->table, $params);
                if ($id) {
                    return TRUE;
                } else {
                    error_log('Record could not be inserted');
                    return FALSE;
                }
            }
        } catch ( Exception $e ) {
            error_log('Exception catched: '.$e->getMessage());
            return FALSE;
        }
    }
}