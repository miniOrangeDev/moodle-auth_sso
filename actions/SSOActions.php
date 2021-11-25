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

namespace auth_sso\Actions;

use auth_sso\Helper\Traits\Instance;
use auth_sso\Handler\ProcessJWTResponseHandler;

class SSOActions
{
    use Instance;

    /** @var ProcessJWTResponseHandler $processJWTResponseHandler */
    private $_processjwtresponsehandler;

    private $_requestparams = array ('jwt', 'token', 'id_token', 'jwt_token');

    private function __construct() {
        $this->_processjwtresponsehandler = ProcessJWTResponseHandler::instance();
    }

    /**
     * Handles all the SSO operations. Checks if there is
     * any token response made to the site.
     * Checks for any exceptions that may occur.
     */
    public function handle_sso() {
        $keys = array_keys($_REQUEST);
        $operation = array_intersect($keys, $this->_requestparams);
        if (count($operation) <= 0) {
            return;
        }
        try {
            $this->route_data(array_values($operation)[0]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Route the request data to appropriate functions for processing.
     * Check for any kind of Exception that may occur during processing
     * of form post data.
     *
     * @param $op - refers to operation to perform
     *
     * @throws InvalidSSOUserException
     * @throws InvalidServiceProviderException
     * @throws InvalidSignatureInRequestException
     * @throws NotRegisteredException
     * @throws \IDP\Exception\InvalidOperationException
     * @throws \IDP\Exception\MissingWaAttributeException
     * @throws \IDP\Exception\MissingWtRealmAttributeException
     */
    public function route_data($op) {
        switch ($op)
        {
            case $this->_requestparams[0]:
                $this->_processjwtresponsehandler->read_token($_REQUEST, $this->_requestparams[0]);
                break;
            case $this->_requestparams[1]:
                $this->_processjwtresponsehandler->read_token($_REQUEST, $this->_requestparams[1]);
                break;
            case $this->_requestparams[2]:
                $this->_processjwtresponsehandler->read_token($_REQUEST, $this->_requestparams[2]);
                break;
            case $this->_requestparams[3]:
                $this->_processjwtresponsehandler->read_token($_REQUEST, $this->_requestparams[3]);
                break;
        }
    }
}
