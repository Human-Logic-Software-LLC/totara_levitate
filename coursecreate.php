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
 * @author Michael Dunstan <michael.dunstan@androgogic.com>
 * @package contentmarketplace_levitate
 */

require('../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/scorm/locallib.php');

use contentmarketplace_levitate\contentmarketplace;
use contentmarketplace_levitate\form\create_course_form;
use contentmarketplace_levitate\form\create_course_controller;

$selection = required_param_array('selection', PARAM_ALPHANUMEXT);
$create = optional_param('create', create_course_form::CREATE_COURSE_MULTI_ACTIVITY, PARAM_INT);
$mode = optional_param('mode', \totara_contentmarketplace\explorer::MODE_CREATE_COURSE, PARAM_ALPHAEXT);

$category = optional_param('category', 0, PARAM_INT);
if (!$category) {
    $category = isset($selection[0]) ? optional_param('category_' . $selection[0], 0, PARAM_INT) : 0;
}

if ($category === 0) {
    $context = context_system::instance();
    $pageparams = [];
} else {
    $context = context_coursecat::instance($category);
    $pageparams = ['category' => $category];
}
$PAGE->set_context($context);
$PAGE->set_url(new \moodle_url('/totara/contentmarketplace/contentmarketplaces/levitate/coursecreate.php', $pageparams));

require_login();
require_capability('totara/contentmarketplace:add', $context);

// Check marketplaces are enabled.
\totara_contentmarketplace\local::require_contentmarketplace();

// Check levitate marketplace plugin is enabled.
/** @var \totara_contentmarketplace\plugininfo\contentmarketplace $plugin */
$plugin = \core_plugin_manager::instance()->get_plugin_info("contentmarketplace_levitate");
if ($plugin === null) {
    throw new coding_exception('The contentmarketplace_levitate plugin is not yet installed.');
}
if (!$plugin->is_enabled()) {
    throw new \moodle_exception('error:disabledmarketplace', 'totara_contentmarketplace', '', $plugin->displayname);
}

$PAGE->set_title(get_string('addcourse', 'contentmarketplace_levitate'));
$PAGE->set_pagelayout('noblocks');

/**
 * Check this levitate account can access the learning object.
 * (Used to guard against unexpected learning objects being used to create content.)
 *
 * @param array $ids
 * @param context $context
 * @throws moodle_exception
 * @return bool always true
 */
function check_availability_of_learning_objects($ids, $context) {
    $api = new \contentmarketplace_levitate\api();
    foreach ($ids as $id) {
        // Throws API exception if learning object $id does not exist or is not available.
        $api->get_learning_object($id);
    }
    return true;
}



list($currentdata, $params) = create_course_controller::get_current_data_and_params($selection, $create, $category, $mode);
$form = new create_course_form($currentdata, $params);

