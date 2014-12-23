<?php
namespace Libraries;

/**
 * Created by JetBrains PhpStorm.
 * User: ronald
 * Date: 5/8/13
 * Time: 12:38 AM
 * To change this template use File | Settings | File Templates.
 */

class Session {

    // A cookie
    private $cookie;

    // The name of the cookie:
    private $cookie_name = 'session';

    // Must in the passed options
    public $domain = null;

    // Life span of the session cookie (6 months)
    public $persist_time = 15870000;

    // Boolean
    public $secure = false;

    /**
     * Construct function grabs the CI instance, note that the DB is not connected here, called as needed.
     * @param $options <array>
     *      - $domain <string> - ***Required
     *      - $cookie_name <string> - the name of the cookie used to store the session token.
     *      - $persist_time <number> - time in seconds to persist the cookie
     *      - $secure <boolean> - https if true
     * @throws \ErrorException
     */
    function __construct($options) {

        $option_names = array('cookie_name', 'domain', 'persist_time', 'secure');

        // Assign each option to the top level
        foreach ($option_names as $o_name) {
            if (isset($options[$o_name])) {
                $this->$o_name = $options[$o_name];
            }
        }

        // Domain is required :
        if (!$this->domain) {
            throw new \ErrorException('A "domain" option is required to use Session Class.');
        }

        $this->_init();
    }

    private function _init () {
        // Get the cookie:
        $this->cookie = isset($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : null;
    }

    /**
     * Read the session cookie.
     */
    public function read_session() {
        // TODO: DECRYPT THE COOKIE HERE SO THAT THE DATA IS SECURE!!!
        $session = $this->cookie;
        return $session;
    }

    /**
     * Sets the user session on the client machine.
     * This is used whenever the user logs in, or when we want to create a new user from scratch.
     */
    public function create_session() {
        $new_sessid = $this->_generate_token();
        return $new_sessid;
    }

    /**
     * Sets the session for the client:
     * @param $cookie
     * @return bool
     */
    public function set_session($cookie) {
        $expiration = time() + $this->persist_time;
        $domain = $this->domain;
        $delete_time = time() - 3600;

        // Delete the old cookie first :
        setcookie($this->cookie_name, '', $delete_time, '/', $domain, $this->secure);

        if (!setcookie($this->cookie_name, $cookie, $expiration, "/", $domain, $this->secure)) {
            return false;
        }
        return $cookie;
    }

    /**
     * Generates a new session token.
     * @return string
     */
    private function _generate_token() {
        // Init and create the next session ID.
        $new_sessid = '';
        while (strlen($new_sessid) < 32)
        {
            $new_sessid .= mt_rand(0, mt_getrandmax());
        }

        // Turn it into a hash
        $new_sessid = md5(uniqid($new_sessid, TRUE));

        $ip = $this->_get_ip() || '';
        $new_sessid .= $ip;

        return $new_sessid;
    }

    /**
     * @private helper to get client IP address.
     * @return mixed
     */
    private function _get_ip () {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }

}
