<?php
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

// use contentmarketplace_levitate\contentmarketplace;
// use contentmarketplace_levitate\oauth;
// use contentmarketplace_levitate\config_session_storage;

// $code = required_param('code', PARAM_RAW);
// $client_id = required_param('client_id', PARAM_RAW);
// $client_secret = required_param('client_secret', PARAM_RAW);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/totara/contentmarketplace/contentmarketplaces/levitate/signin.php');

require_login();
require_capability('totara/contentmarketplace:config', $context);
\totara_contentmarketplace\local::require_contentmarketplace();

$PAGE->set_pagelayout('popup');

$get_token = $DB->get_record('config_plugins', array('name' => 'secret','plugin'=>'contentmarketplace_levitate'));
$data = new \stdclass();
    $data->value = $_REQUEST['token'];
    $data->plugin = 'contentmarketplace_levitate';
    $data->name ='secret';
if($get_token){
    $data->id = $get_token->id;
    $updated = $DB->update_record('config_plugins',$data);
}
else{
    $updated = $DB->insert_record('config_plugins',$data);

}

$redirect = new moodle_url('/totara/contentmarketplace/contentmarketplaces/levitate/setup.php');
$PAGE->requires->js_init_code("window.opener.location.href = '$redirect'; window.close();");

echo $OUTPUT->header();
echo $OUTPUT->footer();
