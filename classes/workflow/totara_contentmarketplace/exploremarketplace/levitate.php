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

namespace contentmarketplace_levitate\workflow\totara_contentmarketplace\exploremarketplace;

use totara_contentmarketplace\workflow\marketplace_workflow;

/**
 * Levitate explore marketplace workflow implementation.
 */
class levitate extends marketplace_workflow {

    public function get_name(): string {
        return get_string('explorego1marketplace', 'contentmarketplace_levitate');
    }

    public function get_description(): string {
        return get_string('explorego1marketplacedesc', 'contentmarketplace_levitate');
    }

}
