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

    /**
     * Construct function grabs the CI instance, note that the DB is not connected here, called as needed.
     * @param $options <array>
     *      - $domain <string> - ***Required
     *      - $cookie_name <string> - the name of the cookie used to store the session token.
     * @throws \ErrorException
     */
    function __construct($options) {

        $option_names = array('cookie_name', 'domain');

        // Assign each option to the top level
        foreach ($option_names as $o_name) {
            if ($options[$o_name]) {
                $this->cookie_name = $options[$o_name];
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
     * Sets the user session. This is used whenever the user logs in, or when we want to create a new user from scratch
     */
    public function create_session() {

        $new_sessid = $this->_generate_token();

        // Create the cookie with the UID and the session ID.
        $cookie_arr = array(
            'name' => $this->cookie_name,
            'value'	=> $new_sessid,
            'expire' => '15870000', // 6 months.
        );

        // Set the session on the client machine.
        $this->_set_session($cookie_arr);

        return $new_sessid;
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

        // To make the session ID even more secure we'll combine it with the user's IP
        // TODO !!! REMOVE THE FOLLOWING SO I DON'T RELY ON CI AT ALL.
        $new_sessid .= $this->CI->input->ip_address();

        return $new_sessid;
    }

    /**
     * Helper function.
     * Sets the session for the client:
     * @param $cookie <string> - the cookie array.
     */
    private function _set_session($cookie) {
        $this->CI->input->set_cookie($cookie);
    }

}
