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
 * Edwiser form cleanup task
 *
 * @package local_edwiserform
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// namespace totara_contentmarketplace_contentmarketplaces_levitate\task;
namespace contentmarketplace_levitate\task;
// namespace create_course\task;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))).'/config.php');
defined('MOODLE_INTERNAL') || die;

// require_once($CFG->dirroot . '/local/hl_scorm_marketplace/locallib.php');

use context_system;
use dml_exception;
use core_plugin_manager;

// use contentmarketplace_levitate\contentmarketplace;
use contentmarketplace_levitate\form\create_course_controller;
use contentmarketplace_levitate\form\create_course_form;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/scorm/locallib.php');
require_once($CFG->dirroot.'/mod/scorm/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
class create_course extends \core\task\scheduled_task {
// class create_course extends base_sync_task {

    // Use the logging trait to get some nice, juicy, logging.
    // use \core\task\logging_trait;

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return 'coursecreate_levitate';
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {
        global $DB,$CFG;
        
        
        // $taskquery = 'SELECT mltd.coursedata, mlf.formdata FROM {marketplace_levitate_task_details} mltd JOIN {marketplace_levitate_formdata} mlf ON mlf.id = mltd.formid';
        // $data = $DB->get_records_sql($taskquery);
        // foreach($data)
        function enrol_course_creator($course) {
          global $CFG, $USER;
          $context = \context_course::instance($course->id, MUST_EXIST);
          if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
              // Deal with course creators - enrol them internally with default role.
              enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
          }
        }
        
        
        function storedfile($name, $packageid, $scorm) {
          global $USER;
        
          $fs = get_file_storage();
        
          $itemid = file_get_unused_draft_itemid();
          $usercontext = \context_user::instance($USER->id);
          $now = time();
        
          /** @var totara_contentmarketplace\plugininfo\contentmarketplace $plugin */
          $plugin = core_plugin_manager::instance()->get_plugin_info("contentmarketplace_levitate");
          $marketplace = $plugin->contentmarketplace();
        
          // Prepare file record.
          $record = new \stdClass();
          $record->filepath = "/";
          $record->filename = clean_filename($name . ".zip");
          $record->component = 'user';
          $record->filearea = 'draft';
          $record->itemid = $itemid;
          $record->license = "allrightsreserved";
          $record->author = "Content Marketplace";
          $record->contextid = $usercontext->id;
          $record->timecreated = $now;
          $record->timemodified = $now;
          $record->userid = $USER->id;
          $record->sortorder = 0;
          $record->source = $marketplace->get_source($packageid, $name);
        
          return $fs->create_file_from_string($record, $scorm);
        }
        
        function add_scorm_module($course, $name, $course_id, $descriptionhtml, $assessable, $section = 0) {
          global $CFG, $DB;
          require_once($CFG->dirroot.'/mod/scorm/lib.php');
        
          $moduleinfo = new \stdClass();
          $moduleinfo->name = $name;
          $moduleinfo->modulename = 'scorm';
          $moduleinfo->module = $DB->get_field('modules', 'id', ['name' => 'scorm'], MUST_EXIST);
          $moduleinfo->cmidnumber = "";
        
          $moduleinfo->visible = 1;
          $moduleinfo->section = $section;
        
          $moduleinfo->intro = $descriptionhtml;
          $moduleinfo->introformat = FORMAT_HTML;
        
          $moduleinfo->popup = 1;
          $moduleinfo->width = 100;
          $moduleinfo->height = 100;
          $moduleinfo->skipview = 2;
          $moduleinfo->hidebrowse = 1;
          $moduleinfo->displaycoursestructure = 0;
          $moduleinfo->hidetoc = 3;
          $moduleinfo->nav = 1;
          $moduleinfo->displayactivityname = false;
          $moduleinfo->displayattemptstatus = 1;
          $moduleinfo->forcenewattempt = 1;
          $moduleinfo->maxattempt = 0;
        
          $moduleinfo->scormtype = SCORM_TYPE_LOCAL;
        
          // $api = new \contentmarketplace_levitate\api();
          // $scormzip = $api->get_scorm($itemid);
          $tokensettings = $DB->get_record('config_plugins', ['plugin' => 'contentmarketplace_levitate', 'name' => 'secret'], 'value');

          $tokenid = $tokensettings->value;
        
          $curl = curl_init();
        
          curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://server.levitate.coach/webservice/rest/server.php?moodlewsrestformat=json&wstoken='.$tokenid.'&wsfunction=mod_levitateserver_get_tiny_scorms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('cmid' => $course_id),
          ));
        
          $tinyscorm = curl_exec($curl);
        
          curl_close($curl);
          $packagefile = storedfile($name, $itemid, $tinyscorm);
          $moduleinfo->packagefile = $packagefile->get_itemid();
          scorm_add_trusted_package_contenthash($packagefile->get_contenthash());
        
          if ($assessable) {
              $moduleinfo->grademethod = GRADEHIGHEST;
              $moduleinfo->maxgrade = 100;
              $moduleinfo->completion = COMPLETION_TRACKING_AUTOMATIC;
              $moduleinfo->completionscoredisabled = 1;
              $moduleinfo->completionstatusrequired = get_completionstatusrequired('passed');
          } else {
              $moduleinfo->grademethod = GRADESCOES;
              $moduleinfo->completion = COMPLETION_TRACKING_AUTOMATIC;
              $moduleinfo->completionscoredisabled = 1;
              $moduleinfo->completionstatusrequired = get_completionstatusrequired('completed');
          }
        
          return add_moduleinfo($moduleinfo, $course);
        }
        function get_completionstatusrequired($option) {
          foreach (scorm_status_options() as $key => $value) {
              if ($value == $option) {
                  return $key;
              }
          }
          throw new \coding_exception('Unknown completionstatus option: ' . $option);
        }
        
        $forms = $DB->get_records('marketplace_levitate_formdata',array('taskexecuted'=>0));
        
        foreach($forms as $form){
          if($form->taskexecuted==1){
            break;
          }
          
          
          $formid =$form->id;
          $form= json_decode($form->formdata);
          $selection = $form->selection;
          $approval_update = new \stdclass();
          $approval_update->id = $formid;
          $approval_update->taskexecuted = 1;//Pending Attempt
          $updated = $DB->update_record('marketplace_levitate_formdata',$approval_update);
         
           
          if (count($selection) == 1 || $form->create == create_course_form::CREATE_COURSE_MULTI_ACTIVITY) {
              $coursedata = new \stdClass();
              $suffix = count($selection) == 1 ? '_' .$selection[0] : '';
              $coursedata->category = $form->{'category' . $suffix};
              $coursedata->fullname = $form->{'fullname' . $suffix};
              $coursedata->shortname = $form->{'shortname' . $suffix};
              $coursedata->visible = true;
        
              $coursedata->enablecompletion = COMPLETION_ENABLED;
              $coursedata->completionstartonenrol = 1;
        
              if ($form->create == create_course_form::CREATE_COURSE_SINGLE_ACTIVITY) {
                  $coursedata->format = 'singleactivity';
                  $coursedata->activitytype = 'scorm';
                  $section = 0;
              } else {
                  $section = 1;
              }
        
              $course = \create_course($coursedata);
              enrol_course_creator($course);
        
          
              foreach ($selection as $id) {
                
                $learningobject =$DB->get_record('marketplace_levitate_task_details',array('contextid'=>$id,'formid'=>$formid));
                $learningobject = json_decode($learningobject->coursedata);
               
                /**added by human-logic **/
                $course->summary = clean_text($learningobject->course_description); 
                $course->summaryformat = 1;
              //  $DB->update_record('course',$course);
        
                //below code is added by human-logic to upload images in course summary
                // if($learningobject->imageURL)
                // {
                //   $context = \context_course::instance($course->id);
                //   $fs = get_file_storage();
                //   $rc = [
                //       'contextid' => $context->id,
                //       'component' => 'course',
                //       'filearea' => 'images',
                //       'filepath' => '/',
                //       'itemid' => 0,
                //       'license' => 'public'
                //       ];
                //   $fs->delete_area_files($context->id, 'course', 'images');
                //   $fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
                // }
        
                //below code is added by human-logic to upload images in course overviewfiles
                // if($learningobject->imageURL)
                // {
                //   $context = \context_course::instance($course->id);
                //   $fs = get_file_storage();
                //   $rc = [
                //       'contextid' => $context->id,
                //       'component' => 'course',
                //       'filearea' => 'overviewfiles',
                //       'filepath' => '/',
                //       'itemid' => 0,
                //       'license' => 'public'
                //       ];
                //   $fs->delete_area_files($context->id, 'course', 'overviewfiles');
                //   $fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
                // }
              /**/
                  $title = clean_param($learningobject->title, !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEANHTML);
                  $descriptionhtml = clean_text($learningobject->course_description);
                  $learningobject->assessable='';
                  add_scorm_module($course, $title, $learningobject->course_id, $descriptionhtml, $learningobject->assessable, $section);
              }
            }
          else {
            $courselinkshtml = [];
            foreach ($selection as $id) {
              $learningobject =$DB->get_record('marketplace_levitate_task_details',array('contextid'=>$id,'formid'=>$formid));
              $learningobject = json_decode($learningobject->coursedata);
             
                  $coursedata = new \stdClass();
                  $coursedata->category = $form->{'category_' . $id};
                  $coursedata->fullname = $form->{'fullname_' . $id};
                  $coursedata->shortname = $form->{'shortname_' . $id};
                  $coursedata->visible = true;
        
                  $coursedata->enablecompletion = COMPLETION_ENABLED;
                  $coursedata->completionstartonenrol = 1;
        
                  $coursedata->format = 'singleactivity';
                  $coursedata->activitytype = 'scorm';
                  $course = \create_course($coursedata);
            /**added by human-logic **/
            $course->summary = clean_text($learningobject->course_description); 
            $course->summaryformat = 1;	
            $DB->update_record('course',$course); 
            
            //below code is added by humanlogic to upload images in course summary
            //$url = course_get_image($course);
            if($learningobject->imageURL)
            {
              $context = \context_course::instance($course->id);
              $fs = get_file_storage();
              $rc = [
                  'contextid' => $context->id,
                  'component' => 'course',
                  'filearea' => 'images',
                  'filepath' => '/',
                  'itemid' => 0,
                  'license' => 'public'
                  ];
              $fs->delete_area_files($context->id, 'course', 'images');
              $fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
            }
            
            //below code is added by humanlogic to upload images in course overviewfiles
            //$url = course_get_image($course);
            if($learningobject->imageURL)
            {
              $context = \context_course::instance($course->id);
              $fs = get_file_storage();
              $rc = [
                  'contextid' => $context->id,
                  'component' => 'course',
                  'filearea' => 'overviewfiles',
                  'filepath' => '/',
                  'itemid' => 0,
                  'license' => 'public'
                  ];
              $fs->delete_area_files($context->id, 'course', 'overviewfiles');
              $fs->create_file_from_url($rc,$learningobject->imageURL,null,true);
            }
            /**/
        
                  enrol_course_creator($course);
            
                  $title = clean_param($learningobject->title, !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEANHTML);
                  $descriptionhtml = clean_text($learningobject->course_description);
                  $learningobject->assessable = '';
                  add_scorm_module($course, $title, $learningobject->course_id, $descriptionhtml, $learningobject->assessable);
        
                  $courselinkshtml[] = s($coursedata->fullname);
              }
          }
          // $learningobject =$DB->get_record('marketplace_levitate_formdata',array('contextid'=>$id,'formid'=>$formid));
          $approval_update = new \stdclass();
          $approval_update->id = $formid;
          $approval_update->taskexecuted = 2;//Attempt completed
          
        
         
          $updated = $DB->update_record('marketplace_levitate_formdata',$approval_update);
         
        
        }
    }
}
