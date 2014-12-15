<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ronald
 * Date: 6/17/13
 * Time: 8:00 PM
 * To change this template use File | Settings | File Templates.
 */

// TODO !!!!!!! Add user model.
//use Models\User;

require(__DIR__ . '/Auth_Controller.php');

class Login extends Auth_Controller {

    private $email;
    private $pw;

    public function __construct() {
        parent::__construct();

    }

    /**
     * Does the login. Uses the user model to check the credentials, then uses the session library to drop the cookie
     * and update the cookie in the DB if credentials pass.
     */
    public function do_login () {

    }

    public function logout () {}

}

