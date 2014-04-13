<?php

require_once dirname(__FILE__).'/config.php';

$course_plugin = 'lti'; //needed in order to load the plugin lang variables
$course_code = api_get_course_id();
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
        //$message = Display::return_message(get_lang('lti_warning'), 'warning');
        //}
} else {
    $message = Display::return_message(get_lang('lti_warning_external_tool_not_configured'), 'warning');
}

$iframe_heigth = api_get_course_setting('lti_course_iframe_height', $course_code); 
$iframe_heigth = (int)$iframe_heigth > 0 ? $iframe_heigth : "500";
$open_new_window = api_get_course_setting('lti_open_new_window', $course_code) == 1 ? true : false;


//Code imported from IMS
require_once dirname(__FILE__).'/lib/lti_util.php';

$lmsdata = array(
		"resource_link_id" => "120988f929-274612",
		"resource_link_title" => "Weekly Blog",
		"resource_link_description" => "A weekly blog.",
		"user_id" => "292832126",
		"roles" => "Instructor",  // or Learner
		"lis_person_name_full" => 'Jane Q. Public',
		"lis_person_name_family" => 'Public',
		"lis_person_name_given" => 'Given',
		"lis_person_contact_email_primary" => "user@school.edu",
		"lis_person_sourcedid" => "school.edu:user",
		"context_id" => "456434513",
		"context_title" => "Design of Personal Environments",
		"context_label" => "SI182",
        "tool_consumer_info_product_family_code" => "chamilo",
        "tool_consumer_info_version" => "1.9.6", 
		"tool_consumer_instance_guid" => "lmsng.school.edu",
        "tool_consumer_instance_name" => "SchoolU",
		"tool_consumer_instance_description" => "University of School (LMSng)",
        "tool_consumer_instance_url" => "http://lmsng.school.edu"
);

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

 $content = '<h2>'.get_lang('lti_tool_name').'</h2>';
         
 $content .= postLaunchHTML($parms, $lti->endpoint, true,
		"width=\"100%\" height=\"".$iframe_heigth."\" scrolling=\"auto\" frameborder=\"0\" transparency");

 /*
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


?>