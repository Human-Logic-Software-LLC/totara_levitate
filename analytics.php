<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     contentmarketplace_levitate
 * @copyright   2023, Human Logic Software LLC
 * @author     Sreenu Malae <sreenivas@human-logic.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'../../../../../config.php');

global $CFG, $DB, $PAGE, $OUTPUT;

$PAGE->set_context(context_system::instance());
require_login();
// $PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');

$PAGE->requires->js(new \moodle_url('https://server.levitate.coach/blocks/levitate_report/javascript/datatables.min.js'), true);
$PAGE->requires->css(new \moodle_url('https://server.levitate.coach/blocks/levitate_report/javascript/datatables.min.css'));
$PAGE->requires->js(new \moodle_url('https://d3js.org/d3.v4.js'), true);

$PAGE->requires->js(new \moodle_url($CFG->wwwroot.'/totara/contentmarketplace/contentmarketplaces/levitate/js/report.js'), true);
$PAGE->set_title(get_string('heading_analytics', 'contentmarketplace_levitate'));
$PAGE->set_heading(get_string('heading_analytics', 'contentmarketplace_levitate'));
echo "<div id='topofthePage'></div>";
$PAGE->set_pagelayout('base');

echo $OUTPUT->header();

echo '<script src="https://server.levitate.coach/blocks/levitate_report/javascript/datatables.min.js"></script>';

$tokensettings = $DB->get_record('config_plugins', ['plugin' => 'contentmarketplace_levitate', 'name' => 'secret'], 'value');

$tokenid = $tokensettings->value;

$curl = curl_init();


