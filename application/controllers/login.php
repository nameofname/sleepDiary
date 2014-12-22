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

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_Model');
    }

    public function login () {
        $data = $this->_get_data();
        die(json_encode($data));
    }

    public function register () {
        $data = $this->_get_data();
        $new_user = $this->User_Model->create($data);
        return $this->_send_output($new_user);
    }

    /**
     * Does the login. Uses the user model to check the credentials, then uses the session library to drop the cookie
     * and update the cookie in the DB if credentials pass.
     */
    private function _do_login () {

    }

//    public function logout () {}

    private function _get_data () {
        header('Content-type: application/json');
        $data = json_decode(file_get_contents('php://input'),true);
        return $data ? $data : null;
    }

    private function _send_output ($data) {
        echo json_encode($data);
    }

}
