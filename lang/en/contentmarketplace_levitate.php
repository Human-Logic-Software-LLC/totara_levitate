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

$string['account'] = 'Account';
$string['addcourse'] = 'Add a new course';
$string['addcourselevitate'] = 'Add courses from the Levitate content marketplace';
$string['addcourselevitate_description'] = 'Create single or multi-activity courses based on what is available in the Levitate content marketplace.';
$string['all_content'] = 'All content ({$a})';
$string['annualcost'] = 'Annual subscription cost';
$string['availability-filter:all'] = 'All';
$string['availability-filter:collection'] = 'Custom collection';
$string['availability-filter:subscription'] = 'Subscription';
$string['cachedef_levitatewslearningobject'] = 'Levitate learning object';
$string['cachedef_levitatewslearningobjectbulk'] = 'Levitate bulk learning objects';
$string['cachedef_levitatewscount'] = 'Count of Levitate items';
$string['collection_content'] = 'Custom collection ({$a})';
$string['collections'] = 'Collections';
$string['content_creators'] = 'Content creators';
$string['content_creators_help'] = 'Content creators are anyone with permissions to create courses';
$string['content_settings'] = 'Content settings';
$string['content_settings_description'] = 'When exploring content marketplace, which Levitate content should these users be able to access?';
$string['continue'] = 'Continue';
$string['course_creation'] = 'Course creation';
$string['course_creation_help'] = 'Include all content in a single course, or create a new course for each content item?';
$string['coursecreated'] = 'New course has been created';
$string['coursecreatedx'] = '{$a} new courses have been created';
$string['courses_amount_label'] = '({$a} courses)';
$string['currentplan'] = 'Current Plan';
$string['duration'] = '{$a} minutes';
$string['enabledby'] = 'Enabled by';
$string['enabledbyunknown'] = 'Unknown';
$string['enableddate'] = 'Enabled date';
$string['error:invalid_token'] = 'There is an authentication problem when connecting to the Levitate servers. The Levitate content marketplace will need to be set up again.';
$string['error:rest_client_timeout'] = 'Communication with Levitate server timed out. Please try again in a moment.';
$string['error:unavailable_learning_object'] = 'Attempted to use unavailable learning object. Please return to the Levitate content marketplace.';
$string['explorelevitatemarketplace'] = 'Levitate content marketplace';
$string['explorelevitatemarketplacedesc'] = 'Explore content from the Levitate marketplace';
$string['filter:availability'] = 'Availability';
$string['filter:language'] = 'Languages';
$string['filter:provider'] = 'Providers';
$string['filter:tags'] = 'Tags';
$string['levitateplanname'] = 'Levitate {$a}';
$string['levitateplantype'] = 'Levitate {$a}';
$string['langwithcode'] = '{$a->lang} ({$a->country})';
$string['learners'] = 'Learners';
$string['managesubscription'] = 'Manage subscription';
$string['no_content'] = 'No content marketplace';
$string['notapplicable'] = 'N/A';
$string['numberactiveusers'] = 'Number of active users';
$string['noofseats'] = 'Number of Seats enrolled';
$string['numberlicensedusers'] = 'Number of licensed users';
$string['online'] = 'Online';
$string['pay_per_seat'] = 'Pay per seat courses';
$string['pay_per_seat:admin'] = 'Request sent to admin';
$string['pay_per_seat:learner'] = 'Learner pays';
$string['pay_per_seat_help'] = 'When a course is not included in your subscription, how should learners access the course?';
$string['plugin_description_html'] = "Levitate, brought to you by Human Logic, offers an extensive eLearning library featuring more than 300 courses across a wide range of knowledge domains. 
Whether it's mastering petrochemical fundamentals, ensuring safety and environmental compliance, or sharpening cybersecurity skills, Levitate has you covered. 
Our library spans from customer service excellence to sustainability, project management to leadership skills, and more. <a href='https://www.levitate.coach/' target='_blank'>Find out more</a>";
$string['pluginname'] = 'Levitate';
$string['portalurl'] = 'Portal URL';
$string['price:free'] = 'Free';
$string['price:included'] = 'Included';
$string['pricewithtax'] = '{$a->baseprice} (+{$a->tax}% tax)';
$string['region'] = 'Region';
$string['region:'] = 'Unknown';
$string['region:AU'] = 'Australia';
$string['region:EU'] = 'European Union';
$string['region:MY'] = 'Malaysia';
$string['region:OTHER'] = 'Rest of the world';
$string['region:UK'] = 'United Kingdom';
$string['region:US'] = 'United States of America';
$string['renewaldate'] = 'Renewal date';
$string['saveandexplorelevitate'] = 'Save and explore Levitate';
$string['search:placeholder'] = 'Search course title, provider, or keyword';
$string['selectcontent'] = 'Select {$a}';
$string['settings_saved'] = 'Settings have been saved.';
$string['setup_page_header'] = 'Set up Levitate integration';
$string['sort:created:desc'] = 'Latest';
$string['sort:popularity'] = 'Popular';
$string['sort:price'] = 'Price (low to high)';
$string['sort:price:desc'] = 'Price (high to low)';
$string['sort:relevance'] = 'Ranking / most relevant';
$string['sort:title'] = 'Alphabetical (A to Z)';
$string['specific_collection'] = 'Specific collection';
$string['subscribed_content'] = 'Subscribed content ({$a})';
$string['subscription_details'] = 'Subscription details';
$string['unknownlanguage'] = 'Unknown';
$string['warningdisablemarketplace:body:html'] = '<p>You are about to disable the Levitate content marketplace. If you proceed, items from the marketplace will no longer be available to course creators for inclusion in newly created courses.</p>
<p>Users who have previously already started Levitate activities will continue to have access to that content, but they will not be able to start new Levitate activities.</p>
<p>Are you sure you wish to proceed?</p>';
$string['warningdisablemarketplace:title'] = 'Disable Levitate content';
$string['warningenablemarketplace:body:html'] = 'You are about to enable the Levitate content marketplace. If you proceed, items from the marketplace will be available to course creators for inclusion in newly created courses.';
$string['warningenablemarketplace:title'] = 'Enable Levitate content';
$string['explorego1marketplace'] = 'Levitate';
$string['explorego1marketplacedesc'] = 'Explore the content from Levitate';

// Deprecated since Totara 15

$string['warningdisablemarketplace:yes'] = 'Disable Levitate';

$string['total_users'] = 'Active / Total seats';
$string['total_minutes'] = 'Total minutes';
$string['total_courses'] = 'Total courses';
$string['course_statistics'] = 'Course statistics (courses enrolled)';
$string['completion_statistics'] = 'Completion statistics';
$string['total_enrolls'] = 'Total enrollments';
$string['total_completions'] = 'Total completions';
$string['popular_courses'] = 'Popular courses';
$string['my_details'] = 'My details';
$string['contact_person'] = 'Contact person';
$string['contact_details'] = 'Contact details';
$string['access_domain'] = 'Access domain(s)';
$string['subscripton_start'] = 'Subscription start & end';
$string['seat_utilization'] = 'Seat utilization';
$string['seats_bought'] = 'Total seats';
$string['seats_used'] = 'Remaining seats';
$string['heading_analytics'] = 'Analytics dashboard';
$string['view_analytics'] = 'View Analytics';