curl_setopt_array($curl, [
CURLOPT_URL => 'https://server.levitate.coach/webservice/rest/server.php?wstoken='.$tokenid.
                    '&wsfunction=mod_levitateserver_get_analytics&moodlewsrestformat=json',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($curl);
curl_close($curl);

$json = json_decode($response);
$jsondata = json_decode(json_encode($json), true);

$totalusers = $jsondata['participant_count'];
$totalseats = $jsondata['total_seats'];
$totalcourses = $jsondata['total_courses'];
$totaltimespent = floor($jsondata['total_timespent'] / 60000);
$levitatedata = json_decode($jsondata['userinfo']);

$totalpercent = (int)$levitatedata->seats;
$utilized = (int)$jsondata["participant_count"];
$utilizationpercentage = ($utilized / $totalpercent) * 100;
$utilizationpercentage = number_format($utilizationpercentage, 1);
$graphvalue = ($utilizationpercentage / 100) * 630;
?>

<head>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class='dashboard'>
  <div class='main-region'>
    <div class='total-statistics'>
      <div class='total-users'>
        <div class='graph-icon'>&nbsp;</div>
        <div class='stats'>
          <div class='lead'><?php echo get_string('total_users', 'contentmarketplace_levitate'); ?> </div>
          <div class='nums'><?php echo $totalusers; ?> </div>
        </div>
      </div>
     <div class='total-minutes'>
      <div class='graph-icon'>&nbsp;</div>
        <div class='stats'>
          <div class='lead'><?php echo get_string('total_minutes', 'contentmarketplace_levitate'); ?> </div>
          <div class='nums'><?php echo $totaltimespent; ?></div>
        </div>
     </div>
    <div class='total-courses'>
      <div class='graph-icon'>&nbsp;</div>
      <div class='stats'>
        <div class='lead'><?php echo get_string('total_courses', 'contentmarketplace_levitate'); ?> </div>
        <div class='nums'><?php echo $totalcourses; ?></div>
      </div>
    </div>
</div>
        <div class='graphs-container'>
			<!-- <div class="course-statistics popular-courses">
				<div class="heads">
					<h2><?php echo get_string('course_statistics', 'contentmarketplace_levitate'); ?> </h2>
					<select class="course_statistics">
				   
					</select>
				</div>
			   
				<div class="graph" id="enroll_graph">
				</div>
			</div> -->
			
			<div class="course-statistics popular-courses">
				<div class="heads">
					<h2><?php echo get_string('completion_statistics', 'contentmarketplace_levitate'); ?> </h2>
					<select class="completion_statistics">
				   
					</select>
				</div>
				<div class="legends">
					<span class="ttl-enrolls"><?php echo get_string('total_enrolls', 'contentmarketplace_levitate'); ?> </span>
					<span class="ttl-completes"><?php echo get_string('total_completions', 'contentmarketplace_levitate'); ?> </span>
				</div>
				<div class="graph" id="course_graph">
				</div>
			</div>
		</div>
        <div class="course-statistics popular-table">
            <div class="heads">
                <h2><?php echo get_string('popular_courses', 'contentmarketplace_levitate'); ?> </h2>
                <select class="table_select"></select>
            </div>
            <div class="popular-table">
                <table id="datatable">
                   
                </table>
            </div>
        </div>
  </div>
  <div class='right-region'>
      <div class='back_to_explore'><a href="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/explorer.php?marketplace=levitate">Explore Levitate</a></div>
      <!-- <div class="total-courses"><a href="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/explorer.php?marketplace=levitate" />Explore Levitate</div> -->
        <div class="blocks">
            <div class="heads">
                <h2><?php echo get_string('my_details', 'contentmarketplace_levitate'); ?> </h2>
                <div class="chev-down"><img src="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/contentmarketplaces/levitate/pix/chevron-down.svg" /></div>
            </div>
            <div class="detail-item">
            <div class="detail-item-icon"><img src="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/contentmarketplaces/levitate/pix/contact-person.svg" /></div>
                <div class="details">
                    <p class="lead"><?php echo get_string('contact_person', 'contentmarketplace_levitate'); ?> </p>
                    <p><?php echo $levitatedata->contactname?></p>
                    <p><?php echo $levitatedata->profession?></p>
                </div>
            </div>
            <div class="detail-item">
            <div class="detail-item-icon"><img src="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/contentmarketplaces/levitate/pix/contact-details.svg" /></div>
                <div class="details">
                    <p class="lead"><?php echo get_string('contact_details', 'contentmarketplace_levitate'); ?> </p>
                    <p><?php echo $levitatedata->contactemail?></p>
                    <p><?php echo $levitatedata->mobile?></p>
                </div>
            </div>
            <div class="detail-item">
            <div class="detail-item-icon"><img src="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/contentmarketplaces/levitate/pix/access-domain.svg" /></div>
                <div class="details">
                    <p class="lead"><?php echo get_string('access_domain', 'contentmarketplace_levitate'); ?> </p>
                    <p><?php echo $levitatedata->primarydomain?></p>
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-item-icon"><img src="<?php echo $CFG->wwwroot;?>/totara/contentmarketplace/contentmarketplaces/levitate/pix/calendar.svg" /></div>
                <div class="details">
                    <p class="lead"><?php echo get_string('subscripton_start', 'contentmarketplace_levitate'); ?> </p>
                    <p><?php echo date("F d, Y", $levitatedata->subscriptionstart)?></p>
                </div>
            </div>

            <div class="seat-utilization">
                <div class="heads">
                    <h2><?php echo get_string('seat_utilization', 'contentmarketplace_levitate'); ?> </h2>
                </div>
                <div class="progress">
                    <div class="bar" data-name="SVG Skill" data-percent="<?php echo $utilizationpercentage;?>%">
                        <svg viewBox="-10 -10 220 220">
                        <g fill="none" stroke-width="9" transform="translate(100,100)">
                        <path d="M 0,-100 A 100,100 0 0,1 86.6,-50" stroke="url(#cl1)"/>
                        <path d="M 86.6,-50 A 100,100 0 0,1 86.6,50" stroke="url(#cl1)"/>
                        <path d="M 86.6,50 A 100,100 0 0,1 0,100" stroke="url(#cl1)"/>
                        <path d="M 0,100 A 100,100 0 0,1 -86.6,50" stroke="url(#cl1)"/>
                        <path d="M -86.6,50 A 100,100 0 0,1 -86.6,-50" stroke="url(#cl1)"/>
                        <path d="M -86.6,-50 A 100,100 0 0,1 0,-100" stroke="url(#cl1)"/>
                        </g>
                        </svg>
                        <svg viewBox="-10 -10 220 220">
                          <path d="M200,100 C200,44.771525 155.228475,0 100,0 C44.771525,0 0,44.771525 0,
                                   100 C0,155.228475 44.771525,200 100,200 C155.228475,200 200,155.228475 200,100 Z"
                                   stroke-dashoffset="<?php echo $graphvalue; ?>">
                          </path>
                        </svg>
                    </div>

                <!--  Defining Angle Gradient Colors  -->
                <svg width="0" height="0">
                <defs>
                <linearGradient id="cl1" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="1">
                    <stop stop-color="#4050E7"/>
                    <stop offset="100%" stop-color="#4050E7"/>
                </linearGradient>
                </defs>
                </svg>
                </div>
                <div class="utilization">
                    <div class="total-bought">
                        <div class="lead"><?php echo get_string('seats_bought', 'contentmarketplace_levitate'); ?> </div>
                        <div class="nums"><?php echo $levitatedata->seats; ?></div>
                    </div>
                    <div class="total-used">
                        <div class="lead"><?php echo get_string('seats_used', 'contentmarketplace_levitate'); ?> </div>
                        <div class="nums"><?php echo $totalusers; ?></div>
                    </div>
                </div>
            </div>
        </div>
  </div>
  </div>
</body>
<?php echo $OUTPUT->footer();
