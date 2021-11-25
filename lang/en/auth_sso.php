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
 * Plugin strings are defined here.
 *
 * @package   auth_sso
 * @category  string
 * @copyright 2021 miniOrange <info@xecurify.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_description'] = 'This plugin enables Single Sign-On for your Moodle website.';
$string['pluginname'] = 'miniOrange SSO';
$string['mosso'] = 'miniOrange Single Sign-On for Moodle';
$string['mosso_help'] = 'miniOrange SSO help text';
$string['mojwtclientmetadata'] = 'JWT Client Metadata';
$string['mojwtclientmetadata_help'] = 'This information is required to configure your JWT Provider. It contains the Callback URL (where to post the JWT token).';
$string['mojwtprovidersetup'] = 'JWT Provider Configuration';
$string['mojwtprovidersetup_help'] = 'Here you can configure the JWT Provider. Enter the Provider Name to be displayed on the Moodle Login Page, and the link to initiate the Single Sign-On.';
$string['mojwtcallbackurlkey'] = 'Callback URL';
$string['mossologinerror'] = 'Login error';
$string['mossologinnoattribute'] = 'You have logged in successfully but we could not find your \'{$a}\' attribute to associate you to an account in Moodle.';
$string['mossologinnouser'] = 'You have logged in successfully as \'{$a}\' but do not have an account in Moodle.';
$string['mossologinsuspendeduser'] = 'You have logged in successfully as \'{$a}\' but your account has been suspended in Moodle.';
$string['mossologinwrongauth'] = 'You have logged in successfully as \'{$a}\' but are not authorized to access Moodle.';
$string['anyauthotherdisabled'] = 'You have logged in successfully as \'{$a->username}\' but your auth type of \'{$a->auth}\' is disabled.';
$string['updateusersettings'] = 'Update';
