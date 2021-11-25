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
 * Authentication class for sso is defined here.
 *
 * @package   auth_sso
 * @copyright 2021 miniOrange <info@xecurify.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/login/lib.php');
require_once('autoload.php');

// For further information about authentication plugins please read
// https://docs.moodle.org/dev/Authentication_plugins.
//
// The base class auth_plugin_base is located at /lib/authlib.php.
// Override functions as needed.

/**
 * Authentication class for sso.
 */
class auth_plugin_sso extends auth_plugin_base
{

    /**
     * Set the properties of the instance.
     */
    public function __construct() {
        $this->authtype = 'sso';
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param  string $username The username.
     * @param  string $password The password.
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {
        global $CFG, $DB;

        // Validate the login by using the Moodle user table.
        // Remove if a different authentication method is desired.
        $user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id));

        // User does not exist.
        if (!$user) {
            return false;
        }

        return validate_internal_user_password($user, $password);
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param object $config
     * @param object $err
     * @param array $userfields
     */
    public function config_form($config, $err, $userfields) {

        // The form file can be included here.
        // phpcs:ignore moodle.Commenting.InlineComment
        // include('config.html');

    }

    /**
     * Processes and stores configuration data for the plugin.
     *
     * @param stdClass $config Object with submitted configuration settings (without system magic quotes).
     * @return bool True if the configuration was processed successfully.
     */
    public function process_config($config) {
        return true;
    }

    /**
     * Shows an error page for various authentication issues.
     *
     * @param string $msg The error message.
     */
    public function error_page($msg) {
        global $PAGE, $OUTPUT, $SESSION;

        // Clean up $SESSION->wantsurl that was set explicitly in {@see login.php},
        // we don't go anywhere.
        unset($SESSION->wantsurl);

        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url('/auth/sso/error.php');
        $PAGE->set_title(get_string('mossologinerror', 'auth_sso'));
        $PAGE->set_heading(get_string('mossologinerror', 'auth_sso'));
        echo $OUTPUT->header();
        echo $OUTPUT->box($msg, 'generalbox', 'notice');
        echo $OUTPUT->footer();
        exit(1);
    }

