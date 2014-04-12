<?php

$course_plugin = 'lti'; //needed in order to load the plugin lang variables
require_once dirname(__FILE__).'/config.php';
$lti = new lti();

$tool_name = 'External Tool'; //get_lang('lti_tool_name');
$tpl = new Template($tool_name);

if ($lti->plugin_enabled) {
    //if ($lti->is_server_running()) {

        //if (isset($_GET['launch']) && $_GET['launch'] == 1) {
            //echo '<p>Hello GET launch</p>';

        //} else {
            //echo '<p>Hello POST (or other) launch</p>';

        //}
    //} else {
        $message = Display::return_message(get_lang('lti_warning'), 'warning');
        //}
} else {
    $message = Display::return_message(get_lang('ServerIsNotConfigured'), 'warning');
}
$content = '<h2>'.get_lang('lti_tool_name').'</h2>
        <form action="iframe.php" target="my-iframe" method="post">
            <label for="text">Some text:</label>
            <input type="text" name="text" id="text">
            <input type="submit" value="post">
        </form>
		
<iframe name="my-iframe" src="iframe.php"></iframe>
            <iframe src="http://www.w3schools.com" width="100%" height="500">Test</iframe>';


$tpl->assign('message', $message);
$tpl->assign('content', $content);
$tpl->display_one_col_template();


?>