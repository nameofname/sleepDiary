<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ronald
 * Date: 6/17/13
 * Time: 8:00 PM
 * To change this template use File | Settings | File Templates.
 */

require(__DIR__ . '/Auth_Controller.php');
use Helpers\SessionInstance;

class Login extends Auth_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_Model');
        $this->session = SessionInstance::getInstance();
    }

    /**
     * Login is done over ajax (unsecure I know) and returns the user info on success.
     */
    public function login () {
        $data = $this->_get_data();
        $existing_user = $this->User_Model->by_login($data);

        if ($existing_user) {
            // Set the session on the client machine :
            $this->session->set_session($existing_user->token);
            return $this->_send_output($existing_user);

        } else {
            $error = new stdClass();
            $error->status = 'login_failed';
            $error->message = 'Your login failed. Please try again with different credentials.';
            return $this->_send_output($error, 400);
        }
    }

    /**
     * Register a new user. First check to see if an existing user with the same email exists :
     */
    public function register () {
        $data = $this->_get_data();

        if ($data === 'missing_data') {
            $error = new stdClass();
            $error->status = $data;
            $error->message = 'An email address and a password are required to create an account.';
            return $this->_send_output($error, 400);
        }

        $existing_user = $this->User_Model->by_email($data['email']);
        if ($existing_user)
        {
            $error = new stdClass();
            $error->status = 'existing_user';
            $error->message = 'A user with this email already exists. Please try again with another email or login.';
            return $this->_send_output($error, 400);
        }

        $new_user = $this->User_Model->create($data);

        // Set the session on the client machine :
        $this->session->set_session($new_user->token);

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

        if (empty($data['email']) || empty($data['password'])) {
            return 'missing_data';
        }

        return $data ? $data : null;
    }

}