    /**
     * The user has completed the Single Sign-On now we can log them in
     *
     * @param array $attributes Decoded attributes from the SSO token
     */
    public function sso_login_complete($attributes) {
        global $CFG, $DB, $USER, $SESSION;

        // TODO: configurable attribute mapping.
        $usernameattribute = 'username';
        if (empty($attributes->$usernameattribute)) {
            // Missing mapping IdP attribute. Login failed.
            $event = \core\event\user_login_failed::create(
                ['other' => ['username' => 'unknown', 'reason' => AUTH_LOGIN_NOUSER] ]
            );
            $event->trigger();
            $this->error_page(get_string('mossologinnoattribute', 'auth_sso', $usernameattribute));
        }

        // Find Moodle user.
        $user = null;
        $uid = $attributes->$usernameattribute;
        // TODO: configurable account matcher.
        if ($user = get_complete_user_data('username', $uid)) {
            // We found a user.
        }

        $newuser = false;
        // TODO: toggle to enable/disable auto create user at the time of SSO.
        $autocreate = true;
        if (!$user) {
            // No existing user.
            if ($autocreate) {
                // TODO: configurable attribute mapping.
                $emailattribute = 'email';
                $email = $attributes->$emailattribute;

                // Honor the core allowemailaddresses setting.
                if ($error = email_is_not_allowed($email)) {
                    $event = \core\event\user_login_failed::create(
                        ['other' => ['username' => $uid, 'reason' => AUTH_LOGIN_FAILED]]
                    );
                    $event->trigger();
                }

                $user = create_user_record($uid, '', 'sso');
                $newuser = true;
            } else {
                // Moodle user does not exist and settings prevent creating new accounts.
                $event = \core\event\user_login_failed::create(
                    ['other' => ['username' => $uid, 'reason' => AUTH_LOGIN_NOUSER]]
                );
                $event->trigger();

                $this->error_page(get_string('mossologinnouser', 'auth_sso', $uid));
            }
        } else {
            // Prevent access to users who are suspended.
            if ($user->suspended) {
                $event = \core\event\user_login_failed::create(
                    ['userid' => $user->id,
                    'other' => [
                        'username' => $user->username,
                        'reason' => AUTH_LOGIN_SUSPENDED,
                    ]]
                );
                $event->trigger();
                $this->error_page(get_string('mossologinsuspendeduser', 'auth_sso', $uid));
            }
        }

        // TODO: configurable allow login for other auth methods.
        $anyauth = false;
        if (!$anyauth && $user->auth != 'sso') {
            $event = \core\event\user_login_failed::create(
                [ 'userid' => $user->id,
                'other' => [
                    'username' => $user->username,
                    'reason' => AUTH_LOGIN_UNAUTHORISED,
                ]]
            );
            $event->trigger();

            $this->error_page(get_string('mossologinwrongauth', 'auth_sso', $uid));
        }

        if ($anyauth && !is_enabled_auth($user->auth)) {
            $event = \core\event\user_login_failed::create(
                ['userid' => $user->id, 'other' => ['username' => $user->username, 'reason' => AUTH_LOGIN_UNAUTHORISED, ]]
            );
            $event->trigger();

            $this->error_page(get_string('anyauthotherdisabled', 'auth_sso', ['username' => $uid, 'auth' => $user->auth, ]));
        }

        // Do we need to update any user fields? Unlike ldap, we can only do
        // this now. We cannot query the IdP at any time.
        $this->update_user_profile_fields($user, $attributes, $newuser);

        // Make sure all user data is fetched.
        $user = get_complete_user_data('username', $user->username);

        complete_user_login($user);
        $USER->loggedin = true;
        $USER->site = $CFG->wwwroot;
        set_moodle_cookie($USER->username);

        $wantsurl = core_login_get_return_url();
        // If we are not on the page we want, then redirect to it (unless this is CLI).
        if (qualified_me() !== false && qualified_me() !== $wantsurl ) {
            // $this->log(__FUNCTION__ . " redirecting to $wantsurl");
            unset($SESSION->wantsurl);
            redirect($wantsurl);
            exit;
        } //else {
             //$this->log(__FUNCTION__ . " continuing onto " . qualified_me() );
        // }

        return;
    }

    /**
     * Checks the field map config for values that update onlogin or when a new user is created
     * and returns true when the fields have been merged into the user object.
     *
     * @param  $user
     * @param  $attributes
     * @param  bool $newuser
     * @return bool true on success
     */
    public function update_user_profile_fields(&$user, $attributes, $newuser = false) {
        global $CFG;

        // TODO: toggle to enable / disable user profile attributes.
        $update = true;

        // TODO: configurable user profile attributes.
        $firstnameattribute = 'firstName';
        $lastnameattribute = 'lastName';
        $emailattribute = 'email';

        $user->firstname = $attributes->$firstnameattribute;
        $user->lastname = $attributes->$lastnameattribute;
        if ($newuser) {
            $user->email = $attributes->$emailattribute;
        }

        if ($update) {
            require_once($CFG->dirroot.'/user/lib.php');
            if ($user->description === true) {
                // Function get_complete_user_data() sets description = true to avoid keeping in memory.
                // If set to true - don't update based on data from this call.
                unset($user->description);
            }
            // We should save the profile fields first so they are present and
            // then we update the user which also fires events which other
            // plugins listen to so they have the correct user data.
            profile_save_data($user);
            user_update_user($user, false);
        }

        return $update;
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param  string|moodle_url $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        $configuration = get_config('auth_sso');
        $idplist = [];
        if (empty($wantsurl)) {
            $wantsurl = '/';
        }

        if ($configuration != null && $configuration != false) {
            // A default icon.
            $idpiconurl = null;
            $idpicon = new pix_icon('i/user', 'Login');
            $url = $configuration->jwtLoginUrl;

            $name = $configuration->jwtIdpName;

            $idplist[] = [
            'url' => $url,
            'icon' => $idpicon,
            'iconurl' => $idpiconurl,
            'name' => $name
            ];
        }
        return $idplist;
    }
}
