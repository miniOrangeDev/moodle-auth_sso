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
 * This class is being used to auto include all files
 * being used in the plugin. Removes the pain of individually
 * including all files.
 *
 * @package   auth_sso
 * @copyright 2021 miniOrange <info@xecurify.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


global $CFG;

define('MSSO_VERSION', '1.0.0');

define('MSSO_DIR', $CFG->dirroot.'/auth/sso/');
define('MSSO_URL', $CFG->wwwroot.'/auth/sso/');
define('MSSO_CSS_URL', MSSO_URL . 'includes/css/mo_sso_style.min.css');
define('MSSO_FA_URL', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
define('MSSO_JS_URL', MSSO_URL . 'includes/js/settings.min.js');
define('MSSO_ICON', MSSO_URL . 'includes/images/miniorange_icon.png');
define('MSSO_LOGO_URL', MSSO_URL . 'includes/images/logo.png');
define('MSSO_LOADER', MSSO_URL . 'includes/images/loader.gif');

define('MSSO_TEST', false);
define('MSSO_DEBUG', false);
define('MSSO_LK_DEBUG', false);

include_lib_files();

/**
 * Both the classes below are common classes. Check if it's being used by
 * another plugin and not include it.
 */
function include_lib_files() {
    if (!class_exists("Firebase\JWT\BeforeValidException")) {
        include('helper/JWT/BeforeValidException.php');
    }
    if (!class_exists("Firebase\JWT\ExpiredException")) {
        include('helper/JWT/ExpiredException.php');
    }
    if (!class_exists("Firebase\JWT\JWK")) {
        include('helper/JWT/JWK.php');
    }
    if (!class_exists("Firebase\JWT\JWT")) {
        include('helper/JWT/JWT.php');
    }
    if (!class_exists("Firebase\JWT\SignatureInvalidException")) {
        include('helper/JWT/SignatureInvalidException.php');
    }
}
require_once('SplClassLoader.php');

// Call the SplClassLoader to auto include all files.
/** @var SplClassLoader $idpclassloader */
$idpclassloader = new SplClassLoader('auth_sso', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
$idpclassloader->register();
