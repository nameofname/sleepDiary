<?php

namespace Helpers;

use libraries\Session;
use helpers\Config;

class SessionInstance {
    /**
     * Creates an instance of Session class with normal configurations.
     * Note! Uses config over-rides from helper class Config.
     */
    public static function getInstance () {
        $conf = new Config();

        $config = array(
            'cookie_name' => 'session',
            'domain' => $conf->item('cookie_domain'),
            //'persist_time' => 'nerpadoo!',
            'secure' => false
        );

        return new Session($config);
    }
}
