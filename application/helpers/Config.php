<?php
/**
 * User: ronald
 * Date: 12/21/14
 * Time: 1:44 PM
 */

namespace Helpers;

/**
 * Configuration class - gets applied config over-rides from config over-rides file.
 * Usage :
 *      use Helpers\Config;
 *      $conf = new Config();
 *      $cd = $conf->item('cookie_domain');
 * Class Config
 * @package Helpers
 */
class Config {

    public function __construct () {
        $this->CI = &get_instance();
        $this->CI->config->load('configOverRides');
    }

    public function item ($s) {
        return $this->CI->config->item($s);
    }
}