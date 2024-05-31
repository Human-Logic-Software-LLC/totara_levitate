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

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');

use contentmarketplace_levitate\form\setup_form;
use contentmarketplace_levitate\contentmarketplace;
use contentmarketplace_levitate\oauth;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/totara/contentmarketplace/setup.php');

require_login();
require_capability('totara/contentmarketplace:config', $context);
\totara_contentmarketplace\local::require_contentmarketplace();

// if (!oauth::have_config_in_session()) {
//     // Looks like the form has already been successfully processed.
//     redirect(new moodle_url('/totara/contentmarketplace/explorer.php', array('marketplace' => 'levitate')));
// }

// $form = new setup_form();

// if ($form->is_cancelled()) {
//     if (\totara_contentmarketplace\local::should_show_admin_setup_intro()) {
//         $url = new moodle_url('/totara/contentmarketplace/setup.php');
//     } else {
//         $url = new moodle_url('/totara/contentmarketplace/marketplaces.php');
//     }
//     redirect($url);
// } elseif ($data = $form->get_data()) {
//     oauth::move_config_from_session_to_db();
//     contentmarketplace::update_data();
//     contentmarketplace::save_content_settings_data($data);
//     /** @var totara_contentmarketplace\plugininfo\contentmarketplace $plugin */
//     $plugin = core_plugin_manager::instance()->get_plugin_info('contentmarketplace_levitate');
//     $plugin->enable();
//     redirect(new moodle_url('/totara/contentmarketplace/explorer.php', array('marketplace' => 'levitate')));
// }
$plugin = core_plugin_manager::instance()->get_plugin_info('contentmarketplace_levitate');
$plugin->enable();
redirect(new moodle_url('/totara/contentmarketplace/explorer.php', array('marketplace' => 'levitate')));

$PAGE->set_title(get_string('setup_page_header', 'contentmarketplace_levitate'));
$PAGE->set_heading(get_string('setup_page_header', 'contentmarketplace_levitate'));
$PAGE->set_pagelayout('noblocks');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('setup_page_header', 'contentmarketplace_levitate'));
echo $form->render();
echo $OUTPUT->footer();
