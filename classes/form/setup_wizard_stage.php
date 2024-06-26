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

namespace contentmarketplace_levitate\form;

defined('MOODLE_INTERNAL') || die();

final class setup_wizard_stage extends \totara_form\form\group\wizard_stage {

    public function export_for_template(\renderer_base $output) {
        $result = parent::export_for_template($output);
        // We very intentionally override form_item_template here.
        $result['form_item_template'] = 'contentmarketplace_levitate/setup_wizard_stage';
        return $result;
    }
}
