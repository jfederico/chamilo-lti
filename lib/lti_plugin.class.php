<?php

class LTIPlugin extends Plugin
{
    public $is_course_plugin = true;

    //When creating a new course this settings are added to the course
    public $course_settings = array(
                    array('name' => 'lti_course_title',  'type' => 'text'),
                    array('name' => 'lti_course_description',  'type' => 'text'),
                    array('name' => 'lti_course_endpoint',  'type' => 'text'),
                    array('name' => 'lti_course_key',  'type' => 'text'),
                    array('name' => 'lti_course_secret',  'type' => 'text'),
                    array('name' => 'lti_course_custom',  'type' => 'textarea'),
                    array('name' => 'lti_course_iframe_height',  'type' => 'text'),
                    array('name' => 'lti_course_open_new_window', 'type' => 'checkbox'),
                    array('name' => 'lti_course_debug_launch', 'type' => 'checkbox')
    );

    static function create() {
        static $result = null;
        return $result ? $result : $result = new self();
    }

    protected function __construct() {
        //parent::__construct('1.0', 'Jesus Federico', array('tool_enable' => 'boolean', 'endpoint' =>'text', 'key' => 'text', 'secret' => 'text', 'custom' => 'textarea'));
        parent::__construct('1.0', 'Jesus Federico', array('tool_enable' => 'boolean', 'endpoint' =>'text', 'key' => 'text', 'secret' => 'text', 'custom' => 'text'));
    }

    function install() {
        $table = Database::get_main_table('plugin_lti_tool');
        $sql = "CREATE TABLE IF NOT EXISTS $table (
                id INT unsigned NOT NULL auto_increment PRIMARY KEY,
                c_id INT unsigned NOT NULL DEFAULT 0,
                tool_name VARCHAR(255) NOT NULL DEFAULT '',
                endpoint VARCHAR(255) NOT NULL DEFAULT '',
                key VARCHAR(255) NOT NULL DEFAULT '',
                secret VARCHAR(255) NOT NULL DEFAULT '',
                custom VARCHAR(255) NOT NULL DEFAULT '')";
        Database::query($sql);

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
        error_log(json_encode($values), 0);
    }
}