if ($form->is_cancelled()) {
    $url = new moodle_url('/totara/contentmarketplace/explorer.php', ['marketplace' => 'levitate', 'mode' => $mode]);
    if (!empty($category)) {
        $url->param('category', $category);
    }
    redirect($url);
} else if ($data = $form->get_data()) {
    require_once($CFG->dirroot.'/course/modlib.php');
    
    $insert_values1 = new \stdClass();
    $insert_values1->formdata = json_encode($data);
    $insert_values1->taskexecuted = 0;
    $insert_values1->timecreated = time();

    $inserted = $DB->insert_record('marketplace_levitate_formdata', $insert_values1);

    $selection = $data->selection;
    check_availability_of_learning_objects($selection, $context);

    // if (count($selection) == 1 || $data->create == create_course_form::CREATE_COURSE_MULTI_ACTIVITY) {
        // $coursedata = new \stdClass();
        // $suffix = count($selection) == 1 ? '_' .$selection[0] : '';
        // $coursedata->category = $data->{'category' . $suffix};
        // $coursedata->fullname = $data->{'fullname' . $suffix};
        // $coursedata->shortname = $data->{'shortname' . $suffix};
        // $coursedata->visible = true;

        // $coursedata->enablecompletion = COMPLETION_ENABLED;
        // $coursedata->completionstartonenrol = 1;

        // if ($data->create == create_course_form::CREATE_COURSE_SINGLE_ACTIVITY) {
        //     $coursedata->format = 'singleactivity';
        //     $coursedata->activitytype = 'scorm';
        //     $section = 0;
        // } else {
        //     $section = 1;
        // }

        // $course = \create_course($coursedata);
        // enrol_course_creator($course);

        $api = new \contentmarketplace_levitate\api();
		
        foreach ($selection as $id) {
            $learningobject = $api->get_learning_object($id);
            
           $insert_values = new \stdClass();
           $insert_values->taskexecuted = 0;
           $insert_values->formid = $inserted;
           $insert_values->contextid = $id;
           $insert_values->coursedata = json_encode($learningobject);
           $insert_values->userid = $USER->id;
           $insert_values->timecreated = time();

           $inserted_all = $DB->insert_record('marketplace_levitate_task_details', $insert_values);


			/**added by human-logic **/
			// $course->summary = clean_text($learningobject->course_description); 
			// $course->summaryformat = 1;	
			// $DB->update_record('course',$course);

			//below code is added by human-logic to upload images in course summary
			// if($learningobject->imageURL)
			// {
			// 	$context = context_course::instance($course->id);
			// 	$fs = get_file_storage();
			// 	$rc = [
			// 			'contextid' => $context->id,
			// 			'component' => 'course',
			// 			'filearea' => 'images',
			// 			'filepath' => '/',
			// 			'itemid' => 0,
			// 			'license' => 'public'
			// 			];
			// 	$fs->delete_area_files($context->id, 'course', 'images');
			// 	$fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
			// }

			// //below code is added by human-logic to upload images in course overviewfiles
			// //$url = course_get_image($course);
			// if($learningobject->imageURL)
			// {
			// 	$context = context_course::instance($course->id);
			// 	$fs = get_file_storage();
			// 	$rc = [
			// 			'contextid' => $context->id,
			// 			'component' => 'course',
			// 			'filearea' => 'overviewfiles',
			// 			'filepath' => '/',
			// 			'itemid' => 0,
			// 			'license' => 'public'
			// 			];
			// 	$fs->delete_area_files($context->id, 'course', 'overviewfiles');
			// 	$fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
			// }
			// /**/
            // $title = clean_param($learningobject->title, !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEANHTML);
            // $descriptionhtml = clean_text($learningobject->course_description);
            // add_scorm_module($course, $title, $id, $descriptionhtml, $learningobject->assessable, $section);
        }

//        \core\notification::success(get_string('coursecreated', 'contentmarketplace_levitate'));
        $next_task_run_time = $DB->get_field('task_scheduled', 'nextruntime', ['classname' => '\contentmarketplace_levitate\task\create_course'], MUST_EXIST); 
    
//        echo 'Courses will be created in next schedule task execution on '.date('l, d F Y, g:i A', $next_task_run_time);
//        echo '<br>';
//        echo 'To execute now - <a href="/test-content.human-logic.com/admin/tool/task/schedule_task.php?task='.urlencode('contentmarketplace_levitate\task\create_course').'">Click Here</a>';

        $notification_text = 'Courses will be created in next schedule task execution on '.date('l, d F Y, g:i A', $next_task_run_time).PHP_EOL.'';
//        $notification_text_nextline =  'To execute now - <a href="/admin/tool/task/schedule_task.php?task='.urlencode('contentmarketplace_levitate\task\create_course').'">Click Here</a>';
//
        \core\notification::info($notification_text);
//        echo "<br>";
//        \core\notification::info($notification_text_nextline);
    
        $url = new moodle_url('/totara/contentmarketplace/explorer.php', ['marketplace' => 'levitate']);
        redirect($url);

        // $coursecontext = context_course::instance($course->id, MUST_EXIST);
        // $isviewing = is_viewing($coursecontext, NULL, 'moodle/role:assign');
        // $isenrolled = is_enrolled($coursecontext, NULL, 'moodle/role:assign');
        // if ($isviewing || $isenrolled) {
        //     $url = new \moodle_url('/course/view.php', ['id' => $course->id]);
        // } else {
        //     $url = new \moodle_url('/course/index.php', ['categoryid' => $coursedata->category]);
        // }
        // redirect($url);

    // } else {
        // $courselinkshtml = [];
        // foreach ($selection as $id) {
		// 	/**added by human-logic**/
		// 	$api = new \contentmarketplace_levitate\api();
		// 	$learningobject = $api->get_learning_object($id);

            
        //    $insert_values = new \stdClass();
        //    $insert_values->taskexecuted = 1;
        //    $insert_values->formid = $inserted;
        //    $insert_values->coursedata = json_encode($learningobject);
        //    $insert_values->userid = $USER->id;
        //    $insert_values->timecreated = time();

        //    $inserted_all = $DB->insert_record('marketplace_levitate_task_details', $insert_values);
           

            
		// 	/**/
        //     $coursedata = new \stdClass();
        //     $coursedata->category = $data->{'category_' . $id};
        //     $coursedata->fullname = $data->{'fullname_' . $id};
        //     $coursedata->shortname = $data->{'shortname_' . $id};
        //     $coursedata->visible = true;

        //     $coursedata->enablecompletion = COMPLETION_ENABLED;
        //     $coursedata->completionstartonenrol = 1;

        //     $coursedata->format = 'singleactivity';
        //     $coursedata->activitytype = 'scorm';
        //     $course = \create_course($coursedata);
		// 	/**added by human-logic **/
		// 	$course->summary = clean_text($learningobject->description); 
		// 	$course->summaryformat = 1;	
		// 	$DB->update_record('course',$course); 
			
		// 	//below code is added by humanlogic to upload images in course summary
		// 	//$url = course_get_image($course);
		// 	if($learningobject->image)
		// 	{
		// 		$context = context_course::instance($course->id);
		// 		$fs = get_file_storage();
		// 		$rc = [
		// 				'contextid' => $context->id,
		// 				'component' => 'course',
		// 				'filearea' => 'images',
		// 				'filepath' => '/',
		// 				'itemid' => 0,
		// 				'license' => 'public'
		// 				];
		// 		$fs->delete_area_files($context->id, 'course', 'images');
		// 		$fs->create_file_from_url($rc,$learningobject->image,null,true);
		// 	}
			
		// 	//below code is added by humanlogic to upload images in course overviewfiles
		// 	//$url = course_get_image($course);
		// 	if($learningobject->image)
		// 	{
		// 		$context = context_course::instance($course->id);
		// 		$fs = get_file_storage();
		// 		$rc = [
		// 				'contextid' => $context->id,
		// 				'component' => 'course',
		// 				'filearea' => 'overviewfiles',
		// 				'filepath' => '/',
		// 				'itemid' => 0,
		// 				'license' => 'public'
		// 				];
		// 		$fs->delete_area_files($context->id, 'course', 'overviewfiles');
		// 		$fs->create_file_from_url($rc,$learningobject->image,null,true);
		// 	}
		// 	/**/

        //     enrol_course_creator($course);
		// 	/** commented by human-logic **			 
        //     $api = new \contentmarketplace_levitate\api();
        //     $learningobject = $api->get_learning_object($id);
		// 	**/
			
        //     $title = clean_param($learningobject->title, !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEANHTML);
        //     $descriptionhtml = clean_text($learningobject->description);
        //     add_scorm_module($course, $title, $id, $descriptionhtml, $learningobject->assessable);

        //     $courselinkshtml[] = s($coursedata->fullname);
        // }

        // $messagehtml = html_writer::tag('p', get_string('coursecreatedx', 'contentmarketplace_levitate', count($selection)));
        // $messagehtml .= html_writer::alist($courselinkshtml);
        // \core\notification::success($messagehtml);
        // $category = $data->{'category_' . $selection[0]};
        // redirect(new \moodle_url('/course/index.php', ['categoryid' => $category]));
    // }
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('addcourse', 'contentmarketplace_levitate'));

echo $form->render();

echo $OUTPUT->footer();
