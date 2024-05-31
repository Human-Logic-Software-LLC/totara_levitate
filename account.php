<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Sergey Vidusov <sergey.vidusov@androgogic.com>
 * @package contentmarketplace_levitate
 */

defined('MOODLE_INTERNAL') || die();

global $OUTPUT, $DB;

$tokensettings = $DB->get_record(
    "config_plugins",
    ["plugin" => "contentmarketplace_levitate", "name" => "secret"],
    "value"
);
$tokenid = $tokensettings->value;

$endpoint = "https://server.levitate.coach/webservice/rest/server.php?wstoken=";

$data = new stdClass();
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $endpoint.$tokenid.'&wsfunction=mod_levitateserver_get_analytics&moodlewsrestformat=json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
));

$response = curl_exec($curl);

curl_close($curl);
$json = json_decode($response);
$json_data = json_decode(json_encode($json), true);
$data->portal_url='server.levitate.coach';
$userinfo = json_decode($json_data['userinfo']);
$data->plan_name= $userinfo->subscriptiontype;

$data->enabled_on= date('Y-m-d', $userinfo->subscriptionstart);
$data->plan_renewal_date = date('Y-m-d',$userinfo->subscriptionend);
$data->plan_users_active = $json_data['total_seats'];
$data->total_courses = $json_data['total_courses'];
$data->total_timespent = $json_data['total_timespent'];
$data->participant_count = $json_data['participant_count'];
$data->analytics_url = $CFG->wwwroot.'/totara/contentmarketplace/contentmarketplaces/levitate/analytics.php';

// print_r($data);
// exit;

echo $OUTPUT->render_from_template("contentmarketplace_levitate/account", $data);
