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
 * Process JWT Response, and retrieve the user profile attributes.
 *
 * @package     auth_sso
 * @copyright   2021 miniOrange <info@xecurify.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_sso\Handler;

defined('MOODLE_INTERNAL') || die();

use auth_sso\Helper\Traits\Instance;
use \Firebase\JWT\JWT;
use auth_sso\Helper\Utilities\MoSSOUtility;

class ProcessJWTResponseHandler
{
    use Instance;

    /**
     * It reads the JWT token
     *
     * @param mixed  $request contains the JWT response coming from Identity Provider
     * @param  mixed $key     contains the Id_token
     * @return void
     */
    public function read_token($request, $key) {
        $jwtconfig  = get_config('auth_sso');
        $token      = $_GET[$key];
        $attr       = $this->jwt_decode_data($token, $jwtconfig->jwtSignAlg, $jwtconfig->jwtIdpCert);
        $ssoplugin  = get_auth_plugin('sso');
        $ssoplugin->sso_login_complete($attr);
    }
    /**
     * Purpose of this function is to decode token
     * If token is not valid then it will throw an exception
     *
     * @param mixed $jwttoken contains token
     * @param mixed $$signaturealgo contains Algo
     * @param mixed $jwtsecret conatains JWT secret key
     * @return void
     */
    public function jwt_decode_data($jwttoken, $signaturealgo, $jwtsecret) {
        try {
            $decode = JWT::decode($jwttoken, $jwtsecret, array($signaturealgo));
            return $decode;
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $e->getMessage();
        }
        catch(\Firebase\JWT\SignatureInvalidException $e){
            return $e->getMessage();
        }
        catch(\Firebase\JWT\BeforeValidException $e){
            return $e->getMessage();
        }
        catch(\DomainException $e){
            return $e->getMessage();
        }
        catch(\InvalidArgumentException $e){
            return $e->getMessage();
        }
        catch(\UnexpectedValueException $e){
            return $e->getMessage();
        }
    }
}



