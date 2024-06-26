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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package contentmarketplace_levitate
 */

namespace contentmarketplace_levitate\entity;

use core\orm\entity\entity;

/**
 * A levitate learning object that has been fetched and stored locally within Totara.
 *
 * @property-read int $id
 * @property int $external_id
 *
 * @package contentmarketplace_levitate\entity
 */
class learning_object extends entity {

    /**
     * @var string
     */
    public const TABLE = 'marketplace_levitate_learning_object';

}
