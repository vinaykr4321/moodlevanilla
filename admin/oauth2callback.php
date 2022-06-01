<?php

// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * An oauth2 redirection endpoint which can be used for an application:
 * http://tools.ietf.org/html/draft-ietf-oauth-v2-26#section-3.1.2
 *
 * This is used because some oauth servers will not allow a redirect urls
 * with get params (like repository callback) and that needs to be called
 * using the state param.
 *
 * @package    core
 * @copyright  2012 Dan Poltawski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

$error = optional_param('error', '', PARAM_RAW);
if ($error) {
    $message = optional_param('error_description', '', PARAM_RAW);
    if ($message) {
        $SESSION->loginerrormsg = $message;
        redirect(new moodle_url(get_login_url()));
    } else {
        $SESSION->loginerrormsg = $error;
        redirect(new moodle_url(get_login_url()));
    }
}

// The authorization code generated by the authorization server.
$code = required_param('code', PARAM_RAW);
// The state parameter we've given (used in moodle as a redirect url).
$state = required_param('state', PARAM_LOCALURL);

$redirecturl = new moodle_url($state);
$params = $redirecturl->params();

if (isset($params['sesskey']) and confirm_sesskey($params['sesskey'])) {
    $redirecturl->param('oauth2code', $code);
    redirect($redirecturl);
} else {
    $SESSION->loginerrormsg = get_string('invalidsesskey', 'error');
    redirect(new moodle_url(get_login_url()));
}
