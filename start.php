<?php
require_once dirname(__FILE__).'/config.php';

$course_plugin = 'lti'; //needed in order to load the plugin lang variables

$plugin = LTIPlugin::create();
$tool_name = $plugin->get_lang('lti_tool_name');

$tpl = new Template($tool_name);

if ($plugin->is_enabled()) {
    //$message = Display::return_message('Everything OK', 'success');

    /// Initialise content
    $content = '';
    
    /// Start building the content
    //if ( isset($tool_name) )
    //    $content .= '<h2>'.$tool_name.'</h2>'."\n";
    
    //display_action_links($work_id, $curdirpath, $show_tool_options, $display_upload_link, $action);
    
/*    
    <div class="actions">
      <a href="/chamilo/main/work/work.php?cidReq=ENGLISH101&id_session=0&gidReq=0&amp;action=create_dir&origin=&gradebook="><img src="http://web-dev.123it.ca/chamilo/main/img/icons/32/new_work.png" alt="Create assignment" title="Create assignment"  /></a><a href="/chamilo/main/work/work.php?cidReq=ENGLISH101&id_session=0&gidReq=0&amp;action=settings&amp;origin=&amp;gradebook="><img src="http://web-dev.123it.ca/chamilo/main/img/icons/32/settings.png" alt="Assignments settings" title="Assignments settings"  /></a>
    </div>
    <table class="data_table" id="work53bb66577f0fe">
      <tr class="row_odd">
        <th style="width:40px">Type</th>
        <th><a href="/chamilo/main/work/work.php?cidReq=ENGLISH101&id_session=0&gidReq=0&amp;work_direction=DESC&amp;work_page_nr=1&amp;work_per_page=20&amp;work_column=1&amp;id=&amp;origin=">Title</a> &#8595;</th>
        <th style="width:200px"><a href="/chamilo/main/work/work.php?cidReq=ENGLISH101&id_session=0&gidReq=0&amp;work_direction=ASC&amp;work_page_nr=1&amp;work_per_page=20&amp;work_column=2&amp;id=&amp;origin=">Date</a></th>
        <th class="td_actions">Detail</th>
      </tr>
    </table>
*/

    // The action images.
    $action_images['new'] = 'new_lti.png';
    $action_images['settings'] = 'settings.png';
    
    $actions = array();
    $actions[] = array('action' => 'New');
    $actions[] = array('action' => 'Settings');
    
    foreach ($actions as $action) {
        $url = array();
        $url['url'] = api_get_self()."?action=".$action['action'];
        $url['content'] = Display::return_icon($action_images[strtolower($action['action'])], api_ucfirst(get_lang($action['action'])),'',ICON_SIZE_MEDIUM);
        if (strtolower($action['action']) == strtolower($_GET['action'])) {
            $url['active'] = true;
        }
        $action_array[] = $url;
    }
    
    $content .= Display::actions($action_array);
    
    //$content .= Display::div($action_links, array('class'=> 'actions'));
    
} else {
    $message = Display::return_message($plugin->get_lang('lti_warning_external_tool_not_enabled'), 'warning');
}

if ( isset($message) ) 
    $tpl->assign('message', $message);

//<section id="main_content">
$tpl->assign('content', $content);
$tpl->display_one_col_template();

return;



$_tool_name = &$lti_tool_name;
//$_tool_name = get_lang('lti_tool_name');

/// Initialise content
$content = '';

if ($lti->plugin_enabled) {
} else {
    $message = Display::return_message(get_lang('lti_warning_external_tool_not_configured'), 'warning');
}

if( isset($_tool_name) )
    $content .= '<h2>'.$_tool_name.'</h2>';
$content .= '<h2>'.get_lang('lti_tool_name').'</h2>';
$content .= '<h2>'.get_lang('lti_tool_name').'</h2>';
$content .= '<h3>'.get_lang('lti_warning_external_tool_not_configured').'</h3>';

$t = "Hello";
error_log($t);


if (isset($message) )
    $tpl->assign('message', $message);

$tpl->assign('content', $content);
$tpl->display_one_col_template();

return;

$course_code = api_get_course_id();

$iframe_heigth = api_get_course_setting('lti_course_iframe_height', $course_code); 
$iframe_heigth = (int)$iframe_heigth > 0 ? $iframe_heigth : "500";
$open_new_window = api_get_course_setting('lti_course_open_new_window', $course_code) == 1 ? true : false;


