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
 * Route tokens to their handlers for processing.
 *
 * @package     auth_sso
 * @copyright   2021 miniOrange <info@xecurify.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('autoload.php');

use auth_sso\Actions\SSOActions;
use auth_sso\Helper\Traits\Instance;

$ssoactions = SSOActions::instance();

$wantsurl = optional_param('wantsurl', '', PARAM_LOCALURL); // Overrides $SESSION->wantsurl if given.
if ($wantsurl !== '') {
    // This is later used in core_login_get_return_url().
    $SESSION->wantsurl = (new moodle_url($wantsurl))->out(false);
}

$ssoactions->handle_sso();
