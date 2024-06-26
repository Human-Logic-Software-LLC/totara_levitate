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

final class contentmarketplace extends \totara_contentmarketplace\local\contentmarketplace\contentmarketplace {

    public $name = 'levitate';

    /**
     * Returns the URL for the plugin.
     *
     * @return string
     */
    public function url() {
        return 'https://server.levitate.coach';
    }

    /**
     * Returns the path to a page used to create the course(es), relative to the site root.
     *
     * @return string
     */
     public function course_create_page() {
         return "/totara/contentmarketplace/contentmarketplaces/levitate/coursecreate.php";
     }

    /**
     * Returns a HTML snippet responsible for setting up the levitate content marketplace data.
     * All related JavaScript has to be there as well.
     *
     * @param string $label
     * @return string Resulting HTML.
     */
    public function get_setup_html($label) {
        global $OUTPUT,$CFG;
        $data = new \stdClass();
        $data->oauth_authorize_url = 'https://server.levitate.coach/create_token_totara.php?'.$CFG->wwwroot;
        $data->label = $label;
        return $OUTPUT->render_from_template("contentmarketplace_levitate/setup", $data);
    }

    /**
     * Saves the portal data to LMS.
     *
     * @return void
     */
    public static function update_data() {
        $api = new api();
        self::update_portal_data($api);
        self::update_portal_configuration_data($api);
    }

    /**
     * @param \contentmarketplace_levitate\api $api
     */
    private static function update_portal_data($api) {
        $account = self::load_account_data($api);
        if (!empty($account)) {
            set_config('account_portal_url', $account->url, 'contentmarketplace_levitate');
            if (is_object($account->plan)) {
                set_config('account_plan_name', $account->plan->type, 'contentmarketplace_levitate');
                set_config('account_plan_users_licensed', $account->plan->licensed_user_count, 'contentmarketplace_levitate');
                set_config('account_plan_users_active', $account->plan->active_user_count, 'contentmarketplace_levitate');
                set_config('account_plan_region', $account->plan->region, 'contentmarketplace_levitate');
                set_config('account_plan_renewal_date', $account->plan->renewal_date, 'contentmarketplace_levitate');
                set_config('account_plan_price', $account->plan->pricing->price, 'contentmarketplace_levitate');
                if (!empty($account->plan->pricing->currency)) {
                    set_config('account_plan_currency', $account->plan->pricing->currency, 'contentmarketplace_levitate');
                }
            }
        }
        $api->purge_all_caches();
    }

    /**
     * @param \contentmarketplace_levitate\api $api
     */
    private static function update_portal_configuration_data($api) {
        $configuration = $api->get_configuration();
        if (!empty($configuration) && is_object($configuration) && isset($configuration->pay_per_seat)) {
            set_config('pay_per_seat', (bool) $configuration->pay_per_seat, 'contentmarketplace_levitate');
        }
        $api->purge_all_caches();
    }

    /**
     * @param \stdClass $data
     */
    public static function save_content_settings_data(\stdClass $data) {
        set_config('content_settings_creators', $data->creators, 'contentmarketplace_levitate');
        set_config('pay_per_seat', $data->pay_per_seat, 'contentmarketplace_levitate');

        $apidata = ['pay_per_seat' => (bool) $data->pay_per_seat];
        $api = new api();
        $api->save_configuration($apidata);
    }

    /**
     * @return \moodle_url
     */
    public static function oauth_redirect_uri(): \moodle_url {
        return new \moodle_url("/totara/contentmarketplace/contentmarketplaces/levitate/signin.php");
    }

    /**
     * @return array
     */
    private static function oauth_user_state() {
        global $USER, $CFG;

        require_once $CFG->dirroot . '/admin/registerlib.php';
        $regdata = get_registration_data();

        $state = [
            'full_name' => fullname($USER),
            'email' => $USER->email,
            'company' => $regdata['orgname'],
            'phone_number' => $USER->phone1,
            'country' => $USER->country,
            'customer_partner' => 'Totara Learn',
            'users_total' => $regdata['activeusercount'],
        ];

        return $state;
    }

    /**
     * @param \contentmarketplace_levitate\api $api
     * @return mixed The account object.
     */
    public static function load_account_data($api) {
        $account = $api->get_account();
        return $account;
    }

    /**
     * Return listing of content availability options for the current user in the given context.
     *
     * @param \context $context
     * @return string[] Listing of availability options
     */
    public static function content_availability_options(\context $context) {
        if (has_capability('totara/contentmarketplace:config', $context)) {
            return ['all', 'subscribed', 'collection'];
        } elseif (has_capability('totara/contentmarketplace:add', $context)) {
            $content_settings = get_config('contentmarketplace_levitate', 'content_settings_creators');
            switch ($content_settings) {
                case "all":
                    return ['all', 'subscribed', 'collection'];
                case "subscribed":
                    return ['subscribed', 'collection'];
            }
        }
        return [];
    }

    /**
     * @param null|string $tab
     * @return \moodle_url
     */
    public function settings_url($tab = null) {
        $url = new \moodle_url(
            "/totara/contentmarketplace/marketplaces.php",
            ['id' => $this->name],
        );

        if (!empty($tab)) {
            $url->param('tab', $tab);
        }

        return $url;
    }

    public function get_filter_count_data() {
        global $OUTPUT,$CFG;
      
        return $OUTPUT->render_from_template("contentmarketplace_levitate/filtercount", []);
    }
}
