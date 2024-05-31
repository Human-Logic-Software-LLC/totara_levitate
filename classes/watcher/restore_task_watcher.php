<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <mark.metcalfe@totaralearning.com>
 * @package contentmarketplace_levitate
 */

namespace contentmarketplace_levitate\watcher;

use container_course\module\course_module;
use contentmarketplace_levitate\model\learning_object;
use core\orm\query\builder;
use restore_activity_task;
use totara_contentmarketplace\model\course_module_source;
use totara_core\hook\backup_post_restore_task;

class restore_task_watcher {

    /**
     * Create a course module source record if a levitate scorm activity was restored.
     *
     * @param backup_post_restore_task $hook
     */
    public static function post_scorm_activity_restored(backup_post_restore_task $hook): void {
        $task = $hook->get_task();

        if (!$task instanceof restore_activity_task || $task->get_modulename() !== 'scorm') {
            return;
        }

        $source_prefix = 'content-marketplace://levitate/';

        $levitate_scorm_package_file = builder::table('files')
            ->select('source')
            ->where('contextid', $task->get_contextid())
            ->where('filearea', 'package')
            ->where_like_starts_with('source', $source_prefix)
            ->one();

        if ($levitate_scorm_package_file === null) {
            return;
        }

        $course_module = course_module::from_id($task->get_moduleid());

        $levitate_id = str_replace($source_prefix, '', $levitate_scorm_package_file->source);
        $learning_object = learning_object::load_by_external_id($levitate_id);

        course_module_source::create($course_module, $learning_object);
    }

}
