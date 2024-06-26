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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package contentmarketplace_levitate
 */

namespace contentmarketplace_levitate;

defined('MOODLE_INTERNAL') || die();

class plugininfo extends \totara_contentmarketplace\plugininfo\contentmarketplace {
    public function get_usage_for_registration_data() {
        global $DB;

        $data = parent::get_usage_for_registration_data();

        $select = "source LIKE :source AND filearea <> :filearea";
        $params = array(
            'source' => 'content-marketplace://levitate/%',
            'filearea' => 'draft',
        );
        $data['numlevitatefiles'] = $DB->count_records_select('files', $select, $params);

        return $data;
    }
}
