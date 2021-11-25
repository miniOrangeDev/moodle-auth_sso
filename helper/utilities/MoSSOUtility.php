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
 * Common function utilities.
 *
 * @package     auth_sso
 * @copyright   2021 miniOrange <info@xecurify.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_sso\Helper\Utilities;

class MoSSOUtility
{

    /**
	 * This function logs a line in the PHP error log file
	 * for debugging purposes. Should only be used when MSSO_DEBUG
	 * is TRUE.
	 *
	 * @param $message - refers to the message to be logged in the log file
	 */
	public static function mo_debug($message)
	{
		error_log("[MO-MSSO-LOG][".date('m-d-Y', time())."]: " . $message);
	}

}