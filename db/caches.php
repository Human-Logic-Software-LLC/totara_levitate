<?php
/*
 * This file is part of Totara LMS
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
 * @author    Greg Newton greg.newton@androgogic.com
 * @package   totara_contentmarketplace_levitate
 */

/**
 * Cache definition for the levitate content provider.
 */
defined('MOODLE_INTERNAL') || die;

$definitions = array(
    // Cache for individual learning objects
    'levitatewslearningobject' => [
        'mode'        => cache_store::MODE_APPLICATION,
        'ttl'         => 300
    ],
    // Cache for bulk results, eg. a search.
    'levitatewslearningobjectbulk' => [
        'mode'        => cache_store::MODE_APPLICATION,
        'ttl'         => 300
    ],
    // Cache for counts of objects.
    'levitatewscount' => [
        'mode'        => cache_store::MODE_APPLICATION,
        'ttl'         => 300
    ]
);