$tool_info = api_get_tool_information_by_name($course_plugin);
// resource_id is going to be replaced by the right one once this supports multiple LTI links
$resource_id = $tool_info['c_id'].'-'.$tool_info['id'];

$current_user_id = api_get_user_id();
$user_info = api_get_user_info($current_user_id);
$course_info = api_get_course_info($course_code);
$user_roles = api_detect_user_roles($current_user_id, $course_code);
// Roles need to be converted to LIS roles befor the trim
$roles_to_string = api_get_roles_to_string($user_roles);
$roles_to_string = isset($roles_to_string)? rtrim($roles_to_string, ", "): '';

$portal_name = api_get_setting('siteName');

$tool_consumer_instance_guid = parse_url($_configuration['root_web'], PHP_URL_HOST);
$tool_consumer_instance_description = null;



$lti_course_custom = api_get_course_setting('lti_course_custom', $course_code);

$custom_params = split(";", $lti_course_custom);

$y = '';
foreach( $custom_params as $x ){
$y .= empty($y)? 'custom_'.$x: ', custom_'.$x;
}
$content .= '<!-- HOLA ['.$y.'] HOLA -->
';


//Code imported from IMS
require_once dirname(__FILE__).'/lib/lti_util.php';

$lmsdata = array(
		"resource_link_id" => $resource_id,
		"resource_link_title" => api_get_course_setting('lti_course_title', $course_code),
		"resource_link_description" => api_get_course_setting('lti_course_description', $course_code),
		"user_id" => $current_user_id,
		"roles" => $roles_to_string,  // or Learner
        "lis_person_name_full" =>  $user_info['complete_name'], //api_get_person_name($user_info['firstname'], $user_info['lastname'], null, null, null),
		"lis_person_name_family" => $user_info['lastname'],
		"lis_person_name_given" => $user_info['firstname'],
		"lis_person_contact_email_primary" => $user_info['mail'],
		//"lis_person_sourcedid" => "school.edu:user",
		"context_id" => $course_info['real_id'],
		"context_title" => $course_info['title'],
		"context_label" => $course_info['id'],
        "tool_consumer_info_product_family_code" => strtolower(api_get_software_name()),
        "tool_consumer_info_version" => api_get_version(), 
		"tool_consumer_instance_guid" => $tool_consumer_instance_guid,
        "tool_consumer_instance_name" => $portal_name,
		"tool_consumer_instance_description" => $tool_consumer_instance_description,
        "tool_consumer_instance_url" => $_configuration['root_web']
);

foreach( $custom_params as $custom_param ){
    $custom = split('=', $custom_param);
    $lmsdata['custom_'.$custom[0]] = $custom[1];
}


foreach ($lmsdata as $k => $val ) {
	if ( $_POST[$k] && strlen($_POST[$k]) > 0 ) {
		$lmsdata[$k] = $_POST[$k];
	}
}

$parms = $lmsdata;
// Cleanup parms before we sign
foreach( $parms as $k => $val ) {
	if (strlen(trim($parms[$k]) ) < 1 ) {
		unset($parms[$k]);
	}
}

// Add oauth_callback to be compliant with the 1.0A spec
$parms["oauth_callback"] = "about:blank";
//if ( $outcomes ) {
//	$parms["lis_outcome_service_url"] = $outcomes;
//	$parms["lis_result_sourcedid"] = "feb-123-456-2929::28883";
//}

//$parms['launch_presentation_css_url'] = $cssurl;


$parms = signParameters($parms, $lti->endpoint, "POST", $lti->key, $lti->secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

$content .= '
<h2>'.get_lang('lti_tool_name').'</h2>';

/*
$content .= postLaunchHTML($parms, $lti->endpoint, api_get_course_setting('lti_course_debug_launch', $course_code) == 1 ? true : false,
		"width=\"100%\" height=\"".$iframe_heigth."\" scrolling=\"auto\" frameborder=\"0\" transparency");

$content .= '
         <form action="'.$lti->endpoint.'" target="lti-launch" method="post">
           <input type="hidden" name="text" id="text">
           <input type="submit" value="launch">
         </form>

         <iframe name="lti-launch" src="'.$lti->endpoint.'" width="100%" height="'.$iframe_heigth.'"></iframe>
         <iframe src="http://www.w3schools.com"></iframe>
         ';
*/
/* 
 $content .= '
         <iframe src="http://www.w3schools.com" width="100%" height="1000" scrolling="auto" frameborder="0" transparency></iframe>';
*/

if (isset($message) )
    $tpl->assign('message', $message);

$tpl->assign('content', $content);
$tpl->display_one_col_template();
