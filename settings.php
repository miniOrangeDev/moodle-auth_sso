<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Admin settings for the miniOrange Single Sign-On plugin.
 *
 * @package   auth_sso
 * @copyright 2021 miniOrange <info@xecurify.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('autoload.php');
if ($hassiteconfig) {
    if ($ADMIN->fulltree) {
                $settings->add(new admin_setting_heading('auth_sso/jwtClientMetadata',
                get_string('mojwtclientmetadata', 'auth_sso'), ''));
                $settings->add(new admin_setting_description('auth_sso/jwtCallbackUrl',
                get_string('mojwtcallbackurlkey', 'auth_sso'), MSSO_URL.'login.php'));
                $settings->add(new admin_setting_heading('auth_sso/jwtProviderConfig',
                get_string('mojwtprovidersetup', 'auth_sso'), ''));
                $settings->add(new admin_setting_configtext('auth_sso/jwtIdpName', 'Identity Provider Name ', '', '', PARAM_RAW));
                $settings->add(new admin_setting_configtext('auth_sso/jwtLoginUrl', 'Login URL', '', '', PARAM_RAW));
                $signingalgo = array("RS256" => "RS256", "HS256" => "HS256");
                $settings->add(new admin_setting_configselect('auth_sso/jwtSignAlg', 'Signature Algorithm', '', '', $signingalgo));
                $settings->add(new admin_setting_configtextarea('auth_sso/jwtIdpCert',
                'X.509 Signing Certificate / Shared Secret', '', '',
                PARAM_RAW_TRIMMED, $cols = '60', $rows = '8' ));
    }
}
