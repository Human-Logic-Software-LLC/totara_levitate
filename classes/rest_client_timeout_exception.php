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

namespace contentmarketplace_levitate;

defined('MOODLE_INTERNAL') || die();

class rest_client_timeout_exception extends \moodle_exception {

    public function __construct($url) {
        $errorcode = "error:rest_client_timeout";
        $module = "contentmarketplace_levitate";
        $link = null;
        $a = null;
        $debuginfo = "Encountered CURLE_OPERATION_TIMEOUTED when calling levitate API (Called URL $url)";
        parent::__construct($errorcode, $module, $link, $a, $debuginfo);
    }

}